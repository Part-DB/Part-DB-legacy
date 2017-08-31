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
use PartDB\User;

/**
 * @file NamedDBElement.php
 * @brief class NamedDBElement
 *
 * @class NamedDBElement
 * All subclasses of this class have an attribute "name".
 * @author kami89
 */
abstract class NamedDBElement extends DBElement
{
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
     * @param string    $tablename                  the name of the database table where the element is located
     * @param integer   $id                         ID of the element we want to get
     * @param boolean   $allow_virtual_elements     @li if true, it's allowed to set $id to zero
     *                                                  (the StructuralDBElement needs this for the root element)
     *                                              @li if false, $id == 0 is not allowed (throws an Exception)
     *
     * @throws Exception    if there is no such element in the database
     * @throws Exception    if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $tablename, $id, $allow_virtual_elements = false, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, $tablename, $id, $allow_virtual_elements, $db_data);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the name
     *
     * @return string   the name of this element
     */
    public function getName()
    {
        return $this->db_data['name'];
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     * Change the name of this element
     *
     * @note    Spaces at the begin and at the end of the string will be removed
     *          automatically in NamedDBElement::check_values_validity().
     *          So you don't have to do this yourself.
     *
     * @param string $new_name      the new name
     *
     * @throws Exception if the new name is not valid (e.g. empty)
     * @throws Exception if there was an error
     */
    public function setName($new_name)
    {
        $this->setAttributes(array('name' => $new_name));
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * @copydoc DBElement::check_values_validity()
     */
    public static function checkValuesValidity(&$database, &$current_user, &$log, &$values, $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        // we trim the name (spaces at the begin or at the end of a name are ugly, so we remove them)
        $values['name'] = trim($values['name']);

        if (empty($values['name'])) { // empty names are not allowed!
            throw new Exception('Der neue Name ist leer, das ist nicht erlaubt!');
        }
    }

    /**
     * Search elements by name in the given table
     *
     * @param Database  &$database              reference to the database object
     * @param User      &$current_user          reference to the user which is logged in
     * @param Log       &$log                   reference to the Log-object
     * @param string    $keyword                the search string
     * @param boolean   $exact_match            @li If true, only records which matches exactly will be returned
     *                                          @li If false, all similar records will be returned
     *
     * @return array    all found elements as a one-dimensional array of objects,
     *                  sorted by their names
     *
     * @throws Exception if there was an error
     */
    protected static function searchTable(&$database, &$current_user, &$log, $tablename, $keyword, $exact_match)
    {
        if (strlen($keyword) == 0) {
            return array();
        }

        if (! $exact_match) {
            $keyword = str_replace('*', '%', $keyword);
            $keyword = '%'.$keyword.'%';
        }

        $query = 'SELECT * FROM '.$tablename.' WHERE name'.(($exact_match) ? '=' : ' LIKE ').'? ORDER BY name ASC';
        $query_data = $database->query($query, array($keyword));

        $objects = array();

        $classname = get_called_class();

        foreach ($query_data as $row) {
            $objects[] = new $classname($database, $current_user, $log, $row['id'], $row);
        }

        return $objects;
    }
}
