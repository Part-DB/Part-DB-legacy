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

    Changelog (sorted by date):
        [DATE]      [NICKNAME]      [CHANGES]
        2012-09-05  kami89          - created
        2012-09-27  kami89          - added doxygen comments
        2013-04-18  kami89          - changed class name from "FileType" to "AttachementType"
                                      (sorry - when I created this class I didn't find a better name...)
*/

    /**
     * @file class.AttachementType.php
     * @brief class AttachementType
     *
     * @class AttachementType
     * @brief All elements of this class are stored in the database table "attachement_types".
     * @author kami89
     */
    class AttachementType extends StructuralDBElement
    {

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
         * @param integer   $id             ID of the filetype we want to get
         *
         * @throws Exception    if there is no such attachement type in the database
         * @throws Exception    if there was an error
         */
        public function __construct(&$database, &$current_user, &$log, $id)
        {
            parent::__construct($database, $current_user, $log, 'attachement_types', $id);
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get all attachements ("Attachement" objects) with this type
         *
         * @retval array        all attachements with this type, as a one-dimensional array of Attachement-objects
         *                      (sorted by their names)
         *
         * @throws Exception if there was an error
         */
        public function get_attachements()
        {
            // the attribute $this->attachements is used from class "AttachementsContainingDBELement"
            if ( ! is_array($this->attachements))
            {
                $this->attachements = array();

                $query = 'SELECT id FROM attachements '.
                            'WHERE type_id=? '.
                            'ORDER BY name ASC';
                $query_data = $this->database->query($query, array($this->get_id()));

                //debug('temp', 'Anzahl gefundene Dateien: '.count($query_data));
                foreach ($query_data as $row)
                    $this->attachements[] = new Attachement($this->database, $this->current_user, $this->log, $row['id']);
            }

            return $this->attachements;
        }

        /********************************************************************************
        *
        *   Static Methods
        *
        *********************************************************************************/

        /**
         * @brief Get count of attachement types
         *
         * @param Database &$database   reference to the Database-object
         *
         * @retval integer              count of attachement types
         *
         * @throws Exception            if there was an error
         */
        public static function get_count(&$database)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            return $database->get_count_of_records('attachement_types');
        }

        /**
         * @brief Create a new attachement type
         *
         * @param Database  &$database          reference to the database onject
         * @param User      &$current_user      reference to the current user which is logged in
         * @param Log       &$log               reference to the Log-object
         * @param string    $name               the name of the new attachement type (see AttachementType::set_name())
         * @param integer   $parent_id          the parent ID of the new attachement type (see AttachementType::set_parent_id())
         *
         * @retval AttachementType      the new attachement type
         *
         * @throws Exception    if (this combination of) values is not valid
         * @throws Exception    if there was an error
         *
         * @see DBElement::add()
         */
        public static function add(&$database, &$current_user, &$log, $name, $parent_id)
        {
            return parent::add($database, $current_user, $log, 'attachement_types',
                                array(  'name'              => $name,
                                        'parent_id'         => $parent_id));
        }

    }

?>
