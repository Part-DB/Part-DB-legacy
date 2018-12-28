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
use PartDB\Exceptions\DatabaseException;
use PartDB\Exceptions\TableNotExistingException;
use PartDB\PartProperty\PartNameRegEx;
use PartDB\Permissions\PermissionManager;

/**
 * @file Category.php
 * @brief class Category
 *
 * @class Category
 * All elements of this class are stored in the database table "categories".
 */
class Category extends Base\PartsContainingDBElement implements Interfaces\IAPIModel, Interfaces\ISearchable
{

    const TABLE_NAME = 'categories';

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
     * @throws \PartDB\Exceptions\TableNotExistingException If the table is not existing in the DataBase
     * @throws \PartDB\Exceptions\DatabaseException If an error happening during Database AccessDeniedException
     * @throws \PartDB\Exceptions\ElementNotExistingException If no such element exists in DB.
     */
    public function __construct(Database &$database, User &$current_user, Log &$log, int $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $data);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the "disable footprints" attribute
     *
     * @param boolean $including_parents @li If true, this method will return a "true" if at least
     *                                              one parent category has set "disable_footprints == true"
     * @li If false, this method will only return that value
     *                                              which is stored in the database
     *
     * @return boolean          "disable footprints" attribute
     * @throws TableNotExistingException
     * @throws DatabaseException
     */
    public function getDisableFootprints(bool $including_parents = false) : bool
    {
        if ($including_parents) {
            $parent_id = $this->getID();

            while ($parent_id > 0) {
                $category = Category::getInstance($this->database, $this->current_user, $this->log, $parent_id);
                $parent_id = $category->getParentID();

                if ($category->getDisableFootprints()) {
                    return true;
                }
            }

            return false;
        } else {
            return (bool) $this->db_data['disable_footprints'];
        }
    }

    /**
     * Get the "disable manufacturers" attribute
     *
     * @param boolean $including_parents @li If true, this method will return a "true" if at least
     *                                              one parent category has set "disable_manufacturers == true"
     * @li If false, this method will only return that value
     *                                              which is stored in the database
     *
     * @return boolean          the "disable manufacturers" attribute
     * @throws DatabaseException
     * @throws TableNotExistingException
     */
    public function getDisableManufacturers(bool $including_parents = false) : bool
    {
        if ($including_parents) {
            $parent_id = $this->getID();

            while ($parent_id > 0) {
                $category = Category::getInstance($this->database, $this->current_user, $this->log, $parent_id);
                $parent_id = $category->getParentID();

                if ($category->getDisableManufacturers()) {
                    return true;
                }
            }

            return false;
        } else {
            return $this->db_data['disable_manufacturers'];
        }
    }

    /**
     *  Get the "disable automatic datasheets" attribute
     *
     * @param boolean $including_parents @li If true, this method will return a "true" if at least
     *                                              one parent category has set "disable_autodatasheets == true"
     * @li If false, this method will only return that value
     *                                              which is stored in the database
     *
     * @return boolean          the "disable automatic datasheets" attribute
     * @throws DatabaseException
     */
    public function getDisableAutodatasheets(bool $including_parents = false) : bool
    {
        if ($including_parents) {
            $parent_id = $this->getID();

            while ($parent_id > 0) {
                $category = Category::getInstance($this->database, $this->current_user, $this->log, $parent_id);
                $parent_id = $category->getParentID();

                if ($category->getDisableAutodatasheets()) {
                    return true;
                }
            }

            return false;
        } else {
            return (bool) $this->db_data['disable_autodatasheets'];
        }
    }

    /**
     *  Get the "disable automatic properties" attribute
     *
     * @param boolean $including_parents @li If true, this method will return a "true" if at least
     *                                              one parent category has set "disable_properties == true"
     * @li If false, this method will only return that value
     *                                              which is stored in the database
     *
     * @return boolean          the "disable automatic properties" attribute
     * @throws DatabaseException
     */
    public function getDisableProperties(bool $including_parents = false) : bool
    {
        if ($including_parents) {
            $parent_id = $this->getID();

            while ($parent_id > 0) {
                $category = Category::getInstance($this->database, $this->current_user, $this->log, $parent_id);
                $parent_id = $category->getParentID();

                if ($category->getDisableProperties()) {
                    return true;
                }
            }

            return false;
        } else {
            return (bool) $this->db_data['disable_properties'];
        }
    }

    /**
     *  Get the "default description" attribute
     *
     * @param boolean $including_parents @li If true, this method will return the first non empty value from parents
     *                                              if this category has no own value
     * @li If false, this method will only return that value
     *                                              which is stored in the database
     *
     * @return string          the "default description" attribute
     * @throws DatabaseException
     */
    public function getDefaultDescription(bool $including_parents = false, bool $show_escape = true) : string
    {
        if ($including_parents && empty($this->getDefaultDescription())) {
            $parent_id = $this->getID();

            while ($parent_id > 0) {
                $category = Category::getInstance($this->database, $this->current_user, $this->log, $parent_id);
                $parent_id = $category->getParentID();

                if (!empty($category->getDefaultDescription())) {
                    if ($category->getDefaultDescription() == "@@") {
                        break;
                    }
                    return $category->getDefaultDescription();
                }
            }

            return "";
        } else {
            if (isset($this->db_data['default_description'])) {
                if ($show_escape || $this->db_data['default_description'] !== "@@") {
                    return $this->db_data['default_description'];
                }
            }

            return "";
        }
    }

    /**
     *  Get the "default comment" attribute
     *
     * @param boolean $including_parents @li If true, this method will return the first non empty value from parents
     *                                              if this category has no own value
     * @li If false, this method will only return that value
     *                                              which is stored in the database
     *
     * @return string          the "default comment" attribute
     * @throws DatabaseException
     */
    public function getDefaultComment(bool $including_parents = false, bool $show_escape = true) : string
    {
        if ($including_parents && empty($this->getDefaultComment()) && $this->getDefaultComment() != "@@") {
            $parent_id = $this->getID();

            while ($parent_id > 0) {
                $category = Category::getInstance($this->database, $this->current_user, $this->log, $parent_id);
                $parent_id = $category->getParentID();

                if (!empty($category->getDefaultComment())) {
                    if ($category->getDefaultComment() == "@@") {
                        break;
                    }
                    return $category->getDefaultComment();
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
     * @li If false, this method will only return that value
     *                                              which is stored in the database
     * @param bool $show_escape If true, the escape code "@@" will be returned, otherwise it will be "" (empty string).
     * @return string  The partname_hint attribute
     * @throws DatabaseException
     */
    public function getPartnameHint(bool $including_parents = false, bool $show_escape = true) : string
    {
        if ($including_parents && empty($this->getPartnameHint())) {
            $parent_id = $this->getID();

            while ($parent_id > 0) {
                $category = Category::getInstance($this->database, $this->current_user, $this->log, $parent_id);
                $parent_id = $category->getParentID();

                if (!empty($category->getPartnameHint())) {
                    if ($category->getPartnameHint() == "@@") {
                        break;
                    }
                    return $category->getPartnameHint();
                }
            }

            return "";
        } else {
            if ($show_escape || $this->db_data['partname_hint'] !== "@@") {
                if (isset($db_data['partname_hint'])) {
                    return $this->db_data['partname_hint'];
                }
            } else {
                return "";
            }
        }

        return "";
    }

    /**
     * Get the Regular expression the name of parts, which are part of this category, must have.
     * @param bool $including_parents @li If true, this method will return the first non empty value from parents
     *                                              if this category has no own value
     * @li If false, this method will only return that value
     *                                              which is stored in the database
     * @param bool $show_escape If true, the escape code "@@" will be returned, otherwise it will be "" (empty string).
     * @return string  The partname_hint attribute
     * @throws DatabaseException
     */
    public function getPartnameRegexRaw(bool $including_parents = false, bool $show_escape = true) : string
    {
        if ($including_parents && empty($this->getPartnameRegexRaw())) {
            $parent_id = $this->getID();

            while ($parent_id > 0) {
                $category = Category::getInstance($this->database, $this->current_user, $this->log, $parent_id);
                $parent_id = $category->getParentID();

                if (!empty($category->getPartnameRegexRaw())) {
                    if ($category->getPartnameRegexRaw() == "@@") {
                        break;
                    }
                    return $category->getPartnameRegexRaw();
                }
            }

            return "";
        } else {
            if ($show_escape || $this->db_data['partname_regex'] !== "@@") {
                if (isset($this->db_data['partname_regex'])) {
                    return $this->db_data['partname_regex'];
                }
            }
            return "";
        }
    }

    /**
     * Gets the regex of this Category.
     * @param bool $including_parents
     * @return string The regex.
     * @throws Exception
     */
    public function getPartnameRegex(bool $including_parents = true) : string
    {
        return $this->getPartnameRegexObj($including_parents)->getRegex();
    }

    /**
     * Gets a PartNameRegEx element
     * @param bool $including_parents
     * @return PartNameRegEx
     * @throws Exception
     */
    public function getPartnameRegexObj(bool $including_parents = true) : PartNameRegEx
    {
        $str = $this->getPartnameRegexRaw($including_parents);
        return new PartNameRegEx($str);
    }

    /**
     * Check if the partname is valid.
     * @param $name string The name which should be checked.
     * @param bool $including_parents
     * @return bool True if the partname is valid.
     * @throws Exception
     */
    public function checkPartname(string $name, bool $including_parents = true) : bool
    {
        $obj = $this->getPartnameRegexObj($including_parents);
        return $obj->checkName($name);
    }

    /**
     *  Get all parts from this element
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
    public function getParts(bool $recursive = false, bool $hide_obsolete_and_zero = false, int $limit = 50, int $page = 1) : array
    {
        return parent::getPartsForRowName('id_category', $recursive, $hide_obsolete_and_zero, $limit, $page);
    }
    /**
     * Return the number of all parts in this PartsContainingDBElement
     * @param boolean $recursive                if true, the parts of all subcategories will be listed too
     * @return int The number of parts of this PartContainingDBElement
     */
    public function getPartsCount(bool $recursive = false) : int
    {
        return parent::getPartsCountForRowName('id_category', $recursive);
    }



    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     *  Set the "disable footprints" attribute
     *
     * @param boolean $new_disable_footprints           the new value
     *
     * @throws Exception if there was an error
     */
    public function setDisableFootprints(bool $new_disable_footprints)
    {
        $this->setAttributes(array('disable_footprints' => $new_disable_footprints));
    }

    /**
     *  Set the "disable manufacturers" attribute
     *
     * @param boolean $new_disable_manufacturers        the new value
     *
     * @throws Exception if there was an error
     */
    public function setDisableManufacturers(bool $new_disable_manufacturers)
    {
        $this->setAttributes(array('disable_manufacturers' => $new_disable_manufacturers));
    }

    /**
     *  Set the "disable automatic datasheets" attribute
     *
     * @param boolean $new_disable_autodatasheets        the new value
     *
     * @throws Exception if there was an error
     */
    public function setDisableAutodatasheets(bool $new_disable_autodatasheets)
    {
        $this->setAttributes(array('disable_autodatasheets' => $new_disable_autodatasheets));
    }

    /**
     *  Set the "disable automatic properties" attribute
     *
     * @param boolean $new_disable_properties        the new value
     *
     * @throws Exception if there was an error
     */
    public function setDisableProperties(bool $new_disable_properties)
    {
        $this->setAttributes(array('disable_properties' => $new_disable_properties));
    }

    /**
     *  Set the "default description" attribute
     * @param string $new_default_description the new value
     * @throws Exception if there was an error
     */
    public function setDefaultDescription(string $new_default_description)
    {
        $this->setAttributes(array('default_description' => $new_default_description));
    }

    /**
     *  Set the "default comment" attribute
     * @param string $new_default_comment the new value
     * @throws Exception if there was an error
     */
    public function setDefaultComment(string $new_default_comment)
    {
        $this->setAttributes(array('default_comment' => $new_default_comment));
    }

    /**
     * Set the "partname_hint" attribute
     * @param string $new_partname_hint the new value
     * @throws Exception if there was an error
     */
    public function setPartnameHint(string $new_partname_hint)
    {
        $this->setAttributes(array('partname_hint' => $new_partname_hint));
    }

    /**
     *  Set the "partname_regex" attribute
     * @param string $new_default_comment the new value
     * @throws Exception if there was an error
     */
    public function setPartnameRegex(string $new_partname_regex)
    {
        $this->setAttributes(array('partname_regex' => $new_partname_regex));
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * @copydoc DBElement::check_values_validity()
     * @throws Exception
     */
    public static function checkValuesValidity(Database &$database, User &$current_user, Log  &$log, array &$values, bool $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        settype($values['disable_footprints'], 'boolean');
        settype($values['disable_manufacturers'], 'boolean');
        settype($values['disable_autodatasheets'], 'boolean');
    }

    /**
     *  Create a new category
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
     * @return Base\PartsContainingDBElement|Category
     *
     * @throws Exception    if (this combination of) values is not valid
     * @throws Exception    if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(
        Database &$database,
        User &$current_user,
        Log &$log,
        string $name,
        int $parent_id,
        bool $disable_footprints = false,
        bool $disable_manufacturers = false,
        bool $disable_autodatasheets = false,
        bool $disable_properties = false,
        string $default_description = "",
        string $default_comment = "",
        string $comment = ""
    ) {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            array(  'name'                      => $name,
                'parent_id'                 => $parent_id,
                'disable_footprints'        => $disable_footprints,
                'disable_manufacturers'     => $disable_manufacturers,
                'disable_autodatasheets'    => $disable_autodatasheets,
                'disable_properties'        => $disable_properties,
                'default_description'       => $default_description,
                'default_comment'           => $default_comment,
                "comment"                   => $comment)
        );
    }

    /**
     * Returns the ID as an string, defined by the element class.
     * This should have a form like P000014, for a part with ID 14.
     * @return string The ID as a string;
     */
    public function getIDString(): string
    {
        return "C" . sprintf("%09d", $this->getID());
    }

    /**
     * Returns a Array representing the current object.
     * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
     * @return array A array representing the current object.
     * @throws Exception
     * @throws Exception
     */
    public function getAPIArray(bool $verbose = false) : array
    {
        $values = array( "id" => $this->getID(),
            "name" => $this->getName(),
            "fullpath" => $this->getFullPath("/"),
            "parentid" => $this->getParentID(),
            "level" => $this->getLevel()
        );

        if ($verbose == true) {
            $ver = array("disable_footprints" => $this->getDisableFootprints() == true,
                "disable_manufacturers" => $this->getDisableManufacturers() == true,
                "disable_autodatasheets" => $this->getDisableAutodatasheets() == true,
                "disable_properties" => $this->getDisableProperties() == true,
                "default_description" => $this->getDefaultDescription(),
                "default_comment" => $this->getDefaultComment());
            $values = array_merge($values, $ver);
        }

        return $values;
    }

    /**
     * Gets the permission name for control access to this StructuralDBElement
     * @return string The name of the permission for this StructuralDBElement.
     */
    protected static function getPermissionName()
    {
        return PermissionManager::CATEGORIES;
    }
}
