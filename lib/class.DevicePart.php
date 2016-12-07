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

    $Id$

    Changelog (sorted by date):
        [DATE]      [NICKNAME]      [CHANGES]
        2012-08-??  kami89          - created
        2012-09-27  kami89          - added doxygen comments
*/

    /**
     * @file class.DevicePart.php
     * @brief class DevicePart
     *
     * @class DevicePart
     * @brief All elements of this class are stored in the database table "device_parts".
     *
     * A DevicePart contains a Part-object and a Device-object. This class "connects" this two objects.
     * In addition to these two objects, there are the attributes "mount quantity" and "mount name".
     *
     * @author kami89
     */
    class DevicePart extends DBElement
    {
        /********************************************************************************
        *
        *   Calculated Attributes
        *
        *   Calculated attributes will be NULL until they are requested for first time (to save CPU time)!
        *   After changing an element attribute, all calculated data will be NULLed again.
        *   So: the calculated data will be cached.
        *
        *********************************************************************************/

        /** @brief (Device) the device of this device-part */
        private $device     = NULL;
        /** @brief (Part) the part of this device-part */
        private $part       = NULL;

        /********************************************************************************
        *
        *   Constructor / Destructor / reset_attributes()
        *
        *********************************************************************************/

        /**
         * @brief Constructor
         *
         * @param Database  &$database          reference to the Database-object
         * @param User      &$current_user      reference to the current user which is logged in
         * @param Log       &$log               reference to the Log-object
         * @param integer   $id                 ID of the device-part we want to get
         *
         * @throws Exception        if there is no such device-part in the database
         * @throws Exception        if there was an error
         */
        public function __construct(&$database, &$current_user, &$log, $id)
        {
            parent::__construct($database, $current_user, $log, 'device_parts', $id);
        }

        /**
         * @copydoc DBElement::reset_attributes()
         */
        public function reset_attributes($all = false)
        {
            $this->device = NULL;
            $this->part = NULL;

            parent::reset_attributes($all);
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get the device of this device-part
         *
         * @retval Device       the device of this device-part
         *
         * @throws Exception if there was an error
         */
        public function get_device()
        {
            if ( ! is_object($this->device))
            {
                $this->device = new Device($this->database, $this->current_user,
                                            $this->log, $this->db_data['id_device']);
            }

            return $this->device;
        }

        /**
         * @brief Get the part of this device-part
         *
         * @retval Part      the part of this device-part
         *
         * @throws Exception if there was an error
         */
        public function get_part()
        {
            if ( ! is_object($this->part))
            {
                $this->part = new Part($this->database, $this->current_user,
                                        $this->log, $this->db_data['id_part']);
            }

            return $this->part;
        }

        /**
         * @brief Get the mount quantity of this device-part
         *
         * @retval integer      the mount quantity
         */
        public function get_mount_quantity()
        {
            return $this->db_data['quantity'];
        }

        /**
         * @brief Get the mount name(s)
         *
         * @note    The mountname(s) attribute is simply a string. You can use it for what you want...
         *
         * @retval string       the mountname(s)
         */
        public function get_mount_names()
        {
            return $this->db_data['mountnames'];
        }

        /********************************************************************************
        *
        *   Setters
        *
        *********************************************************************************/

        /**
         * @brief Set the mount quantity
         *
         * @param integer $new_mount_quantity       the new mount quantity
         *
         * @throws Exception if the mount quantity is not valid
         * @throws Exception if there was an error
         */
        public function set_mount_quantity($new_mount_quantity)
        {
            $this->set_attributes(array('quantity' => $new_mount_quantity));
        }

        /**
         * @brief Set the mount name(s)
         *
         * @note    The mountname(s) attribute is simply a string. You can use it for what you want...
         *
         * @param string $new_mount_names      the new mount name(s)
         *
         * @throws Exception if there was an error
         */
        public function set_mount_names($new_mount_names)
        {
            $this->set_attributes(array('mountnames' => $new_mount_names));
        }

        /********************************************************************************
        *
        *   Table Builder Methods
        *
        *********************************************************************************/

        /**
         * @copydoc Part::build_template_table_row_array()
         */
        public function build_template_table_row_array($table_type, $row_index, $additional_values = array())
        {
            //$single_prices = $this->get_part()->get_prices(false, '<br>', $this->get_mount_quantity(), 1);
            //$total_prices = $this->get_part()->get_prices(false, '<br>', $this->get_mount_quantity());

            $single_prices_loop = array();
            foreach ($this->get_part()->get_prices(false, NULL, $this->get_mount_quantity(), 1, true) as $price) // prices from obsolete orderdetails will not be shown
            {
                $single_prices_loop[] = array(  'row_index'     => $row_index,
                                                'single_price'  => $price);
            }

            $total_prices_loop = array();
            foreach ($this->get_part()->get_prices(false, NULL, $this->get_mount_quantity(), NULL, true) as $price) // prices from obsolete orderdetails will not be shown
            {
                $total_prices_loop[] = array(   'row_index'     => $row_index,
                                                'total_price'   => $price);
            }

            // We override "not_enought_instock" from the class Part, because here it's less relevant
            // if the part should be ordered (instock < mininstock). More importand is to know if
            // there are enought parts instock to build the device (instock < [mount]quantity)!
            $not_enought_instock = ($this->get_mount_quantity() > $this->get_part()->get_instock());

            $additional_values = array(
                    'id'                    => array('id'                   => $this->get_id()),
                    'single_prices'         => array('single_prices'        => $single_prices_loop),
                    'total_prices'          => array('total_prices'         => $total_prices_loop),
                    'mountnames'            => array('mountnames'           => $this->get_mount_names()),
                    'mountnames_edit'       => array('mountnames'           => $this->get_mount_names()),
                    'quantity'              => array('quantity'             => $this->get_mount_quantity()),
                    'quantity_edit'         => array('quantity'             => $this->get_mount_quantity()),
                    'instock'               => array('not_enought_instock'  => $not_enought_instock),
                    'instock_mininstock'    => array('not_enought_instock'  => $not_enought_instock));

            $table_row = $this->get_part()->build_template_table_row_array($table_type, $row_index, $additional_values);
            $table_row['id'] = $this->get_id(); // we want the DevicePart ID, not the Part ID!!

            return $table_row;
        }

        /**
         * @copydoc Part::build_template_table_array()
         */
        public static function build_template_table_array($parts, $table_type)
        {
            return Part::build_template_table_array($parts, $table_type);
        }

        /********************************************************************************
        *
        *   Static Methods
        *
        *********************************************************************************/

        /**
         * @copydoc DBElement::check_values_validity()
         */
        public static function check_values_validity(&$database, &$current_user, &$log, &$values, $is_new, &$element = NULL)
        {
            // first, we let all parent classes to check the values
            parent::check_values_validity($database, $current_user, $log, $values, $is_new, $element);

            // check "id_device"
            try
            {
                if ($values['id_device'] == 0)
                    throw new Exception(_('Der obersten Ebene können keine Bauteile zugeordnet werden!'));

                $device = new Device($database, $current_user, $log, $values['id_device']);
            }
            catch (Exception $e)
            {
                debug('error', 'Ungültige "id_device": "'.$values['id_device'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception(sprintf(_('Es existiert keine Baugruppe mit der ID "%d"!') ,$values['id_device']));
            }

            // check "id_part"
            try
            {
                $part = new Part($database, $current_user, $log, $values['id_part']);
            }
            catch (Exception $e)
            {
                debug('error', 'Ungültige "id_part": "'.$values['id_part'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception(sprintf(_('Es existiert kein Bauteil mit der ID "%d"!'), $values['id_part']));
            }

            // check "quantity"
            if ((( ! is_int($values['quantity'])) && ( ! ctype_digit($values['quantity'])))
                || ($values['quantity'] < 0))
            {
                debug('error', 'quantity = "'.$values['quantity'].'"', __FILE__, __LINE__, __METHOD__);
                throw new Exception(sprintf(_('Die Bestückungs-Anzahl "%d" ist ungültig!'), $values['quantity']));
            }
        }

        /**
         * @brief Get the DevicePart by part_id + device_id (if exists)
         *
         * @param Database  &$database          reference to the Database-object
         * @param User      &$current_user      reference to the current user which is logged in
         * @param Log       &$log               reference to the Log-object
         * @param integer   $device_id          the ID of the device
         * @param integer   $part_id            the ID of the part
         *
         * @retval  DevicePart      the found DevicePart
         * @retval  NULL            if there is no such DevicePart
         *
         * @throws Exception if there was an error
         */
        public static function get_device_part(&$database, &$current_user, &$log, $device_id, $part_id)
        {
            if (get_class($database) != 'Database')
                throw new Exception(_('$database ist kein Database-Objekt!'));

            $query_data = $database->query('SELECT id FROM device_parts '.
                                            'WHERE id_device=? AND id_part=? LIMIT 1',
                                            array($device_id, $part_id));

            if (count($query_data) > 0)
                return new DevicePart($database, $current_user, $log, $query_data[0]['id']);
            else
                return NULL;
        }

        /**
         * @brief Get all device parts which should be ordered (device marked manually as "to order")
         *
         * @param Database  &$database          reference to the database object
         * @param User      &$current_user      reference to the user which is logged in
         * @param Log       &$log               reference to the Log-object
         * @param integer   $part_id            If this is not NULL, only DevicePart objects with that part_id will be returned
         *
         * @retval array    all device parts as a one-dimensional array of DevicePart objects
         *
         * @throws Exception if there was an error
         */
        public static function get_order_device_parts(&$database, &$current_user, &$log, $part_id = NULL)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            $device_parts = array();

            $query =    'SELECT device_parts.id FROM device_parts '.
                        'LEFT JOIN devices ON devices.id = device_parts.id_device '.
                        'WHERE devices.order_quantity > 0 ';
            if ($part_id)
                $query .= 'AND device_parts.id_part = ? ';
            $query .=   'GROUP BY device_parts.id';

            $query_data = $database->query($query, ($part_id ? array($part_id) : array()));

            foreach ($query_data as $row)
                $device_parts[] = new DevicePart($database, $current_user, $log, $row['id']);

            return $device_parts;
        }

        /**
         * @brief Create a new device-part
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
         * @retval DevicePart   the new device-part
         * @retval DevicePart   the existing device-part, if there is already a DevicePart with
         *                      the same part ID + device ID and "$increment_if_exist == true"
         *
         * @throws Exception    if (this combination of) values is not valid
         * @throws Exception    if there was an error
         *
         * @see DBElement::add()
         */
        public static function add(&$database, &$current_user, &$log, $device_id, $part_id,
                                    $quantity, $mountnames = '', $increase_if_exist = false)
        {
            $existing_devicepart = DevicePart::get_device_part($database, $current_user, $log, $device_id, $part_id);
            if (is_object($existing_devicepart))
            {
                if ($increase_if_exist)
                {
                    if ((( ! is_int($quantity)) && ( ! ctype_digit($quantity))) || ($quantity < 0))
                    {
                        debug('error', 'quantity = "'.$quantity.'"', __FILE__, __LINE__, __METHOD__);
                        throw new Exception(_('Die Bestückungs-Anzahl ist ungültig!'));
                    }

                    $quantity = $existing_devicepart->get_mount_quantity() + $quantity;

                    $old_mountnames = $existing_devicepart->get_mount_names();
                    if (strlen($mountnames) > 0)
                    {
                        if (strlen($old_mountnames) > 0)
                            $mountnames = $old_mountnames . ', ' . $mountnames;
                    }
                    else
                        $mountnames = $old_mountnames;

                    $existing_devicepart->set_attributes(array( 'quantity'      => $quantity,
                                                                'mountnames'    => $mountnames));

                    return $existing_devicepart;
                }
                else
                {
                    $device = new Device($database, $current_user, $log, $device_id);
                    $part = new Part($database, $current_user, $log, $part_id);

                    throw new Exception(sprintf(_('Die Baugruppe "%1$s"'.
                                        ' enthält bereits das Bauteil "%2$s"!'), $device->get_name(),$part->get_name()));
                }
            }

            // there is no such DevicePart, so we will create it
            return parent::add($database, $current_user, $log, 'device_parts',
                                array(  'id_device'     => $device_id,
                                        'id_part'       => $part_id,
                                        'quantity'      => $quantity,
                                        'mountnames'    => $mountnames));
        }


    }
