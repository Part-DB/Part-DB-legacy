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
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\SelfPermission;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/
$pw_old             = isset($_REQUEST['pw_old'])            ? $_REQUEST['pw_old']                   : "";
$pw_1               = isset($_REQUEST['pw_1'])              ? $_REQUEST['pw_1']                   : "";
$pw_2               = isset($_REQUEST['pw_2'])              ? $_REQUEST['pw_2']                   : "";

$new_username       = isset($_REQUEST['username'])          ? $_REQUEST['username']                 : "";
$new_firstname      = isset($_REQUEST['firstname'])         ? $_REQUEST['firstname']                : "";
$new_lastname       = isset($_REQUEST['lastname'])          ? $_REQUEST['lastname']                 : "";
$new_email          = isset($_REQUEST['email'])             ? $_REQUEST['email']                    : "";
$new_department     = isset($_REQUEST['department'])        ? $_REQUEST['department']               : "";

$new_theme          = isset($_REQUEST['custom_css'])        ? $_REQUEST['custom_css']               : "";
$new_timezone       = isset($_REQUEST['timezone'])          ? $_REQUEST['timezone']                 : "";
$new_language       = isset($_REQUEST['language'])          ? $_REQUEST['language']                 : "";

$action = 'default';
if (isset($_REQUEST["change_pw"])) {
    $action = 'change_pw';
}

if (isset($_REQUEST['apply_settings'])) {
    $action = 'apply';
}


/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Benutzereinstellungen'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Execute Actions
 *
 *********************************************************************************/

if (!$fatal_error) {
    switch ($action) {
        case "change_pw":
            try {
                if ($config['is_online_demo']) {
                    $messages[] = array('text' => _("Diese Funktion ist in der Onlinedemo deaktiviert!"), 'strong' => true, 'color' => 'red');
                    break;
                }

                if ($pw_1 == "" || $pw_2 == "") {
                    $messages[] = array('text' => _("Das neue Password darf nicht leer sein!"), 'strong' => true, 'color' => 'red');
                    break;
                }
                if ($pw_1 !== $pw_2) {
                    $messages[] = array('text' => _("Das neue Password und die Bestätigung müssen übereinstimmen!"), 'strong' => true, 'color' => 'red');
                    break;
                }
                if (!$current_user->isPasswordValid($pw_old)) {
                    $messages[] = array('text' => _("Das eingegebene alte Password war falsch!"), 'strong' => true, 'color' => 'red');
                    break;
                }
                //If all checks were ok, change the password!
                $current_user->setPassword($pw_1, false);
                $messages[] = array('text' => _("Das Passwort wurde erfolgreich geändert!"), 'strong' => true, 'color' => 'green');
            } catch (Exception $e) {
                $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }

            break;

        case 'apply':
            if ($config['is_online_demo']) {
                $messages[] = array('text' => _("Diese Funktion ist in der Onlinedemo deaktiviert!"), 'strong' => true, 'color' => 'red');
                break;
            }

            if (!empty($new_username)) {
                $current_user->setName($new_username);
            }
            if (!empty($new_firstname)) {
                $current_user->setFirstName($new_firstname);
            }
            if (!empty($new_lastname)) {
                $current_user->setLastName($new_lastname);
            }
            if (!empty($new_email)) {
                $current_user->setEmail($new_email);
            }
            if (!empty($new_department)) {
                $current_user->setDepartment($new_department);
            }

            //Dont check if this value is set, because empty string is a valid value.
            $current_user->setTheme($new_theme);
            $current_user->setLanguage($new_language);
            $current_user->setTimezone($new_timezone);

            //Apply the settings, with reloading everything.
            $html->setVariable('refresh_navigation_frame', true, 'boolean');

            break;
    }
}

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $html->setVariable("username", $current_user->getName(), "string");
        $html->setVariable("firstname", $current_user->getFirstName(), "string");
        $html->setVariable("lastname", $current_user->getLastName(), "string");
        $html->setVariable("email", $current_user->getEmail(), "string");
        $html->setVariable("department", $current_user->getDepartment(), "string");
        $html->setVariable("group", $current_user->getGroup()->getFullPath(), "string");
        $html->setVariable('avatar_url', $current_user->getAvatar(), "string");

        //Configuration settings
        $html->setLoop('custom_css_loop', build_custom_css_loop($current_user->getTheme(true), true));
        //Convert timezonelist, to a format, we can use
        $timezones_raw = DateTimeZone::listIdentifiers();
        $timezones = array();
        foreach ($timezones_raw as $timezone) {
            $timezones[$timezone] = $timezone;
        }
        $html->setLoop('timezone_loop', arrayToTemplateLoop($timezones, $current_user->getTimezone(true)));
        $html->setLoop('language_loop', arrayToTemplateLoop($config['languages'], $current_user->getLanguage(true)));

        $html->setVariable('can_username', $current_user->canDo(PermissionManager::SELF, SelfPermission::EDIT_USERNAME));
        $html->setVariable('can_infos', $current_user->canDo(PermissionManager::SELF, SelfPermission::EDIT_INFOS));
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


$reload_link = $fatal_error ? 'user_settings.php' : "";  // an empty string means that the...
$html->printHeader($messages, $reload_link);                           // ...reload-button won't be visible


if (! $fatal_error) {
    if ($current_user->getNeedPasswordChange()) {
        $html->printTemplate('password_alert');
    }

    $html->printTemplate('settings');
    $html->printTemplate('password');
}


$html->printFooter();
