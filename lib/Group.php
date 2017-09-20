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
use PartDB\Permissions\PermissionManager;

/**
 * @file Group.php
 * @brief class Group
 *
 * @class Group
 * All elements of this class are stored in the database table "groups".
 * @author kami89
 */
class Group extends Base\StructuralDBElement implements Interfaces\IHasPermissions
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

    /** @var User[] All users of this group as a one-dimensional array of User objects */
    private $users = null;

    protected $perm_manager;

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
     * @param integer   $id             ID of the group we want to get
     *
     * @throws Exception    if there is no such group in the database
     * @throws Exception    if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $id)
    {
        parent::__construct($database, $current_user, $log, 'groups', $id);
        $this->perm_manager = new PermissionManager($this);
    }

    /**
     * @copydoc DBElement::reset_attributes()
     */
    public function resetAttributes($all = false)
    {
        $this->users = null;

        parent::resetAttributes($all);
    }



    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get all users of this group
     *
     * @param boolean $recursive        if true, the users of all subgroups will be listed too
     *
     * @return User[]        all users as a one-dimensional array of User objects,
     *                      sorted by their names
     *
     * @throws Exception if there was an error
     */
    public function getUsers($recursive = false)
    {
        if (! is_array($this->users)) {
            $this->users = array();

            $query =    'SELECT * FROM users '.
                'WHERE group_id=? ORDER BY name ASC';

            $query_data = $this->database->query($query, array($this->getID()));

            foreach ($query_data as $row) {
                $this->users[] = new User($this->database, $this->current_user, $this->log, $row['id'], $row);
            }
        }

        if ($recursive) {
            $all_users = $this->users;
            $subgroups = $this->getSubelements(true);

            foreach ($subgroups as $group) {
                $all_users = array_merge($all_users, $group->getUsers(true));
            }

            return $all_users;
        } else {
            return $this->users;
        }
    }

    /**
     * Returns the comment field of this group.
     * @return string The comment of this group.
     */
    public function getComment()
    {
        return $this->db_data['comment'];
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     * Sets the comment of this group.
     * @param $new_comment string The new comment that this group should get.
     */
    public function setComment($new_comment)
    {
        $this->setAttributes(array('comment' => $new_comment));
    }

    /*********************************************************************
     * Permission system function
     *********************************************************************/

    /**
     * Gets the integer value of a permission of the current object.
     * @param $permsission_name string The name of the permission that should be get. (Without "perms_"
     * @return int The int value of the requested permission.
     */
    public function getPermissionRaw($permsission_name)
    {
        return intval($this->db_data["perms_" . $permsission_name]);
    }

    /**
     * Sets the integer value of a permission of the current object.
     * @param $permsission_name string The name of the permission that should be get. (Without "perms_")
     * @param $value int The value the permission should be set to.
     */
    public function setPermissionRaw($permission_name, $value)
    {
        $this->setAttributes(array("perms_" . $permission_name => $value));
    }

    /**
     * @return PermissionManager
     */
    public function &getPermissionManager()
    {
        return $this->perm_manager;
    }

    /**
     * Returns the PermissionManager of the (permission) parent of the current object.
     * @return PermissionManager|null The PermissionManager of the parent, or null if the current object has no parent.
     */
    public function &getParentPermissionManager()
    {
        //Ask directly, so we dont need any permissions, to resolve perms.
        $parent_id = $this->db_data['parent_id'];

        if ($parent_id < 1) {   //If parent is root, then this object has not a parent perm manager.
            return null;
        }

        $parent = new Group($this->database, $this->current_user, $this->log, $parent_id);
        //Otherwise return the perm manager of the group.
        return $parent->getPermissionManager();
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

        // TODO
    }

    /**
     * Get count of groups
     *
     * @param Database &$database   reference to the Database-object
     *
     * @return integer              count of groups
     *
     * @throws Exception            if there was an error
     */
    public static function getCount(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        return $database->getCountOfRecords('groups');
    }

    public static function getPermissionName()
    {
        return PermissionManager::GROUPS;
    }

    /**
     * Adds a new group to the database.
     * @param $database Database The database which should be used for requests.
     * @param $current_user User The database which should be used for requests.
     * @param $log Log The database which should be used for requests.
     * @param $name string The username of the new user.
     * @param $parent_id int The id of the parental group of the new group.
     * @return static The newly added group.
     */
    public static function add(&$database, &$current_user, &$log, $name, $parent_id)
    {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            'groups',
            array(  'name'                      => $name,
                'parent_id'                      => $parent_id)
        );
    }
}
