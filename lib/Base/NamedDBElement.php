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
use PartDB\Exceptions\ElementNotExistingException;
use PartDB\Exceptions\InvalidElementValueException;
use PartDB\Exceptions\TableNotExistingException;
use PartDB\Interfaces\IHasModifiedCreatedInfos;
use PartDB\Log;
use PartDB\LogSystem\ElementEditedEntry;
use PartDB\User;

/**
 * @file NamedDBElement.php
 * @brief class NamedDBElement
 *
 * @class NamedDBElement
 * All subclasses of this class have an attribute "name".
 */
abstract class NamedDBElement extends DBElement implements IHasModifiedCreatedInfos
{
    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * This creates a new Element object, representing an entry from the Database.
     *
     * @param Database $database reference to the Database-object
     * @param User $current_user reference to the current user which is logged in
     * @param Log $log reference to the Log-object
     * @param integer $id ID of the element we want to get
     * @param array $db_data If you have already data from the database,
     * then use give it with this param, the part, wont make a database request.
     *
     * @throws TableNotExistingException If the table is not existing in the DataBase
     * @throws \PartDB\Exceptions\DatabaseException If an error happening during Database AccessDeniedException
     * @throws ElementNotExistingException If no such element exists in DB.
     */
    public function __construct(Database &$database, User &$current_user, Log &$log, int $id, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $db_data);
    }

    /**
     * Delete this element from the database
     *
     * @throws Exception if there was an error
     */
    public function delete()
    {
        $this->log->elementDeleted($this);
        //Override this, so we can log the deletion to database
        parent::delete();
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
    public function getName() : string
    {
        //Strip HTML from Name, so no XSS injection is possible.
        return strip_tags($this->db_data['name']);
    }

    /**
     * Returns the last time when the element was modified.
     * @param $formatted bool When true, the date gets formatted with the locale and timezone settings.
     *          When false, the raw value from the DB is returned.
     * @return string The time of the last edit.
     */
    public function getLastModified(bool $formatted = true) : string
    {
        $time_str = $this->db_data['last_modified'];
        if ($formatted) {
            $timestamp = strtotime($time_str);
            return formatTimestamp($timestamp);
        }
        return $time_str;
    }

    /**
     * Returns the date/time when the element was created.
     * @param $formatted bool When true, the date gets formatted with the locale and timezone settings.
     *       When false, the raw value from the DB is returned.
     * @return string The creation time of the part.
     */
    public function getDatetimeAdded(bool $formatted = true) : string
    {
        $time_str = $this->db_data['datetime_added'];
        if ($formatted) {
            $timestamp = strtotime($time_str);
            return formatTimestamp($timestamp);
        }
        return $time_str;
    }

    /**
     * Returns the user that edited this element the last time.
     * @return null|User Returns the user if an entry in the Log was found with the info. Null otherwise.
     * @throws Exception
     */
    public function getLastModifiedUser()
    {
        return Log::getLastModifiedUserForElement($this->database, $this->current_user, $this->log, $this);
    }

    /**
     * Returns the user that created this user.
     * @return null|User Returns the user if an entry in the Log was found with the info. Null otherwise.
     * @throws Exception
     */
    public function getCreationUser()
    {
        return Log::getCreationUserForElement($this->database, $this->current_user, $this->log, $this);
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
    public function setName(string $new_name)
    {
        $this->setAttributes(array('name' => $new_name));
    }

    public function setAttributes(array $new_values, $edit_message = null)
    {
        $old_values = $this->db_data;
        parent::setAttributes($new_values);
        ElementEditedEntry::add(
            $this->database,
            $this->current_user,
            $this->log,
            $this,
            $old_values,
            $new_values,
            $edit_message
        );
    }


    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * Check if all values are valid for creating a new element / editing an existing element
     *
     * This function is called by creating a new DBElement (DBElement::add()),
     * respectively a subclass of DBElement. Then the attribute $is_new is true!
     *
     * And if you set data fields with DBElement::set_attributes() (or a subclass of DBElement),
     * the new data (one or more attributes) will be checked with this function
     * (with $is_new = false and with the object as $element).
     *
     * Because we pass the values array by reference, you're able to adjust values in the array.
     * For example, you can trim names of elements. So you don't have to throw an Exception if
     * values are not 100% perfect, you simply can "repair" these uncritical attributes.
     *
     * @warning     You have to implement this function in your subclass to check all data!
     *              You should always let to check the parent class all values, and after that,
     *              you can check the values which are associated with your subclass of DBElement.
     *
     * @param Database      &$database          reference to the database object
     * @param User          &$current_user      reference to the current user which is logged in
     * @param Log           &$log               reference to the Log-object
     * @param array         &$values            @li one-dimensional array of all keys and values (old and new!)
     *                                          @li example: @code
     *                                              array(['name'] => 'abcd', ['parent_id'] => 123, ...) @endcode
     * @param boolean       $is_new             @li if true, this means we will create a new element.
     *                                          @li if false, this means we will set attributes of an existing element
     * @param static|NULL   &$element           if $is_new is 'false', we have to supply the element,
     *                                          which will be edited, here.
     *
     * @throws Exception if the values are not valid / the combination of values is not valid
     * @throws Exception if there was an error
     */
    public static function checkValuesValidity(
        Database &$database,
        User &$current_user,
        Log &$log,
        array &$values,
        bool $is_new,
        &$element = null
    ) {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        // set "last_modified" to current datetime
        $values['last_modified'] = date('Y-m-d H:i:s');

        // we trim the name (spaces at the begin or at the end of a name are ugly, so we remove them)
        $values['name'] = strip_tags(trim($values['name']));

        if (empty($values['name'])) { // empty names are not allowed!
            throw new InvalidElementValueException(_('Der neue Name ist leer, das ist nicht erlaubt!'));
        }
    }

    /**
     * Search elements by name in the given table.
     *
     * @param Database  &$database              reference to the database object
     * @param User      &$current_user          reference to the user which is logged in
     * @param Log       &$log                   reference to the Log-object
     * @param string    $tablename              The table in which should be searched.
     * @param string    $keyword                the search string
     * @param boolean   $exact_match            @li If true, only records which matches exactly will be returned
     *                                          @li If false, all similar records will be returned
     *
     * @return static[]    all found elements as a one-dimensional array of objects,
     *                  sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function search(Database &$database, User &$current_user, Log &$log, string $keyword, bool $exact_match) : array
    {
        $tablename = static::getTablename();

        if (empty($keyword) == 0) {
            return array();
        }

        if (! $exact_match) {
            $keyword = str_replace('*', '%', $keyword);
            $keyword = '%' . $keyword . '%';
        }

        $query = 'SELECT * FROM ' . $tablename .
            ' WHERE name' . (($exact_match) ? '=' : ' LIKE ') . '? ORDER BY name ASC';

        $query_data = $database->query($query, array($keyword));

        $objects = array();

        foreach ($query_data as $row) {
            $objects[] = static::getInstance($database, $current_user, $log, $row['id'], $row);
        }

        return $objects;
    }

    /**
     * Create a new DBElement (store it in the database)
     *
     * @param Database      $database           reference to the database onject
     * @param User          $current_user       reference to the current user which is logged in
     * @param Log           $log                reference to the Log-object
     * @param string        $tablename          the name of the table where the new element should be inserted
     * @param array         $new_values         @li one-dimensional array with all keys (table columns)
     *                                              and the new values
     *                                          @li example: @code
     *                                              array(['name'] => 'abcd', ['parent_id'] => 123, ...) @endcode
     *
     * @return static
     *
     * @throws Exception if the values are not valid / the combination of values is not valid
     */
    public static function addByArray(Database &$database, User &$current_user, Log &$log, array $new_values)
    {
        $element = parent::addByArray($database, $current_user, $log, $new_values);
        //Log this the creation of the element to database.

        $log->elementCreated($element);

        return $element;
    }
}
