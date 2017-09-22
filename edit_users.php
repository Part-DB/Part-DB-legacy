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
use PartDB\Device;
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

$selected_id                = isset($_REQUEST['selected_id'])   ? (integer)$_REQUEST['selected_id'] : -1;
$new_name                   = isset($_REQUEST['name'])          ? (string)$_REQUEST['name']         : '';
$add_more                   = isset($_REQUEST['add_more']);

//Tab standard
$new_first_name             = isset($_REQUEST['first_name'])    ? (string)$_REQUEST['first_name']   : "";
$new_last_name              = isset($_REQUEST['last_name'])     ? (string)$_REQUEST['last_name']    : "";
$new_email                  = isset($_REQUEST['email'])         ? (string)$_REQUEST['email']        : "";
$new_department             = isset($_REQUEST['department'])    ? (string)$_REQUEST['department']   : "";
$new_group_id               = isset($_REQUEST['group_id'])      ? (int)$_REQUEST['group_id']        : 0;

//Tab "set password"
$new_password               = isset($_REQUEST['password_1'])    ? (string)$_REQUEST['password_1']   : "";
$new_password_2             = isset($_REQUEST['password_2'])    ? (string)$_REQUEST['password_2']   : "";
$must_change_pw             = isset($_REQUEST['must_change_pw']);

$action = 'default';
if (isset($_REQUEST["add"])) {
    $action = 'add';
}
if (isset($_REQUEST["delete"])) {
    $action = 'delete';
}
if (isset($_REQUEST["delete_confirmed"])) {
    $action = 'delete_confirmed';
}
if (isset($_REQUEST["apply"])) {
    $action = 'apply';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Benutzer'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);
    $root_group         = new \PartDB\Group($database, $current_user, $log, 0);

    //Check permissions
    $current_user->tryDo(PermissionManager::USERS, UserPermission::READ);

    if ($selected_id > -1) {
        $selected_user = new User($database, $current_user, $log, $selected_id);
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
                    "email" => $new_email,
                    "group_id" => $new_group_id
                );

                $new_user = User::add($database, $current_user, $log, $new_name, $new_group_id, $data);


                $html->setVariable('refresh_navigation_frame', true, 'boolean');
                //Apply permissions
                $new_user->getPermissionManager()->parsePermissionsFromRequest($_REQUEST);

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
                $messages[] = array('html' => '<input type="submit" class="btn btn-default" name="" value="'._("Nein, nicht löschen").'">', 'no_linebreak' => true);
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

                //Apply permissions
                $selected_user->getPermissionManager()->parsePermissionsFromRequest($_REQUEST);

                //When user wants to set a new password
                if ($new_password !== "") {
                    try {
                        if ($new_password !== $new_password_2) {
                            throw new Exception(_("Das neue Password und die Bestätigung müssen übereinstimmen!"));
                        }

                        $selected_user->setPassword($new_password, $must_change_pw);
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

            //Permissions loop
            $perm_loop = $selected_user->getPermissionManager()->generatePermissionsLoop($perm_readonly);

        } elseif ($action == 'add') {
            $name = $new_name;
            $first_name = $new_first_name;
            $last_name = $new_last_name;
            $email = $new_email;
            $department = $new_department;
            $no_password = false;
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
        }

        $html->setVariable('name', $name, 'string');
        $html->setVariable('first_name', $first_name, 'string');
        $html->setVariable('last_name', $last_name,'string');
        $html->setVariable('email', $email, 'string');
        $html->setVariable('department', $department, 'string');
        $html->setVariable('no_password', $no_password, 'string');

        $html->setVariable('group_list', $root_group->buildHtmlTree($group_id), 'string');

        $user_list = User::buildHTMLList($database, $current_user, $log, $selected_id);
        $html->setVariable('user_list', $user_list, 'string');

        $html->setLoop("perm_loop", $perm_loop);

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
