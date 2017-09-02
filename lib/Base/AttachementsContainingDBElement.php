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

namespace PartDB\Base;

use Exception;
use PartDB\Attachement;
use PartDB\AttachementType;
use PartDB\Database;
use PartDB\Log;
use PartDB\User;

/**
 * @file class.AttachementsContainingDBElement.php
 * @brief class AttachementsContainingDBElement
 *
 * @class AttachementsContainingDBElement
 * @brief All subclasses of this class are containing attachements.
 * @author kami89
 */
abstract class AttachementsContainingDBElement extends NamedDBElement
{
    /********************************************************************************
     *
     *   Calculated Attributes
     *
     *   Calculated attributes will be NULL until they are requested for first time (to save CPU time)!
     *   After changing an element attribute, all calculated data will be NULLed again.
     *   So: the calculated data will be cached.
     *
     *********************************************************************************/

    /** @var AttachementType[] All attachement types of the attachements of this element as an array of AttachementType objects.
     *  @see AttachementsContainingDBElement::getAttachementTypes() */
    protected $attachement_types     = null;
    /** @var Attachement[] All attachements of this element as a one-dimensional array of Attachement objects.
     *  @see AttachementsContainingDBElement::getAttachements() */
    protected $attachements          = null;

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @param Database  &$database                  reference to the Database-object
     * @param User      &$current_user              reference to the current user which is logged in
     * @param Log       &$log                       reference to the Log-object
     * @param integer   $id                         ID of the element we want to get
     * @param boolean   $allow_virtual_elements     @li if true, it's allowed to set $id to zero
     *                                                  (the StructuralDBElement needs this for the root element)
     *                                              @li if false, $id == 0 is not allowed (throws an Exception)
     * @param array     $db_data                    If you have already data from the database, then use give it with this param, the part, wont make a database request.
     * @throws Exception    if there is no such element in the database
     * @throws Exception    if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $tablename, $id, $allow_virtual_elements = false, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, $tablename, $id, $allow_virtual_elements, $db_data);
    }

    /**
     * @copydoc DBElement::reset_attributes()
     */
    public function resetAttributes($all = false)
    {
        $this->attachement_types = null;
        $this->attachements = null;

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
     * This function overrides the same-named function from the parent class.
     * This is required to delete the attachements of an element too.
     *
     * @param boolean $delete_files_from_hdd    @li if true, the attached files will be deleted from harddisc drive (!!)
     *                                              If some files are used for other elements, they won't be deleted.
     *                                          @li if false, the files will be deleted from database,
     *                                              but not from the harddisc drive.
     *
     * @throws Exception if there was an error
     */
    public function delete($delete_files_from_hdd = false)
    {
        try {
            $transaction_id = $this->database->beginTransaction(); // start transaction

            // first, we will delete all files of this element
            $attachements = $this->getAttachements();
            $this->resetAttributes(); // set $this->attachements to NULL
            foreach ($attachements as $attachement) {
                $attachement->delete($delete_files_from_hdd);
            }

            parent::delete(); // now delete this element

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->resetAttributes();

            throw new Exception(sprintf(_("Das Element \"%s\" konnte nicht gelöscht werden!\nGrund: "), $this->getName()).$e->getMessage());
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get all different attachement types of the attachements of this element
     *
     * @return AttachementType[] the attachement types as a one-dimensional array of AttachementType objects,
     *                      sorted by their names
     *
     * @throws Exception if there was an error
     */
    public function getAttachementTypes()
    {
        if (! is_array($this->attachement_types)) {
            $this->attachement_types = array();

            $query = 'SELECT attachement_types.* FROM attachements '.
                'LEFT JOIN attachement_types ON attachements.type_id=attachement_types.id '.
                'WHERE (class_name=? OR class_name=?) AND element_id=? '.
                'GROUP BY type_id '.
                'ORDER BY attachement_types.name ASC';
            $query_data = $this->database->query($query, array(get_class($this),getClassShort($this), $this->getID()));

            //debug('temp', 'Anzahl gefundener Dateitypen: '.count($query_data));
            foreach ($query_data as $row) {
                $this->attachement_types[] = new AttachementType($this->database, $this->current_user, $this->log, $row['id'], $row);
            }
        }

        return $this->attachement_types;
    }


    /**
     * Get all attachements of this element / Get the element's attachements with a specific type
     *
     * @param integer   $type_id                    @li if NULL, all attachements of this element will be returned
     *                                              @li if this is a number > 0, only attachements with this type ID will be returned
     * @param boolean   $only_table_attachements    if true, only attachements with "show_in_table == true"
     *
     * @return Attachement[] the attachements as a one-dimensional array of Attachement objects
     *
     * @throws Exception if there was an error
     */
    public function getAttachements($type_id = null, $only_table_attachements = false)
    {
        if (! is_array($this->attachements)) {
            $this->attachements = array();

            $query = 'SELECT attachements.* FROM attachements '.
                'LEFT JOIN attachement_types ON attachements.type_id=attachement_types.id '.
                'WHERE (class_name=? OR class_name=?) AND element_id=? ';
            $query .= 'ORDER BY attachement_types.name ASC, attachements.name ASC';
            $query_data = $this->database->query($query, array(get_class($this), getClassShort($this), $this->getID()));

            //debug('temp', 'Anzahl gefundene Dateianhänge: '.count($query_data));
            foreach ($query_data as $row) {
                $this->attachements[] = new Attachement($this->database, $this->current_user, $this->log, $row['id'], $row);
            }
        }

        if ($only_table_attachements || $type_id) {
            $attachements = $this->attachements;

            foreach ($attachements as $key => $attachement) {
                if (($only_table_attachements && (! $attachement->getShowInTable()))
                    || ($type_id && ($attachement->getType()->getID() != $type_id))) {
                    unset($attachements[$key]);
                }
            }

            return $attachements;
        } else {
            return $this->attachements;
        }
    }
}
