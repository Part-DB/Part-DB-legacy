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
use PartDB\Base\NamedDBElement;
use PartDB\LogSystem\BaseEntry;
use PartDB\LogSystem\ElementCreatedEntry;
use PartDB\LogSystem\ElementDeletedEntry;
use PartDB\LogSystem\ElementEditedEntry;
use PartDB\LogSystem\InstockChangedEntry;
use PartDB\LogSystem\UnknownTypeEntry;
use PartDB\LogSystem\UserLoginEntry;
use PartDB\LogSystem\UserLogoutEntry;
use PartDB\LogSystem\UserNotAllowedEntry;

/**
 * @file Log.php
 * @brief class Log
 *
 * @class Log
 * @brief Class Log
 *
 * This class manages all log types.
 * With one instance of this class, you have access to all supported log types.
 *
 * @author kami89
 *
 * @todo There are no log types implemented yet.
 */
class Log
{
    //Dont change these definitions...
    const TYPE_USERLOGIN = 1;
    const TYPE_USERLOGOUT = 2;
    const TYPE_USERNOTALLOWED = 3;
    const TYPE_EXCEPTION = 4;
    const TYPE_ELEMENTDELETED = 5;
    const TYPE_ELEMENTCREATED = 6;
    const TYPE_ELEMENTEDITED = 7;
    const TYPE_CONFIGCHANGED = 8;
    const TYPE_INSTOCKCHANGE = 9;
    const TYPE_DATABASEUPDATE = 10;

    const TARGET_TYPE_NONE = 0;
    const TARGET_TYPE_USER = 1;
    const TARGET_TYPE_ATTACHEMENT = 2;
    const TARGET_TYPE_ATTACHEMENTTYPE = 3;
    const TARGET_TYPE_CATEGORY = 4;
    const TARGET_TYPE_DEVICE = 5;
    const TARGET_TYPE_DEVICEPART = 6;
    const TARGET_TYPE_FOOTPRINT = 7;
    const TARGET_TYPE_GROUP = 8;
    const TARGET_TYPE_MANUFACTURER = 9;
    const TARGET_TYPE_PART = 10;
    const TARGET_TYPE_STORELOCATION = 11;
    const TARGET_TYPE_SUPPLIER = 12;

    const LEVEL_EMERGENCY = 0;
    const LEVEL_ALERT = 1;
    const LEVEL_CRITICAL = 2;
    const LEVEL_ERROR = 3;
    const LEVEL_WARNING = 4;
    const LEVEL_NOTICE = 5;
    const LEVEL_INFO = 6;
    const LEVEL_DEBUG = 7;

    /********************************************************************************
     *
     *   Attributes
     *
     *********************************************************************************/

    /** @var Database the Database object for the database access of the logs */
    private $database = null;
    protected $current_user = null;

    /********************************************************************************
     *
     *   Constructor / Destructor
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @param Database  &$database      reference to the database
     *
     * @throws Exception if there was an error
     */
    public function __construct(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt'));
        }

        $this->database = $database;
        $this->current_user = User::getLoggedInUser($this->database, $this);
    }

    public function userLogsIn($user, $ip_address = "")
    {
        try {
            UserLoginEntry::add($this->database, User::getLoggedInUser($this->database, $this), $this, $user, $ip_address);
        } catch (Exception $e) {

        }
    }

    public function userLogsOut($user, $ip_address = "")
    {
        try {
            UserLogoutEntry::add($this->database, User::getLoggedInUser($this->database, $this), $this, $user, $ip_address);
        } catch (Exception $e) {

        }
    }

    public function userNotAllowed($permission_string)
    {
        try {
            UserNotAllowedEntry::add($this->database, User::getLoggedInUser($this->database, $this), $this, $permission_string);
        } catch (Exception $e) {

        }
    }

    public function elementDeleted(&$element)
    {
        try {
            ElementDeletedEntry::add($this->database, User::getLoggedInUser($this->database, $this), $this, $element);
        } catch (Exception $e) {

        }
    }

    public function elementCreated(&$element)
    {
        try {
            ElementCreatedEntry::add($this->database, User::getLoggedInUser($this->database, $this), $this, $element);
        } catch (Exception $e) {

        }
    }

    public function elementEdited(&$element, $data_array)
    {
        try {
            ElementEditedEntry::add($this->database, User::getLoggedInUser($this->database, $this), $this, $element, $data_array);
        } catch (Exception $e) {

        }
    }


    /**
     * Returns the user that last modified the given element.
     * @param $database Database The Database element that should be used for query.
     * @param $current_user User The User that should be used for query
     * @param $log Log The Log that should be used for query.
     * @param $element NamedDBElement The element for which the user should be looked up.
     * @return null|User Return the User if an entry was found in the log. Returns null otherwise.
     * @throws Exception
     */
    public static function getLastModifiedUserForElement(&$database, &$current_user, &$log, &$element)
    {
        $data = array();

        $target_id = $element->getID();
        $target_type = static::elementToTargetTypeID($element);

        $query = "SELECT id_user FROM `log` WHERE (type = 6 OR type = 7)";  // Choose element created or element edited entry types.
        $query .= " AND target_id = ?";
        $data[] = $target_id;
        $query .= " AND target_type = ?";
        $data[] = $target_type;
        $query .= " ORDER BY log.datetime DESC";

        $results = $database->query($query, $data);
        if (count($results) > 0) {
            return new User($database, $current_user, $log, $results[0]['id_user']);
        } else {
            return null;
        }
    }

    /**
     * Returns the user that created the given element.
     * @param $database Database The Database element that should be used for query.
     * @param $current_user User The User that should be used for query
     * @param $log Log The Log that should be used for query.
     * @param $element NamedDBElement The element for which the user should be looked up.
     * @return null|User Return the User if an entry was found in the log. Returns null otherwise.
     * @throws Exception
     */
    public static function getCreationUserForElement(&$database, &$current_user, &$log, &$element)
    {
        $data = array();

        $target_id = $element->getID();
        $target_type = static::elementToTargetTypeID($element);

        $query = "SELECT id_user FROM `log` WHERE (type = 6)";  // Choose element created or element edited entry types.
        $query .= " AND target_id = ?";
        $data[] = $target_id;
        $query .= " AND target_type = ?";
        $data[] = $target_type;
        $query .= " ORDER BY log.datetime DESC";

        $results = $database->query($query, $data);
        if (count($results) > 0) {
            return new User($database, $current_user, $log, $results[0]['id_user']);
        } else {
            return null;
        }
    }

    public static function getHistoryForPart(&$database, &$current_user, &$log, &$part)
    {
        if(!$part instanceof Part) {
            throw new \RuntimeException(_("getInstockHistoryForPart() funktioniert nur für Bauteile!"));
        }

        $part_id = $part->getID();

        $query = "SELECT * FROM `log` WHERE";
        $query .= " target_id = ?";
        $data[] = $part_id; //Only parts with the given ID
        $query .= " AND target_type = ?";
        $data[] = Log::TARGET_TYPE_PART;    //Only parts as a target
        $query .= " AND (type = 5"; //ElementDeleted
        $query .= " OR type = 6"; //ElementCreated
        $query .= " OR type = 7";  //ElementEdited
        $query .= " OR type = 9)";  //InstockChanged

        $query .= " ORDER BY log.datetime ASC";

        $results = $database->query($query, $data);

        $entries = $log->queryDataToEntryObjects($results);

        $return_data = array();
        foreach($entries as $entry) {
            $tmp = array();
            //Basic info
            $tmp['timestamp'] = $entry->getTimestamp(false);
            $tmp['timestamp_formatted'] = $entry->getTimestamp(true);
            $tmp['user_name'] = $entry->getUser()->getFullName(true);
            $tmp['user_id'] = $entry->getUser()->getID();
            $tmp['type_id'] = $entry->getTypeID();
            $tmp['type_text'] = static::typeIDToString($entry->getTypeID());

            if($entry instanceof ElementCreatedEntry) {
                /** @var ElementCreatedEntry $entry*/
                $tmp['instock'] = $entry->hasCreationInstockValue() ? $entry->getCreationInstockValue() : 0;
            } elseif($entry instanceof ElementEditedEntry) {
                /** @var ElementEditedEntry $entry */
                $tmp['message'] = $entry->getMessage();
            } elseif($entry instanceof InstockChangedEntry) {
                /** @var InstockChangedEntry $entry */
                $tmp['instock'] = $entry->getNewInstock();
                $tmp['old_instock'] = $entry->getOldInstock();
                $tmp['message'] = $entry->getComment();
                $tmp['price'] = $entry->getPrice(true);
                $tmp['price'] = $entry->getPriceMoneyString(true);
            }
            $return_data[] = $tmp;
        }

        return $return_data;
    }


    /**
     * Converts an type id (integer) to a localized string version.
     * @param $id int The id of the log type you want to have a localized string.
     * @return string The localized string.
     */
    public static function typeIDToString($id)
    {
        switch ($id) {
            case static::TYPE_USERLOGIN:
                return _("Nutzer eingeloggt");
            case static::TYPE_USERLOGOUT:
                return _("Nutzer ausgeloggt");
            case static::TYPE_USERNOTALLOWED:
                return _("Unerlaubter Zugriffsversuch");
            case static::TYPE_EXCEPTION:
                return _("Unbehandelte Exception");
            case static::TYPE_ELEMENTDELETED:
                return _("Element gelöscht");
            case static::TYPE_ELEMENTCREATED:
                return _("Element angelegt");
            case static::TYPE_ELEMENTEDITED:
                return _("Element bearbeitet");
            case static::TYPE_CONFIGCHANGED:
                return _("Systemeinstellungen geändert");
            case static::TYPE_INSTOCKCHANGE:
                return _("Bauteile Entnahme/Zugabe");
            case static::TYPE_DATABASEUPDATE:
                return _("Datenbank Update");
            default:
                return _("Unbekannter Typ");
        }
    }

    /**
     * Determines the type of a target based on the class of an element.
     * @param $element NamedDBElement The element that should be used to generate the target type id.
     * @return int The id of the target type
     * @throws \RuntimeException When no Targettype for this class was found.
     */
    public static function elementToTargetTypeID(&$element)
    {
        if ($element instanceof Attachement) {
            return static::TARGET_TYPE_ATTACHEMENT;
        } elseif ($element instanceof AttachementType) {
            return static::TARGET_TYPE_ATTACHEMENTTYPE;
        } elseif ($element instanceof User) {
            return static::TARGET_TYPE_USER;
        } elseif ($element instanceof Category) {
            return static::TARGET_TYPE_CATEGORY;
        } elseif ($element instanceof Device) {
            return static::TARGET_TYPE_DEVICE;
        } elseif ($element instanceof Footprint) {
            return static::TARGET_TYPE_FOOTPRINT;
        } elseif ($element instanceof Group) {
            return static::TARGET_TYPE_GROUP;
        } elseif ($element instanceof Manufacturer) {
            return static::TARGET_TYPE_MANUFACTURER;
        } elseif ($element instanceof Part) {
            return static::TARGET_TYPE_PART;
        } elseif ($element instanceof Storelocation) {
            return static::TARGET_TYPE_STORELOCATION;
        } elseif ($element instanceof  Supplier) {
            return static::TARGET_TYPE_SUPPLIER;
        } else {
            throw new \RuntimeException(_("Kein Target Typ für diese Klasse gefunden!"));
        }
    }

    /**
     * Returns the class name for a target type.
     * @param $target_id int The id of the target type-
     * @return NamedDBElement The full qualified class name.
     */
    public static function targetTypeIDToClass($target_id)
    {
        $base_ns = "PartDB\\";

        switch ($target_id) {
            case static::TARGET_TYPE_USER:
                return $base_ns . "User";
            case static::TARGET_TYPE_ATTACHEMENT:
                return $base_ns . "Attachement";
            case static::TARGET_TYPE_ATTACHEMENTTYPE:
                return $base_ns . "AttachementType";
            case static::TARGET_TYPE_CATEGORY:
                return $base_ns . "Category";
            case static::TARGET_TYPE_DEVICE:
                return $base_ns . "Device";
            case static::TARGET_TYPE_DEVICEPART:
                return $base_ns . "DevicePart";
            case static::TARGET_TYPE_FOOTPRINT:
                return $base_ns . "Footprint";
            case static::TARGET_TYPE_GROUP:
                return $base_ns . "Group";
            case static::TARGET_TYPE_MANUFACTURER:
                return $base_ns . "Manufacturer";
            case static::TARGET_TYPE_PART:
                return $base_ns . "Part";
            case static::TARGET_TYPE_STORELOCATION:
                return $base_ns . "Storelocation";
            case static::TARGET_TYPE_SUPPLIER:
                return $base_ns . "Supplier";
            default:
                throw new \RuntimeException(_("Unbekannter Target Typ"));
        }
    }

    /**
     * Returns a link to a info page for the target with the given type and id.
     * @param $target_type int The type of the target.
     * @param $target_id int The id of the target.
     * @return string The URL to the info page
     */
    public static function generateLinkForTarget($target_type, $target_id)
    {
        $url = BASE_RELATIVE . "/";
        switch ($target_type) {
            case static::TARGET_TYPE_ATTACHEMENT:
                //Attachements dont have a info page yet.
                return "";
            case static::TARGET_TYPE_ATTACHEMENTTYPE:
                $url.= "edit_attachement_types.php?selected_id=";
                break;
            case static::TARGET_TYPE_CATEGORY:
                $url.= "edit_categories.php?selected_id=";
                break;
            case static::TARGET_TYPE_DEVICE:
                $url.= "edit_devices.php?selected_id=";
                break;
            case static::TARGET_TYPE_DEVICEPART:
                //We dont have a real info page for that too...
                return "";
            case static::TARGET_TYPE_FOOTPRINT:
                $url .= "edit_footprints.php?selected_id=";
                break;
            case static::TARGET_TYPE_GROUP:
                $url .= "edit_groups.php?selected_id=";
                break;
            case static::TARGET_TYPE_MANUFACTURER:
                $url .= "edit_manufacturer?selected_id=";
                break;
            case static::TARGET_TYPE_PART:
                $url .= "show_part_info.php?pid=";
                break;
            case static::TARGET_TYPE_STORELOCATION:
                $url .= "edit_storelocations.php?selected_id=";
                break;
            case static::TARGET_TYPE_SUPPLIER:
                $url .= "edit_suppliers.php?selected_id=";
                break;
            case static::TARGET_TYPE_USER:
                $url .= "edit_users.php?selected_id=";
                break;
            default:
                return "";
        }

        return $url . $target_id;
    }

    /**
     * Returns a localized text representation of the target type with the given id.
     * @param $target_id int The ID for that the text should be returned.
     * @return string The text version for the target type.
     */
    public static function targetTypeIDToString($target_id)
    {
        switch ($target_id) {
            case static::TARGET_TYPE_ATTACHEMENT:
                return _("Anhang");
            case static::TARGET_TYPE_ATTACHEMENTTYPE:
                return _("Dateityp");
            case static::TARGET_TYPE_USER:
                return _("Benutzer");
            case static::TARGET_TYPE_CATEGORY:
                return _("Kategorie");
            case static::TARGET_TYPE_DEVICE:
                return _("Baugruppe");
            case static::TARGET_TYPE_FOOTPRINT:
                return _("Footprint");
            case static::TARGET_TYPE_GROUP:
                return _("Gruppe");
            case static::TARGET_TYPE_MANUFACTURER:
                return _("Hersteller");
            case static::TARGET_TYPE_PART:
                return _("Bauteil");
            case static::TARGET_TYPE_STORELOCATION:
                return _("Lagerort");
            case static::TARGET_TYPE_SUPPLIER:
                return _("Hersteller");
            default:
                return _("Unbekannter Target Typ");
        }
    }

    public static function getLogTypesList()
    {
        $data = array();

        $n = 1;
        while (true) {
            $text = static::typeIDToString($n);
            //TODO: Thats a bit dirty... Find a better way to do this...
            if (strcontains($text, _("Unbekannt"))) {
                break;
            }
            $data[] = array("id" => $n, "text" => $text);
            $n++;
        }

        return $data;
    }

    /**
     * @param $entries BaseEntry[]
     */
    public function generateTemplateLoop($entries)
    {
        $row_index = 0;

        $rows = array();
        /** @var BaseEntry $entry */
        foreach ($entries as $entry) {

            $data = array(
                "id" => $entry->getID(),
                "timestamp" => $entry->getTimestamp(true),
                "type" => $this->typeIDToString($entry->getTypeID()),
                "user" => $entry->getUser()->getFullName(true),
                "user_id" => $entry->getUser()->getID(),
                "comment" => $entry->getExtra(),
                "level" => $entry->getLevel(),
                "level_id" => $entry->getLevelID(),
                "target_text" => $entry->getTargetText(),
                "target_link" => $entry->getTargetLink(),
                "target_id" => $entry->getTargetID(),
                "row_index" => $row_index
            );

            $rows[] = $data;
            $row_index++;
        }

        return $rows;
    }

    /**
     * Get all log entries.
     *
     * @param int       $limit              Limit the result count to the given number. Set to 0 to disable pagination.
     * @param int       $page               Selects the page of the results. Each page contains $limit number of elements.
     *
     *
     * @return BaseEntry[]    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public function getEntries($newest_first = true, $min_level = self::LEVEL_DEBUG, $user_id = -1, $type_id = -1, $search_str = "",
                               $target_type = -1, $target_id = -1, $min_date = "", $max_date = "",  $limit = 50, $page = 1)
    {
        $search_str = "%" . $search_str . "%";

        $data = array();

        $query =    'SELECT * from log ';

        $query .= " WHERE level <= ?";
        $data[] = $min_level;

        //Filter for user
        if ($user_id >= 0) {
            $query .= " AND (id_user = ?)";
            $data[] = $user_id;
        }

        //Filter for type
        if ($type_id > 0) {
            $query .= " AND (type = ?)";
            $data[] = $type_id;
        }

        if ($search_str != "") {
            $query .= " AND (extra LIKE ?)";
            $data[] = $search_str;
        }

        //Filter for target_type
        if ($target_type > 0) {
            $query .= " AND (target_type = ?)";
            $data[] = $target_type;
        }

        //Filter for target_type
        if ($target_id > 0) {
            $query .= " AND (target_id = ?)";
            $data[] = $target_id;
        }

        //Filter for dates
        if ($max_date != "") {
            $query .= " AND (datetime <= ?)";
            $data[] = $max_date;
        }
        if($min_date != "") {
            $query .= " AND (datetime >= ?)";
            $data[] = $min_date;
        }

        $sorting = ($newest_first) ? "DESC" : "ASC";

        $query .=   ' ORDER BY log.datetime ' . $sorting;

        if ($limit > 0 && $page > 0) {
            $query .= " LIMIT " . (($page - 1) * $limit) . ", $limit";
        }

        $query_data = $this->database->query($query, $data);


        return $this->queryDataToEntryObjects($query_data);
    }

    /**
     *  Get count of all log entries.
     *
     * @return BaseEntry[]    all parts as a one-dimensional array of Part objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public function getEntriesCount($newest_first = true, $min_level = self::LEVEL_DEBUG, $user_id = -1,
                                    $type_id = -1, $search_str = "", $target_type = -1, $min_date = "", $max_date = "", $target_id = -1)
    {
        $data = array();

        $query =    'SELECT COUNT(id) AS count from log ';

        $query .= "WHERE level <= ?";
        $data[] = $min_level;

        //Filter for user
        if ($user_id >= 0) {
            $query .= " AND (id_user = ?)";
            $data[] = $user_id;
        }

        //Filter for type
        if ($type_id > 0) {
            $query .= " AND (type = ?)";
            $data[] = $type_id;
        }

        if ($search_str != "") {
            $query .= " AND (extra LIKE ?)";
            $data[] = $search_str;
        }

        //Filter for target_type
        if ($target_type > 0) {
            $query .= " AND (target_type = ?)";
            $data[] = $target_type;
        }

        //Filter for target_type
        if ($target_id > 0) {
            $query .= " AND (target_id = ?)";
            $data[] = $target_id;
        }

        //Filter for dates
        if ($max_date != "") {
            $query .= " AND (datetime <= ?)";
            $data[] = $max_date;
        }
        if($min_date != "") {
            $query .= " AND (datetime >= ?)";
            $data[] = $min_date;
        }

        $query .=   ' ORDER BY log.datetime DESC';

        $query_data = $this->database->query($query, $data);

        return $query_data[0]['count'];
    }

    /**
     * This function takes the results of a database Query and returns an array of BaseEntry entries (or the correct child classes).
     * @param $query_data array The results of the SQL query.
     * @return BaseEntry[] The converted data as BaseEntry objects.
     * @throws Exception If an Error happened.
     */
    protected function queryDataToEntryObjects($query_data)
    {
        $entries = array();


        foreach ($query_data as $row) {
            $class = static::typeIDToClass($row['type']);
            $entries[] = new $class($this->database, $this->current_user, $this, $row['id'], $row);
        }

        return $entries;
    }

    public function deleteSelected($select_string)
    {
        $ids = explode(",", $select_string);

        foreach ($ids as $id) {
            //We dont now for sure which ids the entries have, but UnknownTypeEntry should work always.
            $entry = new UnknownTypeEntry($this->database, $this->current_user, $this, $id);
            $entry->delete();
        }
    }


    /**
     * This function parses a id of a type and returns the name of the related class.
     * @param $type_id int The id of the type.
     * @return BaseEntry The classname for this type id.
     */
    protected static function typeIDToClass($type_id)
    {
        $base_ns = "PartDB\LogSystem\\";

        switch ($type_id) {
            case static::TYPE_USERLOGIN:
                return $base_ns . "UserLoginEntry";
            case static::TYPE_USERLOGOUT:
                return $base_ns . "UserLogoutEntry";
            case static::TYPE_USERNOTALLOWED:
                return $base_ns . "UserNotAllowedEntry";
            case static::TYPE_EXCEPTION:
                return $base_ns . "ExceptionEntry";
            case static::TYPE_ELEMENTDELETED:
                return $base_ns . "ElementDeletedEntry";
            case static::TYPE_ELEMENTCREATED:
                return $base_ns . "ElementCreatedEntry";
            case static::TYPE_ELEMENTEDITED:
                return $base_ns . "ElementEditedEntry";
            case static::TYPE_CONFIGCHANGED:
                return $base_ns . "ConfigChangedEntry";
            case static::TYPE_INSTOCKCHANGE:
                return $base_ns . "InstockChangedEntry";
            case static::TYPE_DATABASEUPDATE:
                return $base_ns . "DatabaseUpdatedEntry";
            default:
                return $base_ns . "UnknownTypeEntry";
        }
    }


}
