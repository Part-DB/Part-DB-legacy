<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 06.02.2018
 * Time: 18:20
 */

namespace PartDB\LogSystem;

use Exception;
use PartDB\Base\DBElement;
use PartDB\Database;
use PartDB\Log;
use PartDB\User;

abstract class BaseEntry extends DBElement
{
    protected $user;

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
    public function __construct(&$database, &$current_user, &$log, $id, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, 'log', $id, $db_data);
    }

    /**
     * Gets the user, which caused this Log entry.
     * @return User The user which caused the log entry.
     * @throws Exception
     */
    public function getUser()
    {
        if ($this->user == null) {
            $this->user = new User($this->database, $this->current_user, $this->log, $this->db_data['id_user']);
        }

        return $this->user;
    }

    /**
     * Returns the date/time, which is relevant for this log entry..
     * @param $formatted bool When true, the date gets formatted with the locale and timezone settings.
     *       When false, the raw value from the DB is returned (unix timestamp).
     * @return string The creation time of the part.
     */
    public function getTimestamp($formatted = true)
    {
        $time_str = $this->db_data['datetime'];
        if ($formatted) {
            $timestamp = strtotime($time_str);
            return formatTimestamp($timestamp);
        }
        return $time_str;
    }

    public function getExtra()
    {
        return $this->db_data['extra'];
    }

    /**
     * Returns the type of this log entry.
     * @return int The id ot the type of the log entry.
     */
    public function getTypeID()
    {
        return $this->db_data['type'];
    }

    /**
     * Returns the priority level of this entry, as an int. Use the Log::LEVEL_* consts for comparison.
     * @return int The id of the level.
     */
    public function getLevelID()
    {
        return $this->db_data['level'];
    }

    /**
     * Returns the priority level of this log entry as a string.
     * @return string The level as a string.
     */
    public function getLevel()
    {
        switch ($this->getLevelID()) {
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
    protected function getTargetType()
    {
        return $this->db_data['target_type'];
    }

    /**
     * Returns the id of the target
     * @return int The id of the target
     */
    protected function getTargetID()
    {
        return $this->db_data['target_id'];
    }

    /**
     * Returns the a text representation of the target
     * @return string The text describing the target
     */
    abstract public function getTargetText();

    /**
     * Return a link to the target. Returns empty string if no link is available.
     * @return string the link to the target.
     */
    abstract public function getTargetLink();

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
     * @param $extra string An string containing some additional informations.
     * @return BaseEntry The newly created BaseEntry.
     * @throws Exception
     */
    protected static function addEntry(&$database, &$current_user, &$log, $type, $level, $user_id, $target_type, $target_id, $extra)
    {
        $data = array(
            "type" => $type,
            "id_user" => $user_id,
            "target_type" => $target_type,
            "target_id" => $target_id,
            "extra" => $extra,
            "level" => $level
        );
        return static::addByArray($database, $current_user, $log, "log", $data);
    }
}