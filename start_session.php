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

    $Id$

    Changelog (sorted by date):
        [DATE]      [NICKNAME]          [CHANGES]
        2012-09-03  kami89              - created
        2013-05-05  kami89              - added config.php updates
                                        - added workaround for STRATO servers
*/

   /**
    * @file start_session.php
    *
    * @brief This file must be included in every PHP file which produces HTML output!
    */

    $BASE_tmp = dirname(__FILE__); // temporary base path of Part-DB, without slash at the end

    include_once($BASE_tmp.'/lib/lib.start_session.php');

    /********************************************************************************
    *
    *   define an exception handler for uncaught exceptions
    *
    *********************************************************************************/

    function exception_handler($e)
    {
        print_messages_without_template(    'Part-DB: Schwerwiegender Fehler!', NULL,
                                            '<font color="red"><strong>Es ist ein schwerwiegender Fehler aufgetreten:'.
                                            '<br><br>'.nl2br($e->getMessage()).'</strong><br><br>'.
                                            '(Exception wurde geworfen in '.$e->getFile().', Zeile '.$e->getLine().')</font>');
        exit;
    }

    set_exception_handler('exception_handler');

    /********************************************************************************
    *
    *   For the Update from Part-DB 0.2.2 to 0.3.0:
    *   Move the file "config.php" and the folders "backup", "media" and "log" to "data/..."
    *
    *********************************************************************************/

    if ((file_exists($BASE_tmp.'/config.php')) || (file_exists($BASE_tmp.'/backup')) ||
        (file_exists($BASE_tmp.'/media')) || (file_exists($BASE_tmp.'/log')))
    {
        print_messages_without_template('Part-DB', 'Update von Part-DB: Manuelle Eingriffe notwendig',
            '<strong>Bitte verschieben Sie die folgenden Dateien und Ordner ins Verzeichnis "data": <br><br>'.
            '"config.php" --> "data/config.php"<br>'.
            '"backup/" --> "data/backup/"<br>'.
            '"media/" --> "data/media/"<br>'.
            '"log/" --> "data/log/"<br><br>'.
            '<font color="red">WICHTIG:<br>Kopieren Sie jeweils nur den Inhalt der genannten Ordner, nicht den ganzen Ordner an sich!<br>'.
            'Die Zielordner enthalten bereits (teilweise versteckte) Dateien, die auf keinen Fall &uuml;berschrieben werden d&uuml;rfen!<br>'.
            'Kopieren Sie also nur den Inhalt dieser Ordner und l&ouml;schen Sie danach die Ordner "backup", "log" und "media" im Hauptverzeichnis.</font></strong>');
        exit;
    }

    /********************************************************************************
    *
    *   include config files
    *
    *********************************************************************************/

    include_once($BASE_tmp.'/config_defaults.php'); // first, we load all default values of the $config array...

    if (file_exists($BASE_tmp.'/data/config.php') && is_readable($BASE_tmp.'/data/config.php'))
        include_once($BASE_tmp.'/data/config.php'); // ...and then we overwrite them with the user settings, if they exist

    if (count($manual_config) > 0) // $manual_config is defined in "config_defaults.php" and can be filled in "config.php"
        $config = array_merge($config, $manual_config); // if there are manual configs, add them to $config

    /********************************************************************************
    *
    *   define directory constants of the part-db installation
    *
    *   If the paths BASE, DOCUMENT_ROOT or BASE_RELATIVE are not correct,
    *   the user can set them manually in his config.php.
    *   Example how to define it in the config.php: $manual_config['BASE'] = '/my/base';
    *
    *********************************************************************************/

    // directory to the part-db installation, without slash at the end
    // Example (Linux): "/var/www/part-db"
    // Example (Windows): "C:\wamp\www\part-db"
    if (isset($manual_config['BASE']))
        define('BASE', $manual_config['BASE']);
    else
        define('BASE', $BASE_tmp);

    // server-directory without slash at the end
    // Example (Linux): "/var/www"
    // Example (Windows): "C:/wamp/www"
    if (isset($manual_config['DOCUMENT_ROOT']))
        define('DOCUMENT_ROOT', $manual_config['DOCUMENT_ROOT']);
    else
        define('DOCUMENT_ROOT', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));

    // the part-db installation directory without document root, with slash at the end
    // Example (Linux): "/part-db/"
    // Example (Windows): "/part-db/"
    if (isset($manual_config['BASE_RELATIVE']))
        define('BASE_RELATIVE', $manual_config['BASE_RELATIVE']);
    elseif (strpos(str_replace(DIRECTORY_SEPARATOR, '/', BASE), DOCUMENT_ROOT) === false)   // workaround for STRATO servers (see german post on uC.net:
        define('BASE_RELATIVE', './');                                                      // http://www.mikrocontroller.net/topic/269289#3152928)
    else
        define('BASE_RELATIVE', str_replace(DOCUMENT_ROOT, '', str_replace(DIRECTORY_SEPARATOR, '/', BASE.DIRECTORY_SEPARATOR)));

    // for debugging uncomment these lines:
    //print 'BASE = "'.BASE.'"<br>';
    //print 'DOCUMENT_ROOT = "'.DOCUMENT_ROOT.'"<br>';
    //print 'BASE_RELATIVE = "'.BASE_RELATIVE.'"<br>';
    //print 'DIRECTORY_SEPARATOR = "'.DIRECTORY_SEPARATOR.'"<br>';
    //exit;

    /********************************************************************************
    *
    *   make some checks
    *
    *********************************************************************************/

    $messages = check_requirements();
    if (count($messages) > 0)
    {
        print_messages_without_template('Part-DB', 'Mindestanforderungen von Part-DB nicht erfüllt!',
            '<font color="red"><strong>&bull;'.implode('<br>&bull;', $messages).'</strong></font><br><br>'.
            'Nähere Informationen gibt es in der <a target="_new" href="'.BASE_RELATIVE.
            'documentation/dokuwiki/doku.php?id=anforderungen">Dokumentation</a>.');
        exit;
    }

    $messages = check_file_permissions();
    if (count($messages) > 0)
    {
        $message = '<strong><font color="red">';
        foreach ($messages as $msg)
            $message .= '&bull;'.$msg.'<br>';
        $message .= '</font></strong><br><br>';
        $message .= 'Nähere Informationen zu den Dateirechten gibt es in der <a target="_new" href="'.BASE_RELATIVE.
                    'documentation/dokuwiki/doku.php?id=installation">Dokumentation</a>.<br><br>';
        $message .= '<form action="" method="post"><input type="submit" value="Seite neu laden"></form>';

        print_messages_without_template('Part-DB', 'Anpassung der Rechte von Verzeichnissen und Dateien', $message);
        exit;

        // please note: the messages and the "exit;" here are very important, we mustn't continue the script!
        // the reasen is: if the config.php is not readable, the array $config is now not loaded successfully.
    }

    $message = check_if_config_is_valid();
    if (is_string($message))
    {
        print_messages_without_template('Part-DB', 'Ihre config.php ist fehlerhaft!',
            '<font color="red"><strong>'.$message.'</strong></font><br><br>'.
            'Nähere Informationen gibt es in der <a target="_new" href="'.BASE_RELATIVE.
            'documentation/dokuwiki/doku.php?id=installation">Dokumentation</a>.<br><br>'.
            '<form action="" method="post"><input type="submit" value="Seite neu laden"></form>');
        exit;
    }

    /********************************************************************************
    *
    *   update the config.php if the system is newer than the user's config.php
    *
    *********************************************************************************/

    if (($config['system']['current_config_version'] < $config['system']['latest_config_version'])
        && (file_exists(BASE.'/data/config.php')) && (is_readable(BASE.'/data/config.php'))
        && (filesize(BASE.'/data/config.php') > 0))
    {
        include_once(BASE.'/updates/config_update_steps.php');

        try
        {
            $update_messages = update_users_config_php();
            $message =  '<strong><font color="darkgreen">Ihre config.php wurde erfolgreich aktualisiert!</font></strong><br><br>'.
                        'Es kann sein, dass jetzt der Installationsassistent startet, '.
                        'um noch einige neue Einstellungen zu tätigen.<br><br>';

            if (count($update_messages) > 0)
            {
                $message .= '<strong><font color="red">';
                foreach ($update_messages as $text)
                    $message .= '&bull;'.$text.'<br>';
                $message .= '</font></strong><br>';
            }
        }
        catch (Exception $e)
        {
            $message =  '<strong><font color="red">Es gab ein Fehler bei der Aktualisierung ihrer config.php:<br><br>'.
                        nl2br($e->getMessage()).'</font></strong><br><br>';
        }

        $message .= '<form action="" method="post"><input type="submit" value="Seite neu laden"></form>';

        print_messages_without_template('Part-DB', 'Aktualisierung ihrer config.php', $message);
        exit;
    }

    $config['html']['http_charset'] = 'utf-8'; ///< @todo remove this later; see config_defaults.php

    /********************************************************************************
    *
    *   set internal encoding / timezone / locale / error reporting
    *
    *********************************************************************************/

    if ($config['debug']['enable'])
    {
        error_reporting(E_ALL & ~E_STRICT);
        ini_set("display_errors", 1);
    }
    else
        ini_set("display_errors", 0);

    mb_internal_encoding($config['html']['http_charset']);
    date_default_timezone_set($config['timezone']);
    own_setlocale(LC_ALL, $config['language']);

    /********************************************************************************
    *
    *   start session
    *
    *********************************************************************************/

    session_name('Part-DB');
    session_start();

    /********************************************************************************
    *
    *   autoload function for classes
    *
    *********************************************************************************/

    function __autoload($classname)
    {
        if (strpos($classname, 'vlib') === 0)
            include_once(BASE.'/lib/vlib/'.$classname.'.php');
        else
            include_once(BASE.'/lib/class.'.$classname.'.php');
    }

    /********************************************************************************
    *
    *   include libraries
    *
    *********************************************************************************/

    include_once(BASE.'/lib/lib.functions.php');
    include_once(BASE.'/lib/lib.debug.php');
    include_once(BASE.'/lib/lib.php');

?>
