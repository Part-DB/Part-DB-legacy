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

include_once __DIR__ . '/start_session.php';

use PartDB\Category;
use PartDB\Database;
use PartDB\Footprint;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Manufacturer;
use PartDB\Part;
use PartDB\Permissions\PartAttributePermission;
use PartDB\Permissions\PartPermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Storelocation;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

$show_no_orderdetails_parts = $_REQUEST['show_no_orderdetails_parts'] ?? false;

$page               = isset($_REQUEST['page'])              ? (int)$_REQUEST['page']            : 1;
$limit              = isset($_REQUEST['limit'])             ? (int)$_REQUEST['limit']           : $config['table']['default_limit'];

$action = 'default';
if (isset($_REQUEST['change_show_no_orderdetails'])) {
    $action = 'change_show_no_orderdetails';
} elseif (isset($_POST['multi_action'])) {
    $action = 'multi_action';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Nicht mehr erhältliche Teile'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);

    $current_user->tryDo(PermissionManager::PARTS, PartPermission::OBSOLETE_PARTS);

    //Remember what page user visited, so user can return there, when he deletes a part.
    session_start();
    $_SESSION['part_delete_last_link'] = $_SERVER['REQUEST_URI'];
    session_write_close();
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
        case 'change_show_no_orderdetails':
            $reload_site = true;
            break;
        case 'multi_action':
            try {
                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
                    $n = substr_count($_REQUEST['selected_ids'], ',') + 1;
                    $messages[] = array('text' => sprintf(_('Sollen die %d gewählten Bauteile wirklich unwiederruflich gelöscht werden?'), $n),
                        'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('<br>Hinweise:'), 'strong' => true);
                    $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Alle Dateien dieses Bauteiles bleiben weiterhin erhalten.'));
                    $messages[] = array('html' => '<input type="hidden" name="action" value="delete_confirmed">', 'no_linebreak' => true);
                    $messages[] = array('html' => '<input type="hidden" name="selected_ids" value="' . $_REQUEST['selected_ids'] . '">');
                    $messages[] = array('html' => '<input type="hidden" name="target" value="' . $_REQUEST['target'] . '">', 'no_linebreak' => true);
                    $messages[] = array('html' => '<button class="btn btn-secondary" type="submit" value="">' . _('Nein, nicht löschen') . '</button>', 'no_linebreak' => true);
                    $messages[] = array('html' => '<button class="btn btn-danger" type="submit" name="multi_action" value="">' . _('Ja, Bauteile löschen') . '</button>');
                } else {
                    parsePartsSelection($database, $current_user, $log, $_REQUEST['selected_ids'], $_REQUEST['action'], $_REQUEST['target']);
                }
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
    }
}

if (isset($reload_site) && $reload_site && (! $config['debug']['request_debugging_enable'])) {
    // reload the site to avoid multiple actions by manual refreshing
    header('Location: show_obsolete_parts.php?show_no_orderdetails_parts='.($show_no_orderdetails_parts ? '1' : '0'));
}

/********************************************************************************
 *
 *   Generate Table
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $parts = Part::getObsoleteParts($database, $current_user, $log, $show_no_orderdetails_parts, $limit, $page);
        $table_loop = Part::buildTemplateTableArray($parts, 'obsolete_parts');
        $html->setVariable('table', $table_loop);
        $html->setVariable('table_rowcount', count($parts), 'int');

        $html->setVariable('pagination', generatePagination(
            'show_obsolete_parts.php?show_no_orderdetails_parts=' .($show_no_orderdetails_parts ? '1' : '0'),
            $page,
            $limit,
            Part::getObsoletePartsCount($database, $current_user, $log, $show_no_orderdetails_parts)
        ));
        $html->setVariable('page', $page);
        $html->setVariable('limit', $limit);
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        // obsolete parts
        $html->setVariable('show_no_orderdetails_parts', $show_no_orderdetails_parts, 'boolean');

        // global stuff
        $html->setVariable('disable_footprints', $config['footprints']['disable'], 'boolean');
        $html->setVariable('disable_manufacturers', $config['manufacturers']['disable'], 'boolean');
        $html->setVariable('disable_auto_datasheets', $config['auto_datasheets']['disable'], 'boolean');

        if ($current_user->canDo(PermissionManager::PARTS, PartPermission::MOVE)) {
            $root_category = Category::getInstance($database, $current_user, $log, 0);
            $html->setVariable('categories_list', $root_category->buildHtmlTree(0, true, false, '', 'c'));
        }
        if ($current_user->canDo(PermissionManager::PARTS_FOOTPRINT, PartAttributePermission::EDIT)) {
            $root_footprint = Footprint::getInstance($database, $current_user, $log, 0);
            $html->setVariable('footprints_list', $root_footprint->buildHtmlTree(0, true, false, '', 'f'));
        }
        if ($current_user->canDo(PermissionManager::PARTS_MANUFACTURER, PartAttributePermission::EDIT)) {
            $root_manufacturer = Manufacturer::getInstance($database, $current_user, $log, 0);
            $html->setVariable('manufacturers_list', $root_manufacturer->buildHtmlTree(0, true, false, '', 'm'));
        }
        if ($current_user->canDo(PermissionManager::PARTS_MANUFACTURER, PartAttributePermission::EDIT)) {
            $root_location = Storelocation::getInstance($database, $current_user, $log, 0);
            $html->setVariable('storelocations_list', $root_location->buildHtmlTree(0, true, false, '', 's'));
        }

        $html->setVariable('can_edit', $current_user->canDo(PermissionManager::PARTS, PartPermission::EDIT));
        $html->setVariable('can_delete', $current_user->canDo(PermissionManager::PARTS, PartPermission::DELETE));
        $html->setVariable('can_create', $current_user->canDo(PermissionManager::PARTS, PartPermission::CREATE));
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

/********************************************************************************
 *
 *   Generate HTML Output
 *
 *********************************************************************************/


//If a ajax version is requested, say this the template engine.
if (isset($_REQUEST['ajax'])) {
    $html->setVariable('ajax_request', true);
}

$html->printHeader($messages);

if (! $fatal_error) {
    $html->printTemplate('show_obsolete_parts');
}

$html->printFooter();
