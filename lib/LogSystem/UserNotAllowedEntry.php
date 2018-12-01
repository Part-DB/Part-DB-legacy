<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 06.02.2018
 * Time: 18:52
 */

namespace PartDB\LogSystem;

use Exception;
use PartDB\Base\DBElement;
use PartDB\Database;
use PartDB\Log;
use PartDB\User;

class UserNotAllowedEntry extends BaseEntry
{
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

        //Check if we have selcted the right type
        if ($this->getTypeID() != Log::TYPE_USERNOTALLOWED) {
            throw new \RuntimeException(_("Falscher Logtyp!"));
        }

        if ($this->getTargetType() != Log::TARGET_TYPE_USER) {
            throw new \RuntimeException(_("Falscher Targettyp!"));
        }
    }


    /**
     * Adds a new log entry to the database.
     * @param $database Database The database which should be used for requests.
     * @param $current_user User The database which should be used for requests.
     * @param $log Log The database which should be used for requests.
     * @param $user User The user that logs in.
     * @param $ip_address string The ip adress the user loggs in from
     *
     * @return static|BaseEntry The new created Entry.
     *
     * @throws Exception
     */
    public static function add(Database &$database, User &$current_user, Log &$log, string $permission_string)
    {
        $arr = array("p" => $permission_string);

        return static::addEntry(
            $database,
            $current_user,
            $log,
            Log::TYPE_USERNOTALLOWED,
            Log::LEVEL_WARNING,
            $current_user->getID(),
            Log::TARGET_TYPE_USER,
            $current_user->getID(),
            $arr
        );
    }

    /**
     * Returns the a text representation of the target
     * @return string The text describing the target
     */
    public function getTargetText() : string
    {
        try {
            $user = new User($this->database, $this->current_user, $this->log, $this->getTargetID());
            return $user->getFullName();
        } catch (Exception $ex) {
            return "ERROR!";
        }
    }

    /**
     * Return a link to the target. Returns empty string if no link is available.
     * @return string the link to the target.
     */
    public function getTargetLink() : string
    {
        return BASE_RELATIVE . "user_info?uid=" . $this->getTargetID();
    }

    /**
     * Returns some extra information which is shown in the extra coloumn, of the log
     * @param $html bool Set this to true, to get an HTML formatted version of the extra.
     * @return string The extra information
     */
    public function getExtra(bool $html = false) : string
    {
        return $this->deserializeExtra()["p"];
    }
}
