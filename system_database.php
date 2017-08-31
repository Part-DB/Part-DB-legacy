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

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

$db_type                    = isset($_REQUEST['db_type'])        ? (string)$_REQUEST['db_type']              : 'mysql';
$db_charset                 = isset($_REQUEST['db_charset'])     ? (string)$_REQUEST['db_charset']           : 'utf8';
$db_host                    = isset($_REQUEST['db_host'])        ? (string)$_REQUEST['db_host']              : 'localhost';
$db_name                    = isset($_REQUEST['db_name'])        ? (string)$_REQUEST['db_name']              : '';
$db_user                    = isset($_REQUEST['db_user'])        ? (string)$_REQUEST['db_user']              : '';
$db_password                = isset($_REQUEST['db_password'])    ? trim((string)$_REQUEST['db_password'])    : '';
$admin_password             = isset($_REQUEST['admin_password']) ? trim((string)$_REQUEST['admin_password']) : '';
$automatic_updates_enabled  = isset($_REQUEST['automatic_updates_enabled']);

$action = 'default';
if (isset($_REQUEST["apply_connection_settings"])) {
    $action = 'apply_connection_settings';
}
if (isset($_REQUEST["apply_auto_updates"])) {
    $action = 'apply_auto_updates';
}
if (isset($_REQUEST["make_update"])) {
    $action = 'make_update';
}
if (isset($_REQUEST["make_new_update"])) {
    $action = 'make_new_update';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Datenbank');

try {
    $database = new Database();
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Execute actions
 *
 *********************************************************************************/

switch ($action) {
    case 'apply_connection_settings':
        $config_old = $config;
        try {
            if ($config['is_online_demo']) {
                break;
            }

            if (! isAdminPassword($admin_password)) {
                throw new Exception(_('Das Administratorpasswort ist falsch!'));
            }

            $config['db']['type'] = $db_type;
            //$config['db']['charset'] = $db_charset; // temporarly deactivated
            $config['db']['host'] = $db_host;
            $config['db']['name'] = $db_name;
            $config['db']['user'] = $db_user;
            $config['db']['password'] = $db_password;
            saveConfig();
            header('Location: system_database.php'); // Reload the page that we can see if the new settings are stored successfully
        } catch (Exception $e) {
            $config = $config_old; // reload the old config
            $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
            $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
        }
        break;

    case 'apply_auto_updates':
        $config_old = $config;
        try {
            $config['db']['auto_update'] = $automatic_updates_enabled;
            saveConfig();
            //header('Location: system_database.php'); // Reload the page that we can see if the new settings are stored successfully --> does not work correctly?!
        } catch (Exception $e) {
            $config = $config_old; // reload the old config
            $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
            $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
        }
        break;

    case 'make_new_update':
        // start the next update attempt from the beginning, not from the step of the last error
        $config['db']['update_error']['version']    = -1;
        $config['db']['update_error']['next_step']  = 0;
    // no break
    case 'make_update':
        try {
            if (! is_object($database)) {
                throw new Exception(_('Es konnte keine Verbindung mit der Datenbank hergestellt werden!'));
            }

            $database_update_executed = true;
            $update_log = $database->update();
            foreach ($update_log as $message) {
                $messages[] = array('text' => nl2br($message['text']), 'color' => ($message['error'] == true ? 'red' : 'black'));
            }
        } catch (Exception $e) {
            $messages[] = array('text' => _('Es trat ein Fehler auf!'), 'strong' => true, 'color' => 'red');
            $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
        }
        $messages[count($messages)-1]['text'] .= nl2br("\n");
        break;
}

/********************************************************************************
 *
 *   Set all HTML variables
 *
 *********************************************************************************/

$html->setVariable('is_online_demo', $config['is_online_demo'], 'boolean');
$html->setLoop('db_type_loop', arrayToTemplateLoop($config['db_types'], $config['db']['type']));
$html->setLoop('db_charset_loop', arrayToTemplateLoop($config['db_charsets'], $config['db']['charset']));
$html->setVariable('db_host', $config['db']['host'], 'string');
$html->setVariable('db_name', $config['db']['name'], 'string');
$html->setVariable('db_user', $config['db']['user'], 'string');
$html->setVariable('automatic_updates_enabled', $config['db']['auto_update'], 'boolean');
$html->setVariable('refresh_navigation_frame', (isset($database_update_executed) && $database_update_executed), 'boolean');

if (! $fatal_error) {
    try {
        $current = $database->getCurrentVersion();
        $latest = $database->getLatestVersion();
        $html->setVariable('current_version', $current, 'integer');
        $html->setVariable('latest_version', $latest, 'integer');
        $html->setVariable('update_required', ($latest > $current), 'boolean');
        $html->setVariable('last_update_failed', ($config['db']['update_error']['version'] == $current), 'boolean');

        if (($current > 0) && ($current < 13) && ($latest >= 13)) { // v12 to v13 was a huge update! show warning!
            $messages[] = array('text' =>   _('Achtung!<br><br>'.
                'Das Datenbankupdate auf Version 13 ist sehr umfangreich, es finden sehr viele Veränderungen statt.<br>'.
                'Es wird dringend empfohlen, vor dem Update eine Sicherung der Datenbank anzulegen, '.
                'damit diese im Fehlerfall wiederhergestellt, und so ein Datenverlust verhindert werden kann.<br>'.
                'Die Entwickler von Part-DB übernehmen keinerlei Haftung für Schäden, die durch fehlgeschlagene Updates, '.
                'Fehler in der Software oder durch andere Ursachen hervorgerufen werden.'),
                'strong' => true, 'color' => 'red', );
        } elseif (($current > 0) && ($latest > $current)) { // normal update...we will show a hint
            $messages[] = array('text' =>   _('Hinweis:<br>'.
                'Es wird dringend empfohlen, vor jedem Datenbankupdate eine Sicherung der Datenbank anzulegen.<br>'.
                'Die Entwickler von Part-DB übernehmen keinerlei Haftung für Schäden, die durch fehlgeschlagene Updates, '.
                'Fehler in der Software oder durch andere Ursachen hervorgerufen werden.'),
                'strong' => true, 'color' => 'red', );
        }
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
        $fatal_error = true;
    }
}

$html->setVariable('hide_status', $fatal_error, 'boolean');

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

//if (! $fatal_error) // we don't hide the site content if there is an error, because this way we can set the database connection data
$html->printTemplate('system_database');

$html->printFooter();
