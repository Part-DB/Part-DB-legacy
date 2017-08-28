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
    use PartDB\PartProperty\PartNameRegEx;

    /**
     * @file Category.php
     * @brief class Category
     *
     * @class Category
     * @brief All elements of this class are stored in the database table "categories".
     * @author kami89
     */
    class Category extends Base\PartsContainingDBElement implements Interfaces\IAPIModel
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
            if ($including_parents) {
                $parent_id = $this->get_id();

                while ($parent_id > 0) {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if ($category->get_disable_footprints()) {
                        return true;
                    }
                }

                return false;
            } else {
                return $this->db_data['disable_footprints'];
            }
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
            if ($including_parents) {
                $parent_id = $this->get_id();

                while ($parent_id > 0) {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if ($category->get_disable_manufacturers()) {
                        return true;
                    }
                }

                return false;
            } else {
                return $this->db_data['disable_manufacturers'];
            }
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
            if ($including_parents) {
                $parent_id = $this->get_id();

                while ($parent_id > 0) {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if ($category->get_disable_autodatasheets()) {
                        return true;
                    }
                }

                return false;
            } else {
                return $this->db_data['disable_autodatasheets'];
            }
        }

        /**
         * @brief Get the "disable automatic properties" attribute
         *
         * @param boolean $including_parents        @li If true, this method will return a "true" if at least
         *                                              one parent category has set "disable_properties == true"
         *                                          @li If false, this method will only return that value
         *                                              which is stored in the database
         *
         * @retval boolean          the "disable automatic properties" attribute
         */
        public function get_disable_properties($including_parents = false)
        {
            if ($including_parents) {
                $parent_id = $this->get_id();

                while ($parent_id > 0) {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if ($category->get_disable_properties()) {
                        return true;
                    }
                }

                return false;
            } else {
                return $this->db_data['disable_properties'];
            }
        }

        /**
         * @brief Get the "default description" attribute
         *
         * @param boolean $including_parents        @li If true, this method will return the first non empty value from parents
         *                                              if this category has no own value
         *                                          @li If false, this method will only return that value
         *                                              which is stored in the database
         *
         * @retval string          the "default description" attribute
         */
        public function get_default_description($including_parents = false, $show_escape = true)
        {
            if ($including_parents && empty($this->get_default_description())) {
                $parent_id = $this->get_id();

                while ($parent_id > 0) {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if (!empty($category->get_default_description())) {
                        if ($category->get_default_description() == "@@") {
                            break;
                        }
                        return $category->get_default_description();
                    }
                }

                return "";
            } else {
                if ($show_escape || $this->db_data['default_description'] !== "@@") {
                    return $this->db_data['default_description'];
                } else {
                    return "";
                }
            }
        }

        /**
         * @brief Get the "default comment" attribute
         *
         * @param boolean $including_parents        @li If true, this method will return the first non empty value from parents
         *                                              if this category has no own value
         *                                          @li If false, this method will only return that value
         *                                              which is stored in the database
         *
         * @retval string          the "default comment" attribute
         */
        public function get_default_comment($including_parents = false, $show_escape = true)
        {
            if ($including_parents && empty($this->get_default_comment()) && $this->get_default_comment() != "@@") {
                $parent_id = $this->get_id();

                while ($parent_id > 0) {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if (!empty($category->get_default_comment())) {
                        if ($category->get_default_comment() == "@@") {
                            break;
                        }
                        return $category->get_default_comment();
                    }
                }

                return "";
            } elseif ($show_escape || $this->db_data['default_comment'] !== "@@") {
                return $this->db_data['default_comment'];
            } else {
                return "";
            }
        }

        /**
         * Get the Hint how to format the name of parts, which are part of this category.
         * @param bool $including_parents @li If true, this method will return the first non empty value from parents
         *                                              if this category has no own value
         *                                 @li If false, this method will only return that value
         *                                              which is stored in the database
         * @param bool $show_escape If true, the escape code "@@" will be returned, otherwise it will be "" (empty string).
         * @return string  The partname_hint attribute
         */
        public function get_partname_hint($including_parents = false, $show_escape = true)
        {
            if ($including_parents && empty($this->get_partname_hint())) {
                $parent_id = $this->get_id();

                while ($parent_id > 0) {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if (!empty($category->get_partname_hint())) {
                        if ($category->get_partname_hint() == "@@") {
                            break;
                        }
                        return $category->get_partname_hint();
                    }
                }

                return "";
            } else {
                if ($show_escape || $this->db_data['partname_hint'] !== "@@") {
                    return $this->db_data['partname_hint'];
                } else {
                    return "";
                }
            }
        }

        /**
         * Get the Regular expression the name of parts, which are part of this category, must have.
         * @param bool $including_parents @li If true, this method will return the first non empty value from parents
         *                                              if this category has no own value
         *                                 @li If false, this method will only return that value
         *                                              which is stored in the database
         * @param bool $show_escape If true, the escape code "@@" will be returned, otherwise it will be "" (empty string).
         * @return string  The partname_hint attribute
         */
        public function get_partname_regex_raw($including_parents = false, $show_escape = true)
        {
            if ($including_parents && empty($this->get_partname_regex_raw())) {
                $parent_id = $this->get_id();

                while ($parent_id > 0) {
                    $category = new Category($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $category->get_parent_id();

                    if (!empty($category->get_partname_regex_raw())) {
                        if ($category->get_partname_regex_raw() == "@@") {
                            break;
                        }
                        return $category->get_partname_regex_raw();
                    }
                }

                return "";
            } else {
                if ($show_escape || $this->db_data['partname_regex'] !== "@@") {
                    return $this->db_data['partname_regex'];
                } else {
                    return "";
                }
            }
        }

        /**
         * Gets the regex of this Category.
         * @param bool $including_parents
         */
        public function get_partname_regex($including_parents = true)
        {
            return $this->get_partname_regex_obj($including_parents)->get_regex();
        }

        /**
         * Gets a PartNameRegEx element
         * @param bool $including_parents
         * @return PartNameRegEx
         */
        public function get_partname_regex_obj($including_parents = true)
        {
            $str = $this->get_partname_regex_raw($including_parents);
            return new PartNameRegEx($str);
        }

        /**
         * Check if the partname is valid.
         * @param $name string The name which should be checked.
         * @param bool $including_parents
         * @return bool True if the partname is valid.
         */
        public function check_partname($name, $including_parents = true)
        {
            $obj = $this->get_partname_regex_obj($including_parents);
            return $obj->check_name($name);
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

        /**
         * @brief Set the "disable automatic properties" attribute
         *
         * @param boolean $new_disable_properties        the new value
         *
         * @throws Exception if there was an error
         */
        public function set_disable_properties($new_disable_properties)
        {
            $this->set_attributes(array('disable_properties' => $new_disable_properties));
        }

        /**
         * @brief Set the "default description" attribute
         * @param string $new_default_description the new value
         * @throws Exception if there was an error
         */
        public function set_default_description($new_default_description)
        {
            $this->set_attributes(array('default_description' => $new_default_description));
        }

        /**
         * @brief Set the "default comment" attribute
         * @param string $new_default_comment the new value
         * @throws Exception if there was an error
         */
        public function set_default_comment($new_default_comment)
        {
            $this->set_attributes(array('default_comment' => $new_default_comment));
        }

        /**
         * Set the "partname_hint" attribute
         * @param string $new_partname_hint the new value
         * @throws Exception if there was an error
         */
        public function set_partname_hint($new_partname_hint)
        {
            $this->set_attributes(array('partname_hint' => $new_partname_hint));
        }

        /**
         * @brief Set the "partname_regex" attribute
         * @param string $new_default_comment the new value
         * @throws Exception if there was an error
         */
        public function set_partname_regex($new_partname_regex)
        {
            $this->set_attributes(array('partname_regex' => $new_partname_regex));
        }

        /********************************************************************************
        *
        *   Static Methods
        *
        *********************************************************************************/

        /**
         * @copydoc DBElement::check_values_validity()
         */
        public static function check_values_validity(&$database, &$current_user, &$log, &$values, $is_new, &$element = null)
        {
            // first, we let all parent classes to check the values
            parent::check_values_validity($database, $current_user, $log, $values, $is_new, $element);

            settype($values['disable_footprints'], 'boolean');
            settype($values['disable_manufacturers'], 'boolean');
            settype($values['disable_autodatasheets'], 'boolean');
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
            if (!$database instanceof Database) {
                throw new Exception(_('$database ist kein Database-Objekt!'));
            }

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
         * @param boolean   $disable_properties         if true, all parts in the new category won't have a property table
         * @param string    $default_description        The default description of parts in the new category.
         * @param string    $default_comment            The default comment of parts in the new category.
         *
         * @retval Category     the new category
         *
         * @throws Exception    if (this combination of) values is not valid
         * @throws Exception    if there was an error
         *
         * @see DBElement::add()
         */
        public static function add(
            &$database,
            &$current_user,
            &$log,
            $name,
            $parent_id,
                                    $disable_footprints = false,
            $disable_manufacturers = false,
                                    $disable_autodatasheets = false,
            $disable_properties = false,
                                    $default_description = "",
            $default_comment = ""
        ) {
            return parent::add(
                $database,
                $current_user,
                $log,
                'categories',
                                array(  'name'                      => $name,
                                        'parent_id'                 => $parent_id,
                                        'disable_footprints'        => $disable_footprints,
                                        'disable_manufacturers'     => $disable_manufacturers,
                                        'disable_autodatasheets'    => $disable_autodatasheets,
                                        'disable_properties'        => $disable_properties,
                                        'default_description'       => $default_description,
                                        'default_comment'           => $default_comment)
            );
        }

        /**
         * @copydoc NamedDBElement::search()
         */
        public static function search(&$database, &$current_user, &$log, $keyword, $exact_match = false)
        {
            return parent::search($database, $current_user, $log, 'categories', $keyword, $exact_match);
        }

        /**
         * Returns a Array representing the current object.
         * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
         * @return array A array representing the current object.
         */
        public function get_API_array($verbose = false)
        {
            $values = array( "id" => $this->get_id(),
                "name" => $this->get_name(),
                "fullpath" => $this->get_full_path("/"),
                "parentid" => $this->get_parent_id(),
                "level" => $this->get_level()
            );

            if ($verbose == true) {
                $ver = array("disable_footprints" => $this->get_disable_footprints() == true,
                    "disable_manufacturers" => $this->get_disable_manufacturers() == true,
                    "disable_autodatasheets" => $this->get_disable_autodatasheets() == true,
                    "disable_properties" => $this->get_disable_properties() == true,
                    "default_description" => $this->get_default_description(),
                    "default_comment" => $this->get_default_comment());
                $values = array_merge($values, $ver);
            }

            return $values;
        }
    }
