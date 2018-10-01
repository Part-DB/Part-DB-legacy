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

include_once('start_session.php');
/** @noinspection PhpIncludeInspection */
include_once(BASE.'/inc/lib.export.php');

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
$starttime = microtime(true); // this is to measure the time while debugging is active

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

$category_id        = isset($_REQUEST['cid'])               ? (integer)$_REQUEST['cid']             : 0;
$with_subcategories = isset($_REQUEST['subcat'])            ? (boolean)$_REQUEST['subcat']          : $config['table']['default_show_subcategories'];
$table_rowcount     = isset($_REQUEST['table_rowcount'])    ? (integer)$_REQUEST['table_rowcount']  : 0;

$page               = isset($_REQUEST['page'])              ? (integer)$_REQUEST['page']            : 1;
$limit              = isset($_REQUEST['limit'])             ? (integer)$_REQUEST['limit']           : $config['table']['default_limit'];

$export_format_id       = isset($_REQUEST['export_format'])     ? (integer)$_REQUEST['export_format']   : 0;

$action = 'default';
if (isset($_REQUEST['subcat_button'])) {
    $action = 'change_subcat_state';
} elseif (isset($_REQUEST['export'])) {
    $action = 'export';
} elseif (isset($_REQUEST["multi_action"])) {
    $action = "multi_action";
}

$selected_part_id = 0;
for ($i=0; $i<$table_rowcount; $i++) {
    $selected_part_id = isset($_REQUEST['id_'.$i]) ? (integer)$_REQUEST['id_'.$i] : 0;

    if (isset($_REQUEST['decrement_'.$i])) {
        $action = 'decrement';
        break;
    }

    if (isset($_REQUEST['increment_'.$i])) {
        $action = 'increment';
        break;
    }
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Teileansicht'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);

    if ($category_id < 1) {
        throw new Exception(_('Es wurde keine gültige Kategorien-ID übermittelt!'));
    }

    $category = new Category($database, $current_user, $log, $category_id);

    if ($selected_part_id > 0) {
        $part = new Part($database, $current_user, $log, $selected_part_id);
    } else {
        $part = null;
    }

    $html->setTitle(_('Teileansicht') . ': ' . $category->getName());

    //Remember what page user visited, so user can return there, when he deletes a part.
    session_start();
    $_SESSION["part_delete_last_link"] = $_SERVER['REQUEST_URI'];
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
        case 'change_subcat_state':
            $reload_site = true;
            break;

        case 'decrement': // remove one part
            try {
                if (! is_object($part)) {
                    throw new Exception('Es wurde keine gültige Bauteil-ID übermittelt!');
                }

                $part->withdrawalParts(1);

                $reload_site = true;
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'increment': // add one part
            try {
                if (! is_object($part)) {
                    throw new Exception(_('Es wurde keine gültige Bauteil-ID übermittelt!'));
                }

                $part->addParts(1);

                $reload_site = true;
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
        case "multi_action":
            try {
                if (isset($_REQUEST['action']) && $_REQUEST['action'] == "delete") {
                    $n = count(explode(",", $_REQUEST['selected_ids']));
                    $messages[] = array('text' => sprintf(_('Sollen die %d gewählten Bauteile wirklich unwiederruflich gelöscht werden?'), $n),
                        'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('<br>Hinweise:'), 'strong' => true);
                    $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Alle Dateien dieses Bauteiles bleiben weiterhin erhalten.'));
                    $messages[] = array('html' => '<input type="hidden" name="action" value="delete_confirmed">', 'no_linebreak' => true);
                    $messages[] = array('html' => '<input type="hidden" name="selected_ids" value="' . $_REQUEST['selected_ids'] . '">');
                    $messages[] = array('html' => '<input type="hidden" name="target" value="' . $_REQUEST['target'] . '">', 'no_linebreak' => true);
                    $messages[] = array('html' => '<button class="btn btn-default" type="submit" value="">' . _('Nein, nicht löschen') . '</button>', 'no_linebreak' => true);
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

/*
if (isset($reload_site) && $reload_site && (! $config['debug']['request_debugging_enable'])) {
    // reload the site to avoid multiple actions by manual refreshing
    header('Location: show_category_parts.php?cid='.$category_id.'&subcat='.$with_subcategories);
}*/

/********************************************************************************
 *
 *   Generate Table
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $parts = $category->getParts($with_subcategories, true, $limit, $page);
        $table_loop = Part::buildTemplateTableArray($parts, 'category_parts');
        $html->setVariable('table_rowcount', count($parts), 'integer');
        $html->setLoop('table', $table_loop);
        $html->setLoop("pagination", generatePagination("show_category_parts.php?cid=$category_id", $page, $limit, $category->getPartsCount($with_subcategories)));
        $html->setVariable("page", $page);
        $html->setVariable('limit', $limit);

        $html->setLoop('breadcrumb', $category->buildBreadcrumbLoop("show_category_parts.php", "cid", true, _("Kategorien")));

        //Export Parts
        if ($action == "export") {
            //When export then get all parts.
            $export_parts = $parts = $category->getParts($with_subcategories, true, 0, 0);
            $export_string = exportParts($export_parts, 'showparts', $export_format_id, true, 'category_parts');
        }
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}


$php_endtime = microtime(true); // For Debug informations

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $html->setVariable('with_subcategories', $with_subcategories, 'boolean');
        $html->setVariable('cid', $category->getID(), 'integer');
        $html->setVariable('category_name', $category->getName(), 'string');
        $html->setVariable('category_fullpath', $category->getFullPath(" / "), 'string');
        $html->setVariable('disable_footprints', ($config['footprints']['disable'] || $category->getDisableFootprints(true)), 'boolean');
        $html->setVariable('disable_manufacturers', ($config['manufacturers']['disable'] || $category->getDisableManufacturers(true)), 'boolean');
        $html->setVariable('disable_auto_datasheets', ($config['auto_datasheets']['disable'] || $category->getDisableAutodatasheets(true)), 'boolean');

        $html->setVariable('use_modal_popup', $config['popup']['modal'], 'boolean');
        $html->setVariable('popup_width', $config['popup']['width'], 'integer');
        $html->setVariable('popup_height', $config['popup']['height'], 'integer');

        $html->setLoop('export_formats', buildExportFormatsLoop('showparts'));

        if ($current_user->canDo(PermissionManager::PARTS, PartPermission::MOVE)) {
            $root_category = new Category($database, $current_user, $log, 0);
            $html->setVariable('categories_list', $root_category->buildHtmlTree(0, true, false, "", "c"));
        }
        if ($current_user->canDo(PermissionManager::PARTS_FOOTPRINT, PartAttributePermission::EDIT)) {
            $root_footprint = new Footprint($database, $current_user, $log, 0);
            $html->setVariable('footprints_list', $root_footprint->buildHtmlTree(0, true, false, "", "f"));
        }
        if ($current_user->canDo(PermissionManager::PARTS_MANUFACTURER, PartAttributePermission::EDIT)) {
            $root_manufacturer = new Manufacturer($database, $current_user, $log, 0);
            $html->setVariable('manufacturers_list', $root_manufacturer->buildHtmlTree(0, true, false, "", "m"));
        }
        if ($current_user->canDo(PermissionManager::PARTS_MANUFACTURER, PartAttributePermission::EDIT)) {
            $root_location = new Storelocation($database, $current_user, $log, 0);
            $html->setVariable('storelocations_list', $root_location->buildHtmlTree(0, true, false, "", "s"));
        }

        $html->setVariable('can_edit', $current_user->canDo(PermissionManager::PARTS, PartPermission::EDIT));
        $html->setVariable('can_delete', $current_user->canDo(PermissionManager::PARTS, PartPermission::DELETE));
        $html->setVariable('can_create', $current_user->canDo(PermissionManager::PARTS, PartPermission::CREATE));
        $html->setVariable('can_favor', $current_user->canDo(PermissionManager::PARTS, PartPermission::CHANGE_FAVORITE));

        $html->setVariable("other_panel_collapse", $config['other_panel']['collapsed'], "boolean");
        $html->setVariable("other_panel_position", $config['other_panel']['position'], "string");
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
if (isset($_REQUEST["ajax"])) {
    $html->setVariable("ajax_request", true);
}



$reload_link = $fatal_error ? 'show_category_parts.php?cid='.$category_id : ''; // an empty string means that the...
$html->printHeader($messages, $reload_link);                                   // ...reload-button won't be visible


if (! $fatal_error) {
    $html->printTemplate('show_category_parts');
}

// If debugging is enabled, print some debug informations
$debug_messages = array();
if ((! $fatal_error) && ($config['debug']['enable'])) {
    $endtime = microtime(true);
    $lifetime = (integer)(1000*($endtime - $starttime));
    $php_lifetime = (integer)(1000*($php_endtime - $starttime));
    $html_lifetime = (integer)(1000*($endtime - $php_endtime));
    $debug_messages[] = array('text' => 'Debug-Meldungen: ', 'strong' => true, 'color' => 'darkblue');
    $debug_messages[] = array('text' => 'Anzahl Teile in dieser Kategorie: '.(count($parts)), 'color' => 'darkblue');
    $debug_messages[] = array('text' => 'Gesamte Laufzeit: '.$lifetime.'ms', 'color' => 'darkblue');
    $debug_messages[] = array('text' => 'PHP Laufzeit: '.$php_lifetime.'ms', 'color' => 'darkblue');
    $debug_messages[] = array('text' => 'HTML Laufzeit: '.$html_lifetime.'ms', 'color' => 'darkblue');
}

$html->printFooter($debug_messages);
