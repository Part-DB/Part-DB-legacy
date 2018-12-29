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

/*
 * Please note:
 *  The files "edit_categories.php", "edit_footprints.php", "edit_manufacturers.php",
 *  "edit_suppliers.php", "edit_devices.php", "edit_storelocations.php" and "edit_filetypes.php"
 *  are quite similar.
 *  If you make changes in one of them, please check if you should change the other files too.
 */

include_once('start_session.php');

use PartDB\Database;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\UserPermission;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *   Notes:
 *       - "$selected_id == 0" means that we will show the form for creating a new device
 *       - the $new_* variables contains the new values after editing an existing
 *           or creating a new device
 *
 *********************************************************************************/

$selected_id                = isset($_REQUEST['selected_id'])   ? (int)$_REQUEST['selected_id'] : -1;
$new_name                   = isset($_POST['name'])          ? (string)$_POST['name']         : '';
$add_more                   = isset($_POST['add_more']);

//Tab standard
$new_first_name             = isset($_POST['first_name'])    ? (string)$_POST['first_name']   : "";
$new_last_name              = isset($_POST['last_name'])     ? (string)$_POST['last_name']    : "";
$new_email                  = isset($_POST['email'])         ? (string)$_POST['email']        : "";
$new_department             = isset($_POST['department'])    ? (string)$_POST['department']   : "";
$new_group_id               = isset($_POST['group_id'])      ? (int)$_POST['group_id']        : 0;

//Tab "set password"
$new_password               = isset($_POST['password_1'])    ? (string)$_POST['password_1']   : "";
$new_password_2             = isset($_POST['password_2'])    ? (string)$_POST['password_2']   : "";
$must_change_pw             = isset($_POST['must_change_pw']);

//Tab configuration
$new_theme          = isset($_POST['custom_css'])        ? $_POST['custom_css']               : "";
$new_timezone       = isset($_POST['timezone'])          ? $_POST['timezone']                 : "";
$new_language       = isset($_POST['language'])          ? $_POST['language']                 : "";
$new_comment_withdrawal = isset($_POST['default_comment_withdrawal']) ? $_POST['default_comment_withdrawal'] : null;
$new_comment_addition = isset($_POST['default_comment_addition']) ? $_POST['default_comment_addition'] : null;


$action = 'default';
if (isset($_POST["add"])) {
    $action = 'add';
}
if (isset($_POST["delete"])) {
    $action = 'delete';
}
if (isset($_POST["delete_confirmed"])) {
    $action = 'delete_confirmed';
}
if (isset($_POST["apply"])) {
    $action = 'apply';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Benutzer'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);
    $root_group         = \PartDB\Group::getInstance($database, $current_user, $log, 0);

    //Check permissions
    $current_user->tryDo(PermissionManager::USERS, UserPermission::READ);

    if ($selected_id > -1) {
        $selected_user = User::getInstance($database, $current_user, $log, $selected_id);
    } else {
        $selected_device = null;
    }
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Execute actions
 *
 *********************************************************************************/

if (! $fatal_error) {
    switch ($action) {
        case 'add':
            try {
                $data = array("first_name" => $new_first_name,
                    "last_name" => $new_last_name,
                    "department" => $new_department,
                    "email" => $new_email
                );

                $new_user = User::add($database, $current_user, $log, $new_name, $new_group_id, $data);

                //Dont check if this value is set, because empty string is a valid value.
                $new_user->setTheme($new_theme);
                $new_user->setLanguage($new_language);
                $new_user->setTimezone($new_timezone);
                $new_user->setDefaultInstockChangeComment($new_comment_withdrawal, $new_comment_addition);

                $html->setVariable('refresh_navigation_frame', true, 'boolean');
                //Apply permissions
                $new_user->getPermissionManager()->parsePermissionsFromRequest($_POST);

                //When user wants to set a new password
                if ($new_password !== "") {
                    try {
                        if ($new_password !== $new_password_2) {
                            throw new Exception(_("Das neue Password und die Bestätigung müssen übereinstimmen!"));
                        }

                        $new_user->setPassword($new_password, $must_change_pw);
                    } catch (Exception $e) {
                        $messages[] = array('text' => _('Das Password des Users konnte nicht geändert werden!'), 'strong' => true, 'color' => 'red');
                        $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                    }
                }

                if (! $add_more) {
                    $selected_user = $new_user;
                    $selected_id = $selected_user->getID();
                }
            } catch (Exception $e) {
                $messages[] = array('text' => _('Der neue Benutzer konnte nicht angelegt werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;
        case 'delete':
            try {
                if (! is_object($selected_user)) {
                    throw new Exception(_('Es ist keine Nutzer gewählt oder es trat ein Fehler auf!'));
                }

                $messages[] = array('text' => sprintf(_('Soll der Benutzer "%s'.
                    '" wirklich unwiederruflich gelöscht werden?'), $selected_user->getFullName(true)), 'strong' => true, 'color' => 'red');
                $messages[] = array('html' => '<input type="hidden" name="selected_id" value="'.$selected_user->getID().'">');
                $messages[] = array('html' => '<input type="submit" class="btn btn-secondary" name="" value="'._("Nein, nicht löschen").'">', 'no_linebreak' => true);
                $messages[] = array('html' => '<input type="submit" class="btn btn-danger" name="delete_confirmed" value="'._('Ja, Nutzer löschen').'">');
            } catch (Exception $e) {
                $messages[] = array('text' => _('Es trat ein Fehler auf!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'delete_confirmed':
            try {
                if (! is_object($selected_user)) {
                    throw new Exception(_('Es ist keine Nutzer gewählt oder es trat ein Fehler auf!'));
                }

                $selected_user->delete();
                $selected_user = null;

                $html->setVariable('refresh_navigation_frame', true, 'boolean');
            } catch (Exception $e) {
                $messages[] = array('text' => _('Der Nutzer konnte nicht gelöscht werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;
        case 'apply':
            try {
                if (! is_object($selected_user)) {
                    throw new Exception(_('Es ist keine Baugruppe markiert oder es trat ein Fehler auf!'));
                }

                $selected_user->setAttributes(array( 'name'     => $new_name,
                    "first_name" => $new_first_name,
                    "last_name" => $new_last_name,
                    "department" => $new_department,
                    "email" => $new_email,
                    "group_id" => $new_group_id
                ));

                //Dont check if this value is set, because empty string is a valid value.
                $selected_user->setTheme($new_theme);
                $selected_user->setLanguage($new_language);
                $selected_user->setTimezone($new_timezone);

                $selected_user->setDefaultInstockChangeComment($new_comment_withdrawal, $new_comment_addition);

                //Apply permissions
                $selected_user->getPermissionManager()->parsePermissionsFromRequest($_POST);

                //When user wants to set a new password
                if ($new_password !== "") {
                    try {
                        if ($new_password !== $new_password_2) {
                            throw new Exception(_("Das neue Password und die Bestätigung müssen übereinstimmen!"));
                        }

                        $selected_user->setPassword($new_password, $must_change_pw, false);
                        $messages[] = array('text' => _("Das Passwort wurde erfolgreich geändert!"), 'strong' => true, 'color' => 'green');
                    } catch (Exception $e) {
                        $messages[] = array('text' => _('Das Password des Users konnte nicht geändert werden!'), 'strong' => true, 'color' => 'red');
                        $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                    }
                }

                $html->setVariable('refresh_navigation_frame', true, 'boolean');
            } catch (Exception $e) {
                $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;
    }
}


/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/

$html->setVariable('add_more', $add_more, 'boolean');

if (! $fatal_error) {
    try {
        $perm_readonly = ! $current_user->canDo(PermissionManager::USERS, UserPermission::EDIT_PERMISSIONS);

        if (isset($selected_user) && is_object($selected_user)) {
            $html->setVariable('id', $selected_user->getID(), 'integer');
            $name = $selected_user->getName();
            $first_name = $selected_user->getFirstName();
            $last_name = $selected_user->getLastName();
            $email = $selected_user->getEmail();
            $department = $selected_user->getDepartment();
            $no_password = $selected_user->hasNoPassword();
            $group_id   = $selected_user->getGroup()->getID();

            $html->setVariable('is_current_user', $selected_user->isLoggedInUser());
            $html->setVariable('datetime_added', $selected_user->getDatetimeAdded(true));
            $html->setVariable('last_modified', $selected_user->getLastModified(true));
            $last_modified_user = $selected_user->getLastModifiedUser();
            $creation_user = $selected_user->getCreationUser();
            if ($last_modified_user != null) {
                $html->setVariable('last_modified_user', $last_modified_user->getFullName(true), "string");
                $html->setVariable('last_modified_user_id', $last_modified_user->getID(), "int");
            }
            if ($creation_user != null) {
                $html->setVariable('creation_user', $creation_user->getFullName(true), "string");
                $html->setVariable('creation_user_id', $creation_user->getID(), "int");
            }


            //Configuration settings
            $html->setVariable('custom_css_loop', build_custom_css_loop($selected_user->getTheme(true), true));
            //Convert timezonelist, to a format, we can use
            $timezones_raw = DateTimeZone::listIdentifiers();
            $timezones = array();
            foreach ($timezones_raw as $timezone) {
                $timezones[$timezone] = $timezone;
            }
            $html->setVariable('timezone_loop', arrayToTemplateLoop($timezones, $selected_user->getTimezone(true)));
            $html->setVariable('language_loop', arrayToTemplateLoop($config['languages'], $selected_user->getLanguage(true)));

            $comment_addition = $selected_user->getDefaultInstockChangeComment(false);
            $comment_withdrawal = $selected_user->getDefaultInstockChangeComment(true);

            //Permissions loop
            $perm_loop = $selected_user->getPermissionManager()->generatePermissionsLoop($perm_readonly);
        } elseif ($action == 'add') {
            $name = $new_name;
            $first_name = $new_first_name;
            $last_name = $new_last_name;
            $email = $new_email;
            $department = $new_department;
            $no_password = false;
            $comment_addition = $new_comment_addition;
            $comment_withdrawal = $new_comment_withdrawal;
            //Permissions loop
            if (isset($new_user)) {
                $perm_loop = $new_user->getPermissionManager()->generatePermissionsLoop($perm_readonly);
            } else {
                $perm_loop = \PartDB\Permissions\PermissionManager::defaultPermissionsLoop($perm_readonly);
            }
            $group_id = $new_group_id;
        } else {
            $name = '';
            $first_name = "";
            $last_name = "";
            $email = "";
            $department = "";
            $no_password = false;
            $group_id = 0;
            $perm_loop = \PartDB\Permissions\PermissionManager::defaultPermissionsLoop($perm_readonly);

            $comment_addition = "";
            $comment_withdrawal = "";

            //Configuration settings
            $html->setVariable('custom_css_loop', build_custom_css_loop("", true));
            //Convert timezonelist, to a format, we can use
            $timezones_raw = DateTimeZone::listIdentifiers();
            $timezones = array();
            foreach ($timezones_raw as $timezone) {
                $timezones[$timezone] = $timezone;
            }
            $html->setVariable('timezone_loop', arrayToTemplateLoop($timezones, ""));
            $html->setVariable('language_loop', arrayToTemplateLoop($config['languages'], ""));
        }

        $html->setVariable('name', $name, 'string');
        $html->setVariable('first_name', $first_name, 'string');
        $html->setVariable('last_name', $last_name, 'string');
        $html->setVariable('email', $email, 'string');
        $html->setVariable('department', $department, 'string');
        $html->setVariable('no_password', $no_password, 'string');

        $html->setVariable('default_comment_addition', $comment_addition, "string");
        $html->setVariable('default_comment_withdrawal', $comment_withdrawal, "string");


        $html->setVariable(
            'group_list',
            $root_group->buildHtmlTree($group_id, true, true, _("Keine Gruppe")),
            'string'
        );

        $user_list = User::buildHTMLList($database, $current_user, $log, $selected_id);
        $html->setVariable('user_list', $user_list, 'string');

        $html->setVariable("perm_loop", $perm_loop);

        //$parent_device_list = $root_device->buildHtmlTree($parent_id, true, true);
        //$html->setVariable('parent_device_list', $parent_device_list, 'string');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
        $fatal_error = true;
    }
}

$html->setVariable('can_create', $current_user->canDo(PermissionManager::USERS, UserPermission::CREATE));
$html->setVariable('can_delete', $current_user->canDo(PermissionManager::USERS, UserPermission::DELETE));
$html->setVariable('can_password', $current_user->canDo(PermissionManager::USERS, UserPermission::SET_PASSWORD));
$html->setVariable('can_infos', $current_user->canDo(PermissionManager::USERS, UserPermission::EDIT_INFOS));
$html->setVariable('can_group', $current_user->canDo(PermissionManager::USERS, UserPermission::CHANGE_GROUP));
$html->setVariable('can_username', $current_user->canDo(PermissionManager::USERS, UserPermission::EDIT_USERNAME));
$html->setVariable('can_config', $current_user->canDo(PermissionManager::USERS, UserPermission::CHANGE_USER_SETTINGS));

$html->setVariable('can_visit_user', $current_user->canDo(PermissionManager::USERS, \PartDB\Permissions\UserPermission::READ));


/********************************************************************************
 *
 *   Generate HTML Output
 *
 *********************************************************************************/


//If a ajax version is requested, say this the template engine.
if (isset($_REQUEST["ajax"])) {
    $html->setVariable("ajax_request", true);
}

$reload_link = $fatal_error ? 'edit_users.php' : '';    // an empty string means that the...
$html->printHeader($messages, $reload_link);             // ...reload-button won't be visible

if (! $fatal_error) {
    $html->printTemplate('edit_users');
}

$html->printFooter();
