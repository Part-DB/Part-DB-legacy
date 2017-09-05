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
use Golonka\BBCode\BBCodeParser;
use PartDB\PartProperty\PartProperty;

/**
 * @file Part.php
 * @brief class Part
 *
 * @class Part
 * All elements of this class are stored in the database table "parts".
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
class Part extends Base\AttachementsContainingDBElement implements Interfaces\IAPIModel
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

    /** @var Category the category of this part */
    private $category;
    /** @var Footprint|null the footprint of this part (if there is one) */
    private $footprint = null;
    /** @var Storelocation|null the storelocation where this part is located (if there is one) */
    private $storelocation = null;
    /** @var Manufacturer|null the manufacturer of this part (if there is one) */
    private $manufacturer = null;
    /** @var Attachement|null the master picture Attachement of this part (if there is one) */
    private $master_picture_attachement = null;
    /** @var Orderdetails[] all orderdetails-objects as a one-dimensional array of Orderdetails-objects
    (empty array if there are no orderdetails) */
    private $orderdetails = null;
    /** @var Orderdetails|null the order orderdetails of this part (for "parts to order") */
    private $order_orderdetails;

    /** @var Device[] all devices in which this part is used (as a one-dimensional array of Device objects) */
    private $devices = null;

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @param Database  &$database:     reference to the Database-object
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log:          reference to the Log-object
     * @param integer   $id:            ID of the part we want to get
     * @param array     $db_data        If you have already data from the database, then use give it with this param, the part, wont make a database request.
     *
     * @throws Exception    if there is no such part in the database
     * @throws Exception    if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $id, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, 'parts', $id, false, $db_data);
    }

    /**
     * @copydoc DBElement::reset_attributes()
     */
    public function resetAttributes($all = false)
    {
        $this->category                     = null;
        $this->footprint                    = null;
        $this->storelocation                = null;
        $this->manufacturer                 = null;
        $this->orderdetails                 = null;
        $this->devices                      = null;
        $this->master_picture_attachement   = null;
        $this->order_orderdetails           = null;

        parent::resetAttributes($all);
    }

    /********************************************************************************
     *
     *   Basic Methods
     *
     *********************************************************************************/

    /**
     * Delete this element
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
        try {
            $transaction_id = $this->database->beginTransaction(); // start transaction

            $devices = $this->getDevices();
            $orderdetails = $this->getOrderdetails();
            $this->resetAttributes(); // set $this->devices ans $this->orderdetails to NULL

            // Check if there are no Devices with this Part (and delete them if neccessary)
            if (count($devices) > 0) {
                if ($delete_device_parts) {
                    foreach ($devices as $device) {
                        foreach ($device->getParts() as $device_part) {
                            /** @var $device_part DevicePart */
                            if ($device_part->getPart()->getId() == $this->getID()) {
                                $device_part->delete();
                            }
                        }
                    }
                } else {
                    throw new Exception('Das Bauteil "'.$this->getName().'" wird noch in '.count($devices).
                        ' Baugruppen verwendet und kann daher nicht gelöscht werden!');
                }
            }

            // Delete all Orderdetails
            foreach ($orderdetails as $details) {
                $details->delete();
            }

            // now we can delete this element + all attachements of it
            parent::delete($delete_files_from_hdd);

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->resetAttributes();

            throw new Exception("Das Bauteil \"".$this->getName()."\" konnte nicht gelöscht werden!\nGrund: ".$e->getMessage());
        }
    }

    /**
     * Gets the content for a 1D/2D barcode for this part
     * @param string $barcode_type the type of the barcode ("EAN8" or "QR")
     * @return string
     * @throws Exception An Exception is thrown if you selected a unknown barcode type.
     */
    public function getBarcodeContent($barcode_type = "EAN8")
    {
        switch ($barcode_type) {
            case "EAN8":
                $code = (string) $this->getID();
                while (strlen($code) < 7) {
                    $code = '0' . $code;
                }
                return $code;

            case "QR":
                return "Part-DB; Part: " . $this->getID();

            default:
                throw new Exception(_("Label type unknown: ").$barcode_type);
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the description
     *
     * @param boolean $parse_bbcode Should BBCode converted to HTML, before returning
     * @param int $short_output If this is bigger than 0, than the description will be shortened to this length.
     * @return string       the description
     */
    public function getDescription($parse_bbcode = true, $short_output = 0)
    {
        $val = htmlspecialchars($this->db_data['description']);

        if ($short_output > 0 && strlen($val) > $short_output) {
            $val = substr($val, 0, $short_output);
            $val = $val . "...";
            $val = '<span class="text-muted">' . $val . '</span class="text-muted">';
        }

        if ($parse_bbcode) {
            $bbcode = new BBCodeParser;
            $val = $bbcode->only("bold", "italic", "underline", "linethrough")->parse($val);
        }

        return $val;
    }

    /**
     *  Get the count of parts which are in stock
     *
     * @return integer       count of parts which are in stock
     */
    public function getInstock()
    {
        return $this->db_data['instock'];
    }

    /**
     *  Get the count of parts which must be in stock at least
     *
     * @return integer       count of parts which must be in stock at least
     */
    public function getMinInstock()
    {
        return $this->db_data['mininstock'];
    }

    /**
     *  Get the comment
     *
     * @param boolean $parse_bbcode Should BBCode converted to HTML, before returning
     * @return string       the comment
     */
    public function getComment($parse_bbcode = true)
    {
        $val = htmlspecialchars($this->db_data['comment']);
        if ($parse_bbcode) {
            $bbcode = new BBCodeParser;
            $val = $bbcode->parse($val);
        }

        return $val;
    }

    /**
     *  Get if this part is obsolete
     *
     * @note    A Part is marked as "obsolete" if all their orderdetails are marked as "obsolete".
     *          If a part has no orderdetails, the part isn't marked as obsolete.
     *
     * @return boolean      @li true if this part is obsolete
     *                      @li false if this part isn't obsolete
     */
    public function getObsolete()
    {
        $all_orderdetails = $this->getOrderdetails();

        if (count($all_orderdetails) == 0) {
            return false;
        }

        foreach ($all_orderdetails as $orderdetails) {
            if (! $orderdetails->getObsolete()) {
                return false;
            }
        }

        return true;
    }

    /**
     *  Get if this part is visible
     *
     * @return boolean      @li true if this part is visible
     *                      @li false if this part isn't visible
     */
    public function getVisible()
    {
        return $this->db_data['visible'];
    }

    /**
     *  Get the selected order orderdetails of this part
     *
     * @return Orderdetails         the selected order orderdetails
     * @return NULL                 if there is no order supplier selected
     */
    public function getOrderOrderdetails()
    {
        if ((! is_object($this->order_orderdetails)) && ($this->db_data['order_orderdetails_id'] != null)) {
            $this->order_orderdetails = new Orderdetails(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['order_orderdetails_id']
            );

            if ($this->order_orderdetails->getObsolete()) {
                $this->setOrderOrderdetailsID(null);
                $this->order_orderdetails = null;
            }
        }

        return $this->order_orderdetails;
    }

    /**
     *  Get the order quantity of this part
     *
     * @return integer      the order quantity
     */
    public function getOrderQuantity()
    {
        return $this->db_data['order_quantity'];
    }

    /**
     *  Get the minimum quantity which should be ordered
     *
     * @param boolean $with_devices     @li if true, all parts from devices which are marked as "to order" will be included in the calculation
     *                                  @li if false, only max(mininstock - instock, 0) will be returned
     *
     * @return integer      the minimum order quantity
     */
    public function getMinOrderQuantity($with_devices = true)
    {
        if ($with_devices) {
            $count_must_order = 0;      // for devices with "order_only_missing_parts == false"
            $count_should_order = 0;    // for devices with "order_only_missing_parts == true"
            $deviceparts = DevicePart::getOrderDeviceParts($this->database, $this->current_user, $this->log, $this->getID());
            foreach ($deviceparts as $devicepart) {
                /** @var $devicepart DevicePart */
                /** @var $device Device */
                $device = $devicepart->getDevice();
                if ($device->getOrderOnlyMissingParts()) {
                    $count_should_order += $device->getOrderQuantity() * $devicepart->getMountQuantity();
                } else {
                    $count_must_order += $device->getOrderQuantity() * $devicepart->getMountQuantity();
                }
            }

            return $count_must_order + max(0, $this->getMinInstock() - $this->getInstock() + $count_should_order);
        } else {
            return max(0, $this->getMinInstock() - $this->getInstock());
        }
    }

    /**
     *  Get the "manual_order" attribute
     *
     * @return boolean      the "manual_order" attribute
     */
    public function getManualOrder()
    {
        return $this->db_data['manual_order'];
    }

    /**
     *  Get the link to the website of the article on the manufacturers website
     *
     * @return string           the link to the article
     */
    public function getManufacturerProductUrl()
    {
        if (strlen($this->db_data['manufacturer_product_url']) > 0) {
            return $this->db_data['manufacturer_product_url'];
        } elseif (is_object($this->getManufacturer())) {
            return $this->getManufacturer()->getAutoProductUrl($this->db_data['name']);
        } else {
            return '';
        } // no url is available
    }

    /**
     * Returns the last time when the part was modified.
     * @return string The time of the last edit.
     */
    public function getLastModified()
    {
        return $this->db_data['last_modified'];
    }

    /**
     * Returns the date/time when the part was created
     * @return string The creation time of the part.
     */
    public function getDatetimeAdded()
    {
        return $this->db_data['datetime_added'];
    }

    /**
     *  Get the category of this part
     *
     * There is always a category, for each part!
     *
     * @return Category     the category of this part
     *
     * @throws Exception if there was an error
     */
    public function getCategory()
    {
        if (! is_object($this->category)) {
            $this->category = new Category(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['id_category']
            );
        }

        return $this->category;
    }

    /**
     *  Get the footprint of this part (if there is one)
     *
     * @return Footprint    the footprint of this part (if there is one)
     * @return NULL         if this part has no footprint
     *
     * @throws Exception if there was an error
     */
    public function getFootprint()
    {
        if ((! is_object($this->footprint)) && ($this->db_data['id_footprint'] != null)) {
            $this->footprint = new Footprint(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['id_footprint']
            );
        }

        return $this->footprint;
    }

    /**
     *  Get the storelocation of this part (if there is one)
     *
     * @return Storelocation    the storelocation of this part (if there is one)
     * @return NULL             if this part has no storelocation
     *
     * @throws Exception if there was an error
     */
    public function getStorelocation()
    {
        if ((! is_object($this->storelocation)) && ($this->db_data['id_storelocation'] != null)) {
            $this->storelocation = new Storelocation(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['id_storelocation']
            );
        }

        return $this->storelocation;
    }

    /**
     *  Get the manufacturer of this part (if there is one)
     *
     * @return Manufacturer     the manufacturer of this part (if there is one)
     * @return NULL             if this part has no manufacturer
     *
     * @throws Exception if there was an error
     */
    public function getManufacturer()
    {
        if ((! is_object($this->manufacturer)) && ($this->db_data['id_manufacturer'] != null)) {
            $this->manufacturer = new Manufacturer(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['id_manufacturer']
            );
        }

        return $this->manufacturer;
    }

    /**
     *  Get the master picture "Attachement"-object of this part (if there is one)
     *
     * @return Attachement      the master picture Attachement of this part (if there is one)
     * @return NULL             if this part has no master picture
     *
     * @throws Exception if there was an error
     */
    public function getMasterPictureAttachement()
    {
        if ((! is_object($this->master_picture_attachement)) && ($this->db_data['id_master_picture_attachement'] != null)) {
            $this->master_picture_attachement = new Attachement(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['id_master_picture_attachement']
            );
        }

        return $this->master_picture_attachement;
    }

    /**
     *  Get all orderdetails of this part
     *
     * @param boolean $hide_obsolete    If true, obsolete orderdetails will NOT be returned
     *
     * @return Orderdetails[]    @li all orderdetails as a one-dimensional array of Orderdetails objects
     *                      (empty array if there are no ones)
     *                  @li the array is sorted by the suppliers names / minimum order quantity
     *
     * @throws Exception if there was an error
     */
    public function getOrderdetails($hide_obsolete = false)
    {
        if (! is_array($this->orderdetails)) {
            $this->orderdetails = array();

            $query = 'SELECT orderdetails.* FROM orderdetails '.
                'LEFT JOIN suppliers ON suppliers.id = orderdetails.id_supplier '.
                'WHERE part_id=? '.
                'ORDER BY suppliers.name ASC';

            $query_data = $this->database->query($query, array($this->getID()));

            foreach ($query_data as $row) {
                $this->orderdetails[] = new Orderdetails($this->database, $this->current_user, $this->log, $row['id'], $row);
            }
        }

        if ($hide_obsolete) {
            $orderdetails = $this->orderdetails;
            foreach ($orderdetails as $key => $details) {
                if ($details->getObsolete()) {
                    unset($orderdetails[$key]);
                }
            }
            return $orderdetails;
        } else {
            return $this->orderdetails;
        }
    }

    /**
     *  Get all devices which uses this part
     *
     * @return Device[]    @li all devices which uses this part as a one-dimensional array of Device objects
     *                      (empty array if there are no ones)
     *                  @li the array is sorted by the devices names
     *
     * @throws Exception if there was an error
     */
    public function getDevices()
    {
        if (! is_array($this->devices)) {
            $this->devices = array();

            $query = 'SELECT devices.* FROM device_parts '.
                'LEFT JOIN devices ON device_parts.id_device=devices.id '.
                'WHERE id_part=? '.
                'GROUP BY id_device '.
                'ORDER BY devices.name ASC';

            $query_data = $this->database->query($query, array($this->getID()));

            foreach ($query_data as $row) {
                $this->devices[] = new Device($this->database, $this->current_user, $this->log, $row['id_device'], $row);
            }
        }

        return $this->devices;
    }

    /**
     *  Get all suppliers of this part
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
     * @return array        all suppliers as a one-dimensional array of Supplier objects
     *                      (if "$object_array == true")
     * @return array        all supplier-names as a one-dimensional array of strings
     *                      ("if $object_array == false" and "$delimeter == NULL")
     * @return string       a sting of all supplier names, delimeted by $delimeter
     *                      ("if $object_array == false" and $delimeter is a string)
     *
     * @throws Exception    if there was an error
     */
    public function getSuppliers($object_array = true, $delimeter = null, $full_paths = false, $hide_obsolete = false)
    {
        $suppliers = array();
        $orderdetails = $this->getOrderdetails($hide_obsolete);

        foreach ($orderdetails as $details) {
            $suppliers[] = $details->getSupplier();
        }

        if ($object_array) {
            return $suppliers;
        } else {
            $supplier_names = array();
            foreach ($suppliers as $supplier) {
                /** @var Supplier $supplier */
                if ($full_paths) {
                    $supplier_names[] = $supplier->getFullPath();
                } else {
                    $supplier_names[] = $supplier->getName();
                }
            }

            if (is_string($delimeter)) {
                return implode($delimeter, $supplier_names);
            } else {
                return $supplier_names;
            }
        }
    }

    /**
     *  Get all supplier-part-Nrs
     *
     * This method simply gets the suppliers-part-Nrs of the orderdetails and prepare them.\n
     * You can get the numbers as an array or as a string with individual delimeter.
     *
     * @param string|NULL   $delimeter      @li if this is a string, this method returns a delimeted string
     *                                      @li otherwise, this method returns an array of strings
     * @param boolean       $hide_obsolete  If true, supplierpartnrs from obsolete orderdetails will NOT be returned
     *
     * @return array        all supplierpartnrs as an array of strings (if "$delimeter == NULL")
     * @return string       all supplierpartnrs as a string, delimeted ba $delimeter (if $delimeter is a string)
     *
     * @throws Exception    if there was an error
     */
    public function getSupplierPartNrs($delimeter = null, $hide_obsolete = false)
    {
        $supplierpartnrs = array();

        foreach ($this->getOrderdetails($hide_obsolete) as $details) {
            $supplierpartnrs[] = $details->getSupplierPartNr();
        }

        if (is_string($delimeter)) {
            return implode($delimeter, $supplierpartnrs);
        } else {
            return $supplierpartnrs;
        }
    }

    /**
     *  Get all prices of this part
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
     * @return array        all prices as an array of floats (if "$delimeter == NULL" & "$float_array == true")
     * @return array        all prices as an array of strings (if "$delimeter == NULL" & "$float_array == false")
     * @return string       all prices as a string, delimeted by $delimeter (if $delimeter is a string)
     *
     * @warning             If there are orderdetails without prices, for these orderdetails there
     *                      will be a "NULL" in the returned float array (or a "-" in the string array)!!
     *                      (This is needed for the HTML output, if there are all orderdetails and prices listed.)
     *
     * @throws Exception    if there was an error
     */
    public function getPrices($float_array = false, $delimeter = null, $quantity = 1, $multiplier = null, $hide_obsolete = false)
    {
        $prices = array();

        foreach ($this->getOrderdetails($hide_obsolete) as $details) {
            $prices[] = $details->getPrice((! $float_array), $quantity, $multiplier);
        }

        if (is_string($delimeter)) {
            return implode($delimeter, $prices);
        } else {
            return $prices;
        }
    }

    /**
     *  Get the average price of all orderdetails
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
     * @return float        price (if "$as_money_string == false")
     * @return NULL         if there are no prices for this part and "$as_money_string == false"
     * @return string       price with currency (if "$as_money_string == true")
     *
     * @throws Exception    if there was an error
     */
    public function getAveragePrice($as_money_string = false, $quantity = 1, $multiplier = null)
    {
        $prices = $this->getPrices(true, null, $quantity, $multiplier, true);
        $average_price = null;

        $count = 0;
        foreach ($prices as $price) {
            if ($price !== null) {
                $average_price += $price;
                $count++;
            }
        }

        if ($count > 0) {
            $average_price /= $count;
        }

        if ($as_money_string) {
            return floatToMoneyString($average_price);
        } else {
            return $average_price;
        }
    }

    /**
     *  Get the filename of the master picture (absolute path from filesystem root)
     *
     * @param boolean $use_footprint_filename   @li if true, and this part has no picture, this method
     *                                              will return the filename of its footprint (if available)
     *                                          @li if false, and this part has no picture,
     *                                              this method will return NULL
     *
     * @return string   the whole path + filename from filesystem root as a UNIX path (with slashes)
     * @return NULL     if there is no picture
     *
     * @throws Exception if there was an error
     */
    public function getMasterPictureFilename($use_footprint_filename = false)
    {
        $master_picture = $this->getMasterPictureAttachement(); // returns an Attachement-object

        if (is_object($master_picture)) {
            return $master_picture->getFilename();
        }

        if ($use_footprint_filename) {
            $footprint = $this->getFootprint();
            if (is_object($footprint)) {
                return $footprint->getFilename();
            }
        }

        return null;
    }

    /**
     * Parses the selected fields and extract Properties of the part.
     * @param bool $use_description Use the description field for parsing
     * @param bool $use_comment Use the comment field for parsing
     * @param bool $force_output Properties are parsed even if properties are disabled.
     * @return array A array of PartProperty objects.
     * @return array If Properties are disabled or nothing was detected, then an empty array is returned.
     */
    public function getProperties($use_description = true, $use_comment = true, $use_name = true, $force_output = false)
    {
        global $config;

        if ($config['properties']['active'] || $force_output) {
            if ($this->getCategory()->getDisableProperties(true)) {
                return array();
            }

            $desc = array();
            $comm = array();

            if ($use_name === true) {
                $name = $this->getCategory()->getPartnameRegexObj()->getProperties($this->getName());
            }
            if ($use_description === true) {
                $desc = PartProperty::parseDescription($this->getDescription());
            }
            if ($use_comment === true) {
                $comm = PartProperty::parseDescription($this->getComment());
            }

            $arr = array_merge($name, $desc, $comm);

            return $arr;
        } else {
            return array();
        }
    }

    /**
     * Returns a loop (array) of the array representations of the properties of this part.
     * @param bool $use_description Use the description field for parsing
     * @param bool $use_comment Use the comment field for parsing
     * @return array A array of arrays with the name and value of the properties.
     */
    public function getPropertiesLoop($use_description = true, $use_comment = true)
    {
        $arr = array();
        foreach ($this->getProperties() as $property) {
            /* @var PartProperty $property */
            $arr[] = $property->getArray($use_description, $use_comment);
        }
        return $arr;
    }

    public function hasValidName()
    {
        return Part::isValidName($this->getName(), $this->getCategory());
    }



    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     *  Set the description
     *
     * @param string $new_description       the new description
     *
     * @throws Exception if there was an error
     */
    public function setDescription($new_description)
    {
        $this->setAttributes(array('description' => $new_description));
    }

    /**
     *  Set the count of parts which are in stock
     *
     * @param integer $new_instock       the new count of parts which are in stock
     *
     * @throws Exception if the new instock is not valid
     * @throws Exception if there was an error
     */
    public function setInstock($new_instock)
    {
        $this->setAttributes(array('instock' => $new_instock));
    }

    /**
     *  Set the count of parts which should be in stock at least
     *
     * @param integer $new_instock       the new count of parts which should be in stock at least
     *
     * @throws Exception if the new mininstock is not valid
     * @throws Exception if there was an error
     */
    public function setMinInstock($new_mininstock)
    {
        $this->setAttributes(array('mininstock' => $new_mininstock));
    }

    /**
     *  Set the comment
     *
     * @param string $new_comment       the new comment
     *
     * @throws Exception if there was an error
     */
    public function setComment($new_comment)
    {
        $this->setAttributes(array('comment' => $new_comment));
    }

    /**
     *  Set the "manual_order" attribute
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
    public function setManualOrder($new_manual_order, $new_order_quantity = 1, $new_order_orderdetails_id = null)
    {
        $this->setAttributes(array('manual_order'          => $new_manual_order,
            'order_orderdetails_id' => $new_order_orderdetails_id,
            'order_quantity'        => $new_order_quantity));
    }

    /**
     *  Set the ID of the order orderdetails
     *
     * @param integer|NULL $new_order_orderdetails_id       @li the new order orderdetails ID
     *                                                      @li Or, to remove the orderdetails, pass a NULL
     *
     * @throws Exception if there was an error
     */
    public function setOrderOrderdetailsID($new_order_orderdetails_id)
    {
        $this->setAttributes(array('order_orderdetails_id' => $new_order_orderdetails_id));
    }

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
     *  Set the ID of the category
     *
     * @note    Every part must have a valid category (in contrast to the
     *          attributes "footprint", "storelocation", ...)!
     *
     * @param integer $new_category_id       the ID of the category
     *
     * @throws Exception if the new category ID is not valid
     * @throws Exception if there was an error
     */
    public function setCategoryID($new_category_id)
    {
        $this->setAttributes(array('id_category' => $new_category_id));
    }

    /**
     *  Set the footprint ID
     *
     * @param integer|NULL $new_footprint_id    @li the ID of the footprint
     *                                          @li NULL means "no footprint"
     *
     * @throws Exception if the new footprint ID is not valid
     * @throws Exception if there was an error
     */
    public function setFootprintID($new_footprint_id)
    {
        $this->setAttributes(array('id_footprint' => $new_footprint_id));
    }

    /**
     *  Set the storelocation ID
     *
     * @param integer|NULL $new_storelocation_id    @li the ID of the storelocation
     *                                              @li NULL means "no storelocation"
     *
     * @throws Exception if the new storelocation ID is not valid
     * @throws Exception if there was an error
     */
    public function setStorelocationID($new_storelocation_id)
    {
        $this->setAttributes(array('id_storelocation' => $new_storelocation_id));
    }

    /**
     *  Set the manufacturer ID
     *
     * @param integer|NULL $new_manufacturer_id     @li the ID of the manufacturer
     *                                              @li NULL means "no manufacturer"
     *
     * @throws Exception if the new manufacturer ID is not valid
     * @throws Exception if there was an error
     */
    public function setManufacturerID($new_manufacturer_id)
    {
        $this->setAttributes(array('id_manufacturer' => $new_manufacturer_id));
    }

    /**
     *  Set the ID of the master picture Attachement
     *
     * @param integer|NULL $new_master_picture_attachement_id       @li the ID of the Attachement object of the master picture
     *                                                              @li NULL means "no master picture"
     *
     * @throws Exception if the new ID is not valid
     * @throws Exception if there was an error
     */
    public function setMasterPictureAttachementID($new_master_picture_attachement_id)
    {
        $this->setAttributes(array('id_master_picture_attachement' => $new_master_picture_attachement_id));
    }

    /********************************************************************************
     *
     *   Table Builder Methods
     *
     *********************************************************************************/

    /**
     *  Build the array for the template table row of this part
     *
     * @param string    $table_type             @li the type of the table which will be builded
     *                                          @li see Part::build_template_table_array()
     * @param int    $row_index                 The index of this table row
     * @param array     $additional_values      Here you can pass more values than only the part attributes.
     *                                          This is used in DevicePart::build_template_table_row_array().
     *
     * @return array    The array for the template output (element of the loop "table")
     *
     * @throws Exception if there was an error
     */
    public function buildTemplateTableRowArray($table_type, $row_index, $additional_values = array())
    {
        global $config;

        if ($config['appearance']['short_description']) {
            $max_length =  $config['appearance']['short_description_length'];
        } else {
            $max_length = 0;
        }

        $table_row = array();
        $table_row['row_odd']       = isOdd($row_index);
        $table_row['row_index']     = $row_index;
        $table_row['id']            = $this->getID();
        $table_row['row_fields']    = array();

        foreach (explode(';', $config['table'][$table_type]['columns']) as $caption) {
            $row_field = array();
            $row_field['row_index']     = $row_index;
            $row_field['caption']       = $caption;
            $row_field['id']            = $this->getID();
            $row_field['name']          = $this->getName();

            switch ($caption) {
                case 'hover_picture':
                    $picture_filename = str_replace(BASE, BASE_RELATIVE, $this->getMasterPictureFilename(true));
                    $row_field['picture_name']  = strlen($picture_filename) ? basename($picture_filename) : '';
                    $row_field['small_picture'] = strlen($picture_filename) ? $picture_filename : '';
                    $row_field['hover_picture'] = strlen($picture_filename) ? $picture_filename : '';
                    break;

                case 'name':
                case 'description':
                case 'comment':
                case 'name_description':
                    $row_field['obsolete']          = $this->getObsolete();
                    $row_field['comment']           = $this->getComment();
                    $row_field['description']       = $this->getDescription(true, $max_length);
                    break;

                case 'instock':
                case 'mininstock':
                case 'instock_mininstock':
                case 'instock_edit_buttons':
                    $row_field['instock']               = $this->getInstock();
                    $row_field['mininstock']            = $this->getMinInstock();
                    $row_field['not_enought_instock']   = ($this->getInstock() < $this->getMinInstock());
                    break;

                case 'category':
                    $category = $this->getCategory();
                    $row_field['category_name'] = $category->getName();
                    $row_field['category_path'] = $category->getFullPath();
                    $row_field['category_id'] = $category->getID();
                    break;

                case 'footprint':
                    $footprint = $this->getFootprint();
                    if (is_object($footprint)) {
                        $row_field['footprint_name'] = $footprint->getName();
                        $row_field['footprint_path'] = $footprint->getFullPath();
                        $row_field['footprint_id'] = $footprint->getID();
                    }
                    break;

                case 'manufacturer':
                    $manufacturer = $this->getManufacturer();
                    if (is_object($manufacturer)) {
                        $row_field['manufacturer_name'] = $manufacturer->getName();
                        $row_field['manufacturer_path'] = $manufacturer->getFullPath();
                        $row_field['manufacturer_id'] = $manufacturer->getID();
                    }
                    break;

                case 'storelocation':
                    $storelocation = $this->getStorelocation();
                    if (is_object($storelocation)) {
                        $row_field['storelocation_name'] = $storelocation->getName();
                        $row_field['storelocation_path'] = $storelocation->getFullPath();
                        $row_field['storelocation_id'] = $storelocation->getID();
                    }
                    break;

                case 'suppliers':
                    $suppliers_loop = array();
                    foreach ($this->getSuppliers(false, null, false, true) as $supplier_name) { // suppliers from obsolete orderdetails will not be shown
                        $suppliers_loop[] = array(  'row_index'         => $row_index,
                            'supplier_name'     => $supplier_name);
                    }

                    $row_field['suppliers'] = $suppliers_loop;
                    break;

                case 'suppliers_radiobuttons':
                    if ($table_type == 'order_parts') {
                        if (is_object($this->getOrderOrderdetails())) {
                            $order_orderdetails_id = $this->getOrderOrderdetails()->getID();
                        } else {
                            $order_orderdetails_id = 0;
                        }

                        $suppliers_loop = array();
                        foreach ($this->getOrderdetails(true) as $orderdetails) { // obsolete orderdetails will not be shown
                            $suppliers_loop[] = array(  'row_index'         => $row_index,
                                'orderdetails_id'   => $orderdetails->getID(),
                                'supplier_name'     => $orderdetails->getSupplier()->getFullPath(),
                                'selected'          => ($order_orderdetails_id == $orderdetails->getID()));
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
                    foreach ($this->getOrderdetails(true) as $details) { // partnrs from obsolete orderdetails will not be shown
                        $partnrs_loop[] = array(    'row_index'            => $row_index,
                            'supplier_partnr'      => $details->getSupplierPartNr(),
                            'supplier_product_url' => $details->getSupplierProductUrl());
                    }

                    $row_field['supplier_partnrs'] = $partnrs_loop;
                    break;

                case 'datasheets':
                    $datasheet_loop = $config['auto_datasheets']['entries'];

                    foreach ($datasheet_loop as $key => $entry) {
                        $datasheet_loop[$key]['url'] = str_replace('%%PARTNAME%%', urlencode($this->getName()), $entry['url']);
                    }

                    if ($config['appearance']['use_old_datasheet_icons'] == true) {
                        foreach ($datasheet_loop as &$sheet) {
                            if (isset($sheet['old_image'])) {
                                $sheet['image'] = $sheet['old_image'];
                            }
                        }
                    }

                    $row_field['datasheets'] = $datasheet_loop;
                    break;

                case 'average_single_price':
                    $row_field['average_single_price'] = $this->getAveragePrice(true, 1);
                    break;

                case 'single_prices':
                    if ($table_type == 'order_parts') {
                        $min_discount_quantity = $this->getOrderQuantity();
                    } else {
                        $min_discount_quantity = 1;
                    }

                    $prices_loop = array();
                    foreach ($this->getPrices(false, null, $min_discount_quantity, 1, true) as $price) { // prices from obsolete orderdetails will not be shown
                        $prices_loop[] = array(     'row_index'         => $row_index,
                            'single_price'      => $price);
                    }

                    $row_field['single_prices'] = $prices_loop;
                    break;

                case 'total_prices':
                    switch ($table_type) {
                        case 'order_parts':
                            $min_discount_quantity = $this->getOrderQuantity();
                            break;
                        default:
                            //throw new Exception('Keine Totalpreise verfügbar für den Tabellentyp "'.$table_type.'"!');
                            $min_discount_quantity = 0;
                    }

                    $prices_loop = array();
                    foreach ($this->getPrices(false, null, $min_discount_quantity, null, true) as $price) { // prices from obsolete orderdetails will not be shown
                        $prices_loop[] = array( 'row_index'     => $row_index,
                            'total_price'   => $price);
                    }

                    $row_field['total_prices'] = $prices_loop;
                    break;

                case 'order_quantity_edit':
                    if ($table_type == 'order_parts') {
                        $row_field['order_quantity'] = $this->getOrderQuantity();
                        $row_field['min_order_quantity'] = $this->getMinOrderQuantity();
                    }
                    break;

                case 'order_options':
                    if ($table_type == 'order_parts') {
                        $suppliers_loop = array();
                        $row_field['enable_remove'] = (($this->getInstock() >= $this->getMinInstock()) && ($this->getManualOrder()));
                    }
                    break;

                case 'button_decrement':
                    $row_field['decrement_disabled'] = ($this->getInstock() < 1);
                    break;

                case 'attachements':
                    $attachements = array();
                    foreach ($this->getAttachements(null, true) as $attachement) {
                        $attachements[] = array(    'name'      => $attachement->getName(),
                            'filename'  => str_replace(BASE, BASE_RELATIVE, $attachement->getFilename()),
                            'type'      => $attachement->getType()->getFullPath());
                    }
                    $row_field['attachements'] = $attachements;
                    break;

                case 'id':
                case 'button_increment':
                case 'button_edit':
                case 'quantity_edit': // for DevicePart Objects
                case 'mountnames_edit': // for DevicePart Objects
                    // nothing to do, only to avoid the Exception in the default-case
                    break;

                default:
                    throw new Exception('Unbekannte Tabellenspalte: "'.$caption.'". Überprüfen Sie die Einstellungen '.
                        'für den Tabellentyp "'.$table_type.'" in Ihrer "config.php"');
            }

            // maybe there are any additional values to add...
            if (array_key_exists($caption, $additional_values)) {
                foreach ($additional_values[$caption] as $key => $value) {
                    $row_field[$key] = $additional_values[$caption][$key];
                }
            }

            $table_row['row_fields'][] = $row_field;
        }

        return $table_row;
    }

    /**
     *  Build the template table array of an array of parts
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
     *  - "location_parts"
     *
     *
     * @return array    the template loop array for the table
     *
     * @throws Exception if there was an error
     */
    public static function buildTemplateTableArray($parts, $table_type)
    {
        global $config;

        if (! isset($config['table'][$table_type])) {
            debug('error', '$table_type = "'.$table_type.'"', __FILE__, __LINE__, __METHOD__);
            throw new Exception(_('"$table_type" ist ungültig!'));
        }

        // table columns
        $columns = array();
        foreach (explode(';', $config['table'][$table_type]['columns']) as $caption) {
            $columns[] = array('caption' => $caption);
        }

        $table_loop = array();
        $table_loop[] = array('print_header' => true, 'columns' => $columns); // print the table header

        $row_index = 0;
        foreach ($parts as $part) {
            /** @var $part Part */
            $table_loop[] = $part->buildTemplateTableRowArray($table_type, $row_index);
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
     * @param $database Database
     * @param $current_user User
     * @param $log Log
     * @param $proposed_name string
     * @param $proposed_storelocation_id integer
     * @param $proposed_category_id integer
     * @return array|bool An array containing parts with similar name and storelocation and category
     */
    public static function checkForExistingPart(&$database, &$current_user, &$log, $proposed_name, $proposed_storelocation_id, $proposed_category_id)
    {
        $query = 'SELECT parts.id FROM parts'.
            ' LEFT JOIN storelocations ON parts.id_storelocation=storelocations.id'.
            ' LEFT JOIN categories ON parts.id_category=categories.id';

        $values = array();

        $query .= ' WHERE (parts.name LIKE ?)';
        $values[] = $proposed_name;

        $query .= ' AND (storelocations.id = ?)';
        $values[] = $proposed_storelocation_id;

        $query .= ' AND (categories.id = ?)';
        $values[] = $proposed_category_id;

        $query_data = $database->query($query, $values);

        $parts = array();

        foreach ($query_data as $row) {
            $part = new Part($database, $current_user, $log, $row['id']);
            $parts[] = $part;
        }

        if (empty($parts)) {
            return false;
        } else {
            return $parts;
        }
    }

    /**
     * @copydoc DBElement::check_values_validity()
     * @param $element Part
     */
    public static function checkValuesValidity(&$database, &$current_user, &$log, &$values, $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        // set "last_modified" to current datetime
        $values['last_modified'] = date('Y-m-d H:i:s');

        // set the datetype of the boolean attributes
        settype($values['visible'], 'boolean');
        settype($values['manual_order'], 'boolean');

        // check "instock"
        if ((! is_int($values['instock'])) && (! ctype_digit($values['instock']))) {
            debug(
                'warning',
                $values['instock'].'"!',
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Der neue Lagerbestand ist ungültig!');
        } elseif ($values['instock'] < 0) {
            throw new Exception('Der neue Lagerbestand von "'.$values['name'].'" wäre negativ und kann deshalb nicht gespeichert werden!');
        }

        // check "order_orderdetails_id"
        try {
            if ($values['order_orderdetails_id'] == 0) {
                $values['order_orderdetails_id'] = null;
            }

            if ((! $is_new) && ($values['order_orderdetails_id'] == null)
                && (($values['instock'] < $values['mininstock']) || ($values['manual_order']))
                && (($element->getInstock() >= $element->getMinInstock()) && (! $element->getManualOrder()))) {
                // if this part will be added now to the list of parts to order (instock is now less than mininstock, or manual_order is now true),
                // and this part has only one orderdetails, we will set that orderdetails as orderdetails to order from (attribute "order_orderdetails_id").
                // Note: If that part was already in the list of parts to order, wo mustn't change the orderdetails to order!!
                $orderdetails = $element->getOrderdetails();
                $order_orderdetails_id = ((count($orderdetails) == 1) ? $orderdetails[0]->getID(): null);
                $values['order_orderdetails_id'] = $order_orderdetails_id;
            }

            if ($values['order_orderdetails_id'] != null) {
                $order_orderdetails = new Orderdetails($database, $current_user, $log, $values['order_orderdetails_id']);
            }
        } catch (Exception $e) {
            debug('error', 'Ungültige "order_orderdetails_id": "'.$values['order_orderdetails_id'].'"'.
                "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(), __FILE__, __LINE__, __METHOD__);
            throw new Exception('Die gewählte Einkaufsinformation existiert nicht!');
        }

        // check "order_quantity"
        if (((! is_int($values['order_quantity'])) && (! ctype_digit($values['order_quantity'])))
            || ($values['order_quantity'] < 1)) {
            debug('error', 'order_quantity = "'.$values['order_quantity'].'"', __FILE__, __LINE__, __METHOD__);
            throw new Exception('Die Bestellmenge ist ungültig!');
        }

        // check if we have to reset the order attributes ("instock" is now less than "mininstock")
        if (($values['instock'] < $values['mininstock']) && (($is_new) || ($element->getInstock() >= $element->getMininstock()))) {
            if (! $values['manual_order']) {
                $values['order_quantity'] = $values['mininstock'] - $values['instock'];
            }

            $values['manual_order'] = false;
        }

        // check "mininstock"
        if (((! is_int($values['mininstock'])) && (! ctype_digit($values['mininstock'])))
            || ($values['mininstock'] < 0)) {
            debug(
                'warning',
                '"mininstock" ist keine gültige Zahl: "'.$values['mininstock'].'"!',
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Der neue Mindestlagerbestand ist ungültig!');
        }

        // check "id_category"
        try {
            // id_category == NULL means "no category", and this is not allowed!
            if ($values['id_category'] == null) {
                throw new Exception('"id_category" ist Null!');
            }

            $category = new Category($database, $current_user, $log, $values['id_category']);
        } catch (Exception $e) {
            debug(
                'warning',
                'Ungültige "id_category": "'.$values['id_category'].'"'.
                "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Die gewählte Kategorie existiert nicht!');
        }

        // check "id_footprint"
        try {
            if (($values['id_footprint'] == 0) && ($values['id_footprint'] !== null)) {
                $values['id_footprint'] = null;
            }
            $footprint = new Footprint($database, $current_user, $log, $values['id_footprint']);
        } catch (Exception $e) {
            debug(
                'warning',
                'Ungültige "id_footprint": "'.$values['id_footprint'].'"'.
                "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Der gewählte Footprint existiert nicht!');
        }

        // check "id_storelocation"
        try {
            if (($values['id_storelocation'] == 0) && ($values['id_storelocation'] !== null)) {
                $values['id_storelocation'] = null;
            }
            $storelocation = new Storelocation($database, $current_user, $log, $values['id_storelocation']);
        } catch (Exception $e) {
            debug(
                'warning',
                'Ungültige "id_storelocation": "'.$values['id_storelocation'].'"'.
                "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Der gewählte Lagerort existiert nicht!');
        }

        // check "id_manufacturer"
        try {
            if (($values['id_manufacturer'] == 0) && ($values['id_manufacturer'] !== null)) {
                $values['id_manufacturer'] = null;
            }
            $manufacturer = new Manufacturer($database, $current_user, $log, $values['id_manufacturer']);
        } catch (Exception $e) {
            debug(
                'warning',
                'Ungültige "id_manufacturer": "'.$values['id_manufacturer'].'"'.
                "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Der gewählte Hersteller existiert nicht!');
        }

        // check "id_master_picture_attachement"
        try {
            if ($values['id_master_picture_attachement']) {
                $master_picture_attachement = new Attachement($database, $current_user, $log, $values['id_master_picture_attachement']);
            } else {
                $values['id_master_picture_attachement'] = null;
            } // this will replace the integer "0" with NULL
        } catch (Exception $e) {
            debug(
                'warning',
                'Ungültige "id_master_picture_attachement": "'.$values['id_master_picture_attachement'].'"'.
                "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Die gewählte Datei existiert nicht!');
        }
    }

    /**
     *  Get count of parts
     *
     * @param Database &$database   reference to the Database-object
     *
     * @return integer              count of parts
     *
     * @throws Exception            if there was an error
     */
    public static function getCount(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        return $database->getCountOfRecords('parts');
    }

    /**
     *  Get the sum of all "instock" attributes of all parts
     *
     * All values in the table row "instock" will be summed up.
     *
     * This method is used in statistics.php.
     *
     * @param Database &$database       reference to the database object
     *
     * @return integer      the sum of all "instock" attributes of all parts
     *
     * @throws Exception if there was an error
     */
    public static function getSumCountInstock(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        $query_data = $database->query('SELECT sum(instock) as sum FROM parts');

        return intval($query_data[0]['sum']);
    }

    /**
     *  Get the sum price of all parts in stock
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
     * @return string       sum price as a money string with currency (if "$as_money_string == true")
     * @return float        sum price as a float (if "$as_money_string == false")
     *
     * @throws Exception if there was an error
     */
    public static function getSumPriceInstock(&$database, &$current_user, &$log, $as_money_string = true)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

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
            if ($instock == 0) {
                continue;
            }
            $price_per_piece = $row['price'] / $row['price_related_quantity'];
            $taken_parts = $row['min_discount_quantity'] * (integer)($instock / $row['min_discount_quantity']);
            $price_sum += $price_per_piece * $taken_parts;
            $instock = $instock - $taken_parts;
        }
        $price_sum = round($price_sum, 2);

        if ($as_money_string) {
            return floatToMoneyString($price_sum);
        } else {
            return $price_sum;
        }
    }

    /**
     *  Get all parts which should be ordered
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
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getOrderParts(&$database, &$current_user, &$log, $supplier_ids = array(), $with_devices = true)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        $parts = array();

        $query =    'SELECT parts.* FROM parts '.
            'LEFT JOIN orderdetails ON orderdetails.id = parts.order_orderdetails_id '.
            'WHERE (parts.instock < parts.mininstock '.
            'OR parts.manual_order = true '.
            'OR parts.id IN '.
            '(SELECT device_parts.id_part FROM device_parts '.
            'LEFT JOIN devices ON devices.id = device_parts.id_device '.
            'WHERE devices.order_quantity > 0)) ';
        if (count($supplier_ids) > 0) {
            $query .= 'AND ((false) OR ';
            foreach ($supplier_ids as $id) {
                $query .= '(orderdetails.id_supplier <=> ?) ';
            }
            $query .= ') ';
        }
        $query .= 'ORDER BY parts.name ASC';

        $query_data = $database->query($query, $supplier_ids);

        foreach ($query_data as $row) {
            $part = new Part($database, $current_user, $log, $row['id'], $row);
            if (($part->getManualOrder()) || ($part->getMinOrderQuantity() > 0)) {
                $parts[] = $part;
            }
        }

        return $parts;
    }

    /**
     *  Get all parts which have no price
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getNoPriceParts(&$database, &$current_user, &$log)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        $parts = array();

        $query =    'SELECT * from parts '.
            'WHERE id NOT IN (SELECT DISTINCT part_id FROM orderdetails '.
            'LEFT JOIN pricedetails ON orderdetails.id=pricedetails.orderdetails_id '.
            'WHERE pricedetails.id IS NOT NULL) '.
            'ORDER BY parts.name ASC';

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $parts[] = new Part($database, $current_user, $log, $row['id'], $row);
        }

        return $parts;
    }

    /**
     *  Get all obsolete parts
     *
     * @param Database  &$database              reference to the database object
     * @param User      &$current_user          reference to the user which is logged in
     * @param Log       &$log                   reference to the Log-object
     * @param boolean   $no_orderdetails_parts  if true, parts without any orderdetails will be returned too
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getObsoleteParts(&$database, &$current_user, &$log, $no_orderdetails_parts = false)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        $parts = array();

        if ($no_orderdetails_parts) {
            // show also parts which have no orderdetails
            $query =    'SELECT parts.* from parts '.
                'LEFT JOIN orderdetails ON orderdetails.part_id = parts.id '.
                'WHERE parts.id IN (SELECT part_id FROM `orderdetails` '.
                'WHERE part_id IN (SELECT part_id FROM `orderdetails` '.
                'WHERE obsolete = true GROUP BY part_id) '.
                'AND part_id NOT IN (SELECT part_id FROM `orderdetails` '.
                'WHERE obsolete = false GROUP BY part_id)) '.
                'OR orderdetails.id IS NULL '.
                'ORDER BY parts.name ASC';
        } else {
            // don't show parts which have no orderdetails
            $query =    'SELECT parts.* from parts '.
                'WHERE parts.id IN (SELECT part_id FROM `orderdetails` '.
                'WHERE part_id IN (SELECT part_id FROM `orderdetails` '.
                'WHERE obsolete = true GROUP BY part_id) '.
                'AND part_id NOT IN (SELECT part_id FROM `orderdetails` '.
                'WHERE obsolete = false GROUP BY part_id)) '.
                'ORDER BY parts.name ASC';
        }

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $parts[] = new Part($database, $current_user, $log, $row['id'], $row);
        }

        return $parts;
    }

    /**
     *  Search parts
     *
     * @param Database  &$database              reference to the database object
     * @param User      &$current_user          reference to the user which is logged in
     * @param Log       &$log                   reference to the Log-object
     * @param string    $keyword                the search string
     * @param string    $group_by               @li if this is a non-empty string, the returned array is a
     *                                              two-dimensional array with the group names as top level.
     *                                          @li supported groups are: '' (none), 'categories',
     *                                              'footprints', 'storelocations', 'manufacturers'
     * @param boolean   $part_name              if true, the search will include this attribute
     * @param boolean   $part_description       if true, the search will include this attribute
     * @param boolean   $part_comment           if true, the search will include this attribute
     * @param boolean   $footprint_name         if true, the search will include this attribute
     * @param boolean   $category_name          if true, the search will include this attribute
     * @param boolean   $storelocation_name     if true, the search will include this attribute
     * @param boolean   $supplier_name          if true, the search will include this attribute
     * @param boolean   $supplierpartnr         if true, the search will include this attribute
     * @param boolean   $manufacturer_name      if true, the search will include this attribute
     * @param boolean   $regex_search           if true, the search will use Regular Expressions to match
     *                                          the results.
     *
     * @return array    all found parts as a one-dimensional array of Part objects,
     *                  sorted by their names (if "$group_by == ''")
     * @return array    @li all parts as a two-dimensional array, grouped by $group_by,
     *                      sorted by name (if "$group_by != ''")
     *                  @li example: array('category1' => array(part1, part2, ...),
     *                      'category2' => array(part123, part124, ...), ...)
     *                  @li for the group names (in the example 'category1', 'category2', ...)
     *                      are the full paths used
     *
     * @throws Exception if there was an error
     */
    public static function searchParts(
        &$database,
        &$current_user,
        &$log,
        $keyword,
        $group_by = '',
        $part_name = true,
        $part_description = true,
        $part_comment = false,
        $footprint_name = false,
        $category_name = false,
        $storelocation_name = false,
        $supplier_name = false,
        $supplierpartnr = false,
        $manufacturer_name = false,
        $regex_search = false
    ) {
        global $config;

        $keyword = trim($keyword);

        //When searchstring begins and ends with a backslash, treat the input as regex query
        if (substr($keyword, 0, 1) === '\\' &&  substr($keyword, -1) === '\\') {
            $regex_search = true;
            $keyword = substr($keyword, 1, -1); //Remove the backslashes
        }

        if (strlen($keyword) == 0) {
            return array();
        }

        $keywords = searchStringToArray($keyword);

        //Select the correct LIKE operator, for Regex or normal search
        if ($regex_search == false) {
            $like = "LIKE";
            /*
            $keyword = str_replace('*', '%', $keyword);
            $keyword = '%'.$keyword.'%'; */

            foreach ($keywords as &$k) {
                if ($k !== "") {
                    $k = str_replace('*', '%', $k);
                    $k = '%' . $k . '%';
                }
            }
        } else {
            $like = "RLIKE";
        }

        $groups = array();
        $parts = array();
        $values = array();



        $query = 'SELECT parts.* FROM parts'.
            ' LEFT JOIN footprints ON parts.id_footprint=footprints.id'.
            ' LEFT JOIN storelocations ON parts.id_storelocation=storelocations.id'.
            ' LEFT JOIN manufacturers  ON parts.id_manufacturer=manufacturers.id'.
            ' LEFT JOIN categories ON parts.id_category=categories.id'.
            ' LEFT JOIN orderdetails ON parts.id=orderdetails.part_id'.
            ' LEFT JOIN suppliers ON orderdetails.id_supplier=suppliers.id'.
            ' WHERE FALSE';

        if ($part_name && $keywords['name']!=="") {
            $query .= " OR (parts.name $like ?)";
            $values[] = $keywords['name'];
        }

        if ($part_description && $keywords['description']!=="") {
            $query .= " OR (parts.description $like ?)";
            $values[] = $keywords['description'];
        }

        if ($part_comment && $keywords['comment']!=="") {
            $query .= " OR (parts.comment $like ?)";
            $values[] = $keywords['comment'];
        }

        if ($footprint_name && $keywords['footprint']!=="") {
            $query .= " OR (footprints.name $like ?)";
            $values[] = $keywords['footprint'];
        }

        if ($category_name && $keywords['category']!=="") {
            $query .= " OR (categories.name $like ?)";
            $values[] = $keywords['category'];
        }

        if ($storelocation_name && $keywords['storelocation']!=="") {
            $query .= " OR (storelocations.name $like ?)";
            $values[] = $keywords['storelocation'];
        }

        if ($supplier_name && $keywords['suppliername']!=="") {
            $query .= " OR (suppliers.name $like ?)";
            $values[] = $keywords['suppliername'];
        }

        if ($supplierpartnr && $keywords['partnr']!=="") {
            $query .= " OR (orderdetails.supplierpartnr $like ?)";
            $values[] = $keywords['partnr'];
        }

        if ($manufacturer_name && $keywords['manufacturername']!=="") {
            $query .= " OR (manufacturers.name $like ?)";
            $values[] = $keywords['manufacturername'];
        }

        if (!isset($config['db']['limit']['search_parts'])) {
            $config['db']['limit']['search_parts'] = 200;
        }

        switch ($group_by) {
            case '':
                $query .= ' GROUP BY parts.id ORDER BY parts.name ASC';
                if (isset($config['db']['limit']['search_parts']) && $config['db']['limit']['search_parts']>0) {
                    $query .= ' LIMIT '.$config['db']['limit']['search_parts'];
                }
                break;

            case 'categories':
                $query .= ' GROUP BY parts.id ORDER BY categories.id, parts.name ASC';
                if (isset($config['db']['limit']['search_parts']) && $config['db']['limit']['search_parts']>0) {
                    $query .= ' LIMIT '.$config['db']['limit']['search_parts'];
                }
                break;

            default:
                throw new Exception('$group_by="'.$group_by.'" is not supported!');
        }

        $query_data = $database->query($query, $values);

        foreach ($query_data as $row) {
            $part = new Part($database, $current_user, $log, $row['id'], $row);

            switch ($group_by) {
                case '':
                    $parts[] = $part;
                    break;

                case 'categories':
                    $groups[$part->getCategory()->getFullPath()][] = $part;
                    break;
            }
        }

        if ($group_by != '') {
            ksort($groups);
            return $groups;
        } else {
            return $parts;
        }
    }

    /**
     * Build a template loop for a <select> list of group by options for the available group by options in part_search
     *
     * @param integer|string    $selected_val
     *
     * @return array    The template loop
     */
    public static function buildSearchGroupByLoop($selected_val = "")
    {
        $loop = array();


        $loop[] = array('value' => "", 'text' => _("Keine"), 'selected' => ($selected_val === ""));
        $loop[] = array('value' => "categories", 'text' => _("Kategorien"), 'selected' => ($selected_val === "categories"));


        return $loop;
    }

    /**
     *  Get all existing parts
     *
     * @param Database  &$database              reference to the database object
     * @param User      &$current_user          reference to the user which is logged in
     * @param Log       &$log                   reference to the Log-object
     * @param string    $group_by               @li if this is a non-empty string, the returned array is a
     *                                              two-dimensional array with the group names as top level.
     *                                          @li supported groups are: '' (none), 'categories'
     *
     * @return array    all found parts as a one-dimensional array of Part objects,
     *                  sorted by their names (if "$group_by == ''")
     * @return array    @li all parts as a two-dimensional array, grouped by $group_by,
     *                      sorted by name (if "$group_by != ''")
     *                  @li example: array('category1' => array(part1, part2, ...),
     *                      'category2' => array(part123, part124, ...), ...)
     *                  @li for the group names (in the example 'category1', 'category2', ...)
     *                      are the full paths used
     *
     * @throws Exception if there was an error
     */
    public static function getAllParts(&$database, &$current_user, &$log, $group_by = '')
    {
        $query = 'SELECT * FROM parts';

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $part = new Part($database, $current_user, $log, $row['id'], $row);

            switch ($group_by) {
                case '':
                    $parts[] = $part;
                    break;

                case 'categories':
                    $groups[$part->getCategory()->getFullPath()][] = $part;
                    break;
            }
        }

        if ($group_by != '') {
            ksort($groups);
            return $groups;
        } else {
            return $parts;
        }
    }

    /**
     *  Create a new part
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
     * @return Part     the new part
     * @return Part     the new part
     *
     * @throws Exception    if (this combination of) values is not valid
     * @throws Exception    if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(
        &$database,
        &$current_user,
        &$log,
        $name,
        $category_id,
        $description = '',
        $instock = 0,
        $mininstock = 0,
        $storelocation_id = null,
        $manufacturer_id = null,
        $footprint_id = null,
        $comment = '',
        $visible = false
    ) {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            'parts',
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
                'id_master_picture_attachement' => null,
                'manual_order'                  => false,
                'order_orderdetails_id'         => null,
                'order_quantity'                => 1)
        );
        // the column "datetime_added" will be automatically filled by MySQL
        // the column "last_modified" will be filled in the function check_values_validity()
    }

    /**
     * Check if the name of the part is valid regarding the partname_regex of the category.
     * @param $partname string The name of the part.
     * @param $category Category The category of the part.
     * @return boolean True if name is valid
     */
    public static function isValidName($partname, $category)
    {
        return $category->checkPartname($partname);
    }

    /**
     * Returns a Array representing the current object.
     * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
     * @return array A array representing the current object.
     */
    public function getAPIArray($verbose = false)
    {
        $json =  array( "id" => $this->getID(),
            "name" => $this->getName(),
            "description" => $this->getDescription(true),
            "description_raw" => $this->getDescription(false),
            "comment" => $this->getComment(true),
            "comment_raw" => $this->getComment(false),
            "instock" => $this->getInstock(),
            "mininstock" => $this->getMinInstock(),
            "category" => $this->getCategory()->getAPIArray(false),
            "footprint" => tryToGetAPIModelArray($this->getFootprint(), false),
            "storelocation" => tryToGetAPIModelArray($this->getStorelocation(), false),
            "manufacturer" => tryToGetAPIModelArray($this->getManufacturer(), false),
            "orderdetails" => convertAPIModelArray($this->getOrderdetails(), false),
        );

        if ($verbose == true) {
            $ver = array(
                "obsolete" => $this->getObsolete() == true,
                "visible" => $this->getVisible() == true,
                "orderquantity" => $this->getOrderQuantity(),
                "minorderquantity" => $this->getMinOrderQuantity(),
                "manualorder" => $this->getManualOrder(),
                "lastmodified" => $this->getLastModified(),
                "datetime_added" => $this->getDatetimeAdded(),
                "avgprice" => $this->getAveragePrice(),
                "properties" => convertAPIModelArray($this->getProperties(), false));
            return array_merge($json, $ver);
        }
        return $json;
    }
}
