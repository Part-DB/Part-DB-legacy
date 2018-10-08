<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 01.10.2018
 * Time: 13:48
 */

namespace PartDB\LogSystem;

use PartDB\Base\NamedDBElement;
use PartDB\Database;
use PartDB\Log;
use PartDB\Part;
use PartDB\User;

class DatabaseUpdatedEntry extends BaseEntry
{
    /** @var int */
    protected $old_version, $new_version;

    protected $successful;

    public function __construct(Database $database, User $current_user, Log $log, $id, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $db_data);

        //Fill our extra values.

        $extra_array = $this->deserializeExtra();

        $this->old_version = $extra_array['o'];
        $this->new_version = $extra_array['n'];
        $this->successful = $extra_array['s'];
    }

    /**
     * Returns the old database version (the one before the update)
     * @return int The old version.
     */
    public function getOldVersion()
    {
        return $this->old_version;
    }

    /**
     * Returns the new database version (the one after the update)
     * @return int The new instock value.
     */
    public function getNewVersion()
    {
        return $this->new_version;
    }

    /**
     * Checks if the database update associated with this entry was successful.
     * @return bool True if the
     */
    public function isSuccessful()
    {
        return $this->successful;
    }

    public function getSuccessString()
    {
        if($this->isSuccessful()) {
            return _("Erfolgreich");
        } else {
            return _("Fehlgeschlagen");
        }
    }

    public function getExtra($html = false)
    {
        return $this->getSuccessString() . _("; Alte Version: ") . $this->getOldVersion() . _("; Neue Version: ") . $this->getNewVersion();
    }

    /**
     * Returns the a text representation of the target
     * @return string The text describing the target
     */
    public function getTargetText()
    {
        return _("Datenbank");
    }

    /**
     * Return a link to the target. Returns empty string if no link is available.
     * @return string the link to the target.
     */
    public function getTargetLink()
    {
        return "";
    }



    /**
     * Adds a new log entry to the database.
     * @param $database Database The database which should be used for requests.
     * @param $current_user User The database which should be used for requests.
     * @param $log Log The database which should be used for requests.
     * @param $part NamedDBElement The ip adress the user loggs in from
     *
     * @return static|BaseEntry The new created Entry.
     *
     * @throws \Exception
     */
    public static function add(&$database, &$current_user, &$log, $old_version, $new_version, $successful = true)
    {
        $old_version = (int) $old_version;
        $new_version = (int) $new_version;

        $extra_array = array();
        $extra_array['o'] = $old_version; //Old version
        $extra_array['n'] = $new_version; //New version
        $extra_array['s'] = (bool) $successful;

        $level = Log::LEVEL_WARNING;

        return static::addEntry(
            $database,
            $current_user,
            $log,
            Log::TYPE_DATABASEUPDATE,
            $level,
            $current_user->getID(),
            0,
            0,
            $extra_array
        );
    }

}