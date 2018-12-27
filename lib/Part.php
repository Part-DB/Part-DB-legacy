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
use PartDB\Exceptions\ElementNotExistingException;
use PartDB\Exceptions\InvalidElementValueException;
use PartDB\LogSystem\InstockChangedEntry;
use PartDB\PartProperty\PartProperty;
use PartDB\Permissions\CPartAttributePermission;
use PartDB\Permissions\PartAttributePermission;
use PartDB\Permissions\PartPermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Tools\BBCodeParsingLevel;

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
class Part extends Base\AttachementsContainingDBElement implements Interfaces\IAPIModel, Interfaces\ILabel
{
    const INSTOCK_UNKNOWN   = -2;

    const TABLE_NAME = "parts";

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
    public function __construct(Database &$database, User &$current_user, Log &$log, int $id, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $db_data);
    }

    /**
     * Reset all attributes of this object (set them to NULL).
     *
     * Reasons why we need this method:
     *      * If we change an attribute of the element, some calculated attributes are no longer valid.
     *          So this method is called with $all=false to set all calculated attributes to NULL ("clear the cache")
     *      * If this element is deleted by delete(), we need to clear ALL data from this element,
     *          including non-calculated attributes. So this method will be called with $all=true.
     *
     * @warning     You should implement this function in your subclass (including a call to this function here!),
     *              if your subclass has its own attributes (calculated or non-calculated)!
     *
     * @param boolean $all * if true, ALL attributes will be deleted (use it only for "destroying" the object).
     * * if false, only the calculated data will be deleted.
     *                              This is needed if you change an attribute of the object.
     * @throws Exception
     */
    public function resetAttributes(bool $all = false)
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
    public function delete(bool $delete_files_from_hdd = false, bool $delete_device_parts = false)
    {
        $this->current_user->tryDo(PermissionManager::PARTS, PartPermission::DELETE);

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
                    throw new Exception(sprintf(_('Das Bauteil "%s" wird noch in %d'.
                        ' Baugruppen verwendet und kann daher nicht gelöscht werden!'), $this->getName(), count($devices)));
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

            throw new Exception(sprintf(_("Das Bauteil \"%s\" konnte nicht gelöscht werden!\n"), $this->getName()) . _("Grund: ").$e->getMessage());
        }
    }

    /**
     * Gets the content for a 1D/2D barcode for this part
     * @param string $barcode_type the type of the barcode ("EAN8" or "QR")
     * @return string
     * @throws Exception An Exception is thrown if you selected a unknown barcode type.
     */
    public function getBarcodeContent(string $barcode_type = "EAN8") : string
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
                throw new Exception(_("Unbekannter Labeltyp: ").$barcode_type);
        }
    }

    /**
     * Calculates the price for an Instock change.
     * @param $old_instock int The old instock value.
     * @param $new_instock int The new instock value after withdrawl.
     * @return float The Price for the instock change. Negative values means withdrewal.
     * @throws Exception
     */
    public function calculateInstockChangePrice(int $old_instock, int $new_instock) : float
    {

        if ($old_instock == Part::INSTOCK_UNKNOWN || $new_instock == Part::INSTOCK_UNKNOWN) {
            return 0;
        }

        if ($old_instock < 0 || $new_instock < 0) {
            throw new \RuntimeException(_('$old_instock und $new_instock müssen positiv sein!'));
        }

        $difference = $new_instock - $old_instock;

        return $difference * $this->getAveragePrice();
    }

    /**
     * Replaces Placeholder strings like %id% or %name% with their corresponding Part properties.
     * Note: If the given Part does not have a property, it will be replaced with "".
     *
     * %id%         : Part id
     * %name%       : Name of the part
     * %desc%       : Description of the part
     * %comment%    : Comment to the part
     * %mininstock% : The minium in stock value
     * %instock%    : The current in stock value
     * %avgprice%   : The average price of this part
     * %cat%        : The name of the category the parts belongs to
     * %cat_full%   : The full path of the parts category
     *
     * @param string $string The string on which contains the placeholders
     * @return string The string with the infos.
     * @throws Exception When an error occured when getting the infos.
     */
    public function replacePlaceholderWithInfos(string $string) : string
    {
        //General infos
        $string = str_replace("%ID%", $this->getID(), $string);                        //part id
        $string = str_replace("%NAME%", $this->getName(), $string);                    //Name of the part
        $string = str_replace("%DESC%", $this->getDescription(), $string);             //description of the part
        $string = str_replace("%COMMENT%", $this->getComment(), $string);              //comment of the part
        $string = str_replace("%MININSTOCK%", $this->getMinInstock(), $string);        //minimum in stock
        $string = str_replace("%INSTOCK%", $this->getInstock(), $string);              //current in stock
        $string = str_replace("%AVGPRICE%", $this->getAveragePrice(), $string);       //average price

        //Category infos
        $string = str_replace("%CAT%", is_object($this->getCategory()) ? $this->getCategory()->getName() : "", $string);
        $string = str_replace("%CAT_FULL%", is_object($this->getCategory()) ? $this->getCategory()->getFullPath() : "", $string);

        //Footprint info
        $string = str_replace("%FOOT%", is_object($this->getFootprint()) ? $this->getFootprint()->getName() : "", $string);
        $string = str_replace("%FOOT_FULL%", is_object($this->getFootprint()) ? $this->getFootprint()->getFullPath() : "", $string);

        //Manufacturer info
        $string = str_replace("%MANUFACT%", is_object($this->getManufacturer()) ? $this->getManufacturer()->getName() : "", $string);
        $string = str_replace("%MANUFACT_FULL%", is_object($this->getManufacturer()) ? $this->getManufacturer()->getFullPath() : "", $string);

        //Order infos
        $all_orderdetails   = $this->getOrderdetails();
        $string = str_replace("%SUPPLIER%", (count($all_orderdetails) > 0) ? $all_orderdetails[0]->getSupplier()->getName() : "", $string);
        $string = str_replace("%SUPPLIER_FULL%", (count($all_orderdetails) > 0) ? $all_orderdetails[0]->getSupplier()->getFullPath() : "", $string);
        $string = str_replace("%ORDER_NR%", (count($all_orderdetails) > 0) ? $all_orderdetails[0]->getSupplierPartNr() : "", $string);

        //Store location
        /* @var Storelocation $storelocation */
        $storelocation      = $this->getStorelocation();
        $string = str_replace("%STORELOC%", is_object($storelocation) ? $storelocation->getName() : '', $string);
        $string = str_replace("%STORELOC_FULL%", is_object($storelocation) ? $storelocation->getFullPath() : '', $string);

        //Remove single '-' without other infos
        if (trim($string) == "-") {
            $string = "";
        }

        return $string;
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    public function getName() : string
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_NAME, PartAttributePermission::READ)) {
            return "???";
        }
        return parent::getName();
    }

    /**
     * Get the description
     *
     * @param boolean|int $bbcode_parse_level Should BBCode converted to HTML, before returning
     * @param int $short_output If this is bigger than 0, than the description will be shortened to this length.
     * @return string       the description
     */
    public function getDescription($bbcode_parse_level = BBCodeParsingLevel::PARSE, int  $short_output = 0) : string
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_DESCRIPTION, PartAttributePermission::READ)) {
            return "???";
        }

        $val = htmlspecialchars($this->db_data['description']);

        if ($short_output > 0 && strlen($val) > $short_output) {
            $val = substr($val, 0, $short_output);
            $val = $val . "...";
            $val = '<span class="text-muted">' . $val . '</span class="text-muted">';
        }

        if ($bbcode_parse_level === BBCodeParsingLevel::PARSE) {
            $bbcode = new BBCodeParser();
            $val = $bbcode->only("bold", "italic", "underline", "linethrough")->parse($val);
        } elseif ($bbcode_parse_level === BBCodeParsingLevel::STRIP) {
            $bbcode = new BBCodeParser();
            $val = $bbcode->stripBBCodeTags($val);
        }

        return $val;
    }

    /**
     *  Get the count of parts which are in stock
     * @param $with_unknown bool Set this, to true, if the unknown state should be returned as string. Otherwise -2 is returned.
     *
     * @return integer|string       count of parts which are in stock, "Unknown" if $with_unknown is set and instock is unknown.
     */
    public function getInstock(bool $with_unknown = false)
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_INSTOCK, PartAttributePermission::READ)) {
            return "-1";
        }

        if ($with_unknown && $this->isInstockUnknown()) {
            return _("[Unbekannt]");
        }

        return $this->db_data['instock'];
    }

    /**
     * Check if the value of the Instock is unknown.
     * @return bool True, if the value of the instock is unknown.
     */
    public function isInstockUnknown() : bool
    {
        return $this->getInstock() <= static::INSTOCK_UNKNOWN;
    }

    /**
     *  Get the count of parts which must be in stock at least
     *
     * @return integer       count of parts which must be in stock at least
     */
    public function getMinInstock() : int
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_MININSTOCK, PartAttributePermission::READ)) {
            return "-1";
        }

        return (int) $this->db_data['mininstock'];
    }

    /**
     *  Get the comment
     *
     * @param boolean|int $bbcode_parsing_level Should BBCode converted to HTML, before returning
     * @return string       the comment
     */
    public function getComment($bbcode_parsing_level = BBCodeParsingLevel::PARSE) : string
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_COMMENT, PartAttributePermission::READ)) {
            return "???";
        }

        $val = htmlspecialchars($this->db_data['comment']);
        if ($bbcode_parsing_level === BBCodeParsingLevel::PARSE) {
            $bbcode = new BBCodeParser();
            $bbcode->setParser('brLinebreak', "/\[br\]/s", "<br/>", "");
            $bbcode->setParser('namedlink', '/\[url\=(.*?)\](.*?)\[\/url\]/s', '<a href="$1" class="link-external" target="_blank">$2</a>', '$2');
            $bbcode->setParser('link', '/\[url\](.*?)\[\/url\]/s', '<a href="$1" class="link-external" target="_blank">$1</a>', '$1');
            $val = $bbcode->parse($val);
        } elseif ($bbcode_parsing_level === BBCodeParsingLevel::STRIP) {
            $bbcode = new BBCodeParser();
            $val = str_replace("\n", " ", $val);
            $val = $bbcode->stripBBCodeTags($val);
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
     * @li false if this part isn't obsolete
     * @throws Exception
     */
    public function getObsolete() : bool
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
    public function getVisible() : bool
    {
        return (bool) $this->db_data['visible'];
    }

    /**
     * Get if this part is a favorite.
     *
     * @return bool * true if this part is a favorite
     *     * false if this part is not a favorite.
     */
    public function getFavorite() : bool
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_NAME, PartAttributePermission::READ)) {
            return false;
        }

        return (bool) $this->db_data['favorite'];
    }

    /**
     *  Get the selected order orderdetails of this part
     *
     * @return Orderdetails         the selected order orderdetails
     * @return NULL                 if there is no order supplier selected
     * @throws Exception
     */
    public function getOrderOrderdetails()
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_ORDER, PartAttributePermission::READ)) {
            return null;
        }

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
    public function getOrderQuantity() : int
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_ORDER, PartAttributePermission::READ)) {
            return -1;
        }

        return (int) $this->db_data['order_quantity'];
    }

    /**
     *  Get the minimum quantity which should be ordered
     *
     * @param boolean $with_devices @li if true, all parts from devices which are marked as "to order" will be included in the calculation
     * @li if false, only max(mininstock - instock, 0) will be returned
     *
     * @return integer      the minimum order quantity
     * @throws Exception
     */
    public function getMinOrderQuantity(bool $with_devices = true) : int
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_ORDER, PartAttributePermission::READ)) {
            return -1;
        }

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
    public function getManualOrder() : bool
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS_ORDER, PartAttributePermission::READ)) {
            return false;
        }

        return (bool) $this->db_data['manual_order'];
    }

    /**
     * Check if the part is automatically marked for Ordering, because the instock value is smaller than the min instock value.
     * @return bool True, if the part should be ordered.
     */
    public function getAutoOrder() : bool
    {
        //Parts with negative instock never gets ordered.
        if ($this->getInstock() < 0) {
            return false;
        }

        return $this->getInstock() < $this->getMinInstock();
    }

    /**
     *  Get the link to the website of the article on the manufacturers website
     *
     * @param
     *
     * @return string           the link to the article
     * @throws Exception
     */
    public function getManufacturerProductUrl(bool $no_auto_url = false) : string
    {
        if ($no_auto_url || strlen($this->db_data['manufacturer_product_url']) > 0) {
            return $this->db_data['manufacturer_product_url'];
        } elseif (is_object($this->getManufacturer())) {
            return $this->getManufacturer()->getAutoProductUrl($this->db_data['name']);
        } else {
            return '';
        } // no url is available
    }

    /**
     * Returns the last time when the part was modified.
     * @param $formatted bool When true, the date gets formatted with the locale and timezone settings.
     *          When false, the raw value from the DB is returned.
     * @return string The time of the last edit.
     */
    public function getLastModified(bool $formatted = true) : string
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS, PartPermission::READ)) {
            return "???";
        }
        return parent::getLastModified($formatted);
    }

    /**
     * Returns the date/time when the part was created.
     * @param $formatted bool When true, the date gets formatted with the locale and timezone settings.
     *       When false, the raw value from the DB is returned.
     * @return string The creation time of the part.
     */
    public function getDatetimeAdded(bool $formatted = true) : string
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS, PartPermission::READ)) {
            return "???";
        }
        return parent::getDatetimeAdded(true);
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
    public function getCategory() : Category
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS, PartPermission::READ)) {
            return new Category(
                $this->database,
                $this->current_user,
                $this->log,
                0
            );
        }

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
        if (!$this->current_user->canDo(PermissionManager::PARTS_FOOTPRINT, PartPermission::READ)) {
            return null;
        }

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
        if (!$this->current_user->canDo(PermissionManager::PARTS_STORELOCATION, PartPermission::READ)) {
            return null;
        }

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
        if (!$this->current_user->canDo(PermissionManager::PARTS_MANUFACTURER, PartPermission::READ)) {
            return null;
        }

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
        //Check for permission.
        if (!$this->current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::READ)) {
            return null;
        }

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
    public function getOrderdetails(bool $hide_obsolete = false) : array
    {
        //Check for permission.
        if (!$this->current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, CPartAttributePermission::READ)) {
            return array();
        }

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
    public function getDevices() : array
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
                $this->devices[] = new Device($this->database, $this->current_user, $this->log, $row['id'], $row);
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
    public function getSuppliers(bool $object_array = true, $delimeter = null, bool $full_paths = false, bool $hide_obsolete = false)
    {
        //Check for permission.
        if (!$this->current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, CPartAttributePermission::READ)) {
            return array();
        }

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
    public function getSupplierPartNrs($delimeter = null, bool $hide_obsolete = false)
    {
        //Check for permission.
        if (!$this->current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, CPartAttributePermission::READ)) {
            return array();
        }
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
    public function getPrices(bool $float_array = false, $delimeter = null, int $quantity = 1, $multiplier = null, bool $hide_obsolete = false)
    {
        //Check for permission.
        if (!$this->current_user->canDo(PermissionManager::PARTS_PRICES, CPartAttributePermission::READ)) {
            return array();
        }

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
    public function getAveragePrice(bool $as_money_string = false, int $quantity = 1, $multiplier = null)
    {
        //Check for permission.
        if (!$this->current_user->canDo(PermissionManager::PARTS_PRICES, CPartAttributePermission::READ)) {
            return null;
        }

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
    public function getMasterPictureFilename(bool $use_footprint_filename = false)
    {
        //Check for permission.
        if (!$this->current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::READ)) {
            return null;
        }

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
     * @param bool $use_name Use the name field for parsing
     * @param bool $force_output Properties are parsed even if properties are disabled.
     * @return array A array of PartProperty objects.
     * @return array If Properties are disabled or nothing was detected, then an empty array is returned.
     * @throws Exception
     */
    public function getProperties(bool $use_description = true, bool $use_comment = true, bool $use_name = true, bool $force_output = false) : array
    {
        global $config;

        if ($config['properties']['active'] || $force_output) {
            if ($this->getCategory()->getDisableProperties(true)) {
                return array();
            }

            $name = array();
            $desc = array();
            $comm = array();

            if ($use_name === true) {
                $name = $this->getCategory()->getPartnameRegexObj()->getProperties($this->getName());
            }
            if ($use_description === true) {
                $desc = PartProperty::parseDescription($this->getDescription());
            }
            if ($use_comment === true) {
                $comm = PartProperty::parseDescription($this->getComment(false));
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
    public function getPropertiesLoop(bool $use_description = true, bool $use_comment = true, bool $use_name = true) : array
    {
        $arr = array();
        foreach ($this->getProperties($use_description, $use_comment, $use_name) as $property) {
            /* @var PartProperty $property */
            $arr[] = $property->getArray(true);
        }
        return $arr;
    }

    public function hasValidName() : bool
    {
        return Part::isValidName($this->getName(), $this->getCategory());
    }

    public function getCreationUser()
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_USERS)) {
            return null;
        }
        return parent::getCreationUser();
    }

    public function getLastModifiedUser()
    {
        if (!$this->current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_USERS)) {
            return null;
        }
        return parent::getLastModifiedUser();
    }

    public function getAttachementTypes() : array
    {
        //Check for permission.
        if (!$this->current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::READ)) {
            return array();
        }
        return parent::getAttachementTypes();
    }

    public function getAttachements($type_id = null, bool $only_table_attachements = false) : array
    {
        //Check for permission.
        if (!$this->current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::READ)) {
            return array();
        }
        return parent::getAttachements($type_id, $only_table_attachements);
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
    public function setDescription(string $new_description)
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
    public function setInstock(int $new_instock, $comment = null)
    {
        $old_instock = (int) $this->getInstock();
        $this->setAttributes(array('instock' => $new_instock));
        InstockChangedEntry::add(
            $this->database,
            $this->current_user,
            $this->log,
            $this,
            $old_instock,
            $new_instock,
            $comment
        );
    }

    /**
     * Withdrawal the given number of parts.
     * @param $count int The number of parts which should be withdrawan.
     * @param $comment string A comment that should be associated with the withdrawal.
     * @throws Exception if there was an error
     */
    public function withdrawalParts(int $count, $comment = null)
    {
        if ($count <= 0) {
            throw new Exception(_("Zahl der entnommenen Bauteile muss größer 0 sein!"));
        }
        if ($count > $this->getInstock()) {
            throw new Exception(_("Es können nicht mehr Bauteile entnommen werden, als vorhanden sind!"));
        }

        $old_instock = (int) $this->getInstock();
        $new_instock = $old_instock - $count;

        InstockChangedEntry::add(
            $this->database,
            $this->current_user,
            $this->log,
            $this,
            $old_instock,
            $new_instock,
            $comment
        );

        $this->setAttributes(array('instock' => $new_instock));
    }

    /**
     * Add the given number of parts.
     * @param $count int The number of parts which should be withdrawan.
     * @param $comment string A comment that should be associated with the withdrawal.
     * @throws Exception if there was an error
     */
    public function addParts(int $count, string $comment = null)
    {
        if ($count <= 0) {
            throw new Exception(_("Zahl der entnommenen Bauteile muss größer 0 sein!"));
        }

        $old_instock = (int) $this->getInstock();
        $new_instock = $old_instock + $count;

        InstockChangedEntry::add(
            $this->database,
            $this->current_user,
            $this->log,
            $this,
            $old_instock,
            $new_instock,
            $comment
        );

        $this->setAttributes(array('instock' => $new_instock));
    }

    /**
     *  Set the count of parts which should be in stock at least
     *
     * @param integer $new_mininstock       the new count of parts which should be in stock at least
     *
     * @throws Exception if the new mininstock is not valid
     * @throws Exception if there was an error
     */
    public function setMinInstock(int $new_mininstock)
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
    public function setComment(string $new_comment)
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
    public function setManualOrder(bool $new_manual_order, int $new_order_quantity = 1, $new_order_orderdetails_id = null)
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
    public function setOrderQuantity(int $new_order_quantity)
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
    public function setCategoryID(int $new_category_id)
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
     * Set the favorite status for this part.
     * @param $new_favorite_status bool The new favorite status, that should be applied on this part.
     *      Set this to true, when the part should be a favorite.
     */
    public function setFavorite(bool $new_favorite_status)
    {
        $this->setAttributes(array('favorite' => $new_favorite_status));
    }

    /**
     * Sets the URL to the manufacturer site about this Part. Set to "" if this part should use the automatically URL based on its manufacturer.
     * @param string $new_url The new url
     * @throws Exception when an error happens.
     */
    public function setManufacturerProductURL(string $new_url)
    {
        $this->setAttributes(array('manufacturer_product_url' => $new_url));
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
    public function setMasterPictureAttachementID(int $new_master_picture_attachement_id)
    {
        $this->setAttributes(array('id_master_picture_attachement' => $new_master_picture_attachement_id));
    }

    public function setAttributes(array $new_values, $edit_message = null)
    {
        //Override this function, so we can check if user has the needed permissions.
        $arr = array();
        if ($this->current_user->canDo(PermissionManager::PARTS, PartPermission::MOVE)) {
            //Make an exception for $parent_id
            if (isset($new_values['id_category'])) {
                $arr['id_category'] = $new_values['id_category'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS, PartPermission::EDIT)) {
            if (isset($new_values['visible'])) {
                $arr['visible'] = $new_values['visible'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_NAME, PartAttributePermission::EDIT)) {
            if (isset($new_values['name'])) {
                $arr['name'] = $new_values['name'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_DESCRIPTION, PartAttributePermission::EDIT)) {
            if (isset($new_values['description'])) {
                $arr['description'] = $new_values['description'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_COMMENT, PartAttributePermission::EDIT)) {
            if (isset($new_values['comment'])) {
                $arr['comment'] = $new_values['comment'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_MININSTOCK, PartAttributePermission::EDIT)) {
            if (isset($new_values['mininstock'])) {
                $arr['mininstock'] = $new_values['mininstock'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_INSTOCK, PartAttributePermission::EDIT)) {
            if (isset($new_values['instock'])) {
                $arr['instock'] = $new_values['instock'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_FOOTPRINT, PartAttributePermission::EDIT)) {
            if (isset($new_values['id_footprint'])) {
                $arr['id_footprint'] = $new_values['id_footprint'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_STORELOCATION, PartAttributePermission::EDIT)) {
            if (isset($new_values['id_storelocation'])) {
                $arr['id_storelocation'] = $new_values['id_storelocation'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_MANUFACTURER, PartAttributePermission::EDIT)) {
            if (isset($new_values['id_manufacturer'])) {
                $arr['id_manufacturer'] = $new_values['id_manufacturer'];
            }
            if (isset($new_values["manufacturer_product_url"])) {
                $arr['manufacturer_product_url'] = $new_values['manufacturer_product_url'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::EDIT)
            || $this->current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::CREATE)
            || $this->current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::DELETE)) {
            if (array_key_exists('id_master_picture_attachement', $new_values)) {
                $arr['id_master_picture_attachement'] = $new_values['id_master_picture_attachement'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::PARTS_ORDER, PartAttributePermission::EDIT)) {
            if (isset($new_values['order_orderdetails_id'])) {
                $arr['order_orderdetails_id'] = $new_values['order_orderdetails_id'];
            }
            if (isset($new_values['order_quantity'])) {
                $arr['order_quantity'] = $new_values['order_quantity'];
            }
            if (isset($new_values['manual_order'])) {
                $arr['manual_order'] = $new_values['manual_order'];
            }
        }

        if (isset($new_values['favorite']) && $this->current_user->canDo(PermissionManager::PARTS, PartPermission::CHANGE_FAVORITE)) {
            $arr['favorite'] = $new_values['favorite'];
        }

        /* Exception, gives problem, with editing the name of the Part, via edit_part_info.php
        //Throw Exception, if nothing can be done!
        if (empty($arr)) {
            throw new UserNotAllowedException(_("Der aktuelle Benutzer darf die gewünschte Operation nicht durchführen!"));
        }*/

        //Only apply attributes, if $arr contains values.
        if (!empty($arr)) {
            parent::setAttributes($arr, $edit_message);
        }
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
    public function buildTemplateTableRowArray(string $table_type, int $row_index, array $additional_values = array())
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
        $table_row['favorite']      = $this->getFavorite();
        $table_row["show_full_paths"] = $config['table']['full_paths'];
        $table_row["instock_warning_full_row"] = $config['table']['instock_warning_full_row_color'] && ($this->getAutoOrder());

        foreach (explode(';', $config['table'][$table_type]['columns']) as $caption) {
            $row_field = array();
            $row_field['row_index']     = $row_index;
            $row_field['caption']       = $caption;
            $row_field['id']            = $this->getID();
            $row_field['name']          = $this->getName();

            switch ($caption) {
                case 'hover_picture':
                    $picture_filename = str_replace(BASE, BASE_RELATIVE, $this->getMasterPictureFilename(true));
                    if ($this->getMasterPictureAttachement() != null && !$this->getMasterPictureAttachement()->isFileExisting()) { //When filename is invalid then dont show picture.
                        $picture_filename = "";
                    }
                    $row_field['picture_name']  = strlen($picture_filename) ? basename($picture_filename) : '';
                    $row_field['small_picture'] = strlen($picture_filename) ? $picture_filename : '';
                    $row_field['hover_picture'] = strlen($picture_filename) ? $picture_filename : '';
                    break;

                case 'name':
                case 'description':
                case 'comment':
                case 'name_description':
                    $row_field['obsolete']          = $this->getObsolete();
                    $row_field['comment']           = mb_substr($this->getComment(BBCodeParsingLevel::STRIP), 0, 250);
                    $row_field['description']       = $this->getDescription(true, $max_length);
                    break;

                case 'instock':
                case 'mininstock':
                case 'instock_mininstock':
                case 'instock_edit_buttons':
                    $row_field['instock']               = $this->getInstock(true);
                    $row_field['mininstock']            = $this->getMinInstock();
                    $row_field['not_enough_instock']   = ($this->getAutoOrder());
                    break;

                case 'category':
                    $category = $this->getCategory();
                    $row_field['category_name'] = $category->getName();
                    $row_field['category_path'] = $category->getFullPath();
                    $row_field['category_id'] = $category->getID();
                    $row_field['category_loop'] = $category->buildBreadcrumbLoop("show_category_parts.php", "cid", false, null, true);
                    break;

                case 'footprint':
                    $footprint = $this->getFootprint();
                    if (is_object($footprint)) {
                        $row_field['footprint_name'] = $footprint->getName();
                        $row_field['footprint_path'] = $footprint->getFullPath();
                        $row_field['footprint_id'] = $footprint->getID();
                        $row_field['footprint_loop'] = $footprint->buildBreadcrumbLoop("show_footprint_parts.php", "fid", false, null, true);
                    }
                    break;

                case 'manufacturer':
                    $manufacturer = $this->getManufacturer();
                    if (is_object($manufacturer)) {
                        $row_field['manufacturer_name'] = $manufacturer->getName();
                        $row_field['manufacturer_path'] = $manufacturer->getFullPath();
                        $row_field['manufacturer_id'] = $manufacturer->getID();
                        $row_field['manufacturer_loop'] = $manufacturer->buildBreadcrumbLoop("show_manufacturer_parts.php", "mid", false, null, true);
                    }
                    break;

                case 'storelocation':
                    $storelocation = $this->getStorelocation();
                    if (is_object($storelocation)) {
                        $row_field['storelocation_name'] = $storelocation->getName();
                        $row_field['storelocation_path'] = $storelocation->getFullPath();
                        $row_field['storelocation_id'] = $storelocation->getID();
                        $row_field['storelocation_loop'] = $storelocation->buildBreadcrumbLoop("show_location_parts.php", "lid", false, null, true);
                    }
                    break;

                case 'suppliers':
                    $suppliers_loop = array();
                    $suppliers = $this->getSuppliers(true, null, false, true);
                    foreach ($suppliers as $supplier) { // suppliers from obsolete orderdetails will not be shown
                        /** @var $supplier Supplier */
                        $suppliers_loop[] = array(  'row_index'         => $row_index,
                            'supplier_name'     => $supplier->getName(),
                            'supplier_id' => $supplier->getID());
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
                            'supplier_name'     => _('Noch nicht bestellen'),
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
                        $row_field['enable_remove'] = (!$this->getAutoOrder()) && ($this->getManualOrder());
                    }
                    break;

                case 'button_decrement':
                    $row_field['decrement_disabled'] = ($this->getInstock() < 1)
                        || !$this->current_user->canDo(PermissionManager::PARTS_INSTOCK, PartAttributePermission::EDIT);
                    break;

                case 'attachements':
                    $attachements = array();
                    foreach ($this->getAttachements(null, true) as $attachement) {
                        $attachements[] = array(    'name'      => $attachement->getName(),
                            'filename'  => str_replace(BASE, BASE_RELATIVE, $attachement->getFilename()),
                            'type'      => $attachement->getType()->getFullPath(),
                            'icon'      => extToFAIcon($attachement->getFilename()));
                    }
                    $row_field['attachements'] = $attachements;
                    break;

                case 'id':
                case 'button_increment':
                    $row_field['increment_disabled'] = ($this->getInstock() < 0) || !$this->current_user->canDo(
                            PermissionManager::PARTS_INSTOCK,
                            PartAttributePermission::EDIT
                        );
                    break;
                case 'button_edit':
                    $row_field['edit_disabled'] = !$this->current_user->canDo(
                        PermissionManager::PARTS,
                        PartPermission::EDIT
                    );
                    break;
                case 'quantity_edit': // for DevicePart Objects
                case 'mountnames_edit': // for DevicePart Objects
                    // nothing to do, only to avoid the Exception in the default-case
                    break;
                case "last_modified":
                    $row_field['last_modified'] = $this->getLastModified(true);
                    break;
                case "created":
                    $row_field['created'] = $this->getDatetimeAdded(true);
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
            $table_row['use_attachements_names'] = $config['attachements']['show_name'];
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
    public static function buildTemplateTableArray(array $parts, string $table_type)
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
        $table_loop[] = array('print_header' => true,
            'columns' => $columns); // print the table header

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
     * @throws Exception
     */
    public static function checkForExistingPart(Database &$database, User &$current_user, Log &$log, string $proposed_name, int $proposed_storelocation_id, int $proposed_category_id)
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
     * Check if all values are valid for creating a new element / editing an existing element
     *
     * This function is called by creating a new DBElement (DBElement::add()),
     * respectively a subclass of DBElement. Then the attribute $is_new is true!
     *
     * And if you set data fields with DBElement::set_attributes() (or a subclass of DBElement),
     * the new data (one or more attributes) will be checked with this function
     * (with $is_new = false and with the object as $element).
     *
     * Because we pass the values array by reference, you're able to adjust values in the array.
     * For example, you can trim names of elements. So you don't have to throw an Exception if
     * values are not 100% perfect, you simply can "repair" these uncritical attributes.
     *
     * @warning     You have to implement this function in your subclass to check all data!
     *              You should always let to check the parent class all values, and after that,
     *              you can check the values which are associated with your subclass of DBElement.
     *
     * @param Database      &$database          reference to the database object
     * @param User          &$current_user      reference to the current user which is logged in
     * @param Log           &$log               reference to the Log-object
     * @param array         &$values            @li one-dimensional array of all keys and values (old and new!)
     *                                          @li example: @code
     *                                              array(['name'] => 'abcd', ['parent_id'] => 123, ...) @endcode
     * @param boolean       $is_new             @li if true, this means we will create a new element.
     *                                          @li if false, this means we will set attributes of an existing element
     * @param static|NULL   &$element           if $is_new is 'false', we have to supply the element,
     *                                          which will be edited, here.
     *
     * @throws Exception if the values are not valid / the combination of values is not valid
     * @throws Exception if there was an error
     */
    public static function checkValuesValidity(Database &$database, User &$current_user, Log &$log, array &$values, bool $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        // set the datetype of the boolean attributes
        settype($values['visible'], 'boolean');
        settype($values['manual_order'], 'boolean');

        // check "instock"
        if ((! is_int($values['instock'])) && (! is_numeric($values['instock']))) {
            throw new InvalidElementValueException(_('Der neue Lagerbestand ist ungültig!'));
        } elseif ($values['instock'] < 0 && $values['instock'] != static::INSTOCK_UNKNOWN) {
            throw new InvalidElementValueException(sprintf(_('Der neue Lagerbestand von "%s" wäre negativ und kann deshalb nicht gespeichert werden!'), $values['name']));
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
            throw new InvalidElementValueException(_('Die gewählte Einkaufsinformation existiert nicht!'));
        }

        // check "order_quantity"
        if (((! is_int($values['order_quantity'])) && (! ctype_digit($values['order_quantity'])))
            || ($values['order_quantity'] < 1)) {
            debug('error', 'order_quantity = "'.$values['order_quantity'].'"', __FILE__, __LINE__, __METHOD__);
            throw new InvalidElementValueException(_('Die Bestellmenge ist ungültig!'));
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
            throw new InvalidElementValueException(_('Der neue Mindestlagerbestand ist ungültig!'));
        }


        // id_category == NULL means "no category", and this is not allowed!
        if ($values['id_category'] == null || $values["id_category"] == 0) {
            throw new InvalidElementValueException(_('Ein Bauteil muss eine Kategorie haben!'));
        }

        // check "id_category"
        try {
            $category = new Category($database, $current_user, $log, $values['id_category']);
        } catch (Exception $e) {
            throw new InvalidElementValueException(_('Die gewählte Kategorie existiert nicht!'));
        }

        // check "id_footprint"
        try {
            $footprint = new Footprint($database, $current_user, $log, $values['id_footprint'] ?? 0);
            if (($values['id_footprint'] == 0) && ($values['id_footprint'] !== null)) {
                $values['id_footprint'] = null;
            }
        } catch (Exception $e) {
            throw new InvalidElementValueException(_('Der gewählte Footprint existiert nicht!'));
        }

        // check "id_storelocation"
        try {
            $storelocation = new Storelocation($database, $current_user, $log, $values['id_storelocation'] ?? 0);
            if (($values['id_storelocation'] == 0) && ($values['id_storelocation'] !== null)) {
                $values['id_storelocation'] = null;
            }
        } catch (ElementNotExistingException $e) {
            throw new InvalidElementValueException(_('Der gewählte Lagerort existiert nicht!'));
        }

        // check "id_manufacturer"
        try {
            $manufacturer = new Manufacturer($database, $current_user, $log, $values['id_manufacturer'] ?? 0);
            if (($values['id_manufacturer'] == 0) && ($values['id_manufacturer'] !== null)) {
                $values['id_manufacturer'] = null;
            }
        } catch (ElementNotExistingException $e) {
            throw new InvalidElementValueException(_('Der gewählte Hersteller existiert nicht!'));
        }

        // check "id_master_picture_attachement"
        try {
            if ($values['id_master_picture_attachement']) {
                $master_picture_attachement = new Attachement($database, $current_user, $log, $values['id_master_picture_attachement']);
            } else {
                $values['id_master_picture_attachement'] = null;
            } // this will replace the integer "0" with NULL
        } catch (Exception $e) {
            throw new InvalidElementValueException(_('Die gewählte Datei existiert nicht!'));
        }
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
    public static function getSumCountInstock(Database &$database) : int
    {
        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $query_data = $database->query('SELECT sum(instock) as sum FROM parts WHERE instock > 0');

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
    public static function getSumPriceInstock(Database &$database, User &$current_user, Log &$log, bool $as_money_string = true)
    {
        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
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
            $taken_parts = $row['min_discount_quantity'] * (int)($instock / $row['min_discount_quantity']);
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
    public static function getOrderParts(Database &$database, User &$current_user, Log &$log, array $supplier_ids = array(), bool $with_devices = true) : array
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::ORDER_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $parts = array();

        $query =    'SELECT parts.* FROM parts '.
            'LEFT JOIN orderdetails ON orderdetails.id = parts.order_orderdetails_id '.
            'WHERE (parts.instock < parts.mininstock '.
            'AND parts.instock >= 0 '.
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
     * @param int       $limit              Limit the result count to the given number. Set to 0 to disable pagination.
     * @param int       $page               Selects the page of the results. Each page contains $limit number of elements.
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getNoPriceParts(Database &$database, User &$current_user, Log &$log, int $limit = 50, int $page = 1)
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::NO_PRICE_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $parts = array();

        $query =    'SELECT * from parts '.
            'WHERE id NOT IN (SELECT DISTINCT part_id FROM orderdetails '.
            'LEFT JOIN pricedetails ON orderdetails.id=pricedetails.orderdetails_id '.
            'WHERE pricedetails.id IS NOT NULL) '.
            'ORDER BY parts.name ASC';

        if ($limit > 0 && $page > 0) {
            $query .= " LIMIT " . (($page - 1) * $limit) . ", $limit";
        }

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $parts[] = new Part($database, $current_user, $log, $row['id'], $row);
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
     * @return int   all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getNoPricePartsCount(Database &$database, User &$current_user, Log &$log) : int
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::NO_PRICE_PARTS)) {
            return 0;
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $parts = array();

        $query =    'SELECT count(id) AS count from parts '.
            'WHERE id NOT IN (SELECT DISTINCT part_id FROM orderdetails '.
            'LEFT JOIN pricedetails ON orderdetails.id=pricedetails.orderdetails_id '.
            'WHERE pricedetails.id IS NOT NULL) '.
            'ORDER BY parts.name ASC';


        $query_data = $database->query($query);

        return $query_data[0]['count'];
    }

    /**
     *  Get all parts which are favorited.
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     *
     * @param int       $limit              Limit the result count to the given number. Set to 0 to disable pagination.
     * @param int       $page               Selects the page of the results. Each page contains $limit number of elements.
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getFavoriteParts(Database &$database, User &$current_user, Log &$log, int $limit = 50, int $page = 1) : array
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_FAVORITE_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $parts = array();

        $query =    'SELECT * from parts '.
            'WHERE favorite = 1';

        if ($limit > 0 && $page > 0) {
            $query .= " LIMIT " . (($page - 1) * $limit) . ", $limit";
        }

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $parts[] = new Part($database, $current_user, $log, $row['id'], $row);
        }

        return $parts;
    }

    /**
     *  Get the count of all parts which are favorited.
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     *
     * @return int    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getFavoritePartsCount(Database &$database, User &$current_user, Log &$log) : int
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_FAVORITE_PARTS)) {
            return 0;
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $parts = array();

        $query =    'SELECT count(id) AS count from parts '.
            'WHERE favorite = 1';


        $query_data = $database->query($query);

        return $query_data[0]['count'];
    }


    /**
     *  Get all parts which have an unknown instock value.
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param int       $limit              Limit the result count to the given number. Set to 0 to disable pagination.
     * @param int       $page               Selects the page of the results. Each page contains $limit number of elements.
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getInstockUnknownParts(Database &$database, User &$current_user, Log &$log, int $limit = 50, int $page = 1)
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::UNKNONW_INSTOCK_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $parts = array();

        $query =    'SELECT * from parts '.
            'WHERE instock = -2 '.
            'ORDER BY parts.name ASC';

        if ($limit > 0 && $page > 0) {
            $query .= " LIMIT " . (($page - 1) * $limit) . ", $limit";
        }

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $parts[] = new Part($database, $current_user, $log, $row['id'], $row);
        }

        return $parts;
    }

    /**
     *  Get all parts which have an unknown instock value.
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     *
     * @return int    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getInstockUnknownPartsCount(Database &$database, User &$current_user, Log &$log) : int
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::UNKNONW_INSTOCK_PARTS)) {
            return 0;
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $parts = array();

        $query =    'SELECT count(id) AS count from parts '.
            'WHERE instock = -2 '.
            'ORDER BY parts.name ASC';


        $query_data = $database->query($query);

        return $query_data[0]['count'];
    }

    /**
     *  Get all parts sorted by their last modified datetime.
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param bool      $newest_first       When this is set to true, newest modified parts are first (DESC sorting).
     *                                      Set to false for (ASC sorting)
     * @param int       $limit              Limit the result count to the given number. Set to 0 to disable pagination.
     * @param int       $page               Selects the page of the results. Each page contains $limit number of elements.
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getLastModifiedParts(Database &$database, User &$current_user, Log &$log, bool $newest_first = true, int $limit = 50, int $page = 1)
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_LAST_EDIT_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $sorting = $newest_first ? "DESC" : "ASC";

        $parts = array();

        /** @noinspection SyntaxError */
        $query =    'SELECT * from parts '.
            'ORDER BY parts.last_modified ' . $sorting;

        if ($limit > 0 && $page > 0) {
            $query .= " LIMIT " . (($page - 1) * $limit) . ", $limit";
        }

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $parts[] = new Part($database, $current_user, $log, $row['id'], $row);
        }

        return $parts;
    }

    /**
     *  Get all parts sorted by their last modified datetime.
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param bool      $newest_first       When this is set to true, newest modified parts are first (DESC sorting).
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getLastModifiedPartsCount(Database &$database, User &$current_user, Log &$log, bool $newest_first = true)
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::UNKNONW_INSTOCK_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $sorting = $newest_first ? "DESC" : "ASC";

        $parts = array();

        /** @noinspection SyntaxError */
        $query =    'SELECT count(id) AS count from parts '.
            'ORDER BY parts.last_modified ' . $sorting;


        $query_data = $database->query($query);

        return $query_data[0]['count'];
    }

    /**
     *  Get all parts sorted by the datetime, when they were created.
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param bool      $newest_first       When this is set to true, newest modified parts are first (DESC sorting).
     *                                      Set to false for (ASC sorting)
     * @param int       $limit              Limit the result count to the given number. Set to 0 to disable pagination.
     * @param int       $page               Selects the page of the results. Each page contains $limit number of elements.
     *
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getLastAddedParts(Database &$database, User &$current_user, Log &$log, bool $newest_first = true, int $limit = 50, int $page = 1)
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_LAST_EDIT_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $sorting = $newest_first ? "DESC" : "ASC";

        $parts = array();

        /** @noinspection SyntaxError */
        $query =    'SELECT * from parts '.
            'ORDER BY parts.datetime_added ' . $sorting;

        if ($limit > 0 && $page > 0) {
            $query .= " LIMIT " . (($page - 1) * $limit) . ", $limit";
        }

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $parts[] = new Part($database, $current_user, $log, $row['id'], $row);
        }

        return $parts;
    }

    /**
     *  Get all parts which have an unknown instock value.
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     *
     * @param int       $limit              Limit the result count to the given number. Set to 0 to disable pagination.
     * @param int       $page               Selects the page of the results. Each page contains $limit number of elements.
     *
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getLastAddedPartsCount(Database &$database, User &$current_user, Log &$log, bool $newest_first = true)
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::UNKNONW_INSTOCK_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $sorting = $newest_first ? "DESC" : "ASC";

        $parts = array();

        /** @noinspection SyntaxError */
        $query =    'SELECT count(id) AS count from parts '.
            'ORDER BY parts.datetime_added ' . $sorting;


        $query_data = $database->query($query);

        return $query_data[0]['count'];
    }

    /**
     *  Get all obsolete parts
     *
     * @param Database  &$database              reference to the database object
     * @param User      &$current_user          reference to the user which is logged in
     * @param Log       &$log                   reference to the Log-object
     * @param boolean   $no_orderdetails_parts  if true, parts without any orderdetails will be returned too
     *
     * @param int       $limit              Limit the result count to the given number. Set to 0 to disable pagination.
     * @param int       $page               Selects the page of the results. Each page contains $limit number of elements.
     *
     *
     * @return array    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getObsoleteParts(Database &$database, User &$current_user, Log &$log, bool $no_orderdetails_parts = false, int $limit = 50, int $page = 1)
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::OBSOLETE_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
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

        if ($limit > 0 && $page > 0) {
            $query .= " LIMIT " . (($page - 1) * $limit) . ", $limit";
        }

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $parts[] = new Part($database, $current_user, $log, $row['id'], $row);
        }

        return $parts;
    }

    /**
     *  Get count of all obsolete parts
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
    public static function getObsoletePartsCount(Database &$database, User &$current_user, Log &$log, bool $no_orderdetails_parts = false)
    {
        if (!$current_user->canDo(PermissionManager::PARTS, PartPermission::OBSOLETE_PARTS)) {
            return array();
        }

        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        $parts = array();

        if ($no_orderdetails_parts) {
            // show also parts which have no orderdetails
            $query =    'SELECT count(parts.id) AS count from parts '.
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
            $query =    'SELECT count(parts.id) AS count from parts '.
                'WHERE parts.id IN (SELECT part_id FROM `orderdetails` '.
                'WHERE part_id IN (SELECT part_id FROM `orderdetails` '.
                'WHERE obsolete = true GROUP BY part_id) '.
                'AND part_id NOT IN (SELECT part_id FROM `orderdetails` '.
                'WHERE obsolete = false GROUP BY part_id)) '.
                'ORDER BY parts.name ASC';
        }

        $query_data = $database->query($query);

        return $query_data[0]['count'];
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
     * @return Part[]    all found parts as a one-dimensional array of Part objects,
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
        Database &$database,
        User &$current_user,
        Log &$log,
        string $keyword,
        string $group_by = '',
        bool $part_name = true,
        bool $part_description = true,
        bool $part_comment = false,
        bool $footprint_name = false,
        bool $category_name = false,
        bool $storelocation_name = false,
        bool $supplier_name = false,
        bool $supplierpartnr = false,
        bool $manufacturer_name = false,
        bool $regex_search = false
    ) {
        global $config;

        $keyword = trim($keyword);

        $current_user->tryDo(PermissionManager::PARTS, PartPermission::SEARCH);

        //Let the user only search properties, for which he has access
        $part_name = $part_name
            && $current_user->canDo(PermissionManager::PARTS_NAME, PartAttributePermission::READ);
        $part_description = $part_description
            && $current_user->canDo(PermissionManager::PARTS_DESCRIPTION, PartAttributePermission::READ);
        $part_comment = $part_comment
            && $current_user->canDo(PermissionManager::PARTS_COMMENT, PartAttributePermission::READ);
        $footprint_name = $footprint_name
            && $current_user->canDo(PermissionManager::PARTS_FOOTPRINT, PartAttributePermission::READ);
        $category_name = $category_name
            && $current_user->canDo(PermissionManager::PARTS, PartPermission::READ);
        $storelocation_name = $storelocation_name
            && $current_user->canDo(PermissionManager::PARTS_STORELOCATION, PartAttributePermission::READ);
        $manufacturer_name = $manufacturer_name
            && $current_user->canDo(PermissionManager::PARTS_MANUFACTURER, PartAttributePermission::READ);
        $supplier_name = $supplier_name
            && $current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, PartAttributePermission::READ);
        $supplierpartnr = $supplierpartnr
            && $current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, PartAttributePermission::READ);

        //When searchstring begins and ends with a backslash, treat the input as regex query
        if (substr($keyword, 0, 1) === '\\' &&  substr($keyword, -1) === '\\'
            || substr($keyword, 0, 1) === '/' &&  substr($keyword, -1) === '/') {
            $regex_search = true;
            $keyword = mb_substr($keyword, 1, -1); //Remove the backslashes
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



        $query = 'SELECT parts.* FROM parts';

        $query .= ' LEFT JOIN categories ON parts.id_category=categories.id';
        if ($footprint_name) {
            $query .= ' LEFT JOIN footprints ON parts.id_footprint=footprints.id';
        }
        if ($storelocation_name) {
            $query .= ' LEFT JOIN storelocations ON parts.id_storelocation=storelocations.id';
        }
        if ($manufacturer_name) {
            $query .= ' LEFT JOIN manufacturers  ON parts.id_manufacturer=manufacturers.id';
        }
        if ($supplierpartnr || $supplier_name) {
            $query .= ' LEFT JOIN orderdetails ON parts.id=orderdetails.part_id';
            $query .= ' LEFT JOIN suppliers ON orderdetails.id_supplier=suppliers.id';
        }

        $query .= ' WHERE FALSE';

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
    public static function buildSearchGroupByLoop($selected_val = "") : array
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
     *  @param int       $limit              Limit the result count to the given number. Set to 0 to disable pagination.
     * @param int       $page               Selects the page of the results. Each page contains $limit number of elements.
     *
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
    public static function getAllParts(Database &$database, User &$current_user, Log &$log, string $group_by = '', int $limit = 50, int $page = 1)
    {
        $current_user->tryDo(PermissionManager::PARTS, PartPermission::ALL_PARTS);

        $query = 'SELECT * FROM parts';

        if ($limit > 0 && $page > 0) {
            $query .= " LIMIT " . (($page - 1) * $limit) . ", $limit";
        }

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
     * @return Base\AttachementsContainingDBElement|Part
     * @return Part     the new part
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
        string $name,
        int $category_id,
        string $description = '',
        int $instock = 0,
        int $mininstock = 0,
        $storelocation_id = null,
        $manufacturer_id = null,
        $footprint_id = null,
        string $comment = '',
        bool $visible = false,
        string $manufacturer_url = ""
    ) {
        $current_user->tryDo(PermissionManager::PARTS, PartPermission::CREATE);

        return parent::addByArray(
            $database,
            $current_user,
            $log,
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
                'order_quantity'                => 1,
                "manufacturer_product_url" => $manufacturer_url)
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
    public static function isValidName(string $partname, Category $category) : bool
    {
        return $category->checkPartname($partname);
    }

    /**
     * Returns the ID as an string, defined by the element class.
     * This should have a form like P000014, for a part with ID 14.
     * @return string The ID as a string;
     */
    public function getIDString(): string
    {
        return "P" . sprintf("%06d", $this->getID());
    }

    /**
     * Returns a Array representing the current object.
     * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
     * @return array A array representing the current object.
     * @throws Exception
     */
    public function getAPIArray(bool $verbose = false) : array
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
