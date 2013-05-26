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
        [DATE]      [NICKNAME]      [CHANGES]
        2013-05-26  kami89          - created
*/

    /**
     * @file lib.start_session.php
     * @brief in this file are some functions which are needed in start_session.php
     * @author kami89
     */

    /**
     * @brief Print out nice formatted messages in Part-DB design without using templates
     *
     * This is needed for uncaught exceptions or other error messages in start_session.php
     *
     * @param string        $page_title     the page title
     * @param string|NULL   $div_title      a DIV title, or NULL for a message without a title
     * @param string        $messages       the HTML-coded messages
     */
    function print_messages_without_template($page_title, $div_title, $messages)
    {
        print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head>';
        print '<title>'.htmlspecialchars($page_title).'</title>';
        print '<meta http-equiv="content-type" content="text/html; charset=utf-8">
                <style type="text/css">
                .body {     background-color: #cdcdcd;
                            font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;}
                .outer {    background-color: #ffffff;
                            margin-top: 10px;
                            margin-bottom: 15px;
                            border: none;
                            border-bottom: 1px solid #000000;
                            border-left: 1px solid #000000;
                            padding: 11px;
                            font-size: 12px;}
                .outer h2 { background-color: #BABABA;
                            margin-top: 0px;
                            margin-bottom: 1px;
                            border-bottom: 2px solid #F76B02;
                            border-left:   1px solid #F76B02;
                            padding: 1px;
                            font-size: 15px;
                            font-weight: bold;}
                .inner {    background-color: #F2F2F2;
                            border-bottom: 3px solid #d0d0d0;
                            border-left: 1px solid #d0d0d0;
                            border-right:  1px solid #d0d0d0;
                            padding: 1px;}
                </style></head>';
        print '<body class="body"><div class="outer">';
        if ($div_title) {print '<div class="inner"><h2>'.$div_title.'</h2>';}
        print $messages;
        if ($div_title) {print '</div>';}
        print '</div></body></html>';
    }

    /**
     * @brief This is an improved version of the setlocale() from PHP
     *
     * This function will first try to set an UTF-8 locale.
     *
     * This function is used in start_session.php, install.php and system_config.php
     *
     * @retval boolean  true if success, false if fail
     */
    function own_setlocale($category, $locale)
    {
        $ret = setlocale($category, $locale.'.utf8', $locale.'.UTF8', $locale.'.utf-8', $locale.'.UTF-8', $locale);
        return ($ret !== false);
    }

    /**
     * @brief Check if the server complies all minimum requirements of Part-DB
     *
     * @warning    All requirements must be defined in the array "$config['requirements']" in "config_defaults.php"!
     *
     * @retval array    For every requirement which is not complied there's a message
     *                  (This array is empty if the server complies all requirements)
     *
     * @throws Exception if there was an error
     */
    function check_requirements()
    {
        global $config;
        $messages = array();

        foreach ($config['requirements'] as $key => $value)
        {
            switch ($key)
            {
                case 'php_version':
                    if (version_compare(PHP_VERSION, $value) < 0)
                    {
                        $messages[] =   'Für Part-DB wird mindestens PHP '.$value.' vorausgesetzt! '.
                                        'Die derzeit installierte Version ist PHP '.PHP_VERSION.'.';
                    }
                    break;

                case 'pdo':
                    if ( ! class_exists('PDO', false))
                    {
                        $messages[] =   'PDO (PHP Data Objects) wird benötigt, ist aber nicht installiert!';
                    }
                    break;

                default:
                    throw new Exception('Unbekannte Mindestanforderung: '.$key);
            }
        }

        return $messages;
    }

    /**
     * @brief Check and try to set file permissions
     *
     * @retval array    @li an array with a string for each message
     *                  @li an empty array means everything is fine
     *
     * @todo    This function is quite ugly, make it better! :-)
     */
    function check_and_set_file_permissions()
    {
        $messages = array();

        $write_permissions = array( BASE.'/data/'                   => true, // false = read, true = read+write
                                    BASE.'/data/config.php'         => true,
                                    BASE.'/data/index.html'         => false,
                                    BASE.'/data/.htaccess'          => false,
                                    BASE.'/data/backup/'            => true,
                                    BASE.'/data/backup/index.html'  => false,
                                    BASE.'/data/log/'               => true,
                                    BASE.'/data/log/index.html'     => false,
                                    BASE.'/data/media/'             => true,
                                    BASE.'/data/media/.htaccess'    => false);

        foreach ($write_permissions as $filename => $write)
        {
            if (( ! $write) && (file_exists($filename)) && (( ! is_readable($filename)) || (is_writable($filename))))
            {
                // file or directory should be (only) readable, but isn't it!
                $new_mode = set_file_permissions($filename, false);

                if ($new_mode)
                    $messages[] = '<font color="darkgreen">Datei oder Verzeichnis "'.$filename.'" wurde erfolgreich auf "'.$new_mode.'" gesetzt.</font>';
                else
                    $messages[] = '<font color="red">Datei oder Verzeichnis "'.$filename.'" ist nicht lesbar, muss aber lesbar sein. Bitte manuell anpassen!</font>';
            }
            elseif (($write) && (file_exists($filename)) && (( ! is_readable($filename)) || ( ! is_writable($filename))))
            {
                // file or directory should be writeable and readable, but isn't it!
                $new_mode = set_file_permissions($filename, true);

                if ($new_mode)
                    $messages[] = '<font color="darkgreen">Datei oder Verzeichnis "'.$filename.'" wurde erfolgreich auf "'.$new_mode.'" gesetzt.</font>';
                else
                    $messages[] = '<font color="red">Datei oder Verzeichnis "'.$filename.'" ist nicht beschreibbar, muss aber beschreibbar sein. Bitte manuell anpassen!</font>';
            }
        }

        return $messages;
    }

    /**
     * @brief set permissions for a file or directory (helper function for check_and_set_file_permissions() )
     *
     * This function will try to set the file permissions first to 644.
     * If this is not enought to make it readable, we will try 664.
     * If this is still not enought we will try 666.
     * For directories we use 755/775/777.
     *
     * @param string    $filename       filename to a file or directory
     * @param boolean   $write          @li true: set read + write permissions
     *                                  @li false: set read permissions
     *
     * @retval  @li (integer) the new file permissions (as a number, like 755) if success
     *          @li false if fails
     *
     * @todo    This function is quite ugly, make it better! :-)
     */
    function set_file_permissions($filename, $write)
    {
        if ( ! $write)
        {
            // only read permissions
            $mode = is_dir($filename) ? 555 : 444;
            chmod($filename, octdec($mode));
            clearstatcache();
            if (is_readable($filename)) return $mode;
        }
        else
        {
            // read + write permissions
            $mode = is_dir($filename) ? 755 : 644;
            chmod($filename, octdec($mode));
            clearstatcache();
            if (is_writable($filename) && is_readable($filename)) return $mode;

            $mode = is_dir($filename) ? 775 : 664;
            chmod($filename, octdec($mode));
            clearstatcache();
            if (is_writable($filename) && is_readable($filename)) return $mode;

            $mode = is_dir($filename) ? 777 : 666;
            chmod($filename, octdec($mode));
            clearstatcache();
            if (is_writable($filename) && is_readable($filename)) return $mode;
        }
        return false;
    }

    /**
     * @brief Check if the config.php is valid
     *
     * Maybe some people are trying to create their config.php with a copy of config_defaults.php.
     * This is not good! In this case we will print out an error message!
     *
     * @retval string|true      @li true if the config.php is valid
     *                          @li an error message if the config.php is not valid
     */
    function check_if_config_is_valid()
    {
        global $config_defaults;

        if (isset($config_defaults['system']) && isset($config_defaults['system']['version']))
        {
            // it seems that the user has copied the config_defaults.php to the config.php, this is not good!
            return  'Es scheint, als hätten Sie die Datei "config_defaults.php" als Vorlage für Ihre "config.php" verwendet.<br>'.
                    'Das ist aber nicht so vorgesehen und darf nicht so gemacht werden, da dies Probleme verursachen wird!<br><br>'.
                    'Löschen Sie Ihre "config.php" und öffnen Sie Part-DB im Webbrowser.<br>'.
                    'Es wird dann ein Installationsassistent gestartet, der automatisch eine korrekte "config.php" anlegen wird.';
        }

        return true;
    }


?>
