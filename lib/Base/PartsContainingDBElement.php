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
use PartDB\Exceptions\DatabaseException;
use PartDB\Exceptions\ElementNotExistingException;
use PartDB\Exceptions\TableNotExistingException;
use PartDB\Log;
use PartDB\Part;
use PartDB\Permissions\PartContainingPermission;
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
     * @param integer   $id             ID of the element we want to get
     *
     * @throws TableNotExistingException If the table is not existing in the DataBase
     * @throws \PartDB\Exceptions\DatabaseException If an error happening during Database AccessDeniedException
     * @throws ElementNotExistingException If no such element exists in DB.
     */
    public function __construct(Database &$database, User &$current_user, Log &$log, int $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $data);
    }

    /**
     * @copydoc DBElement::reset_attributes()
     */
    public function resetAttributes(bool $all = false)
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
    public function delete(bool $delete_recursive = false, bool $delete_files_from_hdd = false)
    {
        try {
            $transaction_id = $this->database->beginTransaction(); // start transaction

            $parts = $this->getParts();

            if (!empty($parts)) {
                throw new Exception('Das Element enthält noch '.count($parts).' Bauteile!');
            }

            parent::delete($delete_recursive, $delete_files_from_hdd);

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->resetAttributes();

            throw new DatabaseException(sprintf(_("Das Element \"%s\" konnte nicht gelöscht werden!"), $this->getName()) . "\n" . _("Grund: ").$e->getMessage());
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/



    /* *  Get all parts from this element
     *
     * @param boolean $recursive                if true, the parts of all subcategories will be listed too
     * @param boolean $hide_obsolete_and_zero   if true, obsolete parts with "instock == 0" will not be returned
     * @param int       $limit                      Limit the number of results, to this value.
     *                                              If set to 0, then the results are not limited.
     * @param int       $page                       Show the results of the page with given number.
     *                                              Use in combination with $limit.
     *
     * @return array        all parts as a one-dimensional array of Part-objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    abstract function getParts(bool $recursive = false, bool $hide_obsolete_and_zero = false, int $limit = 50, int $page = 1) : array;

    /**
     * Get all parts of this element
     *
     * @par Examples:
     *  - $category->get_Parts():       you will get all Part-objects in this category
     *  - $supplier->getParts():       you will get all Part-objects, which have (at least) this supplier
     *
     *     To get the DevicePart-objects of a Device, there is the method Device::get_parts().
     *          (This method here allows only to get Part objects, but a Device has DevicePart objects!)
     *
     *                                              @li example: "id_manufacturer", "id_category", ...
     * @param boolean   $recursive                  if true, the parts of all subelements will be listed too
     * @param boolean   $hide_obsolete_and_zero     if true, obsolete parts with "instock == 0" will not be returned
     * @param int       $limit                      Limit the number of results, to this value.
     *                                              If set to 0, then the results are not limited.
     * @param int       $page                       Show the results of the page with given number.
     *                                              Use in combination with $limit.
     *
     * @return  Part[]   all parts as a one-dimensional array of Part objects
     *
     * @throws Exception if there was an error
     */
    protected function getPartsForRowName(string $parts_rowname, bool $recursive = false, bool $hide_obsolete_and_zero = false, int $limit = 50, int $page = 1) : array
    {
        $this->current_user->tryDo(static::getPermissionName(), PartContainingPermission::LIST_PARTS);

        $subelements = array();

        if ($recursive) {
            $subelements = $this->getSubelements(true);
        }

        if (empty($this->parts)) {
            $this->parts = array();

            $query = 'SELECT parts.* FROM parts WHERE ' . $parts_rowname . '=?';
            $vals = array($this->getID());

            foreach ($subelements as $element) {
                $query = $query . " OR " . $parts_rowname . "=?";
                $vals[] = $element->getID();
            }

            $query = $query .
                ' ORDER BY name, description';

            if ($limit > 0 && $page > 0) {
                $query .= " LIMIT " . (($page - 1) * $limit) . ", $limit";
            }

            //$query_data = $this->database->query($query);
            $query_data = $this->database->query($query, $vals);

            foreach ($query_data as $row) {
                $this->parts[] = new Part($this->database, $this->current_user, $this->log, $row['id'], $row);
            }
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


    /**
     * Get all parts of this element
     *
     * @par Examples:
     *  - $category->get_Parts():       you will get all Part-objects in this category
     *  - $supplier->getParts():       you will get all Part-objects, which have (at least) this supplier
     *
     *     To get the DevicePart-objects of a Device, there is the method Device::get_parts().
     *          (This method here allows only to get Part objects, but a Device has DevicePart objects!)
     *
     *                                              @li example: "id_manufacturer", "id_category", ...
     * @param boolean   $recursive                  if true, the parts of all subelements will be listed too
     * @param boolean   $hide_obsolete_and_zero     if true, obsolete parts with "instock == 0" will not be returned
     * @param int       $limit                      Limit the number of results, to this value.
     *                                              If set to 0, then the results are not limited.
     * @param int       $page                       Show the results of the page with given number.
     *                                              Use in combination with $limit.
     *
     * @return  Part[]   all parts as a one-dimensional array of Part objects
     *
     * @throws Exception if there was an error
     */
    abstract public function getPartsCount(bool $recursive = false) : int;

    /**
     * Return the number of all parts in this PartsContainingDBElement
     * @param boolean $recursive                if true, the parts of all subcategories will be listed too
     * @return int The number of parts of this PartContainingDBElement
     */
    public function getPartsCountForRowName(string $rowname, $recursive)
    {
        $this->current_user->tryDo(static::getPermissionName(), PartContainingPermission::LIST_PARTS);

        $subelements = array();

        if ($recursive) {
            $subelements = $this->getSubelements(true);
        }

        $query = 'SELECT count(id) AS count FROM parts WHERE '.$rowname.'=?';
        $vals = array($this->getID());

        foreach ($subelements as $element) {
            $query = $query . " OR ".$rowname."=?";
            $vals[] = $element->getID();
        }

        $query = $query.
            ' ORDER BY name, description';

        $query_data = $this->database->query($query, $vals);

        return $query_data[0]['count'];
    }

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
    public static function usort_compare(Part $part_1, Part $part_2) : int
    {
        if ($part_1->getName() != $part_2->getName()) {
            return strcasecmp($part_1->getName(), $part_2->getName());
        } else { // names are identical, so we compare the description of the parts
            return strcasecmp($part_1->getDescription(), $part_2->getDescription());
        }
    }
}
