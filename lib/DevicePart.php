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
use PartDB\Exceptions\DatabaseException;
use PartDB\Exceptions\ElementNotExistingException;
use PartDB\Exceptions\InvalidElementValueException;
use PartDB\Permissions\DevicePartPermission;
use PartDB\Permissions\PermissionManager;

/**
 * @file DevicePart.php
 * @brief class DevicePart
 *
 * @class DevicePart
 * All elements of this class are stored in the database table "device_parts".
 *
 * A DevicePart contains a Part-object and a Device-object. This class "connects" this two objects.
 * In addition to these two objects, there are the attributes "mount quantity" and "mount name".
 *
 * @author kami89
 */
class DevicePart extends Base\DBElement
{

    const TABLE_NAME = "device_parts";

    /********************************************************************************
     *
     *   Calculated Attributes
     *
     *   Calculated attributes will be NULL until they are requested for first time (to save CPU time)!
     *   After changing an element attribute, all calculated data will be NULLed again.
     *   So: the calculated data will be cached.
     *
     *********************************************************************************/

    /** @var Device the device of this device-part */
    private $device     = null;
    /** @var Part the part of this device-part */
    private $part       = null;

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /** This creates a new Element object, representing an entry from the Database.
     *
     * @param Database $database reference to the Database-object
     * @param User $current_user reference to the current user which is logged in
     * @param Log $log reference to the Log-object
     * @param integer $id ID of the element we want to get
     * @param array $db_data If you have already data from the database,
     * then use give it with this param, the part, wont make a database request.
     *
     * @throws \PartDB\Exceptions\TableNotExistingException If the table is not existing in the DataBase
     * @throws \PartDB\Exceptions\DatabaseException If an error happening during Database AccessDeniedException
     * @throws \PartDB\Exceptions\ElementNotExistingException If no such element exists in DB.
     */
    public function __construct(&$database, &$current_user, &$log, $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $data);
    }

    /**
     * @copydoc DBElement::reset_attributes()
     */
    public function resetAttributes(bool $all = false)
    {
        $this->device = null;
        $this->part = null;

        parent::resetAttributes($all);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the device of this device-part
     *
     * @return Device       the device of this device-part
     *
     * @throws DatabaseException
     */
    public function getDevice() : Device
    {
        if (! is_object($this->device)) {
            $this->device = Device::getInstance(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['id_device']
            );
        }

        return $this->device;
    }

    /**
     *  Get the part of this device-part
     *
     * @return Part      the part of this device-part
     *
     * @throws DatabaseException
     */
    public function getPart() : Part
    {
        if (! is_object($this->part)) {
            $this->part = Part::getInstance(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['id_part']
            );
        }

        return $this->part;
    }

    /**
     *  Get the mount quantity of this device-part
     *
     * @return integer      the mount quantity
     */
    public function getMountQuantity() : int
    {
        if (!$this->current_user->canDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::READ)) {
            return 1;
        }

        return (int) $this->db_data['quantity'];
    }

    /**
     *  Get the mount name(s)
     *
     * @note    The mountname(s) attribute is simply a string. You can use it for what you want...
     *
     * @return string       the mountname(s)
     */
    public function getMountNames() : string
    {
        if (!$this->current_user->canDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::READ)) {
            return "???";
        }

        return $this->db_data['mountnames'];
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     *  Set the mount quantity
     *
     * @param integer $new_mount_quantity       the new mount quantity
     *
     * @throws Exception if the mount quantity is not valid
     * @throws Exception if there was an error
     */
    public function setMountQuantity(int $new_mount_quantity)
    {
        $this->setAttributes(array('quantity' => $new_mount_quantity));
    }

    /**
     *  Set the mount name(s)
     *
     * @note    The mountname(s) attribute is simply a string. You can use it for what you want...
     *
     * @param string $new_mount_names      the new mount name(s)
     *
     * @throws Exception if there was an error
     */
    public function setMountNames(string $new_mount_names)
    {
        $this->setAttributes(array('mountnames' => $new_mount_names));
    }

    /********************************************************************************
     *
     *   Table Builder Methods
     *
     *********************************************************************************/

    /**
     * @copydoc Part::build_template_table_row_array()
     * @throws Exception
     */
    public function buildTemplateTableRowArray($table_type, $row_index, $additional_values = array())
    {
        //$single_prices = $this->get_part()->get_prices(false, '<br>', $this->get_mount_quantity(), 1);
        //$total_prices = $this->get_part()->get_prices(false, '<br>', $this->get_mount_quantity());

        $single_prices_loop = array();
        foreach ($this->getPart()->getPrices(false, null, $this->getMountQuantity(), 1, true) as $price) { // prices from obsolete orderdetails will not be shown
            $single_prices_loop[] = array(  'row_index'     => $row_index,
                'single_price'  => $price);
        }

        $total_prices_loop = array();
        foreach ($this->getPart()->getPrices(false, null, $this->getMountQuantity(), null, true) as $price) { // prices from obsolete orderdetails will not be shown
            $total_prices_loop[] = array(   'row_index'     => $row_index,
                'total_price'   => $price);
        }

        // We override "not_enought_instock" from the class Part, because here it's less relevant
        // if the part should be ordered (instock < mininstock). More importand is to know if
        // there are enought parts instock to build the device (instock < [mount]quantity)!
        $not_enought_instock = ($this->getMountQuantity() > $this->getPart()->getInstock());

        $additional_values = array(
            'id'                    => array('id'                   => $this->getID()),
            'single_prices'         => array('single_prices'        => $single_prices_loop),
            'total_prices'          => array('total_prices'         => $total_prices_loop),
            'mountnames'            => array('mountnames'           => $this->getMountNames()),
            'mountnames_edit'       => array('mountnames'           => $this->getMountNames()),
            'quantity'              => array('quantity'             => $this->getMountQuantity()),
            'quantity_edit'         => array('quantity'             => $this->getMountQuantity()),
            'instock'               => array('not_enought_instock'  => $not_enought_instock),
            'instock_mininstock'    => array('not_enought_instock'  => $not_enought_instock));

        $table_row = $this->getPart()->buildTemplateTableRowArray($table_type, $row_index, $additional_values);
        $table_row['id'] = $this->getID(); // we want the DevicePart ID, not the Part ID!!

        return $table_row;
    }

    /**
     * @copydoc Part::build_template_table_array()
     * @throws Exception
     */
    public static function buildTemplateTableArray($parts, $table_type)
    {
        return Part::buildTemplateTableArray($parts, $table_type);
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * @copydoc DBElement::check_values_validity()
     * @throws Exception
     */
    public static function checkValuesValidity(Database &$database, User &$current_user, Log &$log, array &$values, bool $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        // check "id_device"
        try {
            if ($values['id_device'] == 0) {
                throw new
                InvalidElementValueException(_('Der obersten Ebene können keine Bauteile zugeordnet werden!'));
            }

            $device = Device::getInstance($database, $current_user, $log, $values['id_device']);
        } catch (ElementNotExistingException $e) {
            throw new InvalidElementValueException(
                sprintf(_('Es existiert keine Baugruppe mit der ID "%d"!'), $values['id_device'])
            );
        }

        // check "id_part"
        try {
            $part = Part::getInstance($database, $current_user, $log, $values['id_part']);
        } catch (Exception $e) {
            throw new InvalidElementValueException(
                sprintf(_('Es existiert kein Bauteil mit der ID "%d"!'), $values['id_part'])
            );
        }

        // check "quantity"
        if (((! is_int($values['quantity'])) && (! ctype_digit($values['quantity'])))
            || ($values['quantity'] < 0)) {
            throw new InvalidElementValueException(
                sprintf(_('Die Bestückungs-Anzahl "%d" ist ungültig!'), $values['quantity'])
            );
        }
    }

    /**
     *  Get the DevicePart by part_id + device_id (if exists)
     *
     * @param Database  &$database          reference to the Database-object
     * @param User      &$current_user      reference to the current user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param integer   $device_id          the ID of the device
     * @param integer   $part_id            the ID of the part
     *
     * @return  DevicePart      the found DevicePart
     * @return  NULL            if there is no such DevicePart
     *
     * @throws Exception if there was an error
     */
    public static function getDevicePart(Database &$database, User &$current_user, Log &$log, int $device_id, int $part_id)
    {
        $query_data = $database->query(
            'SELECT id FROM device_parts '.
            'WHERE id_device=? AND id_part=? LIMIT 1',
            array($device_id, $part_id)
        );

        if (count($query_data) > 0) {
            return DevicePart::getInstance($database, $current_user, $log, $query_data[0]['id']);
        } else {
            return null;
        }
    }

    /**
     *  Get all device parts which should be ordered (device marked manually as "to order")
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param integer   $part_id            If this is not NULL, only DevicePart objects with that part_id will be returned
     *
     * @return array    all device parts as a one-dimensional array of DevicePart objects
     *
     * @throws Exception if there was an error
     */
    public static function getOrderDeviceParts(Database &$database, User &$current_user, Log &$log, $part_id = null) : array
    {
        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $device_parts = array();

        $query =    'SELECT device_parts.* FROM device_parts '.
            'LEFT JOIN devices ON devices.id = device_parts.id_device '.
            'WHERE devices.order_quantity > 0 ';
        if ($part_id) {
            $query .= 'AND device_parts.id_part = ? ';
        }
        $query .=   'GROUP BY device_parts.id';

        $query_data = $database->query($query, ($part_id ? array($part_id) : array()));

        foreach ($query_data as $row) {
            $device_parts[] = DevicePart::getInstance($database, $current_user, $log, $row['id'], $row);
        }

        return $device_parts;
    }

    public function delete()
    {
        $this->current_user->tryDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::DELETE);
        parent::delete();
    }

    public function setAttributes(array $new_values)
    {
        $this->current_user->tryDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::EDIT);
        parent::setAttributes($new_values);
    }


    /**
     * Returns the ID as an string, defined by the element class.
     * This should have a form like P000014, for a part with ID 14.
     * @return string The ID as a string;
     */
    public function getIDString(): string
    {
        return "DP" . sprintf("%06d", $this->getID());
    }

    /**
     *  Create a new device-part
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the current user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param integer   $device_id          the ID of the device
     * @param integer   $part_id            the ID of the part
     * @param integer   $quantity           the mount quantity (see DevicePart::set_mount_quantity())
     * @param string    $mountnames         the mountname(s) (see DevicePart::set_mount_name())
     * @param boolean   $increase_if_exist  @li if true, and there is already a DevicePart with the same
     *                                          part ID + device ID, the mount quantity of the existing
     *                                          DevicePart will be incremented by $quantity. In addition,
     *                                          the new mount name ($mountname) will be attached (with a
     *                                          comma) at the end of the mount name of the existing DevicePart.
     *                                      @li if false, and there is already a DevicePart with the same
     *                                          part ID + device ID, this method will throw an exception.
     *
     * @return Base\DBElement|DevicePart
     * @return DevicePart   the existing device-part, if there is already a DevicePart with
     *                      the same part ID + device ID and "$increment_if_exist == true"
     *
     * @throws Exception    if (this combination of) values is not valid
     * @throws Exception    if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(
        Database &$database,
        User &$current_user,
        Log &$log,
        int $device_id,
        int $part_id,
        int $quantity,
        string $mountnames = '',
        bool $increase_if_exist = false
    ) {
        $current_user->tryDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::CREATE);

        $existing_devicepart = DevicePart::getDevicePart($database, $current_user, $log, $device_id, $part_id);
        if (is_object($existing_devicepart)) {
            if ($increase_if_exist) {
                if (((! is_int($quantity)) && (! ctype_digit($quantity))) || ($quantity < 0)) {
                    throw new Exception(_('Die Bestückungs-Anzahl ist ungültig!'));
                }

                $quantity = $existing_devicepart->getMountQuantity() + $quantity;

                $old_mountnames = $existing_devicepart->getMountNames();
                if (strlen($mountnames) > 0) {
                    if (strlen($old_mountnames) > 0) {
                        $mountnames = $old_mountnames . ', ' . $mountnames;
                    }
                } else {
                    $mountnames = $old_mountnames;
                }

                $existing_devicepart->setAttributes(array( 'quantity'      => $quantity,
                    'mountnames'    => $mountnames));

                return $existing_devicepart;
            } else {
                $device = Device::getInstance($database, $current_user, $log, $device_id);
                $part = Part::getInstance($database, $current_user, $log, $part_id);

                throw new Exception(sprintf(_('Die Baugruppe "%1$s"'.
                    ' enthält bereits das Bauteil "%2$s"!'), $device->getName(), $part->getName()));
            }
        }

        // there is no such DevicePart, so we will create it
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            array(  'id_device'     => $device_id,
                'id_part'       => $part_id,
                'quantity'      => $quantity,
                'mountnames'    => $mountnames)
        );
    }
}
