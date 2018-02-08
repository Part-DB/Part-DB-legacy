<?php
/*
    Part-DB Version 0.4+ "nextgen"
    Copyright (C) 2017 Jan Böhmer
    https://github.com/jbtronics

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

use PartDB\Database;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Part;
use PartDB\Permissions\PartPermission;
use PartDB\Permissions\PermissionManager;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

$page               = isset($_REQUEST['page'])              ? (integer)$_REQUEST['page']            : 1;
$limit              = isset($_REQUEST['limit'])             ? (integer)$_REQUEST['limit']           : $config['table']['default_limit'];

$mode               = isset($_REQUEST['mode'])              ? (string)$_REQUEST['mode']             : "last_modified";
$min_level          = isset($_REQUEST['min_level'])         ? (int)$_REQUEST['min_level']           : Log::LEVEL_DEBUG;
$filter_user        = isset($_REQUEST['filter_user'])       ? (int)$_REQUEST['filter_user']         : -1;
$search             = isset($_REQUEST['search'])            ? (string)$_REQUEST['search']           : "";
$filter_type        = isset($_REQUEST['filter_type'])       ? (int)$_REQUEST['filter_type']         : -1;

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Log'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);

    //$current_user->tryDo(PermissionManager::PARTS, PartPermission::SHOW_LAST_EDIT_PARTS);

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
 *   Generate Table
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $latest_first = true;

        /*
        if ($mode == "last_modified") {
            $parts = Part::getLastModifiedParts($database, $current_user, $log, $latest_first, $limit, $page);
            $count = Part::getLastModifiedPartsCount($database, $current_user, $log, $latest_first);
        } else {
            $parts = Part::getLastAddedParts($database, $current_user, $log, $latest_first, $limit, $page);
            $count = Part::getLastAddedPartsCount($database, $current_user, $log, $latest_first);
        }*/
        $entries = $log->getEntries(true, $min_level, $filter_user, $filter_type, $search, $limit, $page);
        $count = $log->getEntriesCount(true, $min_level, $filter_user, $filter_type, $search);

        $table_loop = $log->generateTemplateLoop($entries);
        $html->setLoop('log', $table_loop);
        $html->setVariable('log_rowcount', count($entries));

        $html->setLoop("pagination", generatePagination(
            "system_log.php?",
            $page,
            $limit,
            $count
        ));
        $html->setVariable("page", $page);
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

    $html->setVariable('min_level', $min_level, "int");
    $user_list = User::buildHTMLList($database, $current_user, $log, $filter_user);
    $html->setVariable('user_list', $user_list, "string");
    $html->setVariable('search', $search, "string");

    $html->setLoop('types_loop', Log::getLogTypesList());
    $html->setVariable('filter_type', $filter_type);


    // global stuff
    $html->setVariable('can_show_user', $current_user->canDo(PermissionManager::USERS, \PartDB\Permissions\UserPermission::READ), 'boolean');

    //$html->setVariable('mode', $mode, "string");

    $html->setVariable('use_modal_popup', $config['popup']['modal'], 'boolean');
    $html->setVariable('popup_width', $config['popup']['width'], 'integer');
    $html->setVariable('popup_height', $config['popup']['height'], 'integer');
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

$html->printHeader($messages);

if (! $fatal_error) {
    $html->printTemplate('main');
}

$html->printFooter();
