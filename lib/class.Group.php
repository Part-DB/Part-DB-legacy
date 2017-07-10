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

    /**
     * @file class.Group.php
     * @brief class Group
     * 
     * @class Group
     * @brief All elements of this class are stored in the database table "groups".
     * @author kami89
     * 
     * @todo    This class is not really complete ;-)
     */
    class Group extends StructuralDBElement
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

        /** (array) All users of this group as a one-dimensional array of User objects */
        private $users = NULL;

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
         * @param integer   $id             ID of the group we want to get
         * 
         * @throws Exception    if there is no such group in the database
         * @throws Exception    if there was an error
         */
        public function __construct(&$database, &$current_user, &$log, $id)
        { 
            //parent::__construct($database, $current_user, $log, 'groups', $id);
        }

        /**
         * @copydoc DBElement::reset_attributes()
         */
        public function reset_attributes($all = false)
        {
            $this->users = NULL;

            parent::reset_attributes($all);
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get all users of this group
         * 
         * @param boolean $recursive        if true, the users of all subgroups will be listed too
         *
         * @retval array        all users as a one-dimensional array of User objects,
         *                      sorted by their names
         * 
         * @throws Exception if there was an error
         */
        public function get_users($recursive = false)
        {
            if ( ! is_array($this->users))
            {
                $this->users = array();

                $query =    'SELECT id FROM users '.
                            'WHERE group_id=? ORDER BY name ASC';

                $query_data = $this->database->query($query, array($this->get_id()));

                foreach ($query_data as $row)
                    $this->users[] = new User($this->database, $this->current_user, $this->log, $row['id']);
            }

            if ($recursive)
            {
                $all_users = $this->users;
                $subgroups = $this->get_subelements(true);

                foreach ($subgroups as $group)
                    $all_users = array_merge($all_users, $group->get_users(true));

                return $all_users;
            }
            else
                return $this->users;
        }

        /********************************************************************************
        *
        *   Static Methods
        *
        *********************************************************************************/ 

        /**
         * @copydoc DBElement::check_values_validity()
         */
        public static function check_values_validity(&$database, &$current_user, &$log, &$values, $is_new, &$element = NULL)
        {
            // first, we let all parent classes to check the values
            parent::check_values_validity($database, $current_user, $log, $values, $is_new, $element);
            
            // TODO
        }

        /**
         * @brief Get count of groups
         *
         * @param Database &$database   reference to the Database-object
         *
         * @retval integer              count of groups
         * 
         * @throws Exception            if there was an error
         */
        public static function get_count(&$database)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            return $database->get_count_of_records('groups');
        }
    
    }
