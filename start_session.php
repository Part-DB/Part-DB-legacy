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

    /********************************************************************************
    *
    *   define an exception handler for uncaught exceptions
    *
    *********************************************************************************/

    function exception_handler($e)
    {
        print "<br><br><strong>Es ist ein schwerwiegender Fehler aufgetreten:</strong><br><br>".
                nl2br($e->getMessage()).'<br><br>'.
                '(Exception wurde geworfen in '.$e->getFile().', Zeile '.$e->getLine().')';
    }

    set_exception_handler('exception_handler');

    /********************************************************************************
    *
    *   For the Update from Part-DB 0.2.2 to 0.3.0:
    *   Move file "config.php" and folder "backup" to "data/..."
    *
    *********************************************************************************/

    if (is_readable($BASE_tmp.'/config.php'))
    {
        if ( ! rename($BASE_tmp.'/config.php', $BASE_tmp.'/data/config.php'))
            die('Die Datei "config.php" kann nicht in den Unterordner "data" verschoben werden! '.
                'Führen Sie dies bitte von Hand durch.');
    }

    if (is_readable($BASE_tmp.'/backup'))
    {
        if ( ! rename($BASE_tmp.'/backup', $BASE_tmp.'/data/backup'))
            die('Das Verzeichnis "backup" kann nicht in den Unterordner "data" verschoben werden! '.
                'Führen Sie dies bitte von Hand durch.');
    }

    // just temporary!! move "media" and "log" to "data/..."
    if ((is_readable($BASE_tmp.'/media')) || (is_readable($BASE_tmp.'/log')))
    {
        die('Bitte alle Dateien, die sich in "media" oder in "log" befinden, nach "data/media/" bzw. "data/log/" verschieben! '.
            'ACHTUNG: Nicht den ganzen Ordner verschieben, sonst wird die .htaccess Datei in "data/media/" gelöscht! '.
            'Danach die Ordner "media" und "log" im Hauptverzeichnis von Part-DB löschen.');
    }
    // end of temporary code

    /********************************************************************************
    *
    *   include config files
    *
    *********************************************************************************/

    include_once($BASE_tmp.'/config_defaults.php'); // first, we load all default values of the $config array...

    if (is_readable($BASE_tmp.'/data/config.php'))
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
    *   update the config.php if the system is newer than the user's config.php
    *
    *********************************************************************************/

    if (($config['system']['current_config_version'] < $config['system']['latest_config_version'])
        && (is_readable(BASE.'/data/config.php')) && (filesize(BASE.'/data/config.php') > 0))
    {
        include_once(BASE.'/updates/config_update_steps.php');

        $html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Aktualisierung ihrer config.php');

        try
        {
            $update_messages = update_users_config_php();
            $messages[] = array('text' =>   'Ihre config.php wurde erfolgreich aktualisiert!<br><br>'.
                                            'Es kann sein, dass jetzt der Installationsassistent startet, '.
                                            'um noch einige neue Einstellungen zu tätigen.', 'strong' => true, 'color' => 'darkgreen');

            if (count($update_messages) > 0)
            {
                foreach ($update_messages as $text)
                    $messages[] = array('text' => '<br>'.$text, 'color' => 'red', 'strong' => true);
            }
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => 'Es gab ein Fehler bei der Aktualisierung ihrer config.php:', 'strong' => true, 'color' => 'red');
            $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        }

        $html->print_header($messages, 'index.php');
        $html->print_footer();
        exit;
    }

    $config['html']['http_charset'] = 'utf-8'; ///< @todo remove this later; see config_defaults.php

    // just temporary!! switch from MD5 to SHA256 password encryption!
    if (strlen($config['admin']['password']) == 32) // MD5 has 32 HEX chars
    {
        $config['admin']['password'] = NULL;
        $config['installation_complete']['admin_password'] = false; // this will show the installer to set a new password
    }

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
    setlocale(LC_ALL, $config['language']);

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
