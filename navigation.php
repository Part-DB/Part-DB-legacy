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
        2012-??-??  weinbauer73         - changed to templates
        2012-09-03  kami89              - changed to OOP
        2012-11-03  kami89              - added possibility to hide search parameters
                                            (DIV "search_selection" in *.tmpl file)
*/

    include_once('start_session.php');

    $messages = array();
    $fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

   /********************************************************************************
    *
    *   Evaluate $_REQUEST / Execute Actions
    *
    *********************************************************************************/


    if ((isset($_REQUEST["enable_template_debugging"])) || (isset($_REQUEST["disable_template_debugging"])))
    {
        $config['debug']['template_debugging_enable'] = isset($_REQUEST["enable_template_debugging"]);
        save_config();
        //header('Location: navigation.php'); // reload the navigation frame
    }

    if ((isset($_REQUEST["enable_request_debugging"])) || (isset($_REQUEST["disable_request_debugging"])))
    {
        $config['debug']['request_debugging_enable'] = isset($_REQUEST["enable_request_debugging"]);
        save_config();
        //header('Location: navigation.php'); // reload the navigation frame
    }

    /********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML();

    try
    {
        $database           = new Database();

        // The database is outdated, so an exception with a huge MySQL error message will be thrown somewhere in this file.
        // This is ugly, so we will only show something like "database update needed"
        if ($database->is_update_required())
            throw new Exception('Datenbank-Update notwendig!');

        $log                = new Log($database);
        $current_user       = new User($database, $current_user, $log, 1); // admin
        $root_category      = new Category($database, $current_user, $log, 0);
        $root_device        = new Device($database, $current_user, $log, 0);
    }
    catch (Exception $e)
    {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }

    /********************************************************************************
    *
    *   Build Navigation Trees
    *
    *********************************************************************************/

    //$html->use_javascript(array('dtree', 'toggle'));

    if (! $fatal_error)
    {
        try
        {
            $javascript = $root_category->build_javascript_tree('cat_navtree', 'show_category_parts.php',
                                                                'cid', 'content_frame');
            $html->set_variable('categories_navtree', $javascript);

            $javascript = $root_device->build_javascript_tree(  'cat_devtree', 'show_device_parts.php',
                                                                'device_id', 'content_frame', true, true,
                                                                'Übersicht', false, true);
            $html->set_variable('devices_navtree', $javascript);
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => 'Die Navigationsmenüs konnten nicht erfolgreich erstellt werden!', 'strong' => true, 'color' => 'red');
            $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
        }
    }

    /********************************************************************************
    *
    *   Set the rest of the HTML variables
    *
    *********************************************************************************/

    $html->set_variable('disable_footprints',       $config['footprints']['disable'],                               'boolean');
    $html->set_variable('disable_manufacturers',    $config['manufacturers']['disable'],                            'boolean');
    $html->set_variable('disable_devices',          $config['devices']['disable'],                                  'boolean');
    $html->set_variable('disable_help',             $config['menu']['disable_help'],                                'boolean');
    $html->set_variable('disable_config',           (($fatal_error) ? false : $config['menu']['disable_config']),   'boolean');
    $html->set_variable('enable_debug_link',        (($fatal_error) ? true : $config['menu']['enable_debug']),      'boolean');
    $html->set_variable('disable_labels',           $config['menu']['disable_labels'],                              'boolean');
    $html->set_variable('disable_calculator',       $config['menu']['disable_calculator'],                          'boolean');
    $html->set_variable('disable_iclogos',          $config['menu']['disable_iclogos'],                             'boolean');
    $html->set_variable('disable_tools_footprints', $config['menu']['disable_footprints'],                          'boolean');
    $html->set_variable('developer_mode',           $config['developer_mode'],                                      'boolean');
    $html->set_variable('db_backup_name',           $config['db']['backup']['name'],                                'string');
    $html->set_variable('db_backup_url',            $config['db']['backup']['url'],                                 'string');

    if ($config['debug']['enable'])
    {
        if ($config['debug']['request_debugging_enable'])
            $messages[] = array('html' => '<input type="submit" name="disable_request_debugging" value="REQUEST-Debugging deaktivieren">');
        else
            $messages[] = array('html' => '<input type="submit" name="enable_request_debugging" value="REQUEST-Debugging aktivieren">');

        if ($config['debug']['template_debugging_enable'])
            $messages[] = array('html' => '<input type="submit" name="disable_template_debugging" value="Template-Debugging deaktivieren">');
        else
            $messages[] = array('html' => '<input type="submit" name="enable_template_debugging" value="Template-Debugging aktivieren">');
    }

    /********************************************************************************
    *
    *   Generate HTML Output
    *
    *********************************************************************************/

    $html->set_meta(array('no_header'=>true));
    $reload_link = $fatal_error ? 'navigation.php' : '';    // an empty string means that the...
    $html->print_header($messages, $reload_link);           // ...reload-button won't be visible

    //if (! $fatal_error)
    // --> we do not hide the navigation, because this way you can reach the debug-link in the menu
    $html->print_template('navigation');

    $html->print_footer();

?>
