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

/**
 * @todo
 *   Soll der SysAdmin einen Datenbankeintrag haben? Mit Admin-Gruppe?
 *   Oder sollen die Rechte des Admins hardgecoded sein (ID = 0) (wie bei "StructuralDBElement")?
 *   Zweiteres wäre theoretisch schöner, da man die Adminrechte nicht verlieren kann durch eine
 *   kaputte Datenbank. Allerdings müsste das Admin-Passwort dann irgendwo gespeichert werden,
 *   wo man es auch bequem wieder ändern kann, vielleicht in $config (config.php)?
 *   Da momentan andere Sachen eine höhere Priorität haben als die Benutzerverwaltung,
 *   lasse ich das hier einfach mal so stehen, das kann man dann anschauen sobald es gebraucht wird.
 *   kami89
 */

use Exception;
use PartDB\Exceptions\UserNotAllowedException;
use PartDB\Interfaces\IHasPermissions;
use PartDB\Interfaces\ISearchable;
use PartDB\Permissions\BasePermission;
use PartDB\Permissions\PermissionManager;

/**
 * @file User.php
 * @brief class User
 *
 * @class User
 * All elements of this class are stored in the database table "users".
 * @author kami89
 */
class User extends Base\NamedDBElement implements ISearchable, IHasPermissions
{

    /** The User id of the anonymous user */
    const ID_ANONYMOUS      = 0;
    /** The user id of the main admin user */
    const ID_ADMIN          = 1;

    /********************************************************************************
     *
     *   Calculated Attributes
     *
     *   Calculated attributes will be NULL until they are requested for first time (to save CPU time)!
     *   After changing an element attribute, all calculated data will be NULLed again.
     *   So: the calculated data will be cached.
     *
     *********************************************************************************/

    /** @var Group the group of this user */
    private $group = null;

    /** @var PermissionManager  */
    protected $perm_manager = null;


    /** @var User  */
    protected static $loggedin_user = null;

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @param Database      &$database      reference to the Database-object
     * @param User|NULL     &$current_user  @li reference to the current user which is logged in
     *                                      @li NULL if $id is the ID of the current user
     * @param Log           &$log           reference to the Log-object
     * @param integer       $id             ID of the user we want to get
     *
     * @throws Exception    if there is no such user in the database
     * @throws Exception    if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $id, $data = null)
    {
        if (! is_object($current_user)) {     // this is that you can create an User-instance for first time
            $current_user = $this;
        }           // --> which one was first: the egg or the chicken? :-)

        parent::__construct($database, $current_user, $log, 'users', $id, true, $data);

        $this->perm_manager = new PermissionManager($this);
    }

    /**
     * @copydoc DBElement::reset_attributes()
     */
    public function resetAttributes($all = false)
    {
        $this->group = null;

        parent::resetAttributes($all);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the group of this user
     *
     * @return Group        the group of this user
     *
     * @throws Exception    if there was an error
     */
    public function getGroup()
    {
        if (! is_object($this->group)) {
            $this->group = new Group(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['group_id']
            );
        }

        return $this->group;
    }

    /**
     * Gets the username of the User.
     * @return string The username.
     */
    public function getName()
    {
        return $this->db_data['name'];
    }

    /**
     * Gets the first name of the user.
     * @return string The first name.
     */
    public function getFirstName()
    {
        return $this->db_data['first_name'];
    }

    /**
     * Gets the last name of the user.
     * @return string The first name.
     */
    public function getLastName()
    {
        return $this->db_data['last_name'];
    }

    /**
     * Gets the email address of the user.
     * @return string The email address.
     */
    public function getEmail()
    {
        return $this->db_data['email'];
    }

    /**
     * Gets the department of the user.
     * @return string The department of the user.
     */
    public function getDepartment()
    {
        return $this->db_data['department'];
    }

    /**
     * Checks if a given password, is valid for this account.
     * @param $password string The password which should be checked.
     */
    public function isPasswordValid($password)
    {
        $hash = $this->db_data['password'];
        if ($hash === "") {
            return false; //When no password was set, the any password is invalid
        }
        return password_verify($password, $hash);
    }

    /**
     * Checks if the user has no password set.
     * @return bool True, if the user has no password yet.
     */
    public function hasNoPassword()
    {
        if ($this->getID() == static::ID_ANONYMOUS) { //Anonymous user is allowed to have an empty password.
            return false;
        }
        $hash = $this->db_data['password'];
        return empty($hash);
    }

    /**
     * Deletes the given User from DB.
     * @throws Exception
     */
    public function delete()
    {
        $loggedin_user = static::getLoggedInUser($this->database, $this->log);
        if($loggedin_user->getID() == $this->getID()) {
            throw new Exception(_("Sie versuchen ihren aktuellen Benutzer zu löschen. Dies ist nicht möglich!"));
        }

        if ($this->getID() == static::ID_ANONYMOUS) {
            throw new Exception(_("Der anonymous Benutzer (ID=0) kann nicht gelöscht werden!"));
        }

        if ($this->getID() == static::ID_ADMIN) {
            throw new Exception(_("Der Systemadministrator (ID=1) kann nicht gelöscht werden!"));
        }
        parent::delete();
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     * Change the group ID of this user
     *
     * @param integer $new_group_id     the ID of the new group
     *
     * @throws Exception if the new group ID is not valid
     * @throws Exception if there was an error
     */
    public function setGroupID($new_group_id)
    {
        $this->setAttributes(array('group_id' => $new_group_id));
    }

    /**
     * Sets a new password, for the User.
     * @param $new_password string The new password.
     */
    public function setPassword($new_password)
    {
        if ($this->getID() == static::ID_ANONYMOUS) {
            throw new Exception(_("Das Password des anonymous Users kann nicht geändert werden!"));
        }
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $this->setAttributes(array("password" => $hash));
    }

    /**
     * Set a new first name.
     * @param $new_first_name string The new first name.
     */
    public function setFirstName($new_first_name)
    {
        $this->setAttributes(array('first_name' => $new_first_name));
    }

    /**
     * Set a new first name.
     * @param $new_first_name string The new first name.
     */
    public function setLastName($new_last_name)
    {
        $this->setAttributes(array('last_name' => $new_last_name));
    }

    /**
     * Returns the full name in the format FIRSTNAME LASTNAME [(USERNAME)].
     * Example: Max Muster (m.muster)
     * @param bool $including_username Include the username in the full name.
     * @return string A string with the full name of this user.
     */
    public function getFullName($including_username = false)
    {
        $str = $this->getFirstName() . " " . $this->getLastName();
        if ($including_username) {
            $str .= " (" . $this->getName() . ")";
        }

        return $str;
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

        // check "group_id"
        try {
            $group = new Group($database, $current_user, $log, $values['group_id']);
        } catch (Exception $e) {
            debug(
                'warning',
                _('Ungültige "group_id": "').$values['group_id'].'"'.
                _("\n\nUrsprüngliche Fehlermeldung: ").$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception(_('Die gewählte Gruppe existiert nicht!'));
        }
    }



    /**
     * Get count of users
     *
     * @param Database &$database   reference to the Database-object
     *
     * @return integer              count of users
     *
     * @throws Exception            if there was an error
     */
    public static function getCount(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        return $database->getCountOfRecords('users');
    }

    /**
     * Search elements by name.
     *
     * @param Database &$database reference to the database object
     * @param User &$current_user reference to the user which is logged in
     * @param Log &$log reference to the Log-object
     * @param string $keyword the search string
     * @param boolean $exact_match @li If true, only records which matches exactly will be returned
     * @li If false, all similar records will be returned
     *
     * @return array    all found elements as a one-dimensional array of objects,
     *                  sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function search(&$database, &$current_user, &$log, $keyword, $exact_match)
    {
        return parent::searchTable($database, $current_user, $log, "user", $keyword, $exact_match);
    }

    /**
     * @param $database Database
     * @param $username string The username, for which the User should be returned.
     * @return User
     * @throws Exception
     */
    public static function getUserByName(&$database, &$log, $username)
    {
        $username = trim($username);
        $query = 'SELECT * FROM users WHERE name = ?';
        $query_data = $database->query($query, array($username));

        if(count($query_data) > 1)
        {
            throw new Exception("Die Abfrage des Nutzernamens hat mehrere Nutzer ergeben");
        }

        $user_data = $query_data[0];
        $user = null;
        return new User($database, $user, $log, $user_data['id'], $user_data);
    }

    /**
     * Checks if a user is logged in, in the current session.
     * @return boolean true, if a user is logged in.
     */
    public static function isLoggedIn()
    {
        return self::getLoggedInID() > 0;
    }

    /**
     * Gets the id of the currently logged in user.
     * @return int The id of the logged in user, if someone is logged in. Else 0 (anonymous).
     */
    public static function getLoggedInID()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']<=0) {
            return 0;   //User anonymous.
        } else {
            try {
                $db = new Database();
                $query = "SELECT id FROM users WHERE id = ?";
                $results = $db->query($query, array($_SESSION['user']));
                if (count($results) !== 1) { //If not exactly one user with the id exists, we are not logged in.
                    return 0;
                }
            } catch (Exception $ex) {
                return 0; //If an error happened, we are not logged in.
            }

            return $_SESSION['user'];
        }
    }

    /**
     * Gets a user instance for the currently logged in user.
     * If nobody is logged in, it will return the anonymous user (id = 0)
     * @param $database Database The Database which should be used for request.
     * @param $log Log The Log, which should be used.
     * @return User The user, which is currently logged in.
     */
    public static function getLoggedInUser(&$database = null, &$log = null)
    {
        $loggedin_ID    = self::getLoggedInID();

        if (is_null($database) || is_null($log)) {
            $database = new Database();
            $log = new Log($database);
        }
        if (!is_object(static::$loggedin_user)) {
            $var = null;
            static::$loggedin_user = new User($database, $var, $log, $loggedin_ID);
        } else { //A user is cached...
            //Check if the the cached user, is the one we want!
            if (static::$loggedin_user->getID() != $loggedin_ID) {
                $var = null;
                static::$loggedin_user = new User($database, $var, $log, $loggedin_ID);
            }
        }

        return static::$loggedin_user;

    }

    /**
     * Log in the given user for the current session.
     * @param $user User The user which should be logged in.
     * @param $password string When not empty, it will be checked if this password is correct, and only then the user
     * will be logged in.
     * @return boolean True, if the user was successfully logged in. False if a error appeared, like a wrong password.
     */
    public static function login(&$user, $password = "")
    {
        if (!empty($password) && !$user->isPasswordValid($password)) { //If $password is set, and wrong.
            return false;
        }
        $_SESSION['user'] = $user->getID();
        return true;
    }

    /**
     * Log out the current user and set logged in to anonymous.
     * @return boolean True, if the user was successful logged out.
     */
    public static function logout()
    {
        $_SESSION['user'] = 0;
        return true;
    }

    /**
     * Builds a HTML List of <option> elemements for an <select> element, showing all users.
     * @param $database Database The database which should be used for requests.
     * @param $current_user User The user which should be used for requests.
     * @param $log Log The log which should be used for requests.
     * @param int $selected_id The ID of the currently selected user. This will the selected user in the list.
     *          Set to -1, when you dont want to have any selection.
     * @return string A string with HTML
     */
    public static function buildHTMLList(&$database, &$current_user, &$log, $selected_id = -1)
    {
        $users = self::getAllUsers($database, $current_user, $log);
        $html = array();
        foreach ($users as $user) {
            /** @var User $user */
            $selected = $user->getID() == $selected_id ? " selected" : "";
            $no_pw    = $user->hasNoPassword() ? _("  [Kein Password]") : "";
            $html[] = "<option value='" . $user->getID()  . "'" . $selected . ">" . $user->getFullName(true) . $no_pw . "</option>";
        }
        return implode("\n", $html);
    }

    /**
     * Returns all users of the database
     * @param Database $database The database which should be used for requests.
     * @param User $current_user The user which should be used for requests.
     * @param Log $log The log which should be used for requests.
     * @return User[] Any array of all users in the database.
     */
    public static function getAllUsers(&$database, &$current_user, &$log)
    {
        $results = $database->query("SELECT * FROM users");
        $users = array();
        foreach ($results as $result) {
            $users[] = new User($database, $current_user, $log, $result['id'], $result);
        }

        return $users;
    }

    /**
     * Adds a new user to the database.
     * @param $database Database The database which should be used for requests.
     * @param $current_user User The database which should be used for requests.
     * @param $log Log The database which should be used for requests.
     * @param $name string The username of the new user.
     * @param $group_id int The id of the group of the new user.
     * @return static The newly added user.
     */
    public static function add(&$database, &$current_user, &$log, $name, $group_id)
    {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            'users',
            array(  'name'                      => $name,
                'group_id'                      => $group_id)
        );
    }

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
        $parent = $this->getGroup();
        if ($parent->getID() == 0) {
            //When group is root, then this user doesnt has a parent perm manager.
            $tmp = null;
            return $tmp;
        }
        //Otherwise return the perm manager of the group.
        return $parent->getPermissionManager();
    }


    /**
     * Check if the user can perform the given operation and return the result as boolean.
     * @param $perm_name string The name of the permission, that should be checked.
     * @param $perm_operation string The name of the operation that should be checked.
     * @return bool True, if the user can perform the action, false if not.
     */
    public function canDo($perm_name, $perm_operation)
    {
        return $this->perm_manager->getPermissionValue($perm_name, $perm_operation, true) == BasePermission::ALLOW;
    }

    /**
     * Check if the current user can permform the action. Throws an exception if he is not allowed to do the operation.
     * @param $perm_name string The name of the permission, that should be checked.
     * @param $perm_operation string The name of the operation that should be checked.
     * @throws UserNotAllowedException This Exception is thrown, when the user is not allowed to do the requested action.
     */
    public function tryDo($perm_name, $perm_operation)
    {
        if ($this->canDo($perm_name, $perm_operation) == false) {
            $group_title = $this->perm_manager->getPermGroupTitle($perm_name);
            $perm = $this->perm_manager->getPermission($perm_name);
            $perm_description = $perm->getDescription();
            $op_description = $perm::opToDescription($perm_operation);
            $str = _("Der aktuelle Benutzer darf die gewünschte Operation nicht durchführen!");
            $str = $str . " ($group_title->$perm_description: $op_description)";
            throw new UserNotAllowedException($str);
        }
    }
}
