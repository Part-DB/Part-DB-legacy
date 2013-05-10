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
        2012-09-18  kami89              - created
*/

    include_once('start_session.php');

    $messages = array();
    $fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

    $messages[] = array('text' => 'Systemupdates werden derzeit noch nicht unterstützt.', 'color' => 'red', 'strong' => true);

    /********************************************************************************
    *
    *   Some special functions for this site
    *
    *********************************************************************************/

    function build_update_type_loop()
    {
        global $config;
        $loop = array(
            array('value' => 'off',      'text' => 'Deaktiviert',               'selected' => ($config['update']['type'] == 'off')),
            array('value' => 'stable',   'text' => 'Stabile Versionen',         'selected' => ($config['update']['type'] == 'stable')),
            array('value' => 'unstable', 'text' => 'Release Kandidaten',        'selected' => ($config['update']['type'] == 'unstable')),
            array('value' => 'svn',      'text' => 'SVN Entwicklerversionen',   'selected' => ($config['update']['type'] == 'svn')));

        return $loop;
    }

    function get_available_local_updates()
    {
        $updates = array();
        $current_version = str_replace(' ', '.', SystemVersion::get_installed_version()->as_string());

        while (is_readable($current_filename = BASE.'/updates/update_for_'.$current_version.'/info.xml'))
        {
            $update = array();
            $update['path'] = realpath(dirname($current_filename));
            $update['info_filename'] = realpath($current_filename);
            $update['from_version'] = $current_version;
            $update['changelog'] = array();

            $dom = new DOMDocument('1.0', 'utf-8');
            $success = $dom->load($current_filename, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NOBLANKS);

            if ( ! $success)
                throw new Exception('Das DOMDocument-Objekt konnte nicht erstellt werden!');

            $header_elements = $dom->getElementsByTagName('header');
            if ($header_elements->length == 0)
                throw new Exception('Es wurden keine Header Einträge in der XML-Datei gefunden!');

            foreach ($header_elements->item(0)->childNodes as $node)
            {
                switch ($node->nodeName)
                {
                    case 'version':
                        if ($current_version == $node->nodeValue)
                            throw new Exception('Unendliche Schleife in Updates entdeckt!');
                        $update['to_version'] = $node->nodeValue;
                        $current_version = $node->nodeValue;
                        break;
                    case 'release_date':
                        $update['release_date'] = $node->nodeValue;
                        break;
                    case 'required_version':
                        $update['required_version'] = $node->nodeValue;
                        break;
                }
            }

            $changelog_elements = $dom->getElementsByTagName('changelog_items');
            if ($changelog_elements->length == 0)
                throw new Exception('Es wurden keine Changelog Einträge in der XML-Datei gefunden!');

            foreach ($changelog_elements->item(0)->childNodes as $node)
                $update['changelog'][] = $node->nodeValue;

            $updates[] = $update;
        }

        return $updates;
    }

    function build_update_table_loop($updates)
    {
        $loop = array();

        // table columns
        $columns = array(   'systemupdate_from_version', 'systemupdate_to_version', 'systemupdate_release_date', 'systemupdate_changelog');
        $column_loop = array();
        foreach ($columns as $column)
            $column_loop[] = array('caption' => $column);
        $loop[] = array('print_header' => true, 'columns' => $column_loop); // print the table header

        $row_index = 0;
        foreach ($updates as $update)
        {
            $from_version = new SystemVersion($update['from_version']);
            $to_version = new SystemVersion($update['to_version']);

            $table_row = array();
            $table_row['row_odd']       = is_odd($row_index);
            $table_row['row_index']     = $row_index;
            $table_row['row_fields']    = array();
            $table_row['stable']        = ($to_version->get_version_type() == 'stable');

            $changelog = array();
            foreach ($update['changelog'] as $log_item)
                $changelog[] = array('log_item' => $log_item);

            $table_row['row_fields'][]  = array(    'caption'       => 'systemupdate_from_version',
                                                    'stable'        => ($to_version->get_version_type() == 'stable'),
                                                    'from_version'  => $from_version->as_string(false, false, false, false));
            $table_row['row_fields'][]  = array(    'caption'       => 'systemupdate_to_version',
                                                    'stable'        => ($to_version->get_version_type() == 'stable'),
                                                    'to_version'    => $to_version->as_string(false, false, false, false));
            $table_row['row_fields'][]  = array(    'caption'       => 'systemupdate_release_date',
                                                    'stable'        => ($to_version->get_version_type() == 'stable'),
                                                    'release_date'  => $update['release_date']);
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

    $update_type = isset($_REQUEST['update_type']) ? (string)$_REQUEST['update_type'] : 'stable';

    $action = 'default';
    if (isset($_REQUEST["apply"]))                      {$action = 'apply';}
    if (isset($_REQUEST["make_update"]))                {$action = 'make_update';}
    if (isset($_REQUEST["download_update_packages"]))   {$action = 'download_update_packages';}

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
                //if ($config['is_online_demo'])
                //    break;

                $config_old = $config;

                $config['update']['type'] = $update_type;

                try
                {
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

            case 'make_update':
                try
                {
                    $system->update();
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'download_update_packages':
                try
                {
                    $system->download_and_extract_update_archives();
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
            $updates = get_available_local_updates();
            if (count($updates) > 0)
            {
                $update_loop = build_update_table_loop($updates);
                $html->set_loop('table', $update_loop);
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

    // updates
    $html->set_loop('update_type_loop', build_update_type_loop());

    if ( ! $fatal_error)
    {
        try
        {
            $current = $system->get_installed_version();
            $html->set_variable('current_version', $current->as_string(false, false, false, true), 'string');

            $latest_stable = $system->get_latest_version('stable');
            $html->set_variable('latest_stable_version', $latest_stable->as_string(false, false, false, false), 'string');

            $latest_unstable = $system->get_latest_version('unstable');
            $html->set_variable('latest_unstable_version', $latest_unstable->as_string(false, false, false, false), 'string');

            //$latest_svn_revision = $system->get_latest_version('svn');
            //$html->set_variable('latest_svn_revision', $latest_svn_revision->as_string(false, false, false, false), 'string');

            $type = ($config['update']['type'] == 'off') ? 'stable' : $config['update']['type'];
            $is_update_available = $system->get_latest_version($type)->is_newer_than($current);
            $html->set_variable('update_available', $is_update_available, 'boolean');
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
