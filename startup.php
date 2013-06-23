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
*/

    include_once('start_session.php');
    include_once('authors.php');

    $messages = array();
    $fatal_error = false;

    /********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Startseite');

    try
    {
        $database           = new Database();
        $log                = new Log($database);
        $system             = new System($database, $log);
        $current_user       = new User($database, $current_user, $log, 1); // admin
    }
    catch (Exception $e)
    {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }

    /********************************************************************************
    *
    *   Database Update (if required and automatic updates are enabled)
    *
    *********************************************************************************/

    if ((! $fatal_error) && ($database->is_update_required()))
    {
        if (($database->get_current_version() < 13) && ($database->get_latest_version() >= 13)) // v12 to v13 was a huge update! disable auto-update temporary!
        {
            $config['db']['auto_update'] = false;
            $html->set_variable('auto_disabled_autoupdate', true, 'boolean');
        }

        $html->set_variable('database_update',      true,                               'boolean');
        $html->set_variable('disabled_autoupdate',  ! $config['db']['auto_update'],     'boolean');
        $html->set_variable('db_version_current',   $database->get_current_version(),   'integer');
        $html->set_variable('db_version_latest',    $database->get_latest_version(),    'integer');

        if ($config['db']['auto_update'] == true)
        {
            $update_log = $database->update();
            $html->set_variable('database_update_log', nl2br($update_log));
        }
    }

    /********************************************************************************
    *
    *   Show a warning if there are empty tables
    *       (categories, storelocations, footprints, suppliers)
    *
    *********************************************************************************/

    if (( ! $fatal_error) && ( ! $database->is_update_required()))
    {
        $good = "&#x2714; ";
        $bad  = "&#x2718; ";

        try
        {
            $missing_category       = ((Category::      get_count($database) == 0) ? $bad : $good);
            $missing_storelocation  = ((Storelocation:: get_count($database) == 0) ? $bad : $good);
            $missing_footprint      = ((Footprint::     get_count($database) == 0) ? $bad : $good);
            $missing_supplier       = ((Supplier::      get_count($database) == 0) ? $bad : $good);

            $display_warning        = (($missing_category == $bad) || ($missing_storelocation == $bad)
                                        || ($missing_footprint == $bad) || ($missing_supplier == $bad));

            $html->set_variable('missing_category',    $missing_category);
            $html->set_variable('missing_storeloc',    $missing_storelocation);
            $html->set_variable('missing_footprint',   $missing_footprint);
            $html->set_variable('missing_supplier',    $missing_supplier);
            $html->set_variable('display_warning',     $display_warning, 'boolean');
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        }
    }

    /********************************************************************************
    *
    *   Show a warning if there are footprints with broken filenames
    *
    *********************************************************************************/

    if (( ! $fatal_error) && ( ! $database->is_update_required()))
    {
        try
        {
            if (count(Footprint::get_broken_filename_footprints($database, $current_user, $log)) > 0)
                $html->set_variable('broken_filename_footprints', true);
            else
                $html->set_variable('broken_filename_footprints', false);
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        }
    }

    /********************************************************************************
    *
    *   Show Update List (RSS Feed from Google Code) if enabled
    *
    *********************************************************************************/

    if (( ! $fatal_error) && ( ! $config['startup']['disable_update_list']))
    {
        $rss_file = implode(' ', file('http://code.google.com/feeds/p/part-db/downloads/basic'));
        $rss_rows = array('title', 'updated', 'id');
        $rss_array = explode('<entry>', $rss_file);

        // show only the last actual versions
        $count = 4;
        $rss_text = array();
        foreach ($rss_array as $string)
        {
            // show all lines from rss feed
            foreach ($rss_rows as $row)
            {
                // find tags
                preg_match_all("|<$row>(.*)</$row>|Usim", $string, $preg_match);
                $$row = $preg_match[1][0];
                // make clickable if http url
                $$row = preg_replace('`((?:http)://\S+[[:alnum:]]/?)`si', '<a target="_blank" href="\\1">\\1</a>', $$row);
                $rss_text[]['row'] = $$row;
            }
            if (!(--$count)) break;
            $rss_text[]['row'] = '';
        }

        $html->set_loop('update_list', $rss_text);
    }

    /********************************************************************************
    *
    *   Set the rest of the HTML variables
    *
    *********************************************************************************/

    $html->set_loop('authors', $authors);

    if (! $fatal_error)
    {
        $html->set_variable('banner', $config['startup']['custom_banner'], 'string');

        try
        {
            $system_version = $system->get_installed_version();
            $html->set_variable('system_version',       $system_version->as_string(false, true, true, false),   'string');
            $html->set_variable('system_version_full',  $system_version->as_string(false, false, false, true),  'string');
            $html->set_variable('svn_revision',         get_svn_revision(),                                     'integer');
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        }
    }

    /********************************************************************************
    *
    *   Generate HTML Output
    *
    *********************************************************************************/

    $reload_link = $fatal_error ? 'startup.php' : '';   // an empty string means that the...
    $html->print_header($messages, $reload_link);       // ...reload-button won't be visible

    if (! $fatal_error)
        $html->print_template('startup');

    $html->print_footer();

?>
