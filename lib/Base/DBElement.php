<?php declare(strict_types=1);
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
use PartDB\Exceptions\NotImplementedException;
use PartDB\Exceptions\TableNotExistingException;
use PartDB\Log;
use PartDB\User;
use Symfony\Component\Finder\Exception\AccessDeniedException;

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

 */
abstract class DBElement
{

    /*******************************************************************************
     * The tablename to use. You have to overwrite this in every sub class, in which you
     * to access a database table
     *******************************************************************************/
    const TABLE_NAME = ''; //Empty string means unset, this will result in a not implemented Exception


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
    protected $tablename = null;

    /**
     * @var array (array [1..*]) the record data from the database
     *         (for every table column there is an element in this array)
     * @par Example:
     * @code array(['row1'] => 'value1', ['row2'] => 'value2', ...) @endcode
     */
    protected $db_data =        null;

    /**
     * @var bool Determines if this element is virtual.
     */
    protected $is_virtual_element = false;

    /**
     * @var array In this array we cache the instances of the objects
     */
    protected static $cache = array();

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * This creates a new Element object, representing an entry from the Database.
     *
     * You should (and can not) use this constructor from outside. You have to use getInstance() with the same params,
     * to get an instance. This way the object instances can be cached.
     *
     * @see DBElement::getInstance()
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
    protected function __construct(Database $database, User $current_user, Log $log, int $id, $db_data = null)
    {
        $this->database = $database;
        $this->current_user = $current_user;
        $this->log = $log;

        $this->tablename = static::getTablename();

        if (($db_data == null) && !$this->database->doesTableExist($this->tablename)) {
            throw new TableNotExistingException(
                sprintf(
                    _('Die Tabelle "%s" existiert nicht in der Datenbank!'),
                    $this->tablename
                )
            );
        }

        //We have to distinguish between real elements (positive ID) and virtual IDs.
        //Furthermore the caller can pass its own data via the $db_data array.
        if (!empty($db_data)) { //Custom data by caller
            $this->is_virtual_element = ($id <= 0); //Negative or zero ID means virtual element here, too!
            $this->db_data  = $db_data;
        } elseif ($id <= 0) { //Virtual Elements
            //If this object can not have virtual elements (implements IHasVirtualElements),
            // then negative IDs are invalid.
            if (!$this->allowsVirtualElements()) {
                throw new ElementNotExistingException(_('Dieser Elementtyp erlaubt keine virtuellen Elemente!'));
            }
            //Otherwise we eventually can get virtual data from the getVirtualData() function
            $virtual_data = $this->getVirtualData($id);
            $this->db_data = array('id' => $id);
            $this->db_data = array_replace_recursive($this->db_data, $virtual_data);
            //Mark this object as virtual
            $this->is_virtual_element = true;
        } else { //Real elements
            try {
                //Retriev the data from the database.
                $this->db_data = $this->database->getRecordData($this->tablename, $id);
            } catch (ElementNotExistingException $ex) {
                //Give getVirtualData to provide data for a virtual element, even if the entry is not existing in
                //the database!
                if ($this->allowsVirtualElements()) {
                    $virtual_data = $this->getVirtualData($id);
                    $this->db_data = array('id' => $id);
                    $this->db_data = array_replace_recursive($this->db_data, $virtual_data);
                    $this->is_virtual_element = true;
                } else {
                    throw $ex;
                }
            }
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
     * @param boolean $all * if true, ALL attributes will be deleted (use it only for "destroying" the object).
     * * if false, only the calculated data will be deleted.
     *                              This is needed if you change an attribute of the object.
     * @throws Exception
     */
    public function resetAttributes(bool $all = false)
    {
        if ($all) {
            $id_tmp = $this->db_data['id']; // backup ID
            $this->db_data = array();
            $this->db_data['id'] = $id_tmp; // restore ID
        } else if ($this->getID() != 0) {
            $this->db_data = $this->database->getRecordData($this->tablename, $this->getID());
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
            throw new Exception(_('Die ID ist kleiner als 1, dies ist nicht erlaubt!'));
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
     * Get the ID. The ID can be zero, or even negative (for virtual elements). If an elemenent is virtual, can be
     * checked with isVirtualElement()
     *
     * @return integer the ID of this element
     */
    final public function getID() : int
    {
        return (int) $this->db_data['id'];
    }

    /**
     * Returns the ID as an string, defined by the element class.
     * This should have a form like P000014, for a part with ID 14.
     * @return string The ID as a string;
     */
    abstract public function getIDString() : string;


    /**
     * Checks if this element is virtual, meaning that it does not have an entry in the database!
     * @return bool True, if the element is virtual, false if not.
     */
    final public function isVirtualElement() : bool
    {
        return $this->is_virtual_element;
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
    public function setAttributes(array $new_values)
    {

        // We create an array of all database data.
        // All values from $new_values will be used instead of the values in $this->db_data (override them).
        $values = array_merge($this->db_data, $new_values);

        //debug('temp', '$values='.print_r($values, true), __FILE__, __LINE__, __METHOD__);

        // we check if the new data is valid
        // (with "static::" we let check EVERY subclass from the class of $this
        // up to the DBElement to check the data!)
        static::checkValuesValidity($this->database, $this->current_user, $this->log, $values, false, $this);

        /**
         * Only write to DB, if this element is not a virtual element.
         */
        if (!$this->is_virtual_element) {
            // all values are valid (there was no exception), so we write them to the database
            // note:    We use the values from $values instead of the values from $new_values
            //          because this way the method check_values_validity() can adjust the values.
            //          For example, names can be trimmed [trim()] in check_values_validity().
            $this->database->setDataFields($this->tablename, $this->getID(), $values);

            // get all data from the database again (this is the savest way to be up-to-date)
            $this->db_data = $this->database->getRecordData($this->tablename, $this->getID());
        }

        // set all calculated attributes to NULL (maybe they are no longer valid)
        // (all same-named methods of every subclass of DBElement will be executed!)
        $this->resetAttributes();
    }

    /**
     * Returns the §db_data that should be used for the Virtual Element.
     * This function can return different virtual elements, based on $virtual_id.
     * @param int $virtual_id The ID of the virtual element which should be created. Virtual Elements has negative
     * (or zero) value IDs. When this function is called with a positive number, then the given element was not found in
     * the DB, and this function has the possibility to create a virtual object with this.
     *
     * You dont have to set $virtual_id into the returned array, this already happenes in the constructor of the
     * DBElement.
     *
     * @return array This array will be merged (replacing mode) with the db_data of the DBElement.
     * @throws ElementNotExistingException If the element with the given virtual ID is not creatable with this function,
     * then this exception should be thrown!
     */
    protected function getVirtualData(int $virtual_id) : array
    {
        throw new ElementNotExistingException(sprintf(_('Es existiert kein Element mit der ID %d'), $virtual_id));
    }

    /**
     * This function determines if the element class allows virtual elements. By default they are disabled.
     * @return bool Retun true, if virtual elements are allowed, false if not.
     */
    protected function allowsVirtualElements() : bool
    {
        return false;
    }

    /********************************************************************************
     *
     *  Magic functions
     *
     *******************************************************************************/

    /**
     * Get a string representation of the DB Element. Mostly this is the same as getIDString(), but it can be
     * overwritten in subclasses.
     * @return string The string representation of this element.
     */
    public function __toString()
    {
        return $this->getIDString();
    }


    /**
     * This function is called, when this object is not referenced any more, or the script ends.
     * We should use it, to ensure, that all data is written to DB.
     *
     * This function must be explicity created in subclasses, and these must call parent::_destruct !!
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     *  Get count of possible Elements (meaning how many rows are in the table for this element)
     *
     * @param Database &$database reference to the Database-object
     *
     * @return int            count of Elements for this Element
     *
     *
     * @throws \PartDB\Exceptions\DatabaseException If there was an error getting the data from DB.
     */
    final public static function getCount(Database $database) : int
    {
        return $database->getCountOfRecords(static::getTablename());
    }

    /**
     * Get the tablename
     *
     * @return string the tablename of the database table where this element is stored
     * @throws NotImplementedException If the TABLE_NAME const has not been overwritten in the subclass.
     */
    final public static function getTablename() : string
    {
        $c = static::class;
        $tablename = $c::TABLE_NAME;
        //Check if the tablename was really set!
        if ($tablename == '') {
            throw new NotImplementedException(
                _('$this->tablename hat keinen Wert! Es muss in jeder Klasse das Feld $tablename überschrieben werden!')
            );
        }
        return $tablename;
    }


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
     * @throws InvalidElementValueException if the values are not valid / the combination of values is not valid
     * @throws \InvalidArgumentException
     *
     */
    public static function checkValuesValidity(
        Database $database,
        User $current_user,
        Log $log,
        array &$values,
        bool $is_new,
        &$element = null
    ) {
        // YOU HAVE TO IMPLEMENT THIS METHOD IN YOUR SUBCLASSES IF YOU WANT TO CHECK NEW VALUES !!

        if ((! $is_new) && (! \is_object($element))) {
            throw new \InvalidArgumentException(_('$element ist kein Objekt!'));
        }
    }

    /**
     * Create a new DBElement (store it in the database)
     *
     * @param Database      $database           reference to the database onject
     * @param User          $current_user       reference to the current user which is logged in
     * @param Log           $log                reference to the Log-object
     * @param array         $new_values         @li one-dimensional array with all keys (table columns)
     *                                              and the new values
     *                                          @li example: @code
     *                                              array(['name'] => 'abcd', ['parent_id'] => 123, ...) @endcode
     *
     * @return static       the created object (e.g. Device, Part, Category, ...)
     *
     * @throws Exception if the values are not valid / the combination of values is not valid
     */
    protected static function addByArray(Database $database, User $current_user, Log $log, array $new_values)
    {
        // we check if the new data is valid
        // (with "static::" we let check every subclass of DBElement to check the data!)
        static::checkValuesValidity($database, $current_user, $log, $new_values, true);

        // now we can insert the new data into the database
        $id = $database->insertRecord(static::getTablename(), $new_values);

        if ($id == null) {
            throw new Exception(_('Der Datenbankeintrag konnte nicht angelegt werden.'));
        }

        //Clear the cache, so all objects are freshly retrieved from DB (this should not have a big impact)
        //We need to do this, because, the elements cache associated objects by there self, and these caches need
        //eventually to be updated, when adding a new part.
        //For example without this line, newly added pricedetails won't be shown directly to user, because
        //Orderdetails cache its associated pricedetails.
        static::$cache = array();

        return static::getInstance($database, $current_user, $log, $id);
    }

    /**
     * Get an instance of the class. You have to use this function instead of the constructor, so it is possible to
     * return an reference to a cached (already) existing object instance
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
     * @return static A reference to the instance you wanted.
     */
    //$current_user must not have a type, because User passes, null!!
    public static function &getInstance(
        Database $database,
        &$current_user,
        Log $log,
        int $id,
        array $db_data = null
    ) : DBElement {
        //Check if we already have a chached instance of the element:
        if (isset(static::$cache[static::class][$id])) {
            return static::$cache[static::class][$id];
        } else {
            $element = new static($database, $current_user, $log, $id, $db_data);

            //Only cache elements, whose user is the currently logged in user (this should be true for nearly all)
            if ($current_user->isLoggedInUser()) {
                static::$cache[static::class][$id] = $element;
                return static::$cache[static::class][$id];
            }

            return $element;
        }
    }
}
