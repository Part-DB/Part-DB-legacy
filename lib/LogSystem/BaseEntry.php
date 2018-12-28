<?php

/**
 *
 * Part-DB Version 0.4+ "nextgen"
 * Copyright (C) 2016 - 2018 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 *
 */


namespace PartDB\LogSystem;

use Exception;
use PartDB\Base\DBElement;
use PartDB\Database;
use PartDB\Log;
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\SystemPermission;
use PartDB\User;

abstract class BaseEntry extends DBElement
{
    protected $user;

    const TABLE_NAME = 'log';

    /**
     * Constructor
     *
     * @note  It's allowed to create an object with the ID 0 (for the root element).
     *
     * @param Database  &$database      reference to the Database-object
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log           reference to the Log-object
     * @param integer   $id             ID of the filetype we want to get
     *
     * @throws Exception    if there is no such attachement type in the database
     * @throws Exception    if there was an error
     */
    public function __construct(Database &$database, User &$current_user, Log &$log, int $id, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $db_data);
    }

    /**
     * Gets the user, which caused this Log entry.
     * @return User The user which caused the log entry.
     * @throws Exception
     */
    public function getUser() : User
    {
        if ($this->user == null) {
            $this->user = User::getInstance($this->database, $this->current_user, $this->log, $this->db_data['id_user']);
        }

        return $this->user;
    }

    /**
     * Returns the date/time, which is relevant for this log entry..
     * @param $formatted bool When true, the date gets formatted with the locale and timezone settings.
     *       When false, the raw value from the DB is returned (unix timestamp).
     * @return string The creation time of the part.
     */
    public function getTimestamp($formatted = true) : string
    {
        $time_str = $this->db_data['datetime'];
        if ($formatted) {
            $timestamp = strtotime($time_str);
            return formatTimestamp($timestamp);
        }
        return $time_str;
    }


    public function getExtraRaw() : string
    {
        return $this->db_data['extra'];
    }

    /**
     * Returns the type of this log entry.
     * @return int The id ot the type of the log entry.
     */
    public function getTypeID() : int
    {
        return $this->db_data['type'];
    }

    /**
     * Returns the priority level of this entry, as an int. Use the Log::LEVEL_* consts for comparison.
     * @return int The id of the level.
     */
    public function getLevelID() : int
    {
        return $this->db_data['level'];
    }

    /**
     * Returns the priority level of this log entry as a string.
     * @return string The level as a string.
     */
    public function getLevel() : string
    {
        switch ($this->getLevelID()) {
            case Log::LEVEL_EMERGENCY:
                return "emergency";
            case Log::LEVEL_ALERT:
                return "alert";
            case Log::LEVEL_CRITICAL:
                return "critical";
            case Log::LEVEL_ERROR:
                return "error";
            case Log::LEVEL_WARNING:
                return "warning";
            case Log::LEVEL_NOTICE:
                return "notice";
            case Log::LEVEL_INFO:
                return "info";
            case Log::LEVEL_DEBUG:
                return "debug";
        }
        throw new \RuntimeException(_("Die verwendetete Level-ID wird nicht unterstützt!"));
    }

    /**
     * Returns the type of the target.
     * @return int A integer describing the type of the Target
     */
    protected function getTargetType() : int
    {
        return $this->db_data['target_type'];
    }

    /**
     * Returns the id of the target
     * @return int The id of the target
     */
    public function getTargetID() : int
    {
        return $this->db_data['target_id'];
    }

    /**
     * Returns the a text representation of the target
     * @return string The text describing the target
     */
    abstract public function getTargetText() : string;

    /**
     * Return a link to the target. Returns empty string if no link is available.
     * @return string the link to the target.
     */
    abstract public function getTargetLink() : string;

    /**
     * Returns some extra information which is shown in the extra coloumn, of the log
     * @param $html bool Set this to true, to get an HTML formatted version of the extra.
     * @return string The extra information
     */
    abstract public function getExtra(bool $html = false) : string;

    public function delete()
    {
        $this->current_user->tryDo(PermissionManager::SYSTEM, SystemPermission::DELETE_LOGS);

        parent::delete();
    }

    /**
     * This function converts the given $extra array to a form, that can be written into the extra field.
     * @param $extra
     * @return false|string
     */
    protected static function serializeExtra($extra)
    {
        return json_encode($extra);
    }

    /**
     * This function converts the string from the extra field, to an array/object.
     * @param bool $assoc_array When set to true, the data is returned as an array, otherwise as an object.
     * @return array|object|null Returns the deserialized array/object, null if it could not be deserialized.
     */
    protected function deserializeExtra(bool $assoc_array = true)
    {
        return json_decode($this->db_data['extra'], $assoc_array);
    }

    /**
     * Adds a new log entry to the database.
     * @param $database Database The database which should be used for requests.
     * @param $current_user User The database which should be used for requests.
     * @param $log Log The database which should be used for requests.
     * @param $type int The type of the entry that should
     * @param $level int The priority level of the entry (see Log::LEVEL_* constants)
     * @param $user_id int The id of the user, that causes this entry.
     * @param $target_type int The type of the target.
     * @param $target_id int The id of the target.
     * @param $extra_obj mixed|array|object An object containing some additional informations.
     * @return BaseEntry|null The newly created BaseEntry, or null if nothing was created (e.g. when logging is disabled)
     * @throws Exception
     */
    protected static function addEntry(Database &$database, User &$current_user, Log &$log, int $type, int $level, int $user_id, int $target_type, int $target_id, $extra_obj)
    {
        global $config;
        //Check if the current Entry has an sufficent priority level
        //Zero is the highest possible priority, so -1 disables logging completly.
        if ($level > $config['logging_system']['min_level']) {
            return null;
        }

        $data = array(
            "type" => $type,
            "id_user" => $user_id,
            "target_type" => $target_type,
            "target_id" => $target_id,
            "extra" => static::serializeExtra($extra_obj),
            "level" => $level
        );
        return static::addByArray($database, $current_user, $log, $data);
    }

    public function getIDString(): string
    {
        return "LE" . sprintf("%06d", $this->getID());
    }
}
