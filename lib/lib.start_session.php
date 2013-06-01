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
     *
     * @todo    the Workaround for Windows is not really pretty -> make it better!
     */
    function own_setlocale($category, $locale)
    {
        // workaround for Windows/XAMPP:
        switch ($locale)
        {
            case 'de_AT': $locale_xampp = 'aut'; break;
            case 'de_CH': $locale_xampp = 'che'; break;
            case 'de_DE': $locale_xampp = 'deu'; break;
            case 'de_LU': $locale_xampp = 'deu'; break;
            case 'en_GB': $locale_xampp = 'gbr'; break;
            case 'en_US': $locale_xampp = 'usa'; break;
            default:      $locale_xampp = $locale;
        }

        $ret = setlocale($category, $locale.'.utf8', $locale.'.UTF8', $locale.'.utf-8', $locale.'.UTF-8', $locale, $locale_xampp);
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
     * @brief Check file permissions
     *
     * @retval array    @li an array with a string for each error message
     *                  @li an empty array means everything is fine
     */
    function check_file_permissions()
    {
        $messages = array();

        $permissions = array(   '/data/'                   => 'rwx',
                                '/data/config.php'         => 'rw',
                                '/data/index.html'         => 'r',
                                '/data/.htaccess'          => 'r',
                                '/data/backup/'            => 'rwx',
                                '/data/backup/index.html'  => 'r',
                                '/data/log/'               => 'rwx',
                                '/data/log/index.html'     => 'r',
                                '/data/media/'             => 'rwx',
                                '/data/media/.htaccess'    => 'r');

        foreach ($permissions as $filename => $needed_perms)
        {
            $whole_filename = BASE.$filename;

            if ((file_exists($whole_filename))
                && (((strpos($needed_perms, 'r') !== false) && ( ! is_readable($whole_filename)))
                 || ((strpos($needed_perms, 'w') !== false) && ( ! is_writable($whole_filename)))
                 || ((strpos($needed_perms, 'x') !== false) && ( ! is_executable($whole_filename)) && (DIRECTORY_SEPARATOR == '/')))) // execution only for UNIX/Linux
            {
                $messages[] =   'Das Verzeichnis bzw. die Datei "'.$filename.'" hat nicht die richtigen Dateirechte! '.
                                'Benötigt werden "'.$needed_perms.'". Bitte manuell korrigieren.';
            }
        }

        return $messages;
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
