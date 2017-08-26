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
        protected $parts = NULL;

        /********************************************************************************
        *
        *   Constructor / Destructor / reset_attributes()
        *
        *********************************************************************************/

        /**
         * @brief Constructor
         *
         * @note  It's allowed to create an object with the ID 0 (for the root element).
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
        public function __construct(&$database, &$current_user, &$log, $tablename, $id)
        {
            parent::__construct($database, $current_user, $log, $tablename, $id);
        }

        /**
         * @copydoc DBElement::reset_attributes()
         */
        public function reset_attributes($all = false)
        {
            $this->parts = NULL;

            parent::reset_attributes($all);
        }

        /********************************************************************************
        *
        *   Basic Methods
        *
        *********************************************************************************/

        /**
         * @brief Delete this element
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
            try
            {
                $transaction_id = $this->database->begin_transaction(); // start transaction

                $parts = $this->get_parts('id_category');

                if (count($parts) > 0)
                    throw new Exception('Das Element enthält noch '.count($parts).' Bauteile!');

                parent::delete($delete_recursive, $delete_files_from_hdd);

                $this->database->commit($transaction_id); // commit transaction
            }
            catch (Exception $e)
            {
                $this->database->rollback(); // rollback transaction

                // restore the settings from BEFORE the transaction
                $this->reset_attributes();

                throw new Exception("Das Element \"".$this->get_name()."\" konnte nicht gelöscht werden!\nGrund: ".$e->getMessage());
            }
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get all parts of this element
         *
         * @par Examples:
         *  - $category->get_parts():       you will get all Part-objects in this category
         *  - $supplier->get_parts():       you will get all Part-objects, which have (at least) this supplier
         *
         * @note    To get the DevicePart-objects of a Device, there is the method Device::get_parts().
         *          (This method here allows only to get Part objects, but a Device has DevicePart objects!)
         *
         * @param string    $parts_rowname              @li this is the name of the table row of the parts table,
         *                                                  where the ID of this element is located (example: 'id_category')
         *                                              @li example: "id_manufacturer", "id_category", ...
         * @param boolean   $recursive                  if true, the parts of all subelements will be listed too
         * @param boolean   $hide_obsolete_and_zero     if true, obsolete parts with "instock == 0" will not be returned
         *
         * @retval array    all parts as a one-dimensional array of Part objects
         *
         * @throws Exception if there was an error
         */
        public function get_parts($parts_rowname, $recursive = false, $hide_obsolete_and_zero = false)
        {
        /*
            if ( ! is_array($this->parts))
            {
                $this->parts = array();

                $query = 'SELECT id FROM parts WHERE '.$parts_rowname.'=? ORDER BY name, description';
                $query_data = $this->database->query($query, array($this->get_id()));

                foreach ($query_data as $row)
                    $this->parts[] = new Part($this->database, $this->current_user, $this->log, $row['id']);
            }

            $parts = $this->parts;

            if ($hide_obsolete_and_zero)
            {
                // remove obsolete parts from array
                $parts = array_values(array_filter($parts, function($part) {return (( ! $part->get_obsolete()) || ($part->get_instock() > 0));}));
            }

            if ($recursive)
            {
                $subelements = $this->get_subelements(false);

                foreach ($subelements as $element) {
                    $i = $element->get_id();

                    $parts = array_merge($parts, $element->get_parts(true, $hide_obsolete_and_zero));
                }
                usort($parts, 'PartsContainingDBElement::usort_compare'); // Sort all parts by their names and descriptions
            }

            return $parts;
            */

            $subelements = array();

            if ($recursive)
            {
                $subelements = $this->get_subelements(true);
            }

            if ( is_null($this->parts) || ! is_array($this->parts))
            {
                $this->parts = array();

                /*
                $query = 'SELECT id FROM parts WHERE '.$parts_rowname.'= '. $this->get_id();

                foreach($subelements as $element)
                {
                    $query = $query . " OR ".$parts_rowname."= ".$element->get_id();
                }
                */
                $query = 'SELECT parts.* FROM parts WHERE '.$parts_rowname.'=?';
                $vals = array($this->get_id());

                foreach($subelements as $element)
                {
                    $query = $query . " OR ".$parts_rowname."=?";
                    $vals[] = $element->get_id();
                }



                $query = $query.
                    ' ORDER BY name, description';
                //$query_data = $this->database->query($query);
                $query_data = $this->database->query($query, $vals);

                foreach ($query_data as $row)
                    $this->parts[] = new Part($this->database, $this->current_user, $this->log, $row['id'], $row);

                usort($this->parts, 'PartsContainingDBElement::usort_compare');
            }

            $parts = $this->parts;

            if ($hide_obsolete_and_zero)
            {
                // remove obsolete parts from array
                $parts = array_values(array_filter($parts, function($part) {return (( ! $part->get_obsolete()) || ($part->get_instock() > 0));}));
            }

            return $parts;


        }

        /**
         * @brief Compare function for "usort()"
         *
         * From php.net:    The comparison function must return an integer less than,
         *                  equal to, or greater than zero if the first argument is considered
         *                  to be respectively less than, equal to, or greater than the second.
         *
         * @param Part $part_1      The Part Object #1
         * @param Part $part_2      The Part Object #2
         *
         * @retval integer
         */
        static function usort_compare($part_1, $part_2)
        {
            if ($part_1->get_name() != $part_2->get_name())
                return strcasecmp($part_1->get_name(), $part_2->get_name());
            else // names are identical, so we compare the description of the parts
                return strcasecmp($part_1->get_description(), $part_2->get_description());
        }

    }

