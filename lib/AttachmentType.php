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
use PartDB\Exceptions\ElementNotExistingException;
use PartDB\Exceptions\TableNotExistingException;
use PartDB\Permissions\PermissionManager;

/**
 * @file AttachementType.php
 * @brief class AttachementType
 *
 * @class AttachementType
 * All elements of this class are stored in the database table "attachement_types".
 * @author kami89
 */
class AttachmentType extends Base\StructuralDBElement implements Interfaces\IAPIModel
{
    const TABLE_NAME = "attachement_types";

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
     * @throws TableNotExistingException If the table is not existing in the DataBase
     * @throws \PartDB\Exceptions\DatabaseException If an error happening during Database AccessDeniedException
     * @throws ElementNotExistingException If no such element exists in DB.
     */
    protected function __construct(&$database, &$current_user, &$log, $id, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $db_data);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get all attachements ("Attachement" objects) with this type
     *
     * @return Attachment[]        all attachements with this type, as a one-dimensional array of Attachement-objects
     *                      (sorted by their names)
     *
     *
     * @throws Exceptions\DatabaseException
     * @throws Exceptions\TableNotExistingException
     */
    public function getAttachementsForType() : array
    {
        // the attribute $this->attachements is used from class "AttachementsContainingDBELement"
        if (! is_array($this->attachments)) {
            $this->attachments = array();

            $query = 'SELECT * FROM attachements ' .
                'WHERE type_id=? ' .
                'ORDER BY name ASC';
            $query_data = $this->database->query($query, array($this->getID()));

            //debug('temp', 'Anzahl gefundene Dateien: '.count($query_data));
            foreach ($query_data as $row) {
                $this->attachments[] = Attachment::getInstance(
                    $this->database,
                    $this->current_user,
                    $this->log,
                    $row['id'],
                    $row
                );
            }
        }

        return $this->attachments;
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/
    /**
     * Create a new attachement type
     *
     * @param Database  &$database          reference to the database onject
     * @param User      &$current_user      reference to the current user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param string    $name               the name of the new attachement type (see AttachementType::set_name())
     * @param integer   $parent_id          the parent ID of the new attachement type (see AttachementType::set_parent_id())
     *
     * @return AttachmentType|Base\StructuralDBElement
     *
     * @throws Exception    if (this combination of) values is not valid
     * @throws Exception    if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(Database &$database, User &$current_user, Log &$log, string $name, int $parent_id, string $comment = "")
    {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            array(  'name'              => $name,
                'parent_id'         => $parent_id,
                "comment"   => $comment)
        );
    }

    /**
     * Returns the ID as an string, defined by the element class.
     * This should have a form like P000014, for a part with ID 14.
     * @return string The ID as a string;
     */
    public function getIDString(): string
    {
        return "AT" . sprintf("%09d", $this->getID());
    }


    /**
     * Returns a Array representing the current object.
     * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
     * @return array A array representing the current object.
     * @throws Exception
     */
    public function getAPIArray(bool $verbose = false) : array
    {
        return array("id" => $this->getID(),
            "name" => $this->getName(),
            "fullpath" => $this->getFullPath("/"),
            "parentid" => $this->getParentID(),
            "level" => $this->getLevel());
    }

    public static function getPermissionName() : string
    {
        return PermissionManager::ATTACHEMENT_TYPES;
    }
}
