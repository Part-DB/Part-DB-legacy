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
        2012-12-22  kami89          - added "get_manual_order()" + "set_manual_order()"
                                    - added "get_order_quantitiy()" + "set_order_quantity()"
                                    - added "get_order_supplier()" + "set_order_supplier_id()"
        2013-01-29  kami89          - added support for transactions in "delete()"
                                    - moved attrubute "obsolete" from Parts to Orderdetails
        2013-02-16  kami89          - changes "order_supplier" to "order_orderdetails"
        2014-05-12  kami89          - added "get_manufacturer_product_url()"
        2015-09-01  susnux          - fixed "get_sum_price_instock()" to use quantity discount
*/

    /**
     * @file class.Part.php
     * @brief class Part
     *
     * @class Part
     * @brief All elements of this class are stored in the database table "parts".
     *
     * A Part can contain:
     *  - 1     Category
     *  - 0..1  Footprint
     *  - 0..1  Storelocation
     *  - 0..1  Manufacturer
     *  - 0..*  Orderdetails
     *
     * @author kami89
     *
     * @todo    The attribute "visible" is no longer required if there is a user management.
     */
    class Part extends AttachementsContainingDBElement
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

        /** @brief (Category) the category of this part */
        private $category;
        /** @brief (Footprint|NULL) the footprint of this part (if there is one) */
        private $footprint = NULL;
        /** @brief (Storelocation|NULL) the storelocation where this part is located (if there is one) */
        private $storelocation = NULL;
        /** @brief (Manufacturer|NULL) the manufacturer of this part (if there is one) */
        private $manufacturer = NULL;
        /** @brief (Attachement|NULL) the master picture Attachement of this part (if there is one) */
        private $master_picture_attachement = NULL;
        /** @brief (array) all orderdetails-objects as a one-dimensional array of Orderdetails-objects
            (empty array if there are no orderdetails) */
        private $orderdetails = NULL;
        /** @brief (Orderdetails|NULL) the order orderdetails of this part (for "parts to order") */
        private $order_orderdetails;

        /** @brief (array) all devices in which this part is used (as a one-dimensional array of Device objects) */
        private $devices = NULL;

        /********************************************************************************
        *
        *   Constructor / Destructor / reset_attributes()
        *
        *********************************************************************************/

        /**
         * @brief Constructor
         *
         * @param Database  &$database:     reference to the Database-object
         * @param User      &$current_user  reference to the current user which is logged in
         * @param Log       &$log:          reference to the Log-object
         * @param integer   $id:            ID of the part we want to get
         *
         * @throws Exception    if there is no such part in the database
         * @throws Exception    if there was an error
         */
        public function __construct(&$database, &$current_user, &$log, $id)
        {
            parent::__construct($database, $current_user, $log, 'parts', $id);
        }

        /**
         * @copydoc DBElement::reset_attributes()
         */
        public function reset_attributes($all = false)
        {
            $this->category                     = NULL;
            $this->footprint                    = NULL;
            $this->storelocation                = NULL;
            $this->manufacturer                 = NULL;
            $this->orderdetails                 = NULL;
            $this->devices                      = NULL;
            $this->master_picture_attachement   = NULL;
            $this->order_orderdetails           = NULL;

            parent::reset_attributes($all);
        }

        /********************************************************************************
        *
        *   Basic Methods
        *
        *********************************************************************************/

        /**
         * @brief Delete this element
         *
         * @note    This function overrides the same-named function from the parent class.
         * @note    The associated orderdetails and attachements will be deleted too.
         *
         * @param boolean $delete_files_from_hdd    if true, the attached files of this part will be deleted
         *                                          from harddisc drive (!)
         * @param boolean $delete_device_parts      @li if true, all DeviceParts with this part will be deleted
         *                                          @li if false, there will be thrown an exception
         *                                              if there are DeviceParts with this part
         *
         * @throws Exception if there are device parts and $delete_device_parts == false
         * @throws Exception if there was an error
         */
        public function delete($delete_files_from_hdd = false, $delete_device_parts = false)
        {
            try
            {
                $transaction_id = $this->database->begin_transaction(); // start transaction

                $devices = $this->get_devices();
                $orderdetails = $this->get_orderdetails();
                $this->reset_attributes(); // set $this->devices ans $this->orderdetails to NULL

                // Check if there are no Devices with this Part (and delete them if neccessary)
                if (count($devices) > 0)
                {
                    if ($delete_device_parts)
                    {
                        foreach ($devices as $device)
                        {
                            foreach ($device->get_parts() as $device_part)
                            {
                                if ($device_part->get_part()->get_id() == $this->get_id())
                                    $device_part->delete();
                            }
                        }
                    }
                    else
                        throw new Exception('Das Bauteil "'.$this->get_name().'" wird noch in '.count($devices).
                                            ' Baugruppen verwendet und kann daher nicht gelöscht werden!');
                }

                // Delete all Orderdetails
                foreach ($orderdetails as $details)
                    $details->delete();

                // now we can delete this element + all attachements of it
                parent::delete($delete_files_from_hdd);

                $this->database->commit($transaction_id); // commit transaction
            }
            catch (Exception $e)
            {
                $this->database->rollback(); // rollback transaction

                // restore the settings from BEFORE the transaction
                $this->reset_attributes();

                throw new Exception("Das Bauteil \"".$this->get_name()."\" konnte nicht gelöscht werden!\nGrund: ".$e->getMessage());
            }
        }

        /**
         * Returns a array for JSON serialization which contains all important data about this part
         */
        public function get_json_array()
        {
            $ret = array('pid' => $this->get_id(),
                            'description' => $this->get_description(),
                            'name' => $this->get_name(),
                            'instock' => $this->get_instock(),
                            'mininstock' => $this->get_mininstock(),
                            'comment' => $this->get_comment(),
                            'obsolete' => $this->get_obsolete(),
                            'visible' => $this->get_visible(),
                            'order_quantity' => $this->get_order_quantity(),
                            'average_price' => $this->get_average_price(),
                            'manual_order' => $this->get_manual_order(),
                            'category' => $this->get_category()->get_json_array()
                            );

            return $ret;
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get the description
         *
         * @retval string       the description
         */
        public function get_description()
        {
            return $this->db_data['description'];
        }

        /**
         * @brief Get the count of parts which are in stock
         *
         * @retval integer       count of parts which are in stock
         */
        public function get_instock()
        {
            return $this->db_data['instock'];
        }

        /**
         * @brief Get the count of parts which must be in stock at least
         *
         * @retval integer       count of parts which must be in stock at least
         */
        public function get_mininstock()
        {
            return $this->db_data['mininstock'];
        }

        /**
         * @brief Get the comment
         *
         * @retval string       the comment
         */
        public function get_comment()
        {
            return $this->db_data['comment'];
        }

        /**
         * @brief Get if this part is obsolete
         *
         * @note    A Part is marked as "obsolete" if all their orderdetails are marked as "obsolete".
         *          If a part has no orderdetails, the part isn't marked as obsolete.
         *
         * @retval boolean      @li true if this part is obsolete
         *                      @li false if this part isn't obsolete
         */
        public function get_obsolete()
        {
            $all_orderdetails = $this->get_orderdetails();

            if (count($all_orderdetails) == 0)
                return false;

            foreach ($all_orderdetails as $orderdetails)
            {
                if ( ! $orderdetails->get_obsolete())
                    return false;
            }

            return true;
        }

        /**
         * @brief Get if this part is visible
         *
         * @retval boolean      @li true if this part is visible
         *                      @li false if this part isn't visible
         */
        public function get_visible()
        {
            return $this->db_data['visible'];
        }

        /**
         * @brief Get the selected order orderdetails of this part
         *
         * @retval Orderdetails         the selected order orderdetails
         * @retval NULL                 if there is no order supplier selected
         */
        public function get_order_orderdetails()
        {
            if (( ! is_object($this->order_orderdetails)) && ($this->db_data['order_orderdetails_id'] != NULL))
            {
                $this->order_orderdetails = new Orderdetails($this->database, $this->current_user,
                                                             $this->log, $this->db_data['order_orderdetails_id']);

                if ($this->order_orderdetails->get_obsolete())
                {
                    $this->set_order_orderdetails_id(NULL);
                    $this->order_orderdetails = NULL;
                }
            }

            return $this->order_orderdetails;
        }

        /**
         * @brief Get the order quantity of this part
         *
         * @retval integer      the order quantity
         */
        public function get_order_quantity()
        {
            return $this->db_data['order_quantity'];
        }

        /**
         * @brief Get the minimum quantity which should be ordered
         *
         * @param boolean $with_devices     @li if true, all parts from devices which are marked as "to order" will be included in the calculation
         *                                  @li if false, only max(mininstock - instock, 0) will be returned
         *
         * @retval integer      the minimum order quantity
         */
        public function get_min_order_quantity($with_devices = true)
        {
            if ($with_devices)
            {
                $count_must_order = 0;      // for devices with "order_only_missing_parts == false"
                $count_should_order = 0;    // for devices with "order_only_missing_parts == true"
                $deviceparts = DevicePart::get_order_device_parts($this->database, $this->current_user, $this->log, $this->get_id());
                foreach ($deviceparts as $devicepart)
                {
                    $device = $devicepart->get_device();
                    if ($device->get_order_only_missing_parts())
                        $count_should_order += $device->get_order_quantity() * $devicepart->get_mount_quantity();
                    else
                        $count_must_order += $device->get_order_quantity() * $devicepart->get_mount_quantity();
                }

                return $count_must_order + max(0, $this->get_mininstock() - $this->get_instock() + $count_should_order);
            }
            else
                return max(0, $this->get_mininstock() - $this->get_instock());
        }

        /**
         * @brief Get the "manual_order" attribute
         *
         * @retval boolean      the "manual_order" attribute
         */
        public function get_manual_order()
        {
            return $this->db_data['manual_order'];
        }

        /**
         * @brief Get the link to the website of the article on the manufacturers website
         *
         * @retval string           the link to the article
         */
        public function get_manufacturer_product_url()
        {
            if (strlen($this->db_data['manufacturer_product_url']) > 0)
                return $this->db_data['manufacturer_product_url'];  // a manual url is available
            elseif (is_object($this->get_manufacturer()))
                return $this->get_manufacturer()->get_auto_product_url($this->db_data['name']); // an automatic url is available
            else
                return ''; // no url is available
        }

        /**
         * @brief Get the category of this part
         *
         * There is always a category, for each part!
         *
         * @retval Category     the category of this part
         *
         * @throws Exception if there was an error
         */
        public function get_category()
        {
            if ( ! is_object($this->category))
            {
                $this->category = new Category($this->database, $this->current_user,
                                                    $this->log, $this->db_data['id_category']);
            }

            return $this->category;
        }

        /**
         * @brief Get the footprint of this part (if there is one)
         *
         * @retval Footprint    the footprint of this part (if there is one)
         * @retval NULL         if this part has no footprint
         *
         * @throws Exception if there was an error
         */
        public function get_footprint()
        {
            if (( ! is_object($this->footprint)) && ($this->db_data['id_footprint'] != NULL))
            {
                $this->footprint = new Footprint($this->database, $this->current_user,
                                                    $this->log, $this->db_data['id_footprint']);
            }

            return $this->footprint;
        }

        /**
         * @brief Get the storelocation of this part (if there is one)
         *
         * @retval Storelocation    the storelocation of this part (if there is one)
         * @retval NULL             if this part has no storelocation
         *
         * @throws Exception if there was an error
         */
        public function get_storelocation()
        {
            if (( ! is_object($this->storelocation)) && ($this->db_data['id_storelocation'] != NULL))
            {
                $this->storelocation = new Storelocation($this->database, $this->current_user,
                                                            $this->log, $this->db_data['id_storelocation']);
            }

            return $this->storelocation;
        }

        /**
         * @brief Get the manufacturer of this part (if there is one)
         *
         * @retval Manufacturer     the manufacturer of this part (if there is one)
         * @retval NULL             if this part has no manufacturer
         *
         * @throws Exception if there was an error
         */
        public function get_manufacturer()
        {
            if (( ! is_object($this->manufacturer)) && ($this->db_data['id_manufacturer'] != NULL))
            {
                $this->manufacturer = new Manufacturer($this->database, $this->current_user,
                                                        $this->log, $this->db_data['id_manufacturer']);
            }

            return $this->manufacturer;
        }

        /**
         * @brief Get the master picture "Attachement"-object of this part (if there is one)
         *
         * @retval Attachement      the master picture Attachement of this part (if there is one)
         * @retval NULL             if this part has no master picture
         *
         * @throws Exception if there was an error
         */
        public function get_master_picture_attachement()
        {
            if (( ! is_object($this->master_picture_attachement)) && ($this->db_data['id_master_picture_attachement'] != NULL))
            {
                $this->master_picture_attachement = new Attachement($this->database, $this->current_user,
                                                        $this->log, $this->db_data['id_master_picture_attachement']);
            }

            return $this->master_picture_attachement;
        }

        /**
         * @brief Get all orderdetails of this part
         *
         * @param boolean $hide_obsolete    If true, obsolete orderdetails will NOT be returned
         *
         * @retval array    @li all orderdetails as a one-dimensional array of Orderdetails objects
         *                      (empty array if there are no ones)
         *                  @li the array is sorted by the suppliers names / minimum order quantity
         *
         * @throws Exception if there was an error
         */
        public function get_orderdetails($hide_obsolete = false)
        {
            if ( ! is_array($this->orderdetails))
            {
                $this->orderdetails = array();

                $query = 'SELECT orderdetails.id FROM orderdetails '.
                            'LEFT JOIN suppliers ON suppliers.id = orderdetails.id_supplier '.
                            'WHERE part_id=? '.
                            'ORDER BY suppliers.name ASC';

                $query_data = $this->database->query($query, array($this->get_id()));

                foreach ($query_data as $row)
                    $this->orderdetails[] = new Orderdetails($this->database, $this->current_user, $this->log, $row['id']);
            }

            if ($hide_obsolete)
            {
                $orderdetails = $this->orderdetails;
                foreach ($orderdetails as $key => $details)
                {
                    if ($details->get_obsolete())
                        unset($orderdetails[$key]);
                }
                return $orderdetails;
            }
            else
                return $this->orderdetails;
        }

        /**
         * @brief Get all devices which uses this part
         *
         * @retval array    @li all devices which uses this part as a one-dimensional array of Device objects
         *                      (empty array if there are no ones)
         *                  @li the array is sorted by the devices names
         *
         * @throws Exception if there was an error
         */
        public function get_devices()
        {
            if ( ! is_array($this->devices))
            {
                $this->devices = array();

                $query = 'SELECT id_device FROM device_parts '.
                            'LEFT JOIN devices ON device_parts.id_device=devices.id '.
                            'WHERE id_part=? '.
                            'GROUP BY id_device '.
                            'ORDER BY devices.name ASC';

                $query_data = $this->database->query($query, array($this->get_id()));

                foreach ($query_data as $row)
                    $this->devices[] = new Device($this->database, $this->current_user, $this->log, $row['id_device']);
            }

            return $this->devices;
        }

        /**
         * @brief Get all suppliers of this part
         *
         * This method simply gets the suppliers of the orderdetails and prepare them.\n
         * You can get the suppliers as an array or as a string with individual delimeter.
         *
         * @param boolean       $object_array   @li if true, this method returns an array of Supplier objects
         *                                      @li if false, this method returns an array of strings
         * @param string|NULL   $delimeter      @li if this is a string and "$object_array == false",
         *                                          this method returns a string with all
         *                                          supplier names, delimeted by "$delimeter"
         * @param boolean       $full_paths     @li if true and "$object_array = false", the returned
         *                                          suppliernames are full paths (path + name)
         *                                      @li if true and "$object_array = false", the returned
         *                                          suppliernames are only the names (without path)
         * @param boolean       $hide_obsolete  If true, suppliers from obsolete orderdetails will NOT be returned
         *
         * @retval array        all suppliers as a one-dimensional array of Supplier objects
         *                      (if "$object_array == true")
         * @retval array        all supplier-names as a one-dimensional array of strings
         *                      ("if $object_array == false" and "$delimeter == NULL")
         * @retval string       a sting of all supplier names, delimeted by $delimeter
         *                      ("if $object_array == false" and $delimeter is a string)
         *
         * @throws Exception    if there was an error
         */
        public function get_suppliers($object_array = true, $delimeter = NULL, $full_paths = false, $hide_obsolete = false)
        {
            $suppliers = array();
            $orderdetails = $this->get_orderdetails($hide_obsolete);

            foreach ($orderdetails as $details)
                $suppliers[] = $details->get_supplier();

            if ($object_array)
            {
                return $suppliers;
            }
            else
            {
                $supplier_names = array();
                foreach ($suppliers as $supplier)
                {
                    if ($full_paths)
                        $supplier_names[] = $supplier->get_full_path();
                    else
                        $supplier_names[] = $supplier->get_name();
                }

                if (is_string($delimeter))
                    return implode($delimeter, $supplier_names);
                else
                    return $supplier_names;
            }
        }

        /**
         * @brief Get all supplier-part-Nrs
         *
         * This method simply gets the suppliers-part-Nrs of the orderdetails and prepare them.\n
         * You can get the numbers as an array or as a string with individual delimeter.
         *
         * @param string|NULL   $delimeter      @li if this is a string, this method returns a delimeted string
         *                                      @li otherwise, this method returns an array of strings
         * @param boolean       $hide_obsolete  If true, supplierpartnrs from obsolete orderdetails will NOT be returned
         *
         * @retval array        all supplierpartnrs as an array of strings (if "$delimeter == NULL")
         * @retval string       all supplierpartnrs as a string, delimeted ba $delimeter (if $delimeter is a string)
         *
         * @throws Exception    if there was an error
         */
        public function get_supplierpartnrs($delimeter = NULL, $hide_obsolete = false)
        {
            $supplierpartnrs = array();

            foreach ($this->get_orderdetails($hide_obsolete) as $details)
                $supplierpartnrs[] = $details->get_supplierpartnr();

            if (is_string($delimeter))
                return implode($delimeter, $supplierpartnrs);
            else
                return $supplierpartnrs;
        }

        /**
         * @brief Get all prices of this part
         *
         * This method simply gets the prices of the orderdetails and prepare them.\n
         * In the returned array/string there is a price for every supplier.
         *
         * @param boolean       $float_array    @li if true, the returned array is an array of floats
         *                                      @li if false, the returned array is an array of strings
         * @param string|NULL   $delimeter      if this is a string, this method returns a delimeted string
         *                                      instead of an array.
         * @param integer       $quantity       this is the quantity to choose the correct priceinformation
         * @param integer|NULL  $multiplier     @li This is the multiplier which will be applied to every single price
         *                                      @li If you pass NULL, the number from $quantity will be used
         * @param boolean       $hide_obsolete  If true, prices from obsolete orderdetails will NOT be returned
         *
         * @retval array        all prices as an array of floats (if "$delimeter == NULL" & "$float_array == true")
         * @retval array        all prices as an array of strings (if "$delimeter == NULL" & "$float_array == false")
         * @retval string       all prices as a string, delimeted by $delimeter (if $delimeter is a string)
         *
         * @warning             If there are orderdetails without prices, for these orderdetails there
         *                      will be a "NULL" in the returned float array (or a "-" in the string array)!!
         *                      (This is needed for the HTML output, if there are all orderdetails and prices listed.)
         *
         * @throws Exception    if there was an error
         */
        public function get_prices($float_array = false, $delimeter = NULL, $quantity = 1, $multiplier = NULL, $hide_obsolete = false)
        {
            $prices = array();

            foreach ($this->get_orderdetails($hide_obsolete) as $details)
                $prices[] = $details->get_price(( ! $float_array), $quantity, $multiplier);

            if (is_string($delimeter))
                return implode($delimeter, $prices);
            else
                return $prices;
        }

        /**
         * @brief Get the average price of all orderdetails
         *
         * With the $multiplier you're able to multiply the price before it will be returned.
         * This is useful if you want to have the price as a string with currency, but multiplied with a factor.
         *
         * @param boolean   $as_money_string    @li if true, the retruned value will be a string incl. currency,
         *                                          ready to print it out. See float_to_money_string().
         *                                      @li if false, the returned value is a float
         * @param integer       $quantity       this is the quantity to choose the correct priceinformations
         * @param integer|NULL  $multiplier     @li This is the multiplier which will be applied to every single price
         *                                      @li If you pass NULL, the number from $quantity will be used
         *
         * @retval float        price (if "$as_money_string == false")
         * @retval NULL         if there are no prices for this part and "$as_money_string == false"
         * @retval string       price with currency (if "$as_money_string == true")
         *
         * @throws Exception    if there was an error
         */
        public function get_average_price($as_money_string = false, $quantity = 1, $multiplier = NULL)
        {
            $prices = $this->get_prices(true, NULL, $quantity, $multiplier, true);
            $average_price = NULL;

            $count = 0;
            foreach ($prices as $price)
            {
                if ($price !== NULL)
                {
                    $average_price += $price;
                    $count++;
                }
            }

            if ($count > 0)
                $average_price /= $count;

            if ($as_money_string)
                return float_to_money_string($average_price);
            else
                return $average_price;
        }

        /**
         * @brief Get the filename of the master picture (absolute path from filesystem root)
         *
         * @param boolean $use_footprint_filename   @li if true, and this part has no picture, this method
         *                                              will return the filename of its footprint (if available)
         *                                          @li if false, and this part has no picture,
         *                                              this method will return NULL
         *
         * @retval string   the whole path + filename from filesystem root as a UNIX path (with slashes)
         * @retval NULL     if there is no picture
         *
         * @throws Exception if there was an error
         */
        public function get_master_picture_filename($use_footprint_filename = false)
        {
            $master_picture = $this->get_master_picture_attachement(); // returns an Attachement-object

            if (is_object($master_picture))
                return $master_picture->get_filename();

            if ($use_footprint_filename)
            {
                $footprint = $this->get_footprint();
                if (is_object($footprint))
                    return $footprint->get_filename();
            }

            return NULL;
        }

        /********************************************************************************
        *
        *   Setters
        *
        *********************************************************************************/

        /**
         * @brief Set the description
         *
         * @param string $new_description       the new description
         *
         * @throws Exception if there was an error
         */
        public function set_description($new_description)
        {
            $this->set_attributes(array('description' => $new_description));
        }

        /**
         * @brief Set the count of parts which are in stock
         *
         * @param integer $new_instock       the new count of parts which are in stock
         *
         * @throws Exception if the new instock is not valid
         * @throws Exception if there was an error
         */
        public function set_instock($new_instock)
        {
            $this->set_attributes(array('instock' => $new_instock));
        }

        /**
         * @brief Set the count of parts which should be in stock at least
         *
         * @param integer $new_instock       the new count of parts which should be in stock at least
         *
         * @throws Exception if the new mininstock is not valid
         * @throws Exception if there was an error
         */
        public function set_mininstock($new_mininstock)
        {
            $this->set_attributes(array('mininstock' => $new_mininstock));
        }

        /**
         * @brief Set the comment
         *
         * @param string $new_comment       the new comment
         *
         * @throws Exception if there was an error
         */
        public function set_comment($new_comment)
        {
            $this->set_attributes(array('comment' => $new_comment));
        }

        /**
         * @brief Set the "manual_order" attribute
         *
         * @param boolean $new_manual_order                 the new "manual_order" attribute
         * @param integer $new_order_quantity               the new order quantity
         * @param integer|NULL $new_order_orderdetails_id   @li the ID of the new order orderdetails
         *                                                  @li or Zero for "no order orderdetails"
         *                                                  @li or NULL for automatic order orderdetails
         *                                                      (if the part has exactly one orderdetails,
         *                                                      set this orderdetails as order orderdetails.
         *                                                      Otherwise, set "no order orderdetails")
         *
         * @throws Exception if there was an error
         */
        public function set_manual_order($new_manual_order, $new_order_quantity = 1, $new_order_orderdetails_id = NULL)
        {
            $this->set_attributes(array('manual_order'          => $new_manual_order,
                                        'order_orderdetails_id' => $new_order_orderdetails_id,
                                        'order_quantity'        => $new_order_quantity));
        }

        /**
         * @brief Set the ID of the order orderdetails
         *
         * @param integer|NULL $new_order_orderdetails_id       @li the new order orderdetails ID
         *                                                      @li Or, to remove the orderdetails, pass a NULL
         *
         * @throws Exception if there was an error
         */
        public function set_order_orderdetails_id($new_order_orderdetails_id)
        {
            $this->set_attributes(array('order_orderdetails_id' => $new_order_orderdetails_id));
        }

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
         * @brief Set the ID of the category
         *
         * @note    Every part must have a valid category (in contrast to the
         *          attributes "footprint", "storelocation", ...)!
         *
         * @param integer $new_category_id       the ID of the category
         *
         * @throws Exception if the new category ID is not valid
         * @throws Exception if there was an error
         */
        public function set_category_id($new_category_id)
        {
            $this->set_attributes(array('id_category' => $new_category_id));
        }

        /**
         * @brief Set the footprint ID
         *
         * @param integer|NULL $new_footprint_id    @li the ID of the footprint
         *                                          @li NULL means "no footprint"
         *
         * @throws Exception if the new footprint ID is not valid
         * @throws Exception if there was an error
         */
        public function set_footprint_id($new_footprint_id)
        {
            $this->set_attributes(array('id_footprint' => $new_footprint_id));
        }

        /**
         * @brief Set the storelocation ID
         *
         * @param integer|NULL $new_storelocation_id    @li the ID of the storelocation
         *                                              @li NULL means "no storelocation"
         *
         * @throws Exception if the new storelocation ID is not valid
         * @throws Exception if there was an error
         */
        public function set_storelocation_id($new_storelocation_id)
        {
            $this->set_attributes(array('id_storelocation' => $new_storelocation_id));
        }

        /**
         * @brief Set the manufacturer ID
         *
         * @param integer|NULL $new_manufacturer_id     @li the ID of the manufacturer
         *                                              @li NULL means "no manufacturer"
         *
         * @throws Exception if the new manufacturer ID is not valid
         * @throws Exception if there was an error
         */
        public function set_manufacturer_id($new_manufacturer_id)
        {
            $this->set_attributes(array('id_manufacturer' => $new_manufacturer_id));
        }

        /**
         * @brief Set the ID of the master picture Attachement
         *
         * @param integer|NULL $new_master_picture_attachement_id       @li the ID of the Attachement object of the master picture
         *                                                              @li NULL means "no master picture"
         *
         * @throws Exception if the new ID is not valid
         * @throws Exception if there was an error
         */
        public function set_master_picture_attachement_id($new_master_picture_attachement_id)
        {
            $this->set_attributes(array('id_master_picture_attachement' => $new_master_picture_attachement_id));
        }

        /********************************************************************************
        *
        *   Table Builder Methods
        *
        *********************************************************************************/

        /**
         * @brief Build the array for the template table row of this part
         *
         * @param string    $table_type             @li the type of the table which will be builded
         *                                          @li see Part::build_template_table_array()
         * @param boolen    $row_index              The index of this table row
         * @param array     $additional_values      Here you can pass more values than only the part attributes.
         *                                          This is used in DevicePart::build_template_table_row_array().
         *
         * @retval array    The array for the template output (element of the loop "table")
         *
         * @throws Exception if there was an error
         */
        public function build_template_table_row_array($table_type, $row_index, $additional_values = array())
        {
            global $config;

            $table_row = array();
            $table_row['row_odd']       = is_odd($row_index);
            $table_row['row_index']     = $row_index;
            $table_row['id']            = $this->get_id();
            $table_row['row_fields']    = array();

            foreach(explode(';', $config['table'][$table_type]['columns']) as $caption)
            {
                $row_field = array();
                $row_field['row_index']     = $row_index;
                $row_field['caption']       = $caption;
                $row_field['id']            = $this->get_id();
                $row_field['name']          = $this->get_name();

                switch($caption)
                {
                    case 'hover_picture':
                        $picture_filename = str_replace(BASE, BASE_RELATIVE, $this->get_master_picture_filename(true));
                        $row_field['picture_name']  = strlen($picture_filename) ? basename($picture_filename) : '';
                        $row_field['small_picture'] = strlen($picture_filename) ? $picture_filename : '';
                        $row_field['hover_picture'] = strlen($picture_filename) ? $picture_filename : '';
                        break;

                    case 'name':
                    case 'description':
                    case 'comment':
                    case 'name_description':
                        $row_field['obsolete']          = $this->get_obsolete();
                        $row_field['comment']           = $this->get_comment();
                        $row_field['description']       = $this->get_description();
                        break;

                    case 'instock':
                    case 'mininstock':
                    case 'instock_mininstock':
                    case 'instock_edit_buttons':
                        $row_field['instock']               = $this->get_instock();
                        $row_field['mininstock']            = $this->get_mininstock();
                        $row_field['not_enought_instock']   = ($this->get_instock() < $this->get_mininstock());
                        break;

                    case 'category':
                        $category = $this->get_category();
                        $row_field['category_name'] = $category->get_name();
                        $row_field['category_path'] = $category->get_full_path();
                        break;

                    case 'footprint':
                        $footprint = $this->get_footprint();
                        if (is_object($footprint))
                        {
                            $row_field['footprint_name'] = $footprint->get_name();
                            $row_field['footprint_path'] = $footprint->get_full_path();
                        }
                        break;

                    case 'manufacturer':
                        $manufacturer = $this->get_manufacturer();
                        if (is_object($manufacturer))
                        {
                            $row_field['manufacturer_name'] = $manufacturer->get_name();
                            $row_field['manufacturer_path'] = $manufacturer->get_full_path();
                        }
                        break;

                    case 'storelocation':
                        $storelocation = $this->get_storelocation();
                        if (is_object($storelocation))
                        {
                            $row_field['storelocation_name'] = $storelocation->get_name();
                            $row_field['storelocation_path'] = $storelocation->get_full_path();
                        }
                        break;

                    case 'suppliers':
                        $suppliers_loop = array();
                        foreach ($this->get_suppliers(false, NULL, false, true) as $supplier_name) // suppliers from obsolete orderdetails will not be shown
                        {
                            $suppliers_loop[] = array(  'row_index'         => $row_index,
                                                        'supplier_name'     => $supplier_name);
                        }

                        $row_field['suppliers'] = $suppliers_loop;
                        break;

                    case 'suppliers_radiobuttons':
                        if ($table_type == 'order_parts')
                        {
                            if (is_object($this->get_order_orderdetails()))
                                $order_orderdetails_id = $this->get_order_orderdetails()->get_id();
                            else
                                $order_orderdetails_id = 0;

                            $suppliers_loop = array();
                            foreach ($this->get_orderdetails(true) as $orderdetails) // obsolete orderdetails will not be shown
                            {
                                $suppliers_loop[] = array(  'row_index'         => $row_index,
                                                            'orderdetails_id'   => $orderdetails->get_id(),
                                                            'supplier_name'     => $orderdetails->get_supplier()->get_full_path(),
                                                            'selected'          => ($order_orderdetails_id == $orderdetails->get_id()));
                            }
                            $suppliers_loop[] = array(      'row_index'         => $row_index,
                                                            'orderdetails_id'   => 0,
                                                            'supplier_name'     => 'Noch nicht bestellen',
                                                            'selected'          => ($order_orderdetails_id == 0));

                            $row_field['suppliers_radiobuttons'] = $suppliers_loop;
                        }
                        break;

                    case 'supplier_partnrs':
                        $partnrs_loop = array();
                        foreach ($this->get_orderdetails(true) as $details) // partnrs from obsolete orderdetails will not be shown
                        {
                            $partnrs_loop[] = array(    'row_index'            => $row_index,
                                                        'supplier_partnr'      => $details->get_supplierpartnr(),
                                                        'supplier_product_url' => $details->get_supplier_product_url());
                        }

                        $row_field['supplier_partnrs'] = $partnrs_loop;
                        break;

                    case 'datasheets':
                        $datasheet_loop = $config['auto_datasheets']['entries'];

                        foreach ($datasheet_loop as $key => $entry)
                            $datasheet_loop[$key]['url'] = str_replace('%%PARTNAME%%', urlencode($this->get_name()), $entry['url']);

                        $row_field['datasheets'] = $datasheet_loop;
                        break;

                    case 'average_single_price':
                        $row_field['average_single_price'] = $this->get_average_price(true, 1);
                        break;

                    case 'single_prices':
                        if ($table_type == 'order_parts')
                            $min_discount_quantity = $this->get_order_quantity();
                        else
                            $min_discount_quantity = 1;

                        $prices_loop = array();
                        foreach ($this->get_prices(false, NULL, $min_discount_quantity, 1, true) as $price) // prices from obsolete orderdetails will not be shown
                        {
                            $prices_loop[] = array(     'row_index'         => $row_index,
                                                        'single_price'      => $price);
                        }

                        $row_field['single_prices'] = $prices_loop;
                        break;

                    case 'total_prices':
                        switch ($table_type)
                        {
                            case 'order_parts':
                                $min_discount_quantity = $this->get_order_quantity();
                                break;
                            default:
                                //throw new Exception('Keine Totalpreise verfügbar für den Tabellentyp "'.$table_type.'"!');
                                $min_discount_quantity = 0;
                        }

                        $prices_loop = array();
                        foreach ($this->get_prices(false, NULL, $min_discount_quantity, NULL, true) as $price) // prices from obsolete orderdetails will not be shown
                        {
                            $prices_loop[] = array( 'row_index'     => $row_index,
                                                    'total_price'   => $price);
                        }

                        $row_field['total_prices'] = $prices_loop;
                        break;

                    case 'order_quantity_edit':
                        if ($table_type == 'order_parts')
                        {
                            $row_field['order_quantity'] = $this->get_order_quantity();
                            $row_field['min_order_quantity'] = $this->get_min_order_quantity();
                        }
                        break;

                    case 'order_options':
                        if ($table_type == 'order_parts')
                        {
                            $suppliers_loop = array();
                            $row_field['enable_remove'] = (($this->get_instock() >= $this->get_mininstock()) && ($this->get_manual_order()));
                        }
                        break;

                    case 'button_decrement':
                        $row_field['decrement_disabled'] = ($this->get_instock() < 1);
                        break;

                    case 'attachements':
                        $attachements = array();
                        foreach ($this->get_attachements(NULL, true) as $attachement)
                        {
                            $attachements[] = array(    'name'      => $attachement->get_name(),
                                                        'filename'  => str_replace(BASE, BASE_RELATIVE, $attachement->get_filename()),
                                                        'type'      => $attachement->get_type()->get_full_path());
                        }
                        $row_field['attachements'] = $attachements;
                        break;

                    case 'id':
                    case 'button_increment':
                    case 'quantity_edit': // for DevicePart Objects
                    case 'mountnames_edit': // for DevicePart Objects
                        // nothing to do, only to avoid the Exception in the default-case
                        break;

                    default:
                        throw new Exception('Unbekannte Tabellenspalte: "'.$caption.'". Überprüfen Sie die Einstellungen '.
                                            'für den Tabellentyp "'.$table_type.'" in Ihrer "config.php"');
                }

                // maybe there are any additional values to add...
                if (array_key_exists($caption, $additional_values))
                {
                    foreach($additional_values[$caption] as $key => $value)
                        $row_field[$key] = $additional_values[$caption][$key];
                }

                $table_row['row_fields'][] = $row_field;
            }

            return $table_row;
        }

        /**
         * @brief Build the template table array of an array of parts
         *
         * @param array     $parts              array of all parts (Part or DevicePart objects) which will be printed
         * @param string    $table_type         the type of the table which will be builded
         *
         * @par Possible Table Types:
         *  - "category_parts"
         *  - "device_parts"
         *  - "order_parts"
         *  - "noprice_parts"
         *  - "obsolete_parts"
         *
         *
         * @retval array    the template loop array for the table
         *
         * @throws Exception if there was an error
         */
        public static function build_template_table_array($parts, $table_type)
        {
            global $config;

            if ( ! isset($config['table'][$table_type]))
            {
                debug('error', '$table_type = "'.$table_type.'"', __FILE__, __LINE__, __METHOD__);
                throw new Exception('"$table_type" ist ungültig!');
            }

            // table columns
            $columns = array();
            foreach(explode(';', $config['table'][$table_type]['columns']) as $caption)
                $columns[] = array('caption' => $caption);

            $table_loop = array();
            $table_loop[] = array('print_header' => true, 'columns' => $columns); // print the table header

            $row_index = 0;
            foreach ($parts as $part)
            {
                $table_loop[] = $part->build_template_table_row_array($table_type, $row_index);
                $row_index++;
            }

            return $table_loop;
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

            // set "last_modified" to current datetime
            $values['last_modified'] = date('Y-m-d H:i:s');

            // set the datetype of the boolean attributes
            settype($values['visible'], 'boolean');
            settype($values['manual_order'], 'boolean');

            // check "instock"
            if (( ! is_int($values['instock'])) && ( ! ctype_digit($values['instock'])))
            {
                debug('warning','"instock" ist keine gültige Zahl: "'.$values['instock'].'"!',
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Der neue Lagerbestand ist ungültig!');
            }
            elseif ($values['instock'] < 0)
                throw new Exception('Der neue Lagerbestand von "'.$values['name'].'" wäre negativ und kann deshalb nicht gespeichert werden!');

            // check "order_orderdetails_id"
            try
            {
                if ($values['order_orderdetails_id'] == 0)
                    $values['order_orderdetails_id'] = NULL;

                if (( ! $is_new) && ($values['order_orderdetails_id'] == NULL)
                    && (($values['instock'] < $values['mininstock']) || ($values['manual_order']))
                    && (($element->get_instock() >= $element->get_mininstock()) && ( ! $element->get_manual_order())))
                {
                    // if this part will be added now to the list of parts to order (instock is now less than mininstock, or manual_order is now true),
                    // and this part has only one orderdetails, we will set that orderdetails as orderdetails to order from (attribute "order_orderdetails_id").
                    // Note: If that part was already in the list of parts to order, wo mustn't change the orderdetails to order!!
                    $orderdetails = $element->get_orderdetails();
                    $order_orderdetails_id = ((count($orderdetails) == 1) ? $orderdetails[0]->get_id() : NULL);
                    $values['order_orderdetails_id'] = $order_orderdetails_id;
                }

                if ($values['order_orderdetails_id'] != NULL)
                    $order_orderdetails = new Orderdetails($database, $current_user, $log, $values['order_orderdetails_id']);
            }
            catch (Exception $e)
            {
                debug('error', 'Ungültige "order_orderdetails_id": "'.$values['order_orderdetails_id'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(), __FILE__, __LINE__, __METHOD__);
                throw new Exception('Die gewählte Einkaufsinformation existiert nicht!');
            }

            // check "order_quantity"
            if ((( ! is_int($values['order_quantity'])) && ( ! ctype_digit($values['order_quantity'])))
                || ($values['order_quantity'] < 1))
            {
                debug('error', 'order_quantity = "'.$values['order_quantity'].'"', __FILE__, __LINE__, __METHOD__);
                throw new Exception('Die Bestellmenge ist ungültig!');
            }

            // check if we have to reset the order attributes ("instock" is now less than "mininstock")
            if (($values['instock'] < $values['mininstock']) && (($is_new) || ($element->get_instock() >= $element->get_mininstock())))
            {
                if ( ! $values['manual_order'])
                    $values['order_quantity'] = $values['mininstock'] - $values['instock'];

                $values['manual_order'] = false;
            }

            // check "mininstock"
            if ((( ! is_int($values['mininstock'])) && ( ! ctype_digit($values['mininstock'])))
                || ($values['mininstock'] < 0))
            {
                debug('warning', '"mininstock" ist keine gültige Zahl: "'.$values['mininstock'].'"!',
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Der neue Mindestlagerbestand ist ungültig!');
            }

            // check "id_category"
            try
            {
                // id_category == NULL means "no category", and this is not allowed!
                if ($values['id_category'] == NULL)
                    throw new Exception('"id_category" ist Null!');

                $category = new Category($database, $current_user, $log, $values['id_category']);
            }
            catch (Exception $e)
            {
                debug('warning', 'Ungültige "id_category": "'.$values['id_category'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Die gewählte Kategorie existiert nicht!');
            }

            // check "id_footprint"
            try
            {
                if (($values['id_footprint'] == 0) && ($values['id_footprint'] !== NULL))
                    $values['id_footprint'] = NULL;
                $footprint = new Footprint($database, $current_user, $log, $values['id_footprint']);
            }
            catch (Exception $e)
            {
                debug('warning', 'Ungültige "id_footprint": "'.$values['id_footprint'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Der gewählte Footprint existiert nicht!');
            }

            // check "id_storelocation"
            try
            {
                if (($values['id_storelocation'] == 0) && ($values['id_storelocation'] !== NULL))
                    $values['id_storelocation'] = NULL;
                $storelocation = new Storelocation($database, $current_user, $log, $values['id_storelocation']);
            }
            catch (Exception $e)
            {
                debug('warning', 'Ungültige "id_storelocation": "'.$values['id_storelocation'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Der gewählte Lagerort existiert nicht!');
            }

            // check "id_manufacturer"
            try
            {
                if (($values['id_manufacturer'] == 0) && ($values['id_manufacturer'] !== NULL))
                    $values['id_manufacturer'] = NULL;
                $manufacturer = new Manufacturer($database, $current_user, $log, $values['id_manufacturer']);
            }
            catch (Exception $e)
            {
                debug('warning', 'Ungültige "id_manufacturer": "'.$values['id_manufacturer'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Der gewählte Hersteller existiert nicht!');
            }

            // check "id_master_picture_attachement"
            try
            {
                if ($values['id_master_picture_attachement'])
                    $master_picture_attachement = new Attachement($database, $current_user, $log, $values['id_master_picture_attachement']);
                else
                    $values['id_master_picture_attachement'] = NULL; // this will replace the integer "0" with NULL
            }
            catch (Exception $e)
            {
                debug('warning', 'Ungültige "id_master_picture_attachement": "'.$values['id_master_picture_attachement'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Die gewählte Datei existiert nicht!');
            }
        }

        /**
         * @brief Get count of parts
         *
         * @param Database &$database   reference to the Database-object
         *
         * @retval integer              count of parts
         *
         * @throws Exception            if there was an error
         */
        public static function get_count(&$database)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            return $database->get_count_of_records('parts');
        }

        /**
         * @brief Get the sum of all "instock" attributes of all parts
         *
         * All values in the table row "instock" will be summed up.
         *
         * This method is used in statistics.php.
         *
         * @param Database &$database       reference to the database object
         *
         * @retval integer      the sum of all "instock" attributes of all parts
         *
         * @throws Exception if there was an error
         */
        public static function get_sum_count_instock(&$database)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            $query_data = $database->query('SELECT sum(instock) as sum FROM parts');

            return intval($query_data[0]['sum']);
        }

        /**
         * @brief Get the sum price of all parts in stock
         *
         * This method is used in statistics.php.
         *
         * @param Database  &$database          reference to the database object
         * @param User      &$current_user      reference to the user which is logged in
         * @param Log       &$log               reference to the Log-object
         * @param boolean   $as_money_string    @li if true, the price will be returned as a money string
         *                                          (with currency)
         *                                      @li if false, the price will be returned as a float
         *
         * @retval string       sum price as a money string with currency (if "$as_money_string == true")
         * @retval float        sum price as a float (if "$as_money_string == false")
         *
         * @throws Exception if there was an error
         */
        public static function get_sum_price_instock(&$database, &$current_user, &$log, $as_money_string = true)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            $query =    'SELECT part_id, min_discount_quantity, price_related_quantity, price, instock FROM pricedetails ' .
                        'LEFT JOIN orderdetails ON pricedetails.orderdetails_id=orderdetails.id ' .
                        'LEFT JOIN parts ON orderdetails.part_id=parts.id ' .
                        'WHERE min_discount_quantity <= instock ' .
                        'ORDER BY part_id ASC, min_discount_quantity DESC';

            $query_data = $database->query($query);
            $price_sum = 0.0;
            $id = -1;
            $instock = 0;
            foreach ($query_data as $row) {
                if ($id != $row['part_id']) {
                    $id = $row['part_id'];
                    $instock = $row['instock'];
                }
                if ($instock == 0)
                    continue;
                $price_per_piece = $row['price'] / $row['price_related_quantity'];
                $taken_parts = $row['min_discount_quantity'] * (integer)($instock / $row['min_discount_quantity']);
                $price_sum += $price_per_piece * $taken_parts;
                $instock = $instock - $taken_parts;
            }
            $price_sum = round($price_sum, 2);

            if ($as_money_string)
                return float_to_money_string($price_sum);
            else
                return $price_sum;
        }

        /**
         * @brief Get all parts which should be ordered
         *
         * "parts which should be ordered" means:
         * ((("instock" is less than "mininstock") AND (Part isn't already ordered))
         *  OR (Part was manually marked as "should be ordered"))
         *
         * @param Database  &$database          reference to the database object
         * @param User      &$current_user      reference to the user which is logged in
         * @param Log       &$log               reference to the Log-object
         * @param array     $supplier_ids       @li array of all supplier IDs which will be listed
         *                                      @li an empty array means, the parts from ALL suppliers will be listed
         * @param boolean   $with_devices       if true, parts which are in devices, marked as "to order", will be listed too
         *
         * @retval array    all parts as a one-dimensional array of Part objects, sorted by their names
         *
         * @throws Exception if there was an error
         */
        public static function get_order_parts(&$database, &$current_user, &$log, $supplier_ids = array(), $with_devices = true)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            $parts = array();

            $query =    'SELECT parts.id FROM parts '.
                        'LEFT JOIN orderdetails ON orderdetails.id = parts.order_orderdetails_id '.
                        'WHERE (parts.instock < parts.mininstock '.
                        'OR parts.manual_order = true '.
                        'OR parts.id IN '.
                            '(SELECT device_parts.id_part FROM device_parts '.
                            'LEFT JOIN devices ON devices.id = device_parts.id_device '.
                            'WHERE devices.order_quantity > 0)) ';
            if (count($supplier_ids) > 0)
            {
                $query .= 'AND ((false) OR ';
                foreach ($supplier_ids as $id)
                    $query .= '(orderdetails.id_supplier <=> ?) ';
                $query .= ') ';
            }
            $query .= 'ORDER BY parts.name ASC';

            $query_data = $database->query($query, $supplier_ids);

            foreach ($query_data as $row)
            {
                $part = new Part($database, $current_user, $log, $row['id']);
                if (($part->get_manual_order()) || ($part->get_min_order_quantity() > 0))
                    $parts[] = $part;
            }

            return $parts;
        }

        /**
         * @brief Get all parts which have no price
         *
         * @param Database  &$database          reference to the database object
         * @param User      &$current_user      reference to the user which is logged in
         * @param Log       &$log               reference to the Log-object
         *
         * @retval array    all parts as a one-dimensional array of Part objects, sorted by their names
         *
         * @throws Exception if there was an error
         */
        public static function get_noprice_parts(&$database, &$current_user, &$log)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            $parts = array();

            $query =    'SELECT id from parts '.
                        'WHERE id NOT IN (SELECT DISTINCT part_id FROM orderdetails '.
                        'LEFT JOIN pricedetails ON orderdetails.id=pricedetails.orderdetails_id '.
                        'WHERE pricedetails.id IS NOT NULL) '.
                        'ORDER BY parts.name ASC';

            $query_data = $database->query($query);

            foreach ($query_data as $row)
                $parts[] = new Part($database, $current_user, $log, $row['id']);

            return $parts;
        }

        /**
         * @brief Get all obsolete parts
         *
         * @param Database  &$database              reference to the database object
         * @param User      &$current_user          reference to the user which is logged in
         * @param Log       &$log                   reference to the Log-object
         * @param boolean   $no_orderdetails_parts  if true, parts without any orderdetails will be returned too
         *
         * @retval array    all parts as a one-dimensional array of Part objects, sorted by their names
         *
         * @throws Exception if there was an error
         */
        public static function get_obsolete_parts(&$database, &$current_user, &$log, $no_orderdetails_parts = false)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            $parts = array();

            if ($no_orderdetails_parts)
            {
                // show also parts which have no orderdetails
                $query =    'SELECT parts.id from parts '.
                            'LEFT JOIN orderdetails ON orderdetails.part_id = parts.id '.
                            'WHERE parts.id IN (SELECT part_id FROM `orderdetails` '.
                            'WHERE part_id IN (SELECT part_id FROM `orderdetails` '.
                            'WHERE obsolete = true GROUP BY part_id) '.
                            'AND part_id NOT IN (SELECT part_id FROM `orderdetails` '.
                            'WHERE obsolete = false GROUP BY part_id)) '.
                            'OR orderdetails.id IS NULL '.
                            'ORDER BY parts.name ASC';
            }
            else
            {
                // don't show parts which have no orderdetails
                $query =    'SELECT parts.id from parts '.
                            'WHERE parts.id IN (SELECT part_id FROM `orderdetails` '.
                            'WHERE part_id IN (SELECT part_id FROM `orderdetails` '.
                            'WHERE obsolete = true GROUP BY part_id) '.
                            'AND part_id NOT IN (SELECT part_id FROM `orderdetails` '.
                            'WHERE obsolete = false GROUP BY part_id)) '.
                            'ORDER BY parts.name ASC';
            }

            $query_data = $database->query($query);

            foreach ($query_data as $row)
                $parts[] = new Part($database, $current_user, $log, $row['id']);

            return $parts;
        }

        /**
         * @brief Search parts
         *
         * @param Database  &$database              reference to the database object
         * @param User      &$current_user          reference to the user which is logged in
         * @param Log       &$log                   reference to the Log-object
         * @param string    $keyword                the search string
         * @param string    $group_by               @li if this is a non-empty string, the returned array is a
         *                                              two-dimensional array with the group names as top level.
         *                                          @li supported groups are: '' (none), 'categories',
         *                                              'footprints', 'storelocations', 'manufacturers'
         * @param boolean   $part_name              if ture, the search will include this attribute
         * @param boolean   $part_description       if ture, the search will include this attribute
         * @param boolean   $part_comment           if ture, the search will include this attribute
         * @param boolean   $footprint_name         if ture, the search will include this attribute
         * @param boolean   $category_name          if ture, the search will include this attribute
         * @param boolean   $storelocation_name     if ture, the search will include this attribute
         * @param boolean   $supplier_name          if ture, the search will include this attribute
         * @param boolean   $supplierpartnr         if ture, the search will include this attribute
         * @param boolean   $manufacturer_name      if ture, the search will include this attribute
         *
         * @retval array    all found parts as a one-dimensional array of Part objects,
         *                  sorted by their names (if "$group_by == ''")
         * @retval array    @li all parts as a two-dimensional array, grouped by $group_by,
         *                      sorted by name (if "$group_by != ''")
         *                  @li example: array('category1' => array(part1, part2, ...),
         *                      'category2' => array(part123, part124, ...), ...)
         *                  @li for the group names (in the example 'category1', 'category2', ...)
         *                      are the full paths used
         *
         * @throws Exception if there was an error
         */
        public static function search_parts(&$database, &$current_user, &$log, $keyword, $group_by = '',
                                            $part_name = true,
                                            $part_description = true,
                                            $part_comment = false,
                                            $footprint_name = false,
                                            $category_name = false,
                                            $storelocation_name = false,
                                            $supplier_name = false,
                                            $supplierpartnr = false,
                                            $manufacturer_name = false)
        {
            global $config;
            
            $keyword = trim($keyword);

            if (strlen($keyword) == 0)
                return array();

            $keyword = str_replace('*', '%', $keyword);
            $keyword = '%'.$keyword.'%';

            $groups = array();
            $parts = array();
            $values = array();

            $query = 'SELECT parts.id FROM parts'.
                    ' LEFT JOIN footprints ON parts.id_footprint=footprints.id'.
                    ' LEFT JOIN storelocations ON parts.id_storelocation=storelocations.id'.
                    ' LEFT JOIN manufacturers  ON parts.id_manufacturer=manufacturers.id'.
                    ' LEFT JOIN categories ON parts.id_category=categories.id'.
                    ' LEFT JOIN orderdetails ON parts.id=orderdetails.part_id'.
                    ' LEFT JOIN suppliers ON orderdetails.id_supplier=suppliers.id'.
                    ' WHERE FALSE';

            if ($part_name)
            {
                $query .= ' OR (parts.name LIKE ?)';
                $values[] = $keyword;
            }

            if ($part_description)
            {
                $query .= ' OR (parts.description LIKE ?)';
                $values[] = $keyword;
            }

            if ($part_comment)
            {
                $query .= ' OR (parts.comment LIKE ?)';
                $values[] = $keyword;
            }

            if ($footprint_name)
            {
                $query .= ' OR (footprints.name LIKE ?)';
                $values[] = $keyword;
            }

            if ($category_name)
            {
                $query .= ' OR (categories.name LIKE ?)';
                $values[] = $keyword;
            }

            if ($storelocation_name)
            {
                $query .= ' OR (storelocations.name LIKE ?)';
                $values[] = $keyword;
            }

            if ($supplier_name)
            {
                $query .= ' OR (suppliers.name LIKE ?)';
                $values[] = $keyword;
            }

            if ($supplierpartnr)
            {
                $query .= ' OR (orderdetails.supplierpartnr LIKE ?)';
                $values[] = $keyword;
            }

            if ($manufacturer_name)
            {
                $query .= ' OR (manufacturers.name LIKE ?)';
                $values[] = $keyword;
            }

            if (!isset($config['db']['limit']['search_parts']))
            {
                $config['db']['limit']['search_parts'] = 200;
            }
            
            switch($group_by)
            {
                case '':
                    $query .= ' GROUP BY parts.id ORDER BY parts.name ASC';
                    if (isset($config['db']['limit']['search_parts']) && $config['db']['limit']['search_parts']>0)
                    {
                        $query .= ' LIMIT '.$config['db']['limit']['search_parts'];
                    }
                    break;

                case 'categories':
                    $query .= ' GROUP BY parts.id ORDER BY categories.id, parts.name ASC';
                    if (isset($config['db']['limit']['search_parts']) && $config['db']['limit']['search_parts']>0)
                    {
                        $query .= ' LIMIT '.$config['db']['limit']['search_parts'];
                    }
                    break;

                default:
                    throw new Exception('$group_by="'.$group_by.'" is not supported!');
            }

            $query_data = $database->query($query, $values);

            foreach ($query_data as $row)
            {
                $part = new Part($database, $current_user, $log, $row['id']);

                switch($group_by)
                {
                    case '':
                        $parts[] = $part;
                        break;

                    case 'categories':
                        $groups[$part->get_category()->get_full_path()][] = $part;
                        break;
                }
            }

            if ($group_by != '')
            {
                ksort($groups);
                return $groups;
            }
            else
                return $parts;
        }

        /**
         * @brief Create a new part
         *
         * @param Database  &$database          reference to the database object
         * @param User      &$current_user      reference to the user which is logged in
         * @param Log       &$log               reference to the Log-object
         * @param string    $name               the name of the new part (see Part::set_name())
         * @param integer   $category_id        the category ID of the new part (see Part::set_category_id())
         * @param string    $description        the description of the new part (see Part::set_description())
         * @param integer   $instock            the instock of the new part (see Part::set_instock())
         * @param integer   $mininstock         the mininstock of the new part (see Part::set_mininstock())
         * @param integer   $storelocation_id   the storelocation ID of the new part (see Part::set_storelocation_id())
         * @param integer   $manufacturer_id    the manufacturer ID of the new part (see Part::set_manufacturer_id())
         * @param integer   $footprint_id       the footprint ID of the new part (see Part::set_footprint_id())
         * @param string    $comment            the comment of the new part (see Part::set_comment())
         * @param boolean   $visible            the visible attribute of the new part (see Part::set_visible())
         *
         * @retval Part     the new part
         *
         * @throws Exception    if (this combination of) values is not valid
         * @throws Exception    if there was an error
         *
         * @see DBElement::add()
         */
        public static function add(&$database, &$current_user, &$log, $name, $category_id, $description = '',
                                    $instock = 0, $mininstock = 0, $storelocation_id = NULL, $manufacturer_id = NULL,
                                    $footprint_id = NULL, $comment = '', $visible = false)
        {
            return parent::add($database, $current_user, $log, 'parts',
                                array(  'name'                          => $name,
                                        'id_category'                   => $category_id,
                                        'description'                   => $description,
                                        'instock'                       => $instock,
                                        'mininstock'                    => $mininstock,
                                        'id_storelocation'              => $storelocation_id,
                                        'id_manufacturer'               => $manufacturer_id,
                                        'id_footprint'                  => $footprint_id,
                                        'visible'                       => $visible,
                                        'comment'                       => $comment,
                                        'id_master_picture_attachement' => NULL,
                                        'manual_order'                  => false,
                                        'order_orderdetails_id'         => NULL,
                                        'order_quantity'                => 1));
                                        // the column "datetime_added" will be automatically filled by MySQL
                                        // the column "last_modified" will be filled in the function check_values_validity()

        }

    }

?>
