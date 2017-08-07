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

    // this file enables write permissions in the DokuWiki
    define('DOKUWIKI_PERMS_FILENAME', BASE.'/data/ENABLE-DOKUWIKI-WRITE-PERMS.txt');

    $messages = array();
    $fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

    /********************************************************************************
    *
    *   Some special functions for this site
    *
    *********************************************************************************/

    function build_theme_loop()
    {
        global $config;
        $loop = array();
        $directories = find_all_directories(BASE.'/templates/');

        foreach ($directories as $directory)
        {
            $name = str_ireplace(BASE.'/templates/', '', $directory);
            if ($name != 'custom_css' && $name != 'fonts')
                $loop[] = array('value' => $name, 'text' => $name, 'selected' => ($name == $config['html']['theme']));
        }

        return $loop;
    }

    function build_custom_css_loop()
    {
        global $config;
        $loop = array();
        $files = find_all_files(BASE.'/templates/custom_css/', true, '.css');

        foreach ($files as $file)
        {
            $name = str_ireplace(BASE.'/templates/custom_css/', '', $file);
            $loop[] = array('value' => $name, 'text' => $name, 'selected' => ($name == $config['html']['custom_css']));
        }

        return $loop;
    }

    /********************************************************************************
    *
    *   Evaluate $_REQUEST
    *
    *********************************************************************************/

    // section "system settings"
    $http_charset               = isset($_REQUEST['http_charset'])      ? (string)$_REQUEST['http_charset']     : 'utf-8';
    $theme                      = isset($_REQUEST['theme'])             ? (string)$_REQUEST['theme']            : $config['html']['theme'];
    $custom_css                 = isset($_REQUEST['custom_css'])        ? (string)$_REQUEST['custom_css']       : $config['html']['custom_css'];
    $timezone                   = isset($_REQUEST['timezone'])          ? (string)$_REQUEST['timezone']         : $config['timezone'];
    $language                   = isset($_REQUEST['language'])          ? (string)$_REQUEST['language']         : $config['language'];
    $disable_updatelist         = isset($_REQUEST['disable_updatelist']);
    $disable_help               = isset($_REQUEST['disable_help']);
    $disable_config             = isset($_REQUEST['disable_config']);
    $enable_debug_link          = isset($_REQUEST['enable_debug_link']);
    $disable_devices            = isset($_REQUEST['disable_devices']);
    $disable_footprints         = isset($_REQUEST['disable_footprints']);
    $disable_manufacturers      = isset($_REQUEST['disable_manufacturers']);
    $disable_labels             = isset($_REQUEST['disable_labels']);
    $disable_calculator         = isset($_REQUEST['disable_calculator']);
    $disable_iclogos            = isset($_REQUEST['disable_iclogos']);
    $disable_auto_datasheets    = isset($_REQUEST['disable_auto_datasheets']);
    $disable_tools_footprints   = isset($_REQUEST['disable_tools_footprints']);
    $tools_footprints_autoload  = isset($_REQUEST['tools_footprints_autoload']);
    $enable_developer_mode      = isset($_REQUEST['enable_developer_mode']);
    $enable_dokuwiki_write_perms= isset($_REQUEST['enable_dokuwiki_write_perms']);
    $use_modal_popup            = isset($_REQUEST['use_modal_popup']);
    $popup_width                = isset($_REQUEST['popup_width'])       ? (integer)$_REQUEST['popup_width']     : $config['popup']['width'];
    $popup_height               = isset($_REQUEST['popup_height'])      ? (integer)$_REQUEST['popup_height']    : $config['popup']['height'];
    $page_title                 = isset($_REQUEST['page_title'])        ? (string)$_REQUEST['page_title']       : $config['page_title'];
    $startup_banner             = isset($_REQUEST['startup_banner'])    ? (string)$_REQUEST['startup_banner']   : $config['startup']['custom_banner'];

    // section "appearance"
    $use_old_datasheet_icons    = isset($_REQUEST['use_old_datasheet_icons']);
    $short_description          = isset($_REQUEST['short_description']);

    // section "3d footprints"
    $foot3d_active              = isset($_REQUEST['foot3d_active']);
    $foot3d_show_info           = isset($_REQUEST['foot3d_show_info']);

    //section "part properites"
    $properties_active          = isset($_REQUEST['properties_active']);

    // section "change administrator password"
    $current_admin_password     = isset($_REQUEST['current_admin_password'])    ? (string)$_REQUEST['current_admin_password']   : '';
    $new_admin_password_1       = isset($_REQUEST['new_admin_password_1'])      ? (string)$_REQUEST['new_admin_password_1']     : '';
    $new_admin_password_2       = isset($_REQUEST['new_admin_password_2'])      ? (string)$_REQUEST['new_admin_password_2']     : '';

    $action = 'default';
    if (isset($_REQUEST["apply"]))                      {$action = 'apply';}
    if (isset($_REQUEST["change_admin_password"]))      {$action = 'change_admin_password';}

    /********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML($config['html']['theme'], /*$config['html']['custom_css']*/ $custom_css, _('Konfiguration'));

    try
    {
        //$database           = new Database();
        //$log                = new Log($database);
        //$system             = new System($database, $log);
        //$current_user       = new User($database, $current_user, $log, 1); // admin
    }
    catch (Exception $e)
    {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }

    /********************************************************************************
    *
    *   Execute actions
    *
    *********************************************************************************/

    if ( ! $fatal_error)
    {
        switch ($action)
        {
            case 'apply':
                $config_old = $config;

                //$config['html']['http_charset']             = $http_charset;
                $config['html']['theme']                    = $theme;
                $config['html']['custom_css']               = $custom_css;
                $config['timezone']                         = $timezone;
                $config['language']                         = $language;
                $config['startup']['disable_update_list']   = $disable_updatelist;
                $config['menu']['disable_help']             = $disable_help;
                $config['menu']['disable_labels']           = $disable_labels;
                $config['menu']['disable_calculator']       = $disable_calculator;
                $config['menu']['disable_iclogos']          = $disable_iclogos;
                $config['menu']['enable_debug']             = $enable_debug_link;
                $config['devices']['disable']               = $disable_devices;
                $config['footprints']['disable']            = $disable_footprints;
                $config['manufacturers']['disable']         = $disable_manufacturers;
                $config['auto_datasheets']['disable']       = $disable_auto_datasheets;
                $config['menu']['disable_footprints']       = $disable_tools_footprints;
                $config['tools']['footprints']['autoload']  = $tools_footprints_autoload;
                $config['developer_mode']                   = ($enable_developer_mode && file_exists(BASE.'/development'));
                $config['popup']['modal']                   = $use_modal_popup;
                $config['popup']['width']                   = $popup_width;
                $config['popup']['height']                  = $popup_height;

                $config['appearance']['use_old_datasheet_icons'] = $use_old_datasheet_icons;
                $config['appearance']['short_description'] = $short_description;

                $config['foot3d']['active']                 = $foot3d_active;
                $config['foot3d']['show_info']              = $foot3d_show_infos;

                $config['properties']['active']             = $properties_active;

                if ( ! $config['is_online_demo'])
                {
                    // settings which should not be able to change in the online demo
                    $config['menu']['disable_config']       = $disable_config;
                    $config['page_title']                   = $page_title;
                    $config['startup']['custom_banner']     = $startup_banner;
                }
                else
                {
                    // this is an online demo!
                    $enable_dokuwiki_write_perms = false; // the DokuWiki must be in read-only mode in the online demo!!
                }

                // change DokuWiki write permissions
                if (($enable_dokuwiki_write_perms) && ( ! file_exists(DOKUWIKI_PERMS_FILENAME)))
                {
                    // enable write permissions
                    $filehandle = fopen(DOKUWIKI_PERMS_FILENAME, 'w');
                    if ( ! $filehandle)
                        $messages[] = array('text' => 'Die Datei "'.DOKUWIKI_PERMS_FILENAME.'" kann nicht gelöscht werden! '.
                                            _('Überprüfen Sie, ob Sie die nötigen Schreibrechte besitzen.'), 'strong' => true, 'color' => 'red');
                    else
                        fclose($filehandle);
                }
                elseif (( ! $enable_dokuwiki_write_perms) && (file_exists(DOKUWIKI_PERMS_FILENAME)))
                {
                    // disable write permissions
                    if ( ! unlink(DOKUWIKI_PERMS_FILENAME))
                        $messages[] = array('text' => 'Die Datei "'.DOKUWIKI_PERMS_FILENAME.'" kann nicht gelöscht werden! '.
                                            _('Überprüfen Sie, ob Sie die nötigen Schreibrechte besitzen.'), 'strong' => true, 'color' => 'red');
                }

                try
                {
                    save_config();
                    $html->set_variable('refresh_navigation_frame', true, 'boolean');
                    //header('Location: system_config.php'); // Reload the page that we can see if the new settings are stored successfully --> does not work correctly?!
                }
                catch (Exception $e)
                {
                    $config = $config_old; // reload the old config
                    $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: '.nl2br($e->getMessage())), 'color' => 'red');
                }
                break;

            case 'change_admin_password':
                try
                {
                    if ($config['is_online_demo'])
                        throw new Exception(_('Diese Funktion steht in der Online-Demo nicht zur Verfügung!'));

                    // set_admin_password() throws an exception if the old or the new passwords are not valid
                    set_admin_password($current_admin_password, $new_admin_password_1, $new_admin_password_2, false);

                    save_config();

                    $messages[] = array('text' => _('Das neue Administratorpasswort wurde erfolgreich gespeichert.'), 'strong' => true, 'color' => 'darkgreen');
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                }
                break;
        }
    }

    /********************************************************************************
    *
    *   Set all HTML variables
    *
    *********************************************************************************/

    // http charset / theme
    $html->set_loop('http_charset_loop',    array_to_template_loop($config['http_charsets'], $config['html']['http_charset']));
    $html->set_loop('theme_loop',           build_theme_loop());
    $html->set_loop('custom_css_loop',      build_custom_css_loop());

    // locale settings
    $html->set_loop('timezone_loop',        array_to_template_loop($config['timezones'], $config['timezone']));
    $html->set_loop('language_loop',        array_to_template_loop($config['languages'], $config['language']));

    // checkboxes
    $html->set_variable('disable_updatelist',           $config['startup']['disable_update_list'],  'boolean');
    $html->set_variable('disable_help',                 $config['menu']['disable_help'],            'boolean');
    $html->set_variable('disable_config',               $config['menu']['disable_config'],          'boolean');
    $html->set_variable('enable_debug_link',            $config['menu']['enable_debug'],            'boolean');
    $html->set_variable('disable_devices',              $config['devices']['disable'],              'boolean');
    $html->set_variable('disable_footprints',           $config['footprints']['disable'],           'boolean');
    $html->set_variable('disable_manufacturers',        $config['manufacturers']['disable'],        'boolean');
    $html->set_variable('disable_labels',               $config['menu']['disable_labels'],          'boolean');
    $html->set_variable('disable_calculator',           $config['menu']['disable_calculator'],      'boolean');
    $html->set_variable('disable_iclogos',              $config['menu']['disable_iclogos'],         'boolean');
    $html->set_variable('disable_auto_datasheets',      $config['auto_datasheets']['disable'],      'boolean');
    $html->set_variable('disable_tools_footprints',     $config['menu']['disable_footprints'],      'boolean');
    $html->set_variable('tools_footprints_autoload',    $config['tools']['footprints']['autoload'], 'boolean');
    $html->set_variable('developer_mode_available',     file_exists(BASE.'/development'),           'boolean');
    $html->set_variable('enable_developer_mode',        $config['developer_mode'],                  'boolean');
    $html->set_variable('enable_dokuwiki_write_perms',  file_exists(DOKUWIKI_PERMS_FILENAME),       'boolean');
    $html->set_variable('use_old_datasheet_icons',      $config['appearance']['use_old_datasheet_icons'], 'boolean');

    // popup settings
    $html->set_variable('use_modal_popup',              $config['popup']['modal'],                  'boolean');
    $html->set_variable('popup_width',                  $config['popup']['width'],                  'integer');
    $html->set_variable('popup_height',                 $config['popup']['height'],                 'integer');

    // site properties
    $html->set_variable('page_title',                   $config['page_title'],                      'string');
    $html->set_variable('startup_banner',               $config['startup']['custom_banner'],        'string');

    // server
    $html->set_variable('php_version',                  phpversion(),                               'string');
    $html->set_variable('htaccess_works',               (getenv('htaccessWorking')=='true'),        'boolean');
    $html->set_variable('is_online_demo',               $config['is_online_demo'],                  'boolean');

    //Part properties
    $html->set_variable('properties_active',                  $config['properties']['active'],               'boolean');

    // 3d Footprints
    $html->set_variable('foot3d_active',                $config['foot3d']['active'],                'boolean');
    $html->set_variable('foot3d_show_info',             $config['foot3d']['show_info'],             'boolean');

    // Appearance
    $html->set_variable( 'short_description', $config['appearance']['short_description'], 'boolean');

    // check if the server supports the selected language and print a warning if not
    if ( ! own_setlocale(LC_ALL, $config['language']))
    {
        $messages[] = array('text' => _('Achtung:'), 'strong' => true, 'color' => 'red');
        $messages[] = array('text' => sprintf(_('Die gewählte Sprache "%s" wird vom Server nicht unterstützt!'), $config['language']), 'color' => 'red', );
        $messages[] = array('text' => _('Bitte installieren Sie diese Sprache oder wählen Sie eine andere.'), 'color' => 'red', );
    }

    /********************************************************************************
    *
    *   Generate HTML Output
    *
    *********************************************************************************/


    //If a ajax version is requested, say this the template engine.
    if(isset($_REQUEST["ajax"]))
    {
        $html->set_variable("ajax_request", true);
    }

    $html->print_header($messages);

    if ( ! $fatal_error)
        $html->print_template('system_config');

    $html->print_footer();
