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
use PartDB\Exceptions\UserNotAllowedException;
use PartDB\Interfaces\IHasPermissions;
use PartDB\Interfaces\ISearchable;
use PartDB\Permissions\BasePermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\SelfPermission;
use PartDB\Permissions\UserPermission;

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
    const ID_ANONYMOUS      = 1;
    /** The user id of the main admin user */
    const ID_ADMIN          = 2;

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
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return null;
        }

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
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }
        return $this->db_data['name'];
    }

    /**
     * Gets the first name of the user.
     * @return string The first name.
     */
    public function getFirstName()
    {
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }
        return $this->db_data['first_name'];
    }

    /**
     * Gets the last name of the user.
     * @return string The first name.
     */
    public function getLastName()
    {
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }
        return $this->db_data['last_name'];
    }

    /**
     * Gets the email address of the user.
     * @return string The email address.
     */
    public function getEmail()
    {
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }
        return $this->db_data['email'];
    }

    /**
     * Gets the department of the user.
     * @return string The department of the user.
     */
    public function getDepartment()
    {
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }
        return $this->db_data['department'];
    }

    /**
     * Try to get an URL to the User Avatar image.
     * When allowed, this function try to generate an avatar URL to Gravatar,
     * otherwise a local image on the server is returned.
     * @param $size int The size of the Avatar in pixels
     * @return string The url to the avatar image.
     */
    public function getAvatar($size = 200)
    {
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }

        global $config;

        if ($config['user']['avatars']['use_gravatar']) {
            return $this->getGravatar($size, "identicon");
        } else {
            return "img/default_avatar.png";
        }
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param bool $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    public function getGravatar($s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $email = $this->getEmail();
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }
        return $url;
    }

    /**
     * Gets the theme configured for this user.
     * @param bool $no_resolve_for_default When this is false, a empty value will be resolved to the system-wide default
     *      theme. When set to true, an empty string will be returned.
     * @return string The name of the configured theme.
     */
    public function getTheme($no_resolve_for_default = false)
    {
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }
        if (empty($this->db_data['config_theme']) && !$no_resolve_for_default) {
            global $config;
            return  $config['html']['custom_css'];
        } else {
            //When set to @@, use no custom_css => default bootstrap theme
            if ($this->db_data['config_theme'] == "@@") {
                return "";
            }
            return $this->db_data['config_theme'];
        }
    }

    /**
     * Gets the timezone configured for this user.
     * @param bool $no_resolve_for_default When this is false, a empty value will be resolved to the system-wide default
     *      timezone. When set to true, an empty string will be returned.
     * @return string The name of the configured timezone.
     */
    public function getTimezone($no_resolve_for_default = false)
    {
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }
        if (empty($this->db_data['config_timezone']) && !$no_resolve_for_default) {
            global $config;
            return $config['timezone'];
        } else {
            return $this->db_data['config_timezone'];
        }
    }

    /**
     * Gets the language configured for this user.
     * @param bool $no_resolve_for_default When this is false, a empty value will be resolved to the system-wide default
     *      language. When set to true, an empty string will be returned.
     * @return string The name of the configured language.
     */
    public function getLanguage($no_resolve_for_default = false)
    {
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }
        if (empty($this->db_data['config_language']) && !$no_resolve_for_default) {
            global $config;
            return $config['language'];
        } else {
            return $this->db_data['config_language'];
        }
    }

    public function getDefaultInstockChangeComment($withdrawal = true)
    {
        if (!$this->isLoggedInUser()
            && !$this->current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return "???";
        }
        if($withdrawal) {
            return $this->db_data['config_instock_comment_w'];
        } else {
            return $this->db_data['config_instock_comment_a'];
        }
    }

    /**
     * Checks if a given password, is valid for this account.
     * @param $password string The password which should be checked.
     * @return bool True, if the password was valid.
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
        $this->current_user->tryDo(PermissionManager::USERS, UserPermission::DELETE);

        $loggedin_user = static::getLoggedInUser($this->database, $this->log);
        if ($loggedin_user->getID() == $this->getID()) {
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
     * @param $need_to_change_pw bool When true, the user has to change the password afterwards.
     * @throws Exception If an error occured.
     */
    public function setPassword($new_password, $need_to_change_pw = false, $check_pw_length = true)
    {
        if ($check_pw_length && strlen($new_password) < 6) {
            throw new Exception(sprintf(_("Das neue Password muss mindestens %d Zeichen lang sein"), 6));
        }
        if ($this->getID() == static::ID_ANONYMOUS) {
            throw new Exception(_("Das Password des anonymous Users kann nicht geändert werden!"));
        }
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $this->setAttributes(array("password" => $hash,
            "need_pw_change" => $need_to_change_pw));
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
     * Sets a new Email address.
     * @param $new_email string The new email address.
     */
    public function setEmail($new_email)
    {
        $this->setAttributes(array('email' => $new_email));
    }

    /**
     * Sets a new Department.
     * @param $new_department string The new department
     */
    public function setDepartment($new_department)
    {
        $this->setAttributes(array('department' => $new_department));
    }

    /**
     * Set the configured theme for this User.
     * @param $new_theme string The new configured theme. Set to empty string to use system-wide config.
     */
    public function setTheme($new_theme)
    {
        $this->setAttributes(array('config_theme' => $new_theme));
    }

    /**
     * Set the configured language for this User.
     * @param $new_language string The new configured language. Set to empty string to use system-wide config.
     */
    public function setLanguage($new_language)
    {
        $this->setAttributes(array('config_language' => $new_language));
    }

    /**
     * Set the configured timezone for this User.
     * @param $new_timezone string The new configured timezone. Set to empty string to use system-wide config.
     */
    public function setTimezone($new_timezone)
    {
        $this->setAttributes(array('config_timezone' => $new_timezone));
    }

    /**
     * Sets the default message for instock changes (withdrawal or addition)
     * @param $withdrawal_message string The withdrawal message. Set to null, to not change it.
     * @param $addition_message string The addition message. Set to null, to not change it.
     */
    public function setDefaultInstockChangeComment($withdrawal_message = null, $addition_message = null)
    {
        if (is_string($withdrawal_message)) {
           $this->setAttributes(array('config_instock_comment_w' => $withdrawal_message));
        }

        if(is_string($addition_message)) {
            $this->setAttributes(array('config_instock_comment_a' => $addition_message));
        }
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

    /**
     * Check if this user must change its password.
     * @param $ignore_rehash bool Set this to true, if you want to ignore Password change needs,
     * because PHP has a better password hash algo.
     * @return bool True, if the user must change its password.
     */
    public function getNeedPasswordChange($ignore_rehash = false)
    {
        //If 'need_pw_change' does not exist in db_data, you dont has to change PW.
        if (!isset($this->db_data['need_pw_change'])) {
            return false;
        }

        //Check if password needs rehash, because a better algo is available.
        if (!$ignore_rehash) {
            if ($this->db_data['password'] !== "" &&
                password_needs_rehash($this->db_data['password'], PASSWORD_DEFAULT)) {
                return true;
            }
        }

        if ($this->getID() == static::ID_ANONYMOUS) {
            return false; //Anonymous never has to change PW, because he has none.
        }

        return $this->db_data['need_pw_change'];
    }

    /**
     * Sets the "need_pw_change" attribute. When set to true, the user is asked to change his password after login.
     * @param $new_val bool The value to which the need_pw_change attribute should be set to.
     */
    public function setNeedPasswordChange($new_val)
    {
        $this->setAttributes(array('need_pw_change' => $new_val));
    }

    public function setAttributes($new_values)
    {
        //Normalize username
        if (isset($new_values['name'])) {
            $new_values['name'] = static::normalizeUsername($new_values['name']);
        }

        //User can not change its own User and Group permissions.
        if ($this->isLoggedInUser()) {
            unset($new_values['perms_users']);
            unset($new_values['perms_groups']);
        }

        //Override this function, so we can check if user has the needed permissions.
        $arr = array();

        //Make exception for logged in user
        //Anonymous user can not change its own settings.
        if ($this->isLoggedInUser() && $this->getID() != static::ID_ANONYMOUS) {
            if (isset($new_values['password'])) {
                $arr['password'] = $new_values['password'];
            }
            if (isset($new_values['need_pw_change'])) {
                $arr['need_pw_change'] = $new_values['need_pw_change'];
            }
            if ($this->current_user->canDo(PermissionManager::SELF, SelfPermission::EDIT_INFOS)) {
                if (isset($new_values['first_name'])) {
                    $arr['first_name'] = $new_values['first_name'];
                }
                if (isset($new_values['last_name'])) {
                    $arr['last_name'] = $new_values['last_name'];
                }
                if (isset($new_values['department'])) {
                    $arr['department'] = $new_values['department'];
                }
                if (isset($new_values['email'])) {
                    $arr['email'] = $new_values['email'];
                }
            }
            if ($this->current_user->canDo(PermissionManager::SELF, UserPermission::EDIT_USERNAME)) {
                if (isset($new_values['name'])) {
                    $arr['name'] = $new_values['name'];
                }
            }

            //A user can always change its own configuration.
            if (isset($new_values['config_theme'])) {
                $arr['config_theme'] = $new_values['config_theme'];
            }
            if (isset($new_values['config_timezone'])) {
                $arr['config_timezone'] = $new_values['config_timezone'];
            }
            if (isset($new_values['config_language'])) {
                $arr['config_language'] = $new_values['config_language'];
            }
            if (isset($new_values['config_instock_comment_w'])) {
                $arr['config_instock_comment_w'] = $new_values['config_instock_comment_w'];
            }
            if (isset($new_values['config_instock_comment_a'])) {
                $arr['config_instock_comment_a'] = $new_values['config_instock_comment_a'];
            }
        }

        if ($this->current_user->canDo(PermissionManager::USERS, UserPermission::EDIT_USERNAME)) {
            if (isset($new_values['name'])) {
                $arr['name'] = $new_values['name'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::USERS, UserPermission::CHANGE_GROUP)) {
            if (isset($new_values['group_id'])) {
                $arr['group_id'] = $new_values['group_id'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::USERS, UserPermission::EDIT_INFOS)) {
            if (isset($new_values['first_name'])) {
                $arr['first_name'] = $new_values['first_name'];
            }
            if (isset($new_values['last_name'])) {
                $arr['last_name'] = $new_values['last_name'];
            }
            if (isset($new_values['department'])) {
                $arr['department'] = $new_values['department'];
            }
            if (isset($new_values['email'])) {
                $arr['email'] = $new_values['email'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::USERS, UserPermission::SET_PASSWORD)) {
            if (isset($new_values['password'])) {
                $arr['password'] = $new_values['password'];
            }
            if (isset($new_values['need_pw_change'])) {
                $arr['need_pw_change'] = $new_values['need_pw_change'];
            }
        }
        if ($this->current_user->canDo(PermissionManager::USERS, UserPermission::EDIT_PERMISSIONS)) {
            foreach ($new_values as $key => $content) {
                if (strpos($key, "perms_") !== false) {
                    $arr[$key] = $content;
                }
            }
        }

        if ($this->current_user->canDo(PermissionManager::USERS, UserPermission::CHANGE_USER_SETTINGS)) {
            if (isset($new_values['config_theme'])) {
                $arr['config_theme'] = $new_values['config_theme'];
            }
            if (isset($new_values['config_timezone'])) {
                $arr['config_timezone'] = $new_values['config_timezone'];
            }
            if (isset($new_values['config_language'])) {
                $arr['config_language'] = $new_values['config_language'];
            }
        }

        if (!empty($arr)) {
            parent::setAttributes($arr);
        }
    }

    /**
     * Check if this user is the one currently logged in.
     * @return bool True, if this is the user, who is currently logged in.
     */
    public function isLoggedInUser()
    {
        /*
        return $this->getID() == $this->current_user->getID(); */
        return $this->db_data['id'] === $this->current_user->db_data['id'];
    }

    /*******************************************************************************
     *
     * Permission Functions
     *
     ******************************************************************************/

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
     * @throws Exception
     */
    public function &getParentPermissionManager()
    {
        $parent = $this->getGroup();
        if ($parent->getID() === 0) {
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

            //Only show, if the user is not logged in.
            if ($this->getID() == static::ID_ANONYMOUS) {
                $str .= _('<br><br>Bitte loggen sie sich ein:') . ' <a href="login.php?redirect=' . urlencode($_SERVER["REQUEST_URI"]) . '">' . _('Login'). '</a>';
            }

            $this->log->userNotAllowed("$group_title->$perm_description: $op_description (" .  basename($_SERVER['PHP_SELF']) . ")");

            throw new UserNotAllowedException($str);
        }
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
        $username = static::normalizeUsername($username);
        $query = 'SELECT * FROM users WHERE name = ?';
        $query_data = $database->query($query, array($username));

        if (count($query_data) > 1) {
            throw new Exception(_("Die Abfrage des Nutzernamens hat mehrere Nutzer ergeben"));
        }

        if (count($query_data) == 0) {
            throw new Exception(_("Kein Benutzer mit folgendem Benutzernamen vorhanden:") . " " .$username);
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
        return self::getLoggedInID() > static::ID_ANONYMOUS;
    }

    /**
     * Gets the id of the currently logged in user.
     * @return int The id of the logged in user, if someone is logged in. Else 0 (anonymous).
     */
    public static function getLoggedInID()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']<=static::ID_ANONYMOUS) {
            return static::ID_ANONYMOUS;   //User anonymous.
        } else {
            try {
                $db = new Database();
                $query = "SELECT id FROM users WHERE id = ?";
                $results = $db->query($query, array($_SESSION['user']));
                if (count($results) !== 1) { //If not exactly one user with the id exists, we are not logged in.
                    return static::ID_ANONYMOUS;
                }
            } catch (Exception $ex) {
                return static::ID_ANONYMOUS; //If an error happened, we are not logged in.
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
     * @throws Exception
     */
    public static function getLoggedInUser(&$database = null, &$log = null)
    {
        $loggedin_ID    = self::getLoggedInID();

        if (is_null($database) || is_null($log)) {
            $database = new Database();
            $log = new Log($database);
        }
        if (!is_object(static::$loggedin_user)) {
            if ($database->doesTableExist('users')) {
                $var = null;
                static::$loggedin_user = new User($database, $var, $log, $loggedin_ID);
            } else {
                $var = null;
                //When no user table exists, create a fake user, with all needed permission
                return new User($database, $var, $log, 0, array("perms_system_database" => 21845));
            }
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
        if (empty($password) || !$user->isPasswordValid($password)) { //If $password is set, and wrong.
            return false;
        }
        //Open session, so we can edit $_SESSION var.
        @session_start();
        $_SESSION['user'] = $user->getID();
        session_write_close();

        //Write the event to the log:
        $user->log->userLogsIn($user, getConnectionIPAddress());

        return true;
    }


    /**
     * Log out the current user and set logged in to anonymous.
     * @return boolean True, if the user was successful logged out.
     */
    public static function logout()
    {

        //Write the event to the log:
        self::getLoggedInUser()->log->userLogsOut(self::getLoggedInUser(), getConnectionIPAddress());

        @session_start();
        $_SESSION['user'] = static::ID_ANONYMOUS;
        session_write_close();

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
     * @throws Exception
     */
    public static function buildHTMLList(&$database, &$current_user, &$log, $selected_id = -1)
    {
        $users = self::getAllUsers($database, $current_user, $log);
        $html = array();
        foreach ($users as $user) {
            /** @var User $user */
            $selected = $user->getID() == $selected_id ? " selected" : "";
            $no_pw    = $user->hasNoPassword() ? _("  [Kein Password]") : "";
            $is_current =  $user->isLoggedInUser() ? _("  [Aktueller Nutzer]") : "";
            $html[] = "<option value='" . $user->getID()  . "'" . $selected . ">" . $user->getFullName(true) . $no_pw . $is_current . "</option>";
        }
        return implode("\n", $html);
    }

    /**
     * Returns all users of the database
     * @param Database $database The database which should be used for requests.
     * @param User $current_user The user which should be used for requests.
     * @param Log $log The log which should be used for requests.
     * @return User[] Any array of all users in the database.
     * @throws Exception
     */
    public static function getAllUsers(&$database, &$current_user, &$log)
    {
        if (!$current_user->canDo(PermissionManager::USERS, UserPermission::READ)) {
            return array($current_user);
        }

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
     * @param $data array Additional data that should be set. (See database colums for index names)
     * @return Base\NamedDBElement|User
     * @throws Exception
     */
    public static function add(&$database, &$current_user, &$log, $name, $group_id, $data)
    {
        $current_user->tryDo(PermissionManager::USERS, UserPermission::CREATE);

        //User needs Change group permission, to create a user with a group.
        if (!$current_user->canDo(PermissionManager::USERS, UserPermission::CHANGE_GROUP)) {
            $group_id = 0; //Set group id to Root Group.
        }

        return parent::addByArray(
            $database,
            $current_user,
            $log,
            'users',
            array(  'name'                      => static::normalizeUsername($name),
                'group_id'                      => $group_id)
            + $data
        );
    }

    /***************************************************************************************
     * Helper functions
     **************************************************************************************/
    /**
     * Normalize a username.
     * This process contains: trim trailing/leading whitespaces, replace whitespaces with chars, and remove all non ASCII chars.
     * @param $username string The username that should be normalized.
     * @return string The normalized username.
     */
    public static function normalizeUsername($username)
    {
        //Strip leading and trailing whitespaces.
        $username = trim($username);
        //Replace all whitespace characters with an underscore
        $username = preg_replace('/\s+/', '_', $username);

        //Remove invalid characters.
        $username = filter_var($username, FILTER_SANITIZE_EMAIL);

        return $username;
    }
}
