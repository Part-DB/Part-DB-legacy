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
use PartDB\Database;
use PartDB\Log;
use PartDB\Part;
use PartDB\User;

/**
 * @file class.PartsContainingDBElement.php
 * @brief class PartsContainingDBElement
 *
 * @class PartsContainingDBElement
 * @brief All subclasses of this class contain Parts (or DeviceParts).
 * @author kami89
 */
abstract class PartsContainingDBElement extends StructuralDBElement
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

    /** @brief (array) all part objects which are included in this element
     *          (see PartsContainingDBElement::get_parts()) */
    protected $parts = null;

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * It's allowed to create an object with the ID 0 (for the root element).
     *
     * @param Database  &$database      reference to the Database-object
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log           reference to the Log-object
     * @param string    $tablename      the name of the database table where the elements are located
     * @param integer   $id             ID of the element we want to get
     *
     * @throws Exception if there is no such element in the database
     * @throws Exception if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $tablename, $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, $tablename, $id, $data);
    }

    /**
     * @copydoc DBElement::reset_attributes()
     */
    public function resetAttributes($all = false)
    {
        $this->parts = null;

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
     * @note    This function overrides the same-named function from the parent class
     *          because we have to check if this element has no parts included.
     *          (It's not allowed to delete an element which has already parts in it!)
     *
     * @param boolean $delete_recursive         @li if true, all child elements (recursive) will be deleted too (!!)
     *                                          @li if false, the parent of the child nodes (not recursive) will be
     *                                              changed to the parent element of this element
     * @param boolean $delete_files_from_hdd    if true, all attached files of this element will be deleted from harddisc drive (!!)
     *
     * @throws Exception if there are already parts included in this element
     * @throws Exception if there was an error
     */
    public function delete($delete_recursive = false, $delete_files_from_hdd = false)
    {
        try {
            $transaction_id = $this->database->beginTransaction(); // start transaction

            $parts = $this->getParts('id_category');

            if (count($parts) > 0) {
                throw new Exception('Das Element enthält noch '.count($parts).' Bauteile!');
            }

            parent::delete($delete_recursive, $delete_files_from_hdd);

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->resetAttributes();

            throw new Exception("Das Element \"".$this->getName()."\" konnte nicht gelöscht werden!\nGrund: ".$e->getMessage());
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get all parts of this element
     *
     * @par Examples:
     *  - $category->get_parts():       you will get all Part-objects in this category
     *  - $supplier->get_parts():       you will get all Part-objects, which have (at least) this supplier
     *
     *     To get the DevicePart-objects of a Device, there is the method Device::get_parts().
     *          (This method here allows only to get Part objects, but a Device has DevicePart objects!)
     *
     * @param string    $parts_rowname              @li this is the name of the table row of the parts table,
     *                                                  where the ID of this element is located (example: 'id_category')
     *                                              @li example: "id_manufacturer", "id_category", ...
     * @param boolean   $recursive                  if true, the parts of all subelements will be listed too
     * @param boolean   $hide_obsolete_and_zero     if true, obsolete parts with "instock == 0" will not be returned
     *
     * @return  Part[]   all parts as a one-dimensional array of Part objects
     *
     * @throws Exception if there was an error
     */
    public function getTableParts($parts_rowname, $recursive = false, $hide_obsolete_and_zero = false)
    {
        $subelements = array();

        if ($recursive) {
            $subelements = $this->getSubelements(true);
        }

        if (is_null($this->parts) || ! is_array($this->parts)) {
            $this->parts = array();

            /*
            $query = 'SELECT id FROM parts WHERE '.$parts_rowname.'= '. $this->get_id();

            foreach($subelements as $element)
            {
                $query = $query . " OR ".$parts_rowname."= ".$element->get_id();
            }
            */
            $query = 'SELECT parts.* FROM parts WHERE '.$parts_rowname.'=?';
            $vals = array($this->getID());

            foreach ($subelements as $element) {
                $query = $query . " OR ".$parts_rowname."=?";
                $vals[] = $element->getID();
            }



            $query = $query.
                ' ORDER BY name, description';
            //$query_data = $this->database->query($query);
            $query_data = $this->database->query($query, $vals);

            foreach ($query_data as $row) {
                $this->parts[] = new Part($this->database, $this->current_user, $this->log, $row['id'], $row);
            }

            usort($this->parts, '\PartDB\Base\PartsContainingDBElement::usort_compare');
        }

        $parts = $this->parts;

        if ($hide_obsolete_and_zero) {
            // remove obsolete parts from array
            $parts = array_values(array_filter($parts, function ($part) {
                /** @var $part Part */
                return ((! $part->getObsolete()) || ($part->getInstock() > 0));
            }));
        }

        return $parts;
    }

    public abstract function getParts($recursive = false, $hide_obsolete_and_zero = false);

    /**
     * Compare function for "usort()"
     *
     * From php.net:    The comparison function must return an integer less than,
     *                  equal to, or greater than zero if the first argument is considered
     *                  to be respectively less than, equal to, or greater than the second.
     *
     * @param Part $part_1      The Part Object #1
     * @param Part $part_2      The Part Object #2
     *
     * @return integer
     */
    public static function usort_compare($part_1, $part_2)
    {
        if ($part_1->getName() != $part_2->getName()) {
            return strcasecmp($part_1->getName(), $part_2->getName());
        } else { // names are identical, so we compare the description of the parts
            return strcasecmp($part_1->getDescription(), $part_2->getDescription());
        }
    }
}
