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

/**
 * Dont use this Class for any Log entry! It is only useful, to have a fallback, when we are not able to find a specific
 * class for a type.
 * @package PartDB\LogSystem
 */
class UnknownTypeEntry extends BaseEntry
{
    /**
     * Constructor
     *
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

        //Every type is allowed.
    }


    /********************************
     *
     * No Add function, because it is not possible/useful to add UnknownTypeEntries to the Log.
     *
     *******************************/
    /**
     * Returns the a text representation of the target
     * @return string The text describing the target
     */
    public function getTargetText() : string
    {
        return _("Typ: ") . Log::targetTypeIDToString($this->getTargetType()) . _(", ID: ") .  $this->getTargetID();
    }

    /**
     * Return a link to the target. Returns empty string if no link is available.
     * @return string
     */
    public function getTargetLink() : string
    {
        return "";
    }

    /**
     * Returns some extra information which is shown in the extra coloumn, of the log
     * @param $html bool Set this to true, to get an HTML formatted version of the extra.
     * @return string The extra information
     */
    public function getExtra(bool $html = false) : string
    {
        return $this->db_data["extra"];
    }
}
