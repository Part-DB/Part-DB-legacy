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
use PartDB\LogSystem\BaseEntry;
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

    const TARGET_TYPE_NONE = 0;
    const TARGET_TYPE_USER = 1;

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
    }

    public function userLogsIn($user, $ip_address = "")
    {
        try {
            UserLoginEntry::add($this->database, User::getLoggedInUser($this->database, $this), $this, $user, $ip_address);
        } catch (Exception $e) {

        }
    }

    public function userLogsOut($user)
    {
        try {
            UserLogoutEntry::add($this->database, User::getLoggedInUser($this->database, $this), $this, $user);
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
            default:
                return _("Unbekannter Typ");
        }
    }

    /**
     * @param $entries BaseEntry[]
     */
    public function generateTemplateLoop($entries)
    {
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
                "target_link" => $entry->getTargetLink()
            );

            $rows[] = $data;
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
    public function getEntries($newest_first = true, $min_level = self::LEVEL_DEBUG, $user_id = -1, $type = -1, $search_str = "", $limit = 50, $page = 1)
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

        if ($search_str != "") {
            $query .= " AND (extra LIKE ?)";
            $data[] = $search_str;
        }

        $query .=   ' ORDER BY log.datetime DESC';

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
    public function getEntriesCount($newest_first = true, $min_level = self::LEVEL_DEBUG, $user_id = -1, $type = -1, $search_str = "")
    {
        $search_str = "%" . $search_str . "%";

        $data = array();

        $query =    'SELECT COUNT(id) AS count from log ';

        $query .= "WHERE level <= ?";
        $data[] = $min_level;

        //Filter for user
        if ($user_id >= 0) {
            $query .= " AND (id_user = ?)";
            $data[] = $user_id;
        }

        if ($search_str != "") {
            $query .= " AND (extra LIKE ?)";
            $data[] = $search_str;
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
        $current_user = User::getLoggedInUser();

        foreach ($query_data as $row) {
            $class = static::typeIDToClass($row['type']);
            $entries[] = new $class($this->database, $current_user, $this, $row['id'], $row);
        }

        return $entries;
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
            default:
                return $base_ns . "UnknownTypeEntry";
        }
    }


}
