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
use PartDB\Interfaces\ISearchable;

/**
 * @file Storelocation.php
 * @brief class Storelocation
 *
 * @class Storelocation
 * All elements of this class are stored in the database table "storelocations".
 * @author kami89
 */
class Storelocation extends Base\PartsContainingDBElement implements Interfaces\IAPIModel, ISearchable
{
    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @note  It's allowed to create an object with the ID 0 (for the root element).
     *
     * @param Database  &$database      reference to the Database-object
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log           reference to the Log-object
     * @param integer   $id             ID of the storelocation we want to get
     *
     * @throws Exception if there is no such storelocation in the database
     * @throws Exception if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, 'storelocations', $id, $data);

        if ($id == 0) {
            // this is the root node
            $this->db_data['is_full'] = false;
            return;
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the "is full" attribute
     *
     * @note    "is_full == true" means that there is no more space in this storelocation.
     * @note    This attribute is only for information, it has no effect.
     *
     * @return boolean      @li true if the storelocation is full
     *                      @li false if the storelocation isn't full
     */
    public function getIsFull()
    {
        return $this->db_data['is_full'];
    }

    /**
     * Get all parts which are located in this storelocation
     *
     * @param boolean $recursive                if true, the parts of all sub-storelocations will be listed too
     * @param boolean $hide_obsolete_and_zero   if true, obsolete parts with "instock == 0" will not be returned
     *
     * @return array        all parts as a one-dimensional array of Part objects
     *
     * @throws Exception    if there was an error
     *
     * @see PartsContainingDBElement::getParts()
     */
    public function getParts($recursive = false, $hide_obsolete_and_zero = false)
    {
        return parent::getTableParts('id_storelocation', $recursive, $hide_obsolete_and_zero);
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     * Change the "is full" attribute of this storelocation
     *
     * @note    "is_full" = true means that there is no more space in this storelocation.
     * @note    This attribute is only for information, it has no effect.
     *
     * @param boolean $new_is_full      @li true means that the storelocation is full
     *                                  @li false means that the storelocation isn't full
     *
     * @throws Exception if there was an error
     */
    public function setIsFull($new_is_full)
    {
        $this->setAttributes(array('is_full' => $new_is_full));
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

        // set the datetype of the boolean attributes
        settype($values['is_full'], 'boolean');
    }

    /**
     *  Get count of storelocations
     *
     * @param Database &$database   reference to the Database-object
     *
     * @return integer              count of storelocations
     *
     * @throws Exception            if there was an error
     */
    public static function getCount(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        return $database->getCountOfRecords('storelocations');
    }

    /**
     *  Create a new storelocation
     *
     * @param Database  &$database      reference to the database onject
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log           reference to the Log-object
     * @param string    $name           the name of the new storelocation (see Storelocation::set_name())
     * @param integer   $parent_id      the parent ID of the new storelocation (see Storelocation::set_parent_id())
     * @param boolean   $is_full        the "is_full" attribute of the new storelocation (see Storelocation::set_is_full())
     *
     * @return Storelocation            the new storelocation
     *
     * @throws Exception if (this combination of) values is not valid
     * @throws Exception if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(&$database, &$current_user, &$log, $name, $parent_id, $is_full = false)
    {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            'storelocations',
            array(  'name'          => $name,
                'parent_id'     => $parent_id,
                'is_full'       => $is_full)
        );
    }

    /**
     * @copydoc NamedDBElement::search()
     */
    public static function search(&$database, &$current_user, &$log, $keyword, $exact_match = false)
    {
        return parent::searchTable($database, $current_user, $log, 'storelocations', $keyword, $exact_match);
    }

    /**
     * Returns a Array representing the current object.
     * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
     * @return array A array representing the current object.
     */
    public function getAPIArray($verbose = false)
    {
        $json =  array( "id" => $this->getID(),
            "name" => $this->getName(),
            "fullpath" => $this->getFullPath("/"),
            "parentid" => $this->getParentID(),
            "level" => $this->getLevel()
        );

        if ($verbose == true) {
            $ver = array("isFull" => $this->getIsFull() == true);
            return array_merge($json, $ver);
        }
        return $json;
    }
}
