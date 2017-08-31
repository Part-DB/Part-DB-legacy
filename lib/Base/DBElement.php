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
 * @file class.DBElement.php
 *
 * @class DBElement
 * This class is for managing all database objects.
 *
 * You should use this class for ALL classes which manages database records!
 *          (except special tables like "internal"...)
 * Every database table which are managed with this class (or a subclass of it)
 *          must have the table row "id"!! The ID is the unique key to identify the elements.
 *
 * @author kami89
 */
abstract class DBElement
{
    /********************************************************************************
     *
     *   Attributes (non-calculated attributes!)
     *
     *********************************************************************************/

    /** @var User object of the user which is logged in */
    protected $current_user =   null;
    /** @var Log a log object for logging events / bookings */
    protected $log =            null;

    /** @var Database the database object for all database transactions */
    protected $database =       null;
    /** @var string the tablename of the element, e.g. "categories" for the class "Category" and so on... */
    protected $tablename =      null;

    /**
     * @var array (array [1..*]) the record data from the database
     *         (for every table column there is an element in this array)
     * @par Example:
     * @code array(['row1'] => 'value1', ['row2'] => 'value2', ...) @endcode
     */
    protected $db_data =        null;

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @param Database  $database                   reference to the Database-object
     * @param User      $current_user               reference to the current user which is logged in
     * @param Log       $log                        reference to the Log-object
     * @param string    $tablename                  the name of the database table
     * @param integer   $id                         ID of the element we want to get
     * @param boolean   $allow_virtual_elements     @li if true, it's allowed to set $id to zero
     *                                                  (the StructuralDBElement needs this for the root element)
     *                                              @li if false, $id == 0 is not allowed (throws an Exception)
     * @param array     $db_data                    If you have already data from the database, then use give it with this param, the part, wont make a database request.
     * @throws Exception    if there is no such element in the database
     *                      (except: the ID=0 is valid, even if there is no such element in the database)
     */
    public function __construct(&$database, &$current_user, &$log, $tablename, $id, $allow_virtual_elements = false, $db_data = null)
    {
        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        if (!$current_user instanceof User) {
            throw new Exception(_('$current_user ist kein User-Objekt!'));
        }

        if (!$log instanceof Log) {
            throw new Exception(_('$log ist kein DebugLog-Objekt!'));
        }

        $this->database = $database;
        $this->current_user = $current_user;
        $this->log = $log;

        if ($db_data==null) { //Dont check for table exist, if we already have db_data
            if (! $this->database->doesTableExist($tablename)) {
                throw new Exception('Die Tabelle "'.$tablename.'" existiert nicht in der Datenbank!');
            }
        }


        $this->tablename = $tablename;

        if (((! is_int($id)) && (! ctype_digit($id)) && (! is_null($id))) || (($id == 0) && (! $allow_virtual_elements))) {
            throw new Exception('$id ist keine gültige ID! $id="'.$id.'"');
        }

        // get all data of the database record with the ID "$id"
        // But if the ID is zero, it could be a root element of StructuralDBElement,
        // so there is no data to get from database.
        if ($id != 0 && $db_data == null) {
            $this->db_data = $this->database->getRecordData($this->tablename, $id);
        }

        if ($db_data !== null) {
            $this->db_data = $db_data;
        }
    }

    /**
     * Reset all attributes of this object (set them to NULL).
     *
     * Reasons why we need this method:
     *      * If we change an attribute of the element, some calculated attributes are no longer valid.
     *          So this method is called with $all=false to set all calculated attributes to NULL ("clear the cache")
     *      * If this element is deleted by delete(), we need to clear ALL data from this element,
     *          including non-calculated attributes. So this method will be called with $all=true.
     *
     * @warning     You should implement this function in your subclass (including a call to this function here!),
     *              if your subclass has its own attributes (calculated or non-calculated)!
     *
     * @param boolean $all      * if true, ALL attributes will be deleted (use it only for "destroying" the object).
     * * if false, only the calculated data will be deleted.
     *                              This is needed if you change an attribute of the object.
     */
    public function resetAttributes($all = false)
    {
        if ($all) {
            //$this->database =       NULL; // we still need them...
            //$this->current_user =   NULL; // ...for commit/rollback...
            //$this->log =            NULL; // ...after deleting an element!
            //$this->tablename =      NULL;

            $id_tmp = $this->db_data['id']; // backup ID
            $this->db_data =        null;
            $this->db_data['id'] = $id_tmp; // restore ID
        } else {
            // get all data of the database record with the ID "$id"
            // But if the ID is zero, it could be a root element of StructuralDBElement,
            // so there is no data to get from database.
            if ($this->getID() != 0) {
                $this->db_data = $this->database->getRecordData($this->tablename, $this->getID());
            }
        }
    }

    /********************************************************************************
     *
     *   Basic Methods
     *
     *********************************************************************************/

    /**
     * Delete this element from the database
     *
     * @throws Exception if there was an error
     */
    public function delete()
    {
        if ($this->getID() < 1) { // is this object a valid element from the database?
            throw new Exception('Die ID ist kleiner als 1, das darf nicht vorkommen!');
        }

        $this->database->deleteRecord($this->tablename, $this->getID());

        // set ALL element attributes to NULL
        $this->resetAttributes(true);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the ID
     *
     * @retval integer the ID of this element
     */
    public function getID()
    {
        return $this->db_data['id'];
    }

    /**
     * Get the tablename
     *
     * @retval string the tablename of the database table where this element is stored
     */
    public function getTablename()
    {
        return $this->tablename;
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     * Set one or more database attributes of this element
     *
     * This method let the method check_values_validity() to check all new values if they are valid!
     *          You don't have to check the data before you call this method!
     *          And the values will also be corrected automatically (e.g. trim names of elements or so).
     *
     * @warning     To ensure that this works correctly, you have to check the data in your
     *              subclasses method check_values_validity()!
     *
     * @param array $new_values     all new values in a one-dimensional array [1..*] like this:
     *                              @code array(['row1'] => 'value1', ['row2'] => 'value2', ...) @endcode
     *
     * @throws Exception if the values are not valid / the combination of values is not valid
     * @throws Exception if there was an error
     */
    public function setAttributes($new_values)
    {
        if ($this->getID() < 1) {
            throw new Exception('Das ausgewählte Element existiert nicht in der Datenbank!');
        }

        if (! is_array($new_values)) {
            debug('error', 'Ungültiger Inhalt von $new_values: "'.$new_values.'"', __FILE__, __LINE__, __METHOD__);
            throw new Exception('$new_values ist kein Array!');
        }

        // We create an array of all database data.
        // All values from $new_values will be used instead of the values in $this->db_data (override them).
        $values = array_merge($this->db_data, $new_values);

        //debug('temp', '$values='.print_r($values, true), __FILE__, __LINE__, __METHOD__);

        // we check if the new data is valid
        // (with "static::" we let check EVERY subclass from the class of $this
        // up to the DBElement to check the data!)
        static::checkValuesValidity($this->database, $this->current_user, $this->log, $values, false, $this);

        // all values are valid (there was no exception), so we write them to the database
        // note:    We use the values from $values instead of the values from $new_values
        //          because this way the method check_values_validity() can adjust the values.
        //          For example, names can be trimmed [trim()] in check_values_validity().
        $this->database->setDataFields($this->tablename, $this->getID(), $values);

        // get all data from the database again (this is the savest way to be up-to-date)
        $this->db_data = $this->database->getRecordData($this->tablename, $this->getID());

        // set all calculated attributes to NULL (maybe they are no longer valid)
        // (all same-named methods of every subclass of DBElement will be executed!)
        $this->resetAttributes();
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
    public static function checkValuesValidity(&$database, &$current_user, &$log, &$values, $is_new, &$element = null)
    {
        // YOU HAVE TO IMPLEMENT THIS METHOD IN YOUR SUBCLASSES IF YOU WANT TO CHECK NEW VALUES !!

        if (! is_array($values)) {
            debug('error', '$values ist kein Array: "'.$values.'"', __FILE__, __LINE__, __METHOD__);
            throw new Exception('$values ist kein Array!');
        }

        if ((! $is_new) && (! is_object($element))) {
            debug('error', '$element="'.$element.'"', __FILE__, __LINE__, __METHOD__);
            throw new Exception('$element ist kein Objekt!');
        }
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
     * @return static       the created object (e.g. Device, Part, Category, ...)
     *
     * @throws Exception if the values are not valid / the combination of values is not valid
     */
    protected static function addByArray(&$database, &$current_user, &$log, $tablename, $new_values)
    {
        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein gültiges Database-Objekt!'));
        }

        if (!$current_user instanceof User) {
            throw new Exception(_('$current_user ist kein gültiges User-Objekt!'));
        }

        if (!$log instanceof Log) {
            throw new Exception(_('$log ist kein gültiges Log-Objekt!'));
        }

        if (! is_string($tablename)) {
            throw new Exception(_('$tablename ist kein String!'));
        }

        if (! is_array($new_values)) {
            throw new Exception(_('$new_values ist kein Array!'));
        }

        if (count($new_values) < 1) {
            throw new Exception(_('Das Array $new_values ist leer!'));
        }

        if (! $database->doesTableExist($tablename)) {
            throw new Exception('Die Tabelle "'.$tablename.'" existiert nicht!');
        }

        // we check if the new data is valid
        // (with "static::" we let check every subclass of DBElement to check the data!)
        static::checkValuesValidity($database, $current_user, $log, $new_values, true);

        // if there was no exception, all values are valid

        // create the query string
        $query = 'INSERT INTO '.$tablename.' ('.implode(', ', array_keys($new_values)).') '.
            'VALUES (?'.str_repeat(', ?', count($new_values)-1).')';

        // now we can insert the new data into the database
        $id = $database->execute($query, $new_values);

        if ($id == null) {
            throw new Exception('Der Datenbankeintrag konnte nicht angelegt werden.');
        }

        $class = get_called_class();

        return new $class($database, $current_user, $log, $id);
    }
}
