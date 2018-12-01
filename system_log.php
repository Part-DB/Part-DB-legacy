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
use PartDB\Permissions\PartPermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\SystemPermission;
use PartDB\Permissions\UserPermission;
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
$target_type        = isset($_REQUEST['target_type'])       ? (int)$_REQUEST['target_type']         : -1;
$target_id          = isset($_REQUEST['target_id'])         ? (int)$_REQUEST['target_id']           : -1;
$datetime_min       = isset($_REQUEST['datetime_min'])      ? (string)$_REQUEST['datetime_min']     : "";
$datetime_max       = isset($_REQUEST['datetime_max'])      ? (string)$_REQUEST['datetime_max']     : "";

$selected_ids        = isset($_POST['selected_ids'])   ? $_POST['selected_ids'] : 0;

$action = 'default';
if (isset($_POST["delete_entries"]) && $selected_ids != 0) {
    $action = 'delete_entries';
}
if (isset($_POST["delete_entries_confirmed"]) && $selected_ids != 0) {
    $action = 'delete_entries_confirmed';
}

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

    //Only check for global permission if user cannot view its own log
    if (!$current_user->canDo(PermissionManager::SELF, \PartDB\Permissions\SelfPermission::SHOW_LOGS)) {
        $current_user->tryDo(PermissionManager::SYSTEM, \PartDB\Permissions\SystemPermission::SHOW_LOGS);
    } else {
        $current_user->tryDo(PermissionManager::SELF, \PartDB\Permissions\SelfPermission::SHOW_LOGS);
        //Restrict log view to your own entries
        $filter_user = $current_user->getID();
    }
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}


if (!$fatal_error) {
    try {
        switch ($action) {
            case "delete_entries":
                $n = count(explode(",", $_REQUEST['selected_ids']));
                $messages[] = array('text' => sprintf(_('Sollen die %d gewählten Logeinträge wirklich unwiederruflich gelöscht werden?'), $n),
                    'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('<br>Hinweise:'), 'strong' => true);
                $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Durch das Löschen von Einträgen kann die Historie verfälscht werden..'));
                $messages[] = array('html' => '<input type="hidden" name="selected_ids" value="' . $selected_ids . '">');
                $messages[] = array('html' => '<button class="btn btn-secondary" type="submit" name="abort" value="">' . _('Nein, nicht löschen') . '</button>', 'no_linebreak' => true);
                $messages[] = array('html' => '<button class="btn btn-danger" type="submit" name="delete_entries_confirmed" value="">' . _('Ja, Einträge löschen') . '</button>');

                break;
            case "delete_entries_confirmed":
                try {
                    $log->deleteSelected($selected_ids);
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;
        }
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
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
        $entries = $log->getEntries(
            true,
            $min_level,
            $filter_user,
            $filter_type,
            $search,
            $target_type,
            $target_id,
            $datetime_min,
            $datetime_max,
            $limit,
            $page
        );
        $count = $log->getEntriesCount(
            true,
            $min_level,
            $filter_user,
            $filter_type,
            $search,
            $target_type,
            $target_id,
            $datetime_min,
            $datetime_max
        );

        $table_loop = $log->generateTemplateLoop($entries);
        $html->setLoop('log', $table_loop);
        $html->setVariable('log_rowcount', count($entries));

        $extra = array("mode" => $mode,
            "min_level" => $min_level,
            "filter_user" => $filter_user,
            "search" => $search,
            "filter_type" => $filter_type,
            "target_type" => $target_type,
            "target_id" => $target_id,
            "datetime_min" => $datetime_min,
            "datetime_max" => $datetime_max);

        $html->setLoop("pagination", generatePagination(
            "system_log.php?",
            $page,
            $limit,
            $count,
            $extra
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

    $html->setVariable("target_type", $target_type);
    $html->setVariable('target_id', $target_id);


    $html->setVariable('datetime_min', $datetime_min);
    $html->setVariable('datetime_max', $datetime_max);

    // global stuff
    $html->setVariable('can_show_user', $current_user->canDo(PermissionManager::USERS, UserPermission::READ), 'boolean');
    $html->setVariable('can_change_user', $current_user->canDo(PermissionManager::SYSTEM, SystemPermission::SHOW_LOGS));
    $html->setVariable('can_delete_entries', $current_user->canDo(PermissionManager::SYSTEM, SystemPermission::DELETE_LOGS), 'bool');

    //$html->setVariable('mode', $mode, "string");
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
