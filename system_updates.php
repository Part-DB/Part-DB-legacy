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

    $Id: system_updates.php 626 2013-05-10 20:52:56Z kami89@gmx.ch $

    Changelog (sorted by date):
        [DATE]      [NICKNAME]          [CHANGES]
        2012-09-18  kami89              - created
*/

    include_once('start_session.php');

    $messages = array();
    $fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

    /********************************************************************************
    *
    *   Some special functions for this site
    *
    *********************************************************************************/

    function build_available_versions_loop(&$available_updates, &$selected_target_version)
    {
        global $config;
        $loop = array();

        // extract all possible versions from $available_updates (remove duplicates!)
        $available_versions = array(); // Example: array(['0.3.0.RC10] => SystemVersionObject, ...)
        foreach ($available_updates as $update)
        {
            if ( ! array_key_exists($update->get_to_version()->as_string(), $available_versions))
                $available_versions[$update->get_to_version()->as_string()] = $update->get_to_version();
        }

        usort($available_versions, function($a, $b) {return $b->is_newer_than($a);}); // sort by version, descending

        if ( ! $config['updates']['use_release_candidates'])
        {
            // remove all unstable versions
            foreach ($available_versions as $key => $version)
            {
                if ($version->get_version_type() != 'stable')
                    unset($available_versions[$key]);
            }
        }

        foreach ($available_versions as $version)
        {
            $loop[] = array('value' => $version->as_string(), 'text' => $version->as_string(false, false, false, true),
                            'selected' => ($version->is_equal_to($selected_target_version)));
        }

        return $loop;
    }

    function build_update_table_loop($updates)
    {
        $loop = array();

        // table columns
        $columns = array('systemupdate_from_version', 'systemupdate_to_version', 'systemupdate_release_date', 'systemupdate_changelog');
        $column_loop = array();
        foreach ($columns as $column)
            $column_loop[] = array('caption' => $column);
        $loop[] = array('print_header' => true, 'columns' => $column_loop); // print the table header

        $row_index = 0;
        foreach ($updates as $update)
        {
            $from_version = $update->get_from_version();
            $to_version = $update->get_to_version();

            $table_row = array();
            $table_row['row_odd']       = is_odd($row_index);
            $table_row['row_index']     = $row_index;
            $table_row['row_fields']    = array();
            $table_row['stable']        = ($to_version->get_version_type() == 'stable');

            $changelog = array();
            foreach ($update->get_changelog() as $log_item)
                $changelog[] = array('log_item' => $log_item);

            $table_row['row_fields'][]  = array(    'caption'       => 'systemupdate_from_version',
                                                    'stable'        => ($to_version->get_version_type() == 'stable'),
                                                    'from_version'  => $from_version->as_string(false, false, false, false));
            $table_row['row_fields'][]  = array(    'caption'       => 'systemupdate_to_version',
                                                    'stable'        => ($to_version->get_version_type() == 'stable'),
                                                    'to_version'    => $to_version->as_string(false, false, false, false));
            $table_row['row_fields'][]  = array(    'caption'       => 'systemupdate_release_date',
                                                    'stable'        => ($to_version->get_version_type() == 'stable'),
                                                    'release_date'  => $update->get_release_date());
            $table_row['row_fields'][]  = array(    'caption'       => 'systemupdate_changelog',
                                                    'stable'        => ($to_version->get_version_type() == 'stable'),
                                                    'changelog'     => $changelog);

            $loop[] = $table_row;
            $row_index++;
        }

        return $loop;
    }

    /********************************************************************************
    *
    *   Evaluate $_REQUEST
    *
    *********************************************************************************/

    $use_release_candidates     = isset($_REQUEST['use_release_candidates'])    ? true : false;
    $disable_startup_check      = isset($_REQUEST['check_updates_on_startup'])  ? false : true;
    $maintenance_mode_enabled   = isset($_REQUEST['maintenance_mode_enabled'])  ? true : false;
    $target_version_string      = isset($_REQUEST['selected_target_version'])   ? (string)$_REQUEST['selected_target_version']  : NULL;

    $action = 'default';
    if (isset($_REQUEST["apply_settings"]))             {$action = 'apply_settings';}
    if (isset($_REQUEST["download_update_packages"]))   {$action = 'download_update_packages';}
    if (isset($_REQUEST["make_update"]))                {$action = 'make_update';}

    /********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'System-Updates');

    try
    {
        $database           = new Database();
        $log                = new Log($database);
        $system             = new System($database, $log);
        //$current_user       = new User($database, $current_user, $log, 1); // admin

        if ($target_version_string == NULL)
            $target_version = SystemVersion::get_latest_version();
        else
            $target_version = new SystemVersion($target_version_string);
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
            case 'apply_settings':

                if ($config['is_online_demo']) // this action is not allowed in the online demo
                    break;

                $config_old = $config; // save old config, so we are able to restore them

                $config['updates']['use_release_candidates']    = $use_release_candidates;
                $config['updates']['disable_startup_check']     = $disable_startup_check;

                try
                {
                    set_maintenance_mode($maintenance_mode_enabled);
                    save_config();
                    header('Location: system_updates.php'); // Reload the page that we can see if the new settings are stored successfully
                }
                catch (Exception $e)
                {
                    $config = $config_old; // reload the old config
                    $messages[] = array('text' => 'Die neuen Werte konnten nicht gespeichert werden!', 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
                }
                break;

            case 'download_update_packages':
                try
                {
                    SystemUpdate::download_update_archives();
                    SystemUpdate::extract_local_update_archives();
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'make_update':
                try
                {
                    $update_steps = SystemUpdate::get_best_update_order(SystemVersion::get_installed_version(), $target_version);
                    $update_log_loop = $system->update($update_steps);
                    $html->set_loop('update_log_loop', $update_log_loop);
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;
        }
    }

    /********************************************************************************
    *
    *   Generate Updates Table Loop
    *
    *********************************************************************************/

    if ( ! $fatal_error)
    {
        try
        {
            $all_available_updates = SystemUpdate::get_all_available_local_updates();
            $update_steps = SystemUpdate::get_best_update_order(SystemVersion::get_installed_version(), $target_version);

            if (count($all_available_updates) > 0)
            {
                $available_versions_loop = build_available_versions_loop($all_available_updates, $target_version);
                $html->set_loop('available_versions_loop', $available_versions_loop);
            }

            if (count($update_steps) > 0)
            {
                $update_steps_loop = build_update_table_loop($update_steps);
                $html->set_loop('table', $update_steps_loop);
            }
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
        }
    }

    /********************************************************************************
    *
    *   Set all HTML variables
    *
    *********************************************************************************/

    if ( ! $fatal_error)
    {
        try
        {
            $html->set_variable('use_release_candidates',   $config['updates']['use_release_candidates'],                       'boolean');
            $html->set_variable('check_updates_on_startup', ( ! $config['updates']['disable_startup_check']),                   'boolean');
            $html->set_variable('disable_internet_access',  $config['disable_internet_access'],                                 'boolean');
            $html->set_variable('maintenance_mode_enabled', $config['maintenance_mode']['active'],                              'boolean');
            $html->set_variable('refresh_navigation_frame', (isset($refresh_navigation_frame) && $refresh_navigation_frame),    'boolean');

            $current_version = SystemVersion::get_installed_version();
            $html->set_variable('current_version',          $current_version->as_string(false, false, false, true),             'string');

            $latest_version = SystemVersion::get_latest_version();
            $html->set_variable('latest_version',           $latest_version->as_string(false, false, false, true),              'string');

            $html->set_variable('is_update_available',      $latest_version->is_newer_than($current_version),                   'boolean');
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
        }
    }

    /********************************************************************************
    *
    *   Generate HTML Output
    *
    *********************************************************************************/

    $html->print_header($messages);

    if ( ! $fatal_error)
        $html->print_template('system_updates');

    $html->print_footer();

?>
