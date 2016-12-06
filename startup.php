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

    $html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Startseite'));

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
        $feed_link = 'https://github.com/sandboxgangster/Part-DB/releases.atom';
        $item_count = 4;

        try
        {
            $rss_loop = array();
            $feed_content = '';

            try
            {
                $feed_content = curl_get_data($feed_link);
            }
            catch (Exception $e)
            {
                $feed_content = file_get_contents($feed_link);
            }

            if (strlen($feed_content) == 0)
                throw new Exception(_('Der Atom-Feed konnte nicht aus dem Internet heruntergeladen werden. '.
                                    'Prüfen Sie bitte, ob Ihre PHP-Konfiguration das Herunterladen aus dem Internet zulässt.'));

            if ( ! class_exists('SimpleXMLElement'))
                throw new Exception(_('Die Klasse "SimpleXMLElement" ist nicht vorhanden!'));

            $xml = simplexml_load_string($feed_content);

            if ( ! is_object($xml))
                throw new Exception(_('Das SimpleXMLElement konnte nicht erzeugt werden!'));

            $rss_loop[] = array('title' => _('Part-DB Releases Atom-Feed'), 'datetime' => $xml->updated, 'link' => $feed_link);

            $item_index = 1;
            foreach ($xml->entry as $entry)
            {
                if ($item_index >= $item_count)
                    break;

                $link = _('FEHLER - Kein Link im Atom-Feed gefunden!');
                foreach ($entry->link as $link_entry)
                {
                    $attributes = $link_entry->attributes();
                    if (isset($attributes['rel']) && ($attributes['rel'] == 'alternate') && isset($attributes['href']))
                        $link = 'https://github.com'.$attributes['href'];
                }

                $rss_loop[] = array('title' => $entry->title, 'datetime' => $entry->updated, 'link' => $link);
                $item_index++;
            }
        }
        catch (Exception $e)
        {
            $rss_loop = array(array('title' => $e->getMessage()));
        }

        $html->set_loop('rss_feed_loop', $rss_loop);
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
            $html->set_variable('git_branch',           get_git_branch_name(),                                  'string');
            $html->set_variable('git_commit',           get_git_commit_hash(10),                                'string');
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
