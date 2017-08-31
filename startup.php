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

use PartDB\Category;
use PartDB\Database;
use PartDB\Footprint;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Storelocation;
use PartDB\Supplier;
use PartDB\System;
use PartDB\User;

include_once('start_session.php');
include_once('inc/authors.php');

$messages = array();
$fatal_error = false;

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Startseite'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $system             = new System($database, $log);
    $current_user       = new User($database, $current_user, $log, 1); // admin
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Database Update (if required and automatic updates are enabled)
 *
 *********************************************************************************/

if ((! $fatal_error) && ($database->isUpdateRequired())) {
    if (($database->getCurrentVersion() < 13) && ($database->getLatestVersion() >= 13)) { // v12 to v13 was a huge update! disable auto-update temporary!
        $config['db']['auto_update'] = false;
        $html->setVariable('auto_disabled_autoupdate', true, 'boolean');
    }

    $html->setVariable('database_update', true, 'boolean');
    $html->setVariable('disabled_autoupdate', ! $config['db']['auto_update'], 'boolean');
    $html->setVariable('db_version_current', $database->getCurrentVersion(), 'integer');
    $html->setVariable('db_version_latest', $database->getLatestVersion(), 'integer');

    if ($config['db']['auto_update'] == true) {
        $update_log = $database->update();
        $html->setVariable('database_update_log', nl2br($update_log));
    }
}

/********************************************************************************
 *
 *   Show a warning if there are empty tables
 *       (categories, storelocations, footprints, suppliers)
 *
 *********************************************************************************/

if ((! $fatal_error) && (! $database->isUpdateRequired())) {
    $good = "&#x2714; ";
    $bad  = "&#x2718; ";

    try {
        $missing_category       = ((Category::      getCount($database) == 0) ? $bad : $good);
        $missing_storelocation  = ((Storelocation:: getCount($database) == 0) ? $bad : $good);
        $missing_footprint      = ((Footprint::     getCount($database) == 0) ? $bad : $good);
        $missing_supplier       = ((Supplier::      getCount($database) == 0) ? $bad : $good);

        $display_warning        = (($missing_category == $bad) || ($missing_storelocation == $bad)
            || ($missing_footprint == $bad) || ($missing_supplier == $bad));

        $html->setVariable('missing_category', $missing_category);
        $html->setVariable('missing_storeloc', $missing_storelocation);
        $html->setVariable('missing_footprint', $missing_footprint);
        $html->setVariable('missing_supplier', $missing_supplier);
        $html->setVariable('display_warning', $display_warning, 'boolean');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    }
}

/********************************************************************************
 *
 *   Show a warning if there are footprints with broken filenames
 *
 *********************************************************************************/

if ((! $fatal_error) && (! $database->isUpdateRequired())) {
    try {
        if (count(Footprint::getBrokenFilenameFootprints($database, $current_user, $log)) > 0) {
            $html->setVariable('broken_filename_footprints', true);
        } else {
            $html->setVariable('broken_filename_footprints', false);
        }
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    }
}

/********************************************************************************
 *
 *   Show Update List (RSS Feed from Google Code) if enabled
 *
 *********************************************************************************/

if ((! $fatal_error) && (! $config['startup']['disable_update_list'])) {
    $feed_link = 'https://github.com/do9jhb/Part-DB/releases.atom';
    $item_count = 4;

    try {
        $rss_loop = array();
        $feed_content = '';

        try {
            $feed_content = curlGetData($feed_link);
        } catch (Exception $e) {
            $feed_content = file_get_contents($feed_link);
        }

        if (strlen($feed_content) == 0) {
            throw new Exception(_('Der Atom-Feed konnte nicht aus dem Internet heruntergeladen werden. '.
                'Prüfen Sie bitte, ob Ihre PHP-Konfiguration das Herunterladen aus dem Internet zulässt.'));
        }

        if (! class_exists('SimpleXMLElement')) {
            throw new Exception(_('Die Klasse "SimpleXMLElement" ist nicht vorhanden!'));
        }

        $xml = simplexml_load_string($feed_content);

        if (! is_object($xml)) {
            throw new Exception(_('Das SimpleXMLElement konnte nicht erzeugt werden!'));
        }

        //$rss_loop[] = array('title' => _('Part-DB Releases Atom-Feed'), 'datetime' => $xml->updated, 'link' => $feed_link);

        $item_index = 1;
        foreach ($xml->entry as $entry) {
            if ($item_index >= $item_count) {
                break;
            }

            $link = _('FEHLER - Kein Link im Atom-Feed gefunden!');
            foreach ($entry->link as $link_entry) {
                $attributes = $link_entry->attributes();
                if (isset($attributes['rel']) && ($attributes['rel'] == 'alternate') && isset($attributes['href'])) {
                    $link = 'https://github.com'.$attributes['href'];
                }
            }

            $rss_loop[] = array('title' => $entry->title, 'datetime' => $entry->updated, 'link' => $link);
            $item_index++;
        }
    } catch (Exception $e) {
        $rss_loop = array(array('title' => $e->getMessage()));
    }

    $html->setLoop('rss_feed_loop', $rss_loop);
}

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/

$html->setLoop('authors', $authors);

if (! $fatal_error) {
    $bbcode = new \Golonka\BBCode\BBCodeParser();
    $str = $bbcode->parse(htmlspecialchars($config['startup']['custom_banner']));
    $html->setVariable('banner', $str, 'string');

    try {
        $system_version = $system->getInstalledVersion();
        $html->setVariable('system_version', $system_version->asString(false, true, true, false), 'string');
        $html->setVariable('system_version_full', $system_version->asString(false, false, false, true), 'string');
        $html->setVariable('git_branch', getGitBranchName(), 'string');
        $html->setVariable('git_commit', getGitCommitHash(10), 'string');
        $html->setVariable('partdb_title', $config['partdb_title'], 'string');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    }
}

/********************************************************************************
 *
 *   Generate HTML Output
 *
 *********************************************************************************/


//If a ajax version is requested, say this the template engine.
if (isset($_REQUEST["ajax"])) {
    $html->setVariable("ajax_request", true);
}

$reload_link = $fatal_error ? 'startup.php' : '';   // an empty string means that the...
$html->printHeader($messages, $reload_link);       // ...reload-button won't be visible

if (! $fatal_error) {
    $html->printTemplate('startup');
}

$html->printFooter();
