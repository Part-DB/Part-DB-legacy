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

/**
 * @file start_session.php
 *
 * @brief This file must be included in every PHP file which produces HTML output!
 */

// set HTTP charset to UTF-8
header('Content-type: text/html; charset=utf-8');

$BASE_tmp = str_replace('\\', '/', dirname(__FILE__)); // temporary base path of Part-DB, without slash at the end

include_once($BASE_tmp.'/inc/lib.start_session.php');

/********************************************************************************
 *
 *   define an exception handler for uncaught exceptions
 *
 *********************************************************************************/

set_exception_handler('exception_handler');

/********************************************************************************
 *
 *   For the Update from Part-DB 0.2.2 to 0.3.0:
 *   Move the file "config.php" and the folders "backup", "media" and "log" to "data/..."
 *
 *********************************************************************************/

$old_config_exists = file_exists($BASE_tmp.'/config.php');
$old_backup_exists = file_exists($BASE_tmp.'/backup');
$old_media_exists = file_exists($BASE_tmp.'/media');
$old_log_exists = file_exists($BASE_tmp.'/log');

if (($old_config_exists) || ($old_backup_exists) || ($old_media_exists) || ($old_log_exists)) {
    $messages = '<strong>'. _('Bitte verschieben Sie die folgenden Dateien und Ordner ins Verzeichnis "data":') . '<br><br>';

    if ($old_config_exists) {
        $messages .= '"config.php" --> "data/config.php"<br>';
    }
    if ($old_backup_exists) {
        $messages .= '"backup/" --> "data/backup/"<br>';
    }
    if ($old_media_exists) {
        $messages .= '"media/" --> "data/media/"<br>';
    }
    if ($old_log_exists) {
        $messages .= '"log/" --> "data/log/"<br>';
    }

    $messages .=    '<br>' . _('WICHTIG:<br>Kopieren Sie jeweils nur den Inhalt der genannten Ordner, nicht den ganzen Ordner an sich!<br>'.
        'Die Zielordner enthalten bereits (teilweise versteckte) Dateien, die auf keinen Fall &uuml;berschrieben werden d&uuml;rfen!<br>'.
        'Kopieren Sie also nur den Inhalt dieser Ordner und l&ouml;schen Sie danach die alten, leeren Ordner im Hauptverzeichnis.') . '</span></strong>';

    printMessagesWithoutTemplate(_('Part-DB'), _('Update von Part-DB: Manuelle Eingriffe notwendig'), $messages);
    exit;
}

/********************************************************************************
 *
 *   include config files
 *
 *********************************************************************************/

include_once($BASE_tmp.'/inc/config_defaults.php'); // first, we load all default values of the $config array...

if (file_exists($BASE_tmp.'/data/config.php') && is_readable($BASE_tmp.'/data/config.php')) {
    include_once($BASE_tmp.'/data/config.php');
} // ...and then we overwrite them with the user settings, if they exist

if (count($manual_config) > 0) { // $manual_config is defined in "config_defaults.php" and can be filled in "config.php"
    $config = array_merge($config, $manual_config);
} // if there are manual configs, add them to $config

/********************************************************************************
 *
 *   define directory constants of the part-db installation
 *
 *   please note: we always use slashes, even if the script runs on Windows!
 *
 *   If the paths BASE, DOCUMENT_ROOT or BASE_RELATIVE are not correct,
 *   the user can set them manually in his config.php.
 *   Example how to define it in the config.php: $manual_config['BASE'] = '/my/base';
 *
 *********************************************************************************/

// directory to the part-db installation, without slash at the end
// Example (UNIX/Linux):    "/var/www/part-db"
// Example (Windows):       "C:/wamp/www/part-db"
if (isset($config['BASE'])) {
    define('BASE', $config['BASE']);
} else {
    define('BASE', $BASE_tmp);
}

/** @const BASE Directory to the part-db installation, without slash at the end
 * Example (UNIX/Linux):    "/var/www/part-db"
 * Example (Windows):       "C:/wamp/www/part-db"
 */

// server-directory without slash at the end
// Example (UNIX/Linux):    "/var/www"
// Example (Windows):       "C:/wamp/www"
if (isset($config['DOCUMENT_ROOT'])) {
    define('DOCUMENT_ROOT', $config['DOCUMENT_ROOT']);
} elseif (isset($_SERVER['DOCUMENT_ROOT'])) {
    define('DOCUMENT_ROOT', rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/'));
} elseif (isset($_SERVER['SCRIPT_FILENAME']) && isset($_SERVER['PHP_SELF'])) {
    define('DOCUMENT_ROOT', rtrim(str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])))));
} elseif (isset($_SERVER['PATH_TRANSLATED']) && isset($_SERVER['PHP_SELF'])) {
    define('DOCUMENT_ROOT', rtrim(str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF'])))));
} else {
    $messages = _('Die Konstante "DOCUMENT_ROOT" konnte auf Ihrem Server nicht ermittelt werden.<br>'.
        'Bitte definieren Sie diese Konstante manuell in Ihrer Konfigurationsdatei "data/config.php".');
    printMessagesWithoutTemplate(_('Part-DB'), _('DOCUMENT_ROOT kann nicht ermittelt werden'), $messages);
    exit;
}

/** @const DOCUMENT_ROOT Server-directory without slash at the end
 * Example (UNIX/Linux):    "/var/www"
 * Example (Windows):       "C:/wamp/www"
 */

// the part-db installation directory without document root, without slash at the end
// Example (UNIX/Linux):    "/part-db"
// Example (Windows):       "/part-db"
if (isset($config['BASE_RELATIVE'])) {
    define('BASE_RELATIVE', $config['BASE_RELATIVE']);
} elseif (mb_strpos(BASE, DOCUMENT_ROOT) === false) {   // workaround for STRATO servers, see german post on uC.net:
    define('BASE_RELATIVE', '.');
} // http://www.mikrocontroller.net/topic/269289#3152928
else {
    define('BASE_RELATIVE', str_replace(DOCUMENT_ROOT, '', BASE));
}

/**
 * @const BASE_DATA The location of the data folder, where Part-DB can write data (without slash)
 */
define('BASE_DATA', BASE . "/data");

/** @const BASE_RELATIVE Server-directory without slash at the end
* Example (UNIX/Linux):    "/part-db"
* Example (Windows):       "/part-db"
*/

// for debugging uncomment these lines:
//print 'BASE = "'.BASE.'"<br>';
//print 'DOCUMENT_ROOT = "'.DOCUMENT_ROOT.'"<br>';
//print 'BASE_RELATIVE = "'.BASE_RELATIVE.'"<br>';
//print 'DIRECTORY_SEPARATOR = "'.DIRECTORY_SEPARATOR.'"<br>';
//exit;


mb_internal_encoding(/*$config['html']['http_charset']*/ 'UTF-8');
mb_regex_encoding('UTF-8');
date_default_timezone_set($config['timezone']);

ownSetlocale(LC_ALL, $config['language']);

//Set gettext locale for PHP
$domain = "php";
bindtextdomain($domain, BASE . '/locale');
textdomain($domain);

/********************************************************************************
 *
 *   make some checks
 *
 *********************************************************************************/

$messages = checkRequirements();
if (count($messages) > 0) {
    printMessagesWithoutTemplate(
        'Part-DB',
        _('Mindestanforderungen von Part-DB nicht erfüllt!'),
        '<span><strong>&bull;' .implode('<br>&bull;', $messages). '</strong></span><br><br>' .
        sprintf(_('Nähere Informationen gibt es in der <a target="_blank" href="%s">Dokumentation</a>.'), _("https://github.com/jbtronics/Part-DB/wiki/Anforderungen"))
    );
    exit;
}

$messages = checkFilePermissions();
if (count($messages) > 0) {
    $message = '<strong><span class="text-danger">';
    foreach ($messages as $msg) {
        $message .= '&bull;'.$msg.'<br>';
    }
    $message .= '</span></strong><br><br>';
    $message .= sprintf(_('Nähere Informationen gibt es in der <a target="_blank" href="%s">Dokumentation</a>.'), _("https://github.com/jbtronics/Part-DB/wiki/Installation"));
    $message .= '<br><br>';
    $message .= _('<form action="" method="post"><button class="btn btn-primary" type="submit" value="Seite neu laden">Seite neu laden</button></form>');

    printMessagesWithoutTemplate('Part-DB', _('Anpassung der Rechte von Verzeichnissen und Dateien'), $message);
    exit;

    // please note: the messages and the "exit;" here are very important, we mustn't continue the script!
    // the reasen is: if the config.php is not readable, the array $config is now not loaded successfully.
}

$message = checkIfConfigIsValid();
if (is_string($message)) {
    printMessagesWithoutTemplate(
        'Part-DB',
        _('Ihre config.php ist fehlerhaft!'),
        sprintf(_('Nähere Informationen gibt es in der <a target="_blank" href="%s">Dokumentation</a>.'), _("https://github.com/jbtronics/Part-DB/wiki/Installation")).
        _('<form action="" method="post"><button class="btn btn-primary" type="submit" value="Seite neu laden">Seite neu laden</button></form>')
    );
    exit;
}

$messages = checkComposerFolder();
if (count($messages) > 0) {
    $message = _("<b>Part-DB benutzt den PHP Abhängikeitsmanager <a href='https://getcomposer.org/' target='_blank'>Composer</a>" .
        " um benötigte Bibliotheken bereitzustellen.<br> Bevor sie Part-DB nutzen können müssen sie diese" .
        " Bibliotheken mit <code>php composer.phar install</code> im Hauptverzeichnis von Part-DB" .
        " installiert werden. <br> Sollten sie keine Möglichkeit haben, auf ihrem Server Konsolenbefehle" .
        " auszuführen, dann benutzen kopieren sie den vendor/ Ordner, aus einem mit composer eingerichteten ".
        " Part-DB oder ein speziellen Release benutzen, der die Abhängikeiten mitliefert.</b><br>");

    $message .= sprintf(_('Nähere Informationen gibt es in der <a target="_blank" href="%s">Dokumentation</a>.'), _("https://github.com/jbtronics/Part-DB/wiki/Installation"));
    $message .= "<br><ul>";

    foreach ($messages as $msg) {
        $message .= '<li>'.$msg.'</li>';
    }

    $message .= "</ul>";

    //$message .= 'Nähere Informationen zu den Dateirechten gibt es in der <a target="_blank" href="' .
    //    'https://github.com/jbtronics/Part-DB/wiki/Installation">Dokumentation</a>.<br><br>';
    $message .= _('<form action="" method="post"><button class="btn btn-primary" type="submit" value="Seite neu laden">Seite neu laden</button></form>');

    printMessagesWithoutTemplate('Part-DB', 'Benötigte Bibliotheken fehlen!', $message);
    exit;

    // please note: the messages and the "exit;" here are very important, we mustn't continue the script!
    // the reasen is: if the config.php is not readable, the array $config is now not loaded successfully.
}

/********************************************************************************
 *
 *   update the config.php if the system is newer than the user's config.php
 *
 *********************************************************************************/

if (($config['system']['current_config_version'] < $config['system']['latest_config_version'])
    && (file_exists(BASE.'/data/config.php')) && (is_readable(BASE.'/data/config.php'))
    && (filesize(BASE.'/data/config.php') > 0)) {
    include_once(BASE.'/updates/config_update_steps.php');

    try {
        $update_messages = update_users_config_php();
        $message =  '<strong><span style="color: darkgreen; ">'._('Ihre config.php wurde erfolgreich aktualisiert!').'</span></strong><br><br>' .
            _('Es kann sein, dass jetzt der Installationsassistent startet, '.
            'um noch einige neue Einstellungen zu tätigen.') . '<br><br>';

        if (count($update_messages) > 0) {
            $message .= '<strong><span style="color: red; ">';
            foreach ($update_messages as $text) {
                $message .= '&bull;'.$text.'<br>';
            }
            $message .= '</span></strong><br>';
        }
    } catch (Exception $e) {
        $message =  '<strong><span class="text-danger">'._('Es gab ein Fehler bei der Aktualisierung ihrer config.php:').'<br><br>' .
            nl2br($e->getMessage()). '</span></strong><br><br>';
    }

    $message .= _('<form action="" method="post"><button class="btn btn-primary" type="submit" value="Seite neu laden">Seite neu laden</button></form>');

    printMessagesWithoutTemplate('Part-DB', _('Aktualisierung ihrer config.php'), $message);
    exit;
}

$config['html']['http_charset'] = 'utf-8'; ///< @todo remove this later; see config_defaults.php

/********************************************************************************
 *
 *   start session
 *
 *********************************************************************************/

if (isset($config['user']['gc_maxlifetime']) && $config['user']['gc_maxlifetime'] > 0) {
    @ini_set("session.gc_maxlifetime", $config['user']['gc_maxlifetime']);
}

session_name('Part-DB');
session_start();
session_write_close();

/********************************************************************************
 *
 *   set internal encoding / timezone / locale / error reporting
 *
 *********************************************************************************/

if (($config['debug']['enable']) && (! $config['debug']['template_debugging_enable'])) { // template debugging produces a lot of warnings!
    error_reporting(E_ALL & ~E_STRICT);
    @ini_set("display_errors", 1);

    //Dont show errors because of function override in php7
    if (PHP_MAJOR_VERSION >= 7) {
        set_error_handler(function ($errno, $errstr) {
            return strpos($errstr, 'Declaration of') === 0;
        }, E_WARNING);
    }
} else {
    @ini_set("display_errors", 0);
}

/********************************************************************************
 *
 *   include libraries
 *
 *********************************************************************************/

include_once(BASE.'/inc/lib.functions.php');
include_once(BASE.'/inc/lib.debug.php');
include_once(BASE.'/inc/lib.php');

/********************************************************************************
 *
 *  Register Autoloaders
 *
 ********************************************************************************/

//Include Composer autoloader
require 'vendor/autoload.php';

//Check if Klass exists, and debugging is enabled.
if (class_exists("\Whoops\Run") && $config['debug']['enable'] &&
    (PHP_MAJOR_VERSION >= 7 || PHP_MINOR_VERSION >= 6)) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

/**************************************************************************************
 * Try to set settings that are only apply for the current user.
 **************************************************************************************/
global $user_config;
$user_config = array();
try {
    $current_user = \PartDB\User::getLoggedInUser();
    $user_config['theme'] = $current_user->getTheme();
    ownSetlocale(LC_ALL, $current_user->getLanguage());
    date_default_timezone_set($config['timezone']);
} catch (Exception $ex) {
    //Ignore all errors.
    //TODO: Log the errors
}
