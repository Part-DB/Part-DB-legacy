<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

namespace PartDB;

use Exception;

/**
 * @file Device.php
 * @brief class Device
 *
 * @class Device
 * All elements of this class are stored in the database table "devices".
 *
 *    There cannot be more than one DeviceParts with the same Part in a Device!
 *          The Reason is, that it would be quite complicated to "calculate" if there are enough parts in stock.
 *          Example: If there is the same Part in a Device two times and only one Part in stock.
 *          Which one should be marked as "enought in stock", which one as "not enought in stock"?
 *          So it's better if every Part can only be one time in a Device...
 *
 * @author kami89
 */
class Device extends Base\PartsContainingDBElement
{
    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @note  It's allowed to create an object with the ID 0 (for the root element).
     *
     * @param Database  &$database          reference to the Database-object
     * @param User      &$current_user      reference to the current user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param integer   $id                 ID of the device we want to get
     *
     * @throws Exception        if there is no such device in the database
     * @throws Exception        if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, 'devices', $id, $data);

        if ($id == 0) {
            // this is the root node
            $this->db_data['order_quantity'] = 0;
            $this->db_data['order_only_missing_parts'] = false;
        }
    }

    /********************************************************************************
     *
     *   Basic Methods
     *
     *********************************************************************************/

    /**
     *  Delete this device (with all DeviceParts in it)
     *
     * @note    This function overrides the same-named function from the parent class.
     * @note    The DeviceParts in this device will be deleted too (not the parts itself,
     *          but the entries in the table "device_parts").
     *
     * @param boolean $delete_recursive         If true, all subdevices will be deleted too (!!)
     * @param boolean $delete_files_from_hdd    @li if true, the attached files of this device will be deleted from
     *                                              harddisc drive (!) See AttachementContainingDBElement::delete()
     *                                          @li if false, only the attachement records in the database will be
     *                                              deleted, but not the files on the harddisc
     *
     * @throws Exception if there was an error
     *
     * @see FilesContainingDBElement::delete()
     * @see DevicePart::delete()
     */
    public function delete($delete_recursive = false, $delete_files_from_hdd = false)
    {
        try {
            $transaction_id = $this->database->beginTransaction(); // start transaction

            // work on subdevices (delete or move up)
            $subdevices = $this->getSubelements(false);
            foreach ($subdevices as $device) {
                if ($delete_recursive) {
                    $device->delete($delete_recursive, $delete_files_from_hdd);
                } // delete all subdevices
                else {
                    $device->setParentID($this->getParentID());
                } // set new parent ID
            }

            // delete all device-parts in this device
            $device_parts = $this->getParts(false); // DevicePart object, not Part objects!
            $this->resetAttributes(); // to set $this->parts to NULL
            foreach ($device_parts as $device_part) {
                $device_part->delete();
            }

            // now we can delete this element + all attachements of it
            parent::delete($delete_files_from_hdd);

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->resetAttributes();

            throw new Exception("Die Baugruppe \"".$this->getName()."\" konnte nicht gelöscht werden!\nGrund: ".$e->getMessage());
        }
    }

    /**
     *  Create a new Device as a copy from this one. All DeviceParts will be copied too.
     *
     * @param string $name                  The name of the new device
     * @param integer $parent_id            The ID of the new device's parent device
     * @param boolean   $with_subdevices    If true, all subdevices will be copied too
     *
     * @throws Exception if there was an error
     */
    public function copy($name, $parent_id, $with_subdevices = false)
    {
        try {
            if (($with_subdevices) && ($parent_id > 0)) { // the root node (ID 0 or -1) is always allowed as the parent object
                // check if $parent_id is NOT a child of this device
                $parent_device = new Device($this->database, $this->current_user, $this->log, $parent_id);

                if (($parent_device->getID() == $this->getID()) || ($parent_device->isChildOf($this))) {
                    throw new Exception('Eine Baugruppe kann nicht in sich selber kopiert werden!');
                }
            }

            $transaction_id = $this->database->beginTransaction(); // start transaction

            $new_device = Device::add($this->database, $this->current_user, $this->log, $name, $parent_id);

            $device_parts = $this->getParts();
            foreach ($device_parts as $part) {
                /** @var DevicePart $part */
                $new_part = DevicePart::add(
                    $this->database,
                    $this->current_user,
                    $this->log,
                    $new_device->getID(),
                    $part->getPart()->getID(),
                    $part->getMountQuantity(),
                    $part->getMountNames()
                );
            }

            if ($with_subdevices) {
                $subdevices = $this->getSubelements(false);
                foreach ($subdevices as $device) {
                    $device->copy($device->getName(), $new_device->getID(), true);
                }
            }

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            throw new Exception("Die Baugruppe \"".$this->getName()."\"konnte nicht kopiert werden!\nGrund: ".$e->getMessage());
        }
    }

    /**
     *  Book all parts (decrease or increase instock)
     *
     * @note    This method will book all parts depending on their "mount_quantity".
     *          @li Example with $book_multiplier = 2:
     *              @li The "instock" of a DevicePart with "mount_quantity = 1" will be reduced by "2".
     *              @li The "instock" of a DevicePart with "mount_quantity = 4" will be reduced by "8".
     *
     * @param integer   $book_multiplier    @li if positive: the instock of the parts will be DEcreased
     *                                      @li if negative: the instock of the parts will be INcreased
     *
     * @throws Exception    if there are not enough parts in stock to book them
     * @throws Exception    if there was an error
     */
    public function bookParts($book_multiplier)
    {
        try {
            $transaction_id = $this->database->beginTransaction(); // start transaction
            $device_parts = $this->getParts(); // DevicePart objects

            // check if there are enought parts in stock
            foreach ($device_parts as $part) {
                /** @var DevicePart $part */
                if (($part->getMountQuantity() * $book_multiplier) > $part->getPart()->getInstock()) {
                    throw new Exception('Es sind nicht von allen Bauteilen genügend an Lager');
                }
            }

            // OK there are enough parts in stock, we will book them
            foreach ($device_parts as $part) {
                /** @var DevicePart $part  */
                $part->getPart()->setInstock($part->getPart()->getInstock() - ($part->getMountQuantity() * $book_multiplier));
            }

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->resetAttributes();

            throw new Exception("Die Teile konnten nicht abgefasst werden!\nGrund: ".$e->getMessage());
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     *  Get the order quantity of this device
     *
     * @return integer      the order quantity
     */
    public function getOrderQuantity()
    {
        return $this->db_data['order_quantity'];
    }

    /**
     *  Get the "order_only_missing_parts" attribute
     *
     * @return boolean      the "order_only_missing_parts" attribute
     */
    public function getOrderOnlyMissingParts()
    {
        return $this->db_data['order_only_missing_parts'];
    }

    /**
     *  Get all device-parts of this device
     *
     * @note    This method overrides the same-named method of the parent class.
     * @note    The attribute "$this->parts" will be used to store the parts.
     *          (but there will be stored DevicePart-objects instead of Part-objects)
     *
     * @param boolean $recursive        if true, the parts of all subelements will be listed too
     *
     * @return Part[]        all parts as a one-dimensional array of "DevicePart"-objects,
     *                      sorted by their names (only if "$recursive == false")
     *
     * @throws Exception if there was an error
     */
    public function getParts($recursive = false, $hide_obsolet_and_zero = false)
    {
        if (! is_array($this->parts)) {
            $this->parts = array();

            $query =    'SELECT device_parts.* FROM device_parts '.
                'LEFT JOIN parts ON device_parts.id_part=parts.id '.
                'WHERE id_device=? ORDER BY parts.name ASC';

            $query_data = $this->database->query($query, array($this->getID()));

            foreach ($query_data as $row) {
                $this->parts[] = new DevicePart($this->database, $this->current_user, $this->log, $row['id'], $row);
            }
        }

        if (! $recursive) {
            return $this->parts;
        } else {
            $parts = $this->parts;
            $subdevices = $this->getSubelements(true);

            foreach ($subdevices as $device) {
                $parts = array_merge($parts, $device->getParts(false));
            }

            return $parts;
        }
    }

    /**
     *  Get the count of different parts in this device
     *
     * This method simply returns the count of the returned array of Device::get_parts().
     *
     * @param boolean $recursive        if true, the parts of all subelements will be counted too
     *
     * @return integer      count of different parts in this device
     *
     * @throws Exception if there was an error
     */
    public function getPartsCount($recursive = false)
    {
        $device_parts = $this->getParts($recursive);

        return count($device_parts);
    }

    /**
     *  Get the count of all parts in this device (every part multiplied by its quantity)
     *
     * @param boolean $recursive        if true, the parts of all subelements will be counted too
     *
     * @return integer      count of all parts in this device
     *
     * @throws Exception if there was an error
     */
    public function getPartsSumCount($recursive = false)
    {
        $count = 0;
        $device_parts = $this->getParts($recursive);

        foreach ($device_parts as $device_part) {
            /** @var DevicePart $device_part */
            $count += $device_part->getMountQuantity();
        }

        return $count;
    }

    /**
     *  Get the total price of all parts in this device (counted with their mount quantity)
     *
     * @note        To calculate the price, the average prices of the parts will be used.
     *              More details: Part::get_average_price()
     *
     * @warning     If some parts don't have a price, they will be ignored!
     *              Only parts with at least one price will be counted.
     *
     * @param boolean $as_money_string      @li if true, this method will return the price as a string incl. currency
     *                                      @li if false, this method will return the price as a float
     * @param boolean $recursive            if true, the parts of all subdevicess will be counted too
     *
     * @return string       the price as a formatted string with currency (if "$as_money_string == true")
     * @return float        the price as a float (if "$as_money_string == false")
     *
     * @see floatToMoneyString()
     *
     * @throws Exception if there was an error
     */
    public function getTotalPrice($as_money_string = true, $recursive = false)
    {
        $price = 0;
        $device_parts = $this->getParts($recursive);

        foreach ($device_parts as $device_part) {
            /** @var DevicePart $device_part */
            $price += $device_part->getPart()->getAveragePrice(false, $device_part->getMountQuantity());
        }

        if ($as_money_string) {
            return floatToMoneyString($price);
        } else {
            return $price;
        }
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     *  Set the order quantity
     *
     * @param integer $new_order_quantity       the new order quantity
     *
     * @throws Exception if the order quantity is not valid
     * @throws Exception if there was an error
     */
    public function setOrderQuantity($new_order_quantity)
    {
        $this->setAttributes(array('order_quantity' => $new_order_quantity));
    }

    /**
     *  Set the "order_only_missing_parts" attribute
     *
     * @param boolean $new_order_only_missing_parts       the new "order_only_missing_parts" attribute
     *
     * @throws Exception if there was an error
     */
    public function setOrderOnlyMissingParts($new_order_only_missing_parts)
    {
        $this->setAttributes(array('order_only_missing_parts' => $new_order_only_missing_parts));
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * @copydoc DBElement::check_values_validity()
     */
    public static function checkValuesValidity(&$database, &$current_user, &$log, &$values, $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        // set the datetype of the boolean attributes
        settype($values['order_only_missing_parts'], 'boolean');

        // check "order_quantity"
        if (((! is_int($values['order_quantity'])) && (! ctype_digit($values['order_quantity'])))
            || ($values['order_quantity'] < 0)) {
            debug('error', 'order_quantity = "'.$values['order_quantity'].'"', __FILE__, __LINE__, __METHOD__);
            throw new Exception('Die Bestellmenge ist ungültig!');
        }
    }

    /**
     *  Get all devices which should be ordered (marked manually as "to order")
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     *
     * @return array    all devices as a one-dimensional array of Device objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getOrderDevices(&$database, &$current_user, &$log)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        $devices = array();

        $query =    'SELECT * FROM devices '.
            'WHERE order_quantity > 0 '.
            'ORDER BY name ASC';

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $devices[] = new Device($database, $current_user, $log, $row['id'], $row);
        }

        return $devices;
    }

    /**
     *  Get count of devices
     *
     * @param Database &$database   reference to the Database-object
     *
     * @return integer              count of devices
     *
     * @throws Exception            if there was an error
     */
    public static function getCount(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        return $database->getCountOfRecords('devices');
    }

    /**
     *  Create a new device
     *
     * @param Database  &$database                  reference to the database object
     * @param User      &$current_user              reference to the current user which is logged in
     * @param Log       &$log                       reference to the Log-object
     * @param string    $name                       the name of the new device (see Device::set_name())
     * @param integer   $parent_id                  the parent ID of the new device (see Device::set_parent_id())
     *
     * @return Device       the new device
     *
     * @throws Exception    if (this combination of) values is not valid
     * @throws Exception    if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(&$database, &$current_user, &$log, $name, $parent_id)
    {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            'devices',
            array(  'name'                      => $name,
                'parent_id'                 => $parent_id,
                'order_quantity'            => 0,
                'order_only_missing_parts'  => false)
        );
    }
}
