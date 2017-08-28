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
 * @brief All elements of this class are stored in the database table "devices".
 *
 * @note    There cannot be more than one DeviceParts with the same Part in a Device!
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
     * @brief Constructor
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
    public function __construct(&$database, &$current_user, &$log, $id)
    {
        parent::__construct($database, $current_user, $log, 'devices', $id);

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
     * @brief Delete this device (with all DeviceParts in it)
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
            $transaction_id = $this->database->begin_transaction(); // start transaction

            // work on subdevices (delete or move up)
            $subdevices = $this->get_subelements(false);
            foreach ($subdevices as $device) {
                if ($delete_recursive) {
                    $device->delete($delete_recursive, $delete_files_from_hdd);
                } // delete all subdevices
                else {
                    $device->set_parent_id($this->get_parent_id());
                } // set new parent ID
            }

            // delete all device-parts in this device
            $device_parts = $this->get_parts(false); // DevicePart object, not Part objects!
            $this->reset_attributes(); // to set $this->parts to NULL
            foreach ($device_parts as $device_part) {
                $device_part->delete();
            }

            // now we can delete this element + all attachements of it
            parent::delete($delete_files_from_hdd);

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->reset_attributes();

            throw new Exception("Die Baugruppe \"".$this->get_name()."\" konnte nicht gelöscht werden!\nGrund: ".$e->getMessage());
        }
    }

    /**
     * @brief Create a new Device as a copy from this one. All DeviceParts will be copied too.
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

                if (($parent_device->get_id() == $this->get_id()) || ($parent_device->is_child_of($this))) {
                    throw new Exception('Eine Baugruppe kann nicht in sich selber kopiert werden!');
                }
            }

            $transaction_id = $this->database->begin_transaction(); // start transaction

            $new_device = Device::add($this->database, $this->current_user, $this->log, $name, $parent_id);

            $device_parts = $this->get_parts();
            foreach ($device_parts as $part) {
                $new_part = DevicePart::add(
                    $this->database,
                    $this->current_user,
                    $this->log,
                    $new_device->get_id(),
                    $part->get_part()->get_id(),
                    $part->get_mount_quantity(),
                    $part->get_mount_names()
                );
            }

            if ($with_subdevices) {
                $subdevices = $this->get_subelements(false);
                foreach ($subdevices as $device) {
                    $device->copy($device->get_name(), $new_device->get_id(), true);
                }
            }

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            throw new Exception("Die Baugruppe \"".$this->get_name()."\"konnte nicht kopiert werden!\nGrund: ".$e->getMessage());
        }
    }

    /**
     * @brief Book all parts (decrease or increase instock)
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
    public function book_parts($book_multiplier)
    {
        try {
            $transaction_id = $this->database->begin_transaction(); // start transaction
            $device_parts = $this->get_parts(); // DevicePart objects

            // check if there are enought parts in stock
            foreach ($device_parts as $part) {
                if (($part->get_mount_quantity() * $book_multiplier) > $part->get_part()->get_instock()) {
                    throw new Exception('Es sind nicht von allen Bauteilen genügend an Lager');
                }
            }

            // OK there are enough parts in stock, we will book them
            foreach ($device_parts as $part) {
                $part->get_part()->set_instock($part->get_part()->get_instock() - ($part->get_mount_quantity() * $book_multiplier));
            }

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->reset_attributes();

            throw new Exception("Die Teile konnten nicht abgefasst werden!\nGrund: ".$e->getMessage());
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * @brief Get the order quantity of this device
     *
     * @retval integer      the order quantity
     */
    public function get_order_quantity()
    {
        return $this->db_data['order_quantity'];
    }

    /**
     * @brief Get the "order_only_missing_parts" attribute
     *
     * @retval boolean      the "order_only_missing_parts" attribute
     */
    public function get_order_only_missing_parts()
    {
        return $this->db_data['order_only_missing_parts'];
    }

    /**
     * @brief Get all device-parts of this device
     *
     * @note    This method overrides the same-named method of the parent class.
     * @note    The attribute "$this->parts" will be used to store the parts.
     *          (but there will be stored DevicePart-objects instead of Part-objects)
     *
     * @param boolean $recursive        if true, the parts of all subelements will be listed too
     *
     * @retval array        all parts as a one-dimensional array of "DevicePart"-objects,
     *                      sorted by their names (only if "$recursive == false")
     *
     * @throws Exception if there was an error
     */
    public function get_parts($recursive = false)
    {
        if (! is_array($this->parts)) {
            $this->parts = array();

            $query =    'SELECT device_parts.* FROM device_parts '.
                'LEFT JOIN parts ON device_parts.id_part=parts.id '.
                'WHERE id_device=? ORDER BY parts.name ASC';

            $query_data = $this->database->query($query, array($this->get_id()));

            foreach ($query_data as $row) {
                $this->parts[] = new DevicePart($this->database, $this->current_user, $this->log, $row['id'], $row);
            }
        }

        if (! $recursive) {
            return $this->parts;
        } else {
            $parts = $this->parts;
            $subdevices = $this->get_subelements(true);

            foreach ($subdevices as $device) {
                $parts = array_merge($parts, $device->get_parts(false));
            }

            return $parts;
        }
    }

    /**
     * @brief Get the count of different parts in this device
     *
     * This method simply returns the count of the returned array of Device::get_parts().
     *
     * @param boolean $recursive        if true, the parts of all subelements will be counted too
     *
     * @retval integer      count of different parts in this device
     *
     * @throws Exception if there was an error
     */
    public function get_parts_count($recursive = false)
    {
        $device_parts = $this->get_parts($recursive);

        return count($device_parts);
    }

    /**
     * @brief Get the count of all parts in this device (every part multiplied by its quantity)
     *
     * @param boolean $recursive        if true, the parts of all subelements will be counted too
     *
     * @retval integer      count of all parts in this device
     *
     * @throws Exception if there was an error
     */
    public function get_parts_sum_count($recursive = false)
    {
        $count = 0;
        $device_parts = $this->get_parts($recursive);

        foreach ($device_parts as $device_part) {
            $count += $device_part->get_mount_quantity();
        }

        return $count;
    }

    /**
     * @brief Get the total price of all parts in this device (counted with their mount quantity)
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
     * @retval string       the price as a formatted string with currency (if "$as_money_string == true")
     * @retval float        the price as a float (if "$as_money_string == false")
     *
     * @see float_to_money_string()
     *
     * @throws Exception if there was an error
     */
    public function get_total_price($as_money_string = true, $recursive = false)
    {
        $price = 0;
        $device_parts = $this->get_parts($recursive);

        foreach ($device_parts as $device_part) {
            $price += $device_part->get_part()->get_average_price(false, $device_part->get_mount_quantity());
        }

        if ($as_money_string) {
            return float_to_money_string($price);
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
     * @brief Set the order quantity
     *
     * @param integer $new_order_quantity       the new order quantity
     *
     * @throws Exception if the order quantity is not valid
     * @throws Exception if there was an error
     */
    public function set_order_quantity($new_order_quantity)
    {
        $this->set_attributes(array('order_quantity' => $new_order_quantity));
    }

    /**
     * @brief Set the "order_only_missing_parts" attribute
     *
     * @param boolean $new_order_only_missing_parts       the new "order_only_missing_parts" attribute
     *
     * @throws Exception if there was an error
     */
    public function set_order_only_missing_parts($new_order_only_missing_parts)
    {
        $this->set_attributes(array('order_only_missing_parts' => $new_order_only_missing_parts));
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * @copydoc DBElement::check_values_validity()
     */
    public static function check_values_validity(&$database, &$current_user, &$log, &$values, $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::check_values_validity($database, $current_user, $log, $values, $is_new, $element);

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
     * @brief Get all devices which should be ordered (marked manually as "to order")
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     *
     * @retval array    all devices as a one-dimensional array of Device objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function get_order_devices(&$database, &$current_user, &$log)
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
     * @brief Get count of devices
     *
     * @param Database &$database   reference to the Database-object
     *
     * @retval integer              count of devices
     *
     * @throws Exception            if there was an error
     */
    public static function get_count(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        return $database->get_count_of_records('devices');
    }

    /**
     * @brief Create a new device
     *
     * @param Database  &$database                  reference to the database object
     * @param User      &$current_user              reference to the current user which is logged in
     * @param Log       &$log                       reference to the Log-object
     * @param string    $name                       the name of the new device (see Device::set_name())
     * @param integer   $parent_id                  the parent ID of the new device (see Device::set_parent_id())
     *
     * @retval Device       the new device
     *
     * @throws Exception    if (this combination of) values is not valid
     * @throws Exception    if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(&$database, &$current_user, &$log, $name, $parent_id)
    {
        return parent::add(
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
