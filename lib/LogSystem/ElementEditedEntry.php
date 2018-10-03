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
use PartDB\Base\NamedDBElement;
use PartDB\Database;
use PartDB\Log;
use PartDB\User;

class ElementEditedEntry extends BaseEntry
{

    /**
     * @var $element NamedDBElement
     */
    protected $element;

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
        parent::__construct($database, $current_user, $log, $id, $db_data);

        //Check if we have selcted the right type
        if ($this->getTypeID() != Log::TYPE_ELEMENTEDITED) {
            throw new \RuntimeException(_("Falscher Logtyp!"));
        }

        try {
            $class = Log::targetTypeIDToClass($this->getTargetType());
            $this->element = new $class($database, $current_user, $log, $this->getTargetID());
        } catch (Exception $ex) {

        }
    }


    /**
     * Adds a new log entry to the database.
     * @param $database Database The database which should be used for requests.
     * @param $current_user User The database which should be used for requests.
     * @param $log Log The database which should be used for requests.
     * @param $element NamedDBElement The ip adress the user loggs in from
     *
     * @return static|BaseEntry The new created Entry.
     *
     * @throws Exception
     */
    public static function add(&$database, &$current_user, &$log, &$element, $old_values = null, $new_values = null)
    {
        static $type_id, $element_id, $user_id, $last_log = 0;

        //Only use timeout when nothing has changed since the last time.
        if($element_id == $element->getID()
            && $type_id == Log::elementToTargetTypeID($element)
            && $user_id == $current_user->getID()
            && time() - $last_log < 2) //2 seconds timeout
        {
            $last_log = time();
            return null;
        }

        $type_id = Log::elementToTargetTypeID($element);
        $element_id = $element->getID();
        $user_id = $current_user->getID();

        //When a part change only changes the instock value, then dont create a own entry, because an Instock Change entry was already created.
        if($element_id = LOG::TARGET_TYPE_PART
            && count($new_values) == 1
            && isset($new_values['instock']))
        {
            return null;
        }

        //Check if there is a change in the new db_data.
        $difference = false;
        foreach($new_values as $key => $value) {
            if (isset($old_values[$key])) {
                if($old_values[$key] != $value) { //Dont use strict compare here!!
                    $difference = true;
                    break;  //We need only one difference
                }
            }
        }
        //Nothing was changed, so we dont need to create an entry.
        if(!$difference) {
            return null;
        }


        if ($type_id == Log::TARGET_TYPE_USER || $type_id == Log::TARGET_TYPE_GROUP) {
            //When a user or group is edited, this needs more attention, so higher level.
            $level = Log::LEVEL_NOTICE;
        } else {
            $level = Log::LEVEL_INFO;
        }

        return static::addEntry(
            $database,
            $current_user,
            $log,
            Log::TYPE_ELEMENTEDITED,
            $level,
            $current_user->getID(),
            $type_id,
            $element->getID(),
            ""
        );
    }

    /**
     * Returns the a text representation of the target
     * @return string The text describing the target
     */
    public function getTargetText()
    {
        try {
            $part_name = ($this->element != null) ? $this->element->getName() : $this->getTargetID();
            return Log::targetTypeIDToString($this->getTargetType()) . ": " . $part_name;
        } catch (Exception $ex) {
            return "ERROR!";
        }
    }

    /**
     * Return a link to the target. Returns empty string if no link is available.
     * @return string the link to the target.
     */
    public function getTargetLink()
    {
        //We can not link to a part, that dont exists any more...
        return Log::generateLinkForTarget($this->getTargetType(), $this->getTargetID());
    }
}