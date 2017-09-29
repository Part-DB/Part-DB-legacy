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

use PartDB\Database;
use PartDB\HTML;
use PartDB\Permissions\DatabasePermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\SystemPermission;
use PartDB\Updater\SystemUpdater;
use PartDB\Updater\UpdateStatus;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

$refresh = false;

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

$action = 'default';
if (isset($_POST['download'])) {
    $action = "download";
}
if (isset($_POST['update'])) {
    $action = "update";
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/


$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Systemupdate'));

try {
    $database = new Database();
    $log = new \PartDB\Log($database);
    $current_user = User::getLoggedInUser($database, $log);
    $current_user->tryDo(PermissionManager::SYSTEM, SystemPermission::UPDATE);

    $status = new UpdateStatus();

    $updater = new SystemUpdater(SystemUpdater::CHANNEL_DEV);
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Execute actions
 *
 *********************************************************************************/
if (!$fatal_error) { //Allow to save connection settings, even when a error happened.
    switch ($action) {
        case "default":
            break;
        case "download":
            $updater->downloadUpdate();
            $refresh = true;
            break;
        case "update":
            $updater->startUpdate();
            $refresh = true;
            break;
    }
}

/********************************************************************************
 *
 *   Set all HTML variables
 *
 *********************************************************************************/






if (! $fatal_error) {
    try {
        if ($status->getDownloading() || $status->getDownloading()){
            $refresh = true;
        }
        $html->setVariable('is_downloading', $status->getDownloading(), "bool");
        $html->setVariable('is_updating', $status->getUpdating(), "bool");
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
        $fatal_error = true;
    }
}




if($refresh ) {
    $html->setMeta(array('autorefresh' => 1000));
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

// an empty $reload_link means that the reload-button won't be visible
$reload_link = ($fatal_error || isset($database_update_executed)) ? 'system_database.php' : '';
$html->printHeader($messages, $reload_link);

if (!$fatal_error) {
    $html->printTemplate('update_start');
}


$html->printFooter();
