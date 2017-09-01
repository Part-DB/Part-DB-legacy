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
 * Steps:
 *
 *  - set_locales
 *  - set_admin_password
 *  - set_db_settings
 *  - set_db_backup_path
 *
 */

include_once('start_session.php');

use PartDB\Database;
use PartDB\HTML;
use PartDB\Tools\SystemVersion;

$messages = array();
$fatal_error = false;

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

// step "set_locales"
$timezone       = isset($_REQUEST['timezone'])          ? (string)$_REQUEST['timezone']             : 'Europe/Berlin';
$language       = isset($_REQUEST['language'])          ? (string)$_REQUEST['language']             : 'de_DE';

// step "set_admin_password"
$adminpass_1    = isset($_REQUEST['adminpass_1'])       ? trim((string)$_REQUEST['adminpass_1'])    : '';
$adminpass_2    = isset($_REQUEST['adminpass_2'])       ? trim((string)$_REQUEST['adminpass_2'])    : '';

// step "set_db_settings"
$db_type        = isset($_REQUEST['db_type'])           ? (string)$_REQUEST['db_type']              : 'mysql';
$db_charset     = isset($_REQUEST['db_charset'])        ? (string)$_REQUEST['db_charset']           : 'utf8';
$db_host        = isset($_REQUEST['db_host'])           ? (string)$_REQUEST['db_host']              : 'localhost';
$db_name        = isset($_REQUEST['db_name'])           ? (string)$_REQUEST['db_name']              : 'part-db';
$db_user        = isset($_REQUEST['db_user'])           ? (string)$_REQUEST['db_user']              : '';
$db_password    = isset($_REQUEST['db_password'])       ? trim((string)$_REQUEST['db_password'])    : '';
$space_fix      = isset($_REQUEST['space_fix'])         ? true                                      : false;

// step "set_db_backup_path"
$db_backup_name = isset($_REQUEST['db_backup_name'])    ? (string)$_REQUEST['db_backup_name']       : '';
$db_backup_path = isset($_REQUEST['db_backup_path'])    ? (string)$_REQUEST['db_backup_path']       : '';

// actions
$action = 'default';
if (isset($_REQUEST['save_locales'])) {
    $action = 'save_locales';
}
if (isset($_REQUEST['save_admin_password'])) {
    $action = 'save_admin_password';
}
if (isset($_REQUEST['save_db_settings'])) {
    $action = 'save_db_settings';
}
if (isset($_REQUEST['save_db_backup_path'])) {
    $action = 'save_db_backup_path';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Part-DB Installation/Update'));

try {
    $system_version = SystemVersion::getInstalledVersion();
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
        case 'save_locales':
            try {
                $config['timezone'] = $timezone;
                $config['language'] = $language;

                // check if the server supports the selected language and print a warning if not
                if (! ownSetlocale(LC_ALL, $config['language'])) {
                    throw new Exception(sprintf(_('Die gewählte Sprache "%s" wird vom Server nicht unterstützt!'.
                        "\nBitte installieren Sie diese Sprache oder wählen Sie eine andere."), $config['language']));
                }

                $config['installation_complete']['locales'] = true; // locales successful set
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'save_admin_password':
            try {
                // set_admin_password() throws an exception if the new passwords are not valid
                setAdminPassword(null, $adminpass_1, $adminpass_2, false);

                $config['installation_complete']['admin_password'] = true; // admin password successful set
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'save_db_settings':
            try {
                $config['db']['type'] = $db_type;
                //$config['db']['charset'] = $db_charset; // temporarly deactivated
                $config['db']['host'] = $db_host;
                $config['db']['name'] = $db_name;
                $config['db']['user'] = $db_user;
                $config['db']['password'] = $db_password;
                $config['db']['space_fix'] = $space_fix;

                if (strlen($config['db']['name']) == 0) {
                    throw new Exception('Der Datenbankname darf nicht leer sein!');
                }

                $database = new Database(); // test the connection --> Exception if it doesn't work

                $config['installation_complete']['database'] = true; // database settings successful set
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'save_db_backup_path':
            try {
                $config['db']['backup']['name'] = $db_backup_name;
                $config['db']['backup']['url'] = $db_backup_path;

                $config['installation_complete']['db_backup_path'] = true; // database backup path successful set
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        default:
            break;
    }
}

// try to save the config array in config.php --> fatal error if this does not work!
try {
    saveConfig();
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Decide which installation step should be displayed next
 *   and set the rest of the HTML variables
 *
 *********************************************************************************/

if (! $fatal_error) {
    // global variables
    $html->setVariable('system_version', $system_version->asString(false, true, true, false), 'string');
    $html->setVariable('system_version_full', $system_version->asString(false, false, false, true), 'string');

    if (! $config['installation_complete']['locales']) {
        // step "set_locales"
        $tmpl_site_to_show = 'set_locales';
        $html->setLoop('timezone_loop', arrayToTemplateLoop($config['timezones'], $config['timezone']));
        $html->setLoop('language_loop', arrayToTemplateLoop($config['languages'], $config['language']));
    } elseif (! $config['installation_complete']['admin_password']) {
        $tmpl_site_to_show = 'set_admin_password';
    } elseif (! $config['installation_complete']['database']) {
        // step "set_db_settings"
        $tmpl_site_to_show = 'set_db_settings';
        $html->setLoop('db_type_loop', arrayToTemplateLoop($config['db_types'], $config['db']['type']));
        $html->setLoop('db_charset_loop', arrayToTemplateLoop($config['db_charsets'], $config['db']['charset']));
        $html->setVariable('db_host', $config['db']['host'], 'string');
        $html->setVariable('db_name', $config['db']['name'], 'string');
        $html->setVariable('db_user', $config['db']['user'], 'string');
        $html->setVariable("space_fix", $config['db']['space_fix'], 'boolean');
    } elseif (! $config['installation_complete']['db_backup_path']) {
        // step "set_db_backup_path"
        $tmpl_site_to_show = 'set_db_backup_path';
        $html->setVariable('db_backup_name', $config['db']['backup']['name'], 'string');
        $html->setVariable('db_backup_path', $config['db']['backup']['url'], 'string');
    } else {
        // installation/update complete
        $tmpl_site_to_show = 'finish';
    }
}

/********************************************************************************
 *
 *   Generate HTML Output
 *
 *********************************************************************************/

$reload_link = $fatal_error ? 'install.php' : '';   // an empty string means that the...
//$html->print_header($messages, $reload_link);       // ...reload-button won't be visible

if (!empty($messages)) {
    $html->setLoop("messages", $messages);
}

$html->printTemplate('header');

if (! $fatal_error) {
    $html->printTemplate($tmpl_site_to_show);
}

//$html->print_footer();
