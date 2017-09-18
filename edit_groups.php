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
use PartDB\Group;
use PartDB\HTML;
use PartDB\Log;
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

$selected_id                = isset($_REQUEST['selected_id'])   ? (integer)$_REQUEST['selected_id'] : 0;
$new_name                   = isset($_REQUEST['name'])          ? (string)$_REQUEST['name']         : '';
$new_parent_id              = isset($_REQUEST['parent_id'])     ? (integer)$_REQUEST['parent_id']   : 0;
$new_comment                = isset($_REQUEST['comment'])       ? (string)$_REQUEST['comment']      : "";
$add_more                   = isset($_REQUEST['add_more']);

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

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Baugruppen'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);
    $root_group         = new Group($database, $current_user, $log, 0);

    if ($selected_id > 0) {
        $selected_group = new Group($database, $current_user, $log, $selected_id);
    } else {
        $selected_group = null;
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
                $new_group = Group::add($database, $current_user, $log, $new_name, $new_parent_id);
                $new_group->setComment($new_comment);

                $html->setVariable('refresh_navigation_frame', true, 'boolean');

                //Apply permissions
                $new_group->getPermissionManager()->parsePermissionsFromRequest($_REQUEST);

                if (! $add_more) {
                    $selected_group = $new_group;
                    $selected_id = $selected_group->getID();
                }
            } catch (Exception $e) {
                $messages[] = array('text' => _('Die neue Baugruppe konnte nicht angelegt werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'delete':
            try {
                if (! is_object($selected_group)) {
                    throw new Exception(_('Es ist keine Baugruppe markiert oder es trat ein Fehler auf!'));
                }

                $users = $selected_group->getUsers();
                $count = count($users);

                $messages[] = array('text' => sprintf(_('Soll die Gruppe "%s'.
                    '" wirklich unwiederruflich gelöscht werden?'), $selected_group->getFullPath()), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('<br>Hinweise:'), 'strong' => true);
                if ($count > 0) {
                    $messages[] = array(
                        'text' => sprintf(_('&nbsp;&nbsp;&bull; Es gibt noch %d Benutzer mit dieser Gruppe! Daher kann diese Gruppe nicht gelöscht werden'), $count)
                    , 'strong' => true, 'color' => 'red');
                } else {
                    $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Es gibt keine Benutzer mit dieser Gruppe.'));
                    $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Beinhaltet diese Gruppe noch Untergruppen, dann werden diese eine Ebene nach oben verschoben.'));
                    $messages[] = array('html' => '<input type="hidden" name="selected_id" value="' . $selected_group->getID() . '">');
                    $messages[] = array('html' => '<input type="submit" class="btn btn-default" name="" value="' . _("Nein, nicht löschen") . '">', 'no_linebreak' => true);
                    $messages[] = array('html' => '<input type="submit" class="btn btn-danger" name="delete_confirmed" value="' . _('Ja, Gruppe löschen') . '">');
                }
            } catch (Exception $e) {
                $messages[] = array('text' => _('Es trat ein Fehler auf!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'delete_confirmed':
            try {
                if (! is_object($selected_group)) {
                    throw new Exception(_('Es ist keine Baugruppe markiert oder es trat ein Fehler auf!'));
                }

                $selected_group->delete();
                $selected_group = null;

                $html->setVariable('refresh_navigation_frame', true, 'boolean');
            } catch (Exception $e) {
                $messages[] = array('text' => _('Die Gruppe konnte nicht gelöscht werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'apply':
            try {
                if (! is_object($selected_group)) {
                    throw new Exception(_('Es ist keine Gruppe markiert oder es trat ein Fehler auf!'));
                }

                $selected_group->setAttributes(array( 'name'                  => $new_name,
                    'parent_id'             => $new_parent_id,
                    'comment'               => $new_comment));

                //Apply permissions
                $selected_group->getPermissionManager()->parsePermissionsFromRequest($_REQUEST);

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
        if (is_object($selected_group)) {
            $parent_id = $selected_group->getParentID();
            $html->setVariable('id', $selected_group->getID(), 'integer');
            $name = $selected_group->getName();
            $comment = $selected_group->getComment();
            //Permissions loop
            $perm_loop = $selected_group->getPermissionManager()->generatePermissionsLoop();
        } elseif ($action == 'add') {
            $parent_id = $new_parent_id;
            $name = $new_name;
            $comment = $new_comment;
            //Permissions loop
            if (isset($new_user)) {
                $perm_loop = $selected_group->getPermissionManager()->generatePermissionsLoop();
            } else {
                $perm_loop = \PartDB\Permissions\PermissionManager::defaultPermissionsLoop();
            }

        } else {
            $parent_id = 0;
            $name = '';
            $comment = "";
            $perm_loop = \PartDB\Permissions\PermissionManager::defaultPermissionsLoop();
        }

        $html->setVariable('name', $name, 'string');
        $html->setVariable('comment', $comment, 'string');

        $device_list = $root_group->buildHtmlTree($selected_id, true, false);
        $html->setVariable('group_list', $device_list, 'string');

        $html->setLoop("perm_loop", $perm_loop);

        $parent_device_list = $root_group->buildHtmlTree($parent_id, true, true);
        $html->setVariable('parent_group_list', $parent_device_list, 'string');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
        $fatal_error = true;
    }
}

/********************************************************************************
 *
 *   Generate HTML Output
 *
 *********************************************************************************/


//If a ajax version is requested, say this the template engine.
if (isset($_REQUEST["ajax"])) {
    $html->setVariable("ajax_request", true);
}

$reload_link = $fatal_error ? 'edit_groups.php' : '';    // an empty string means that the...
$html->printHeader($messages, $reload_link);             // ...reload-button won't be visible

if (! $fatal_error) {
    $html->printTemplate('edit_groups');
}

$html->printFooter();
