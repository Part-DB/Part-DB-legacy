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

    $Id$

    [DATE]      [NICKNAME]      [CHANGES]
    2012-08-??  kami89          - created
    2012-09-27  kami89          - added doxygen comments
    2012-11-03  kami89          - added attribute "disable_autodatasheets"
                                - added method "check_values_validity()"
*/

    /**
     * @file class.Category.php
     * @brief class Category
     *
     * @class Category
     * @brief All elements of this class are stored in the database table "categories".
     * @author kami89
     */
    class Category extends PartsContainingDBElement
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
         * @param Database  &$database          reference to the Database-object
         * @param User      &$current_user      reference to the current user which is logged in
         * @param Log       &$log               reference to the Log-object
         * @param integer   $id                 ID of the category we want to get
         *
         * @throws Exception        if there is no such category in the database
         * @throws Exception        if there was an error
         */
        public function __construct(&$database, &$current_user, &$log, $id)
        {
            parent::__construct($database, $current_user, $log, 'categories', $id);
        }

        public function get_json_array($verbose=false)
        {
            if($verbose)
            {
                $ret = array( 'id' => $this->get_id(),
                          'name' => $this->get_name(),
                          'path' => $this->get_full_path(),
                          'parent_id' => $this->get_parent_id(),
                          'level' => $this->get_level()
                    );
            }
            else
            {
                $ret = array( 'id' => $this->get_id(),
                        'name' => $this->get_name(),
                        'path' => $this->get_full_path()
                  );
            }

            return $ret;
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get the "disable footprints" attribute
         *
         * @param boolean $including_parents        @li If true, this method will return a "true" if at least
         *                                              one parent category has set "disable_footprints == true"
         *                                          @li If false, this method will only return that value
         *                                              which is stored in the database
         *
         * @retval boolean          "disable footprints" attribute
         */
        public function get_disable_footprints($including_parents = false)
        {
            if ($including_parents)
            {
                $parent_id = $this->get_id();

                while ($parent_id > 0)
                {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if ($category->get_disable_footprints())
                        return true;
                }

                return false;
            }
            else
                return $this->db_data['disable_footprints'];
        }

        /**
         * @brief Get the "disable manufacturers" attribute
         *
         * @param boolean $including_parents        @li If true, this method will return a "true" if at least
         *                                              one parent category has set "disable_manufacturers == true"
         *                                          @li If false, this method will only return that value
         *                                              which is stored in the database
         *
         * @retval boolean          the "disable manufacturers" attribute
         */
        public function get_disable_manufacturers($including_parents = false)
        {
            if ($including_parents)
            {
                $parent_id = $this->get_id();

                while ($parent_id > 0)
                {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if ($category->get_disable_manufacturers())
                        return true;
                }

                return false;
            }
            else
                return $this->db_data['disable_manufacturers'];
        }

        /**
         * @brief Get the "disable automatic datasheets" attribute
         *
         * @param boolean $including_parents        @li If true, this method will return a "true" if at least
         *                                              one parent category has set "disable_autodatasheets == true"
         *                                          @li If false, this method will only return that value
         *                                              which is stored in the database
         *
         * @retval boolean          the "disable automatic datasheets" attribute
         */
        public function get_disable_autodatasheets($including_parents = false)
        {
            if ($including_parents)
            {
                $parent_id = $this->get_id();

                while ($parent_id > 0)
                {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if ($category->get_disable_autodatasheets())
                        return true;
                }

                return false;
            }
            else
                return $this->db_data['disable_autodatasheets'];
        }


        /**
         * @brief Get all parts from this element
         *
         * @param boolean $recursive                if true, the parts of all subcategories will be listed too
         * @param boolean $hide_obsolete_and_zero   if true, obsolete parts with "instock == 0" will not be returned
         *
         * @retval array        all parts as a one-dimensional array of Part-objects, sorted by their names
         *
         * @throws Exception if there was an error
         */
        public function get_parts($recursive = false, $hide_obsolete_and_zero = false)
        {
            return parent::get_parts('id_category', $recursive, $hide_obsolete_and_zero);
        }

        /********************************************************************************
        *
        *   Setters
        *
        *********************************************************************************/

        /**
         * @brief Set the "disable footprints" attribute
         *
         * @param boolean $new_disable_footprints           the new value
         *
         * @throws Exception if there was an error
         */
        public function set_disable_footprints($new_disable_footprints)
        {
            $this->set_attributes(array('disable_footprints' => $new_disable_footprints));
        }

        /**
         * @brief Set the "disable manufacturers" attribute
         *
         * @param boolean $new_disable_manufacturers        the new value
         *
         * @throws Exception if there was an error
         */
        public function set_disable_manufacturers($new_disable_manufacturers)
        {
            $this->set_attributes(array('disable_manufacturers' => $new_disable_manufacturers));
        }

        /**
         * @brief Set the "disable automatic datasheets" attribute
         *
         * @param boolean $new_disable_autodatasheets        the new value
         *
         * @throws Exception if there was an error
         */
        public function set_disable_autodatasheets($new_disable_autodatasheets)
        {
            $this->set_attributes(array('disable_autodatasheets' => $new_disable_autodatasheets));
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

            settype($values['disable_footprints'],      'boolean');
            settype($values['disable_manufacturers'],   'boolean');
            settype($values['disable_autodatasheets'],  'boolean');
        }

        /**
         * @brief Get the count of categories
         *
         * @param Database &$database   reference to the Database-object
         *
         * @retval integer              count of categories
         *
         * @throws Exception            if there was an error
         */
        public static function get_count(&$database)
        {
            if (get_class($database) != 'Database')
                throw new Exception(_('$database ist kein Database-Objekt!'));

            return $database->get_count_of_records('categories');
        }

        /**
         * @brief Create a new category
         *
         * @param Database  &$database                  reference to the database object
         * @param User      &$current_user              reference to the current user which is logged in
         * @param Log       &$log                       reference to the Log-object
         * @param string    $name                       the name of the new category (Category::set_name())
         * @param integer   $parent_id                  the parent ID of the new category (Category::set_parent_id())
         * @param boolean   $disable_footprints         if true, all parts in the new category can't have a footprint (Category::set_disable_footprints())
         * @param boolean   $disable_manufacturers      if true, all parts in the new category can't have a manufacturer (Category::set_disable_manufacturers())
         * @param boolean   $disable_autodatasheets     if true, all parts in the new category won't have automatic datasheets (Category::set_disable_autodatasheets())
         *
         * @retval Category     the new category
         *
         * @throws Exception    if (this combination of) values is not valid
         * @throws Exception    if there was an error
         *
         * @see DBElement::add()
         */
        public static function add(&$database, &$current_user, &$log, $name, $parent_id,
                                    $disable_footprints = false, $disable_manufacturers = false,
                                    $disable_autodatasheets = false)
        {
            return parent::add($database, $current_user, $log, 'categories',
                                array(  'name'                      => $name,
                                        'parent_id'                 => $parent_id,
                                        'disable_footprints'        => $disable_footprints,
                                        'disable_manufacturers'     => $disable_manufacturers,
                                        'disable_autodatasheets'    => $disable_autodatasheets));
        }

        /**
         * @copydoc NamedDBElement::search()
         */
        public static function search(&$database, &$current_user, &$log, $keyword, $exact_match = false)
        {
            return parent::search($database, $current_user, $log, 'categories', $keyword, $exact_match);
        }

    }

?>
