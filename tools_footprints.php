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

use PartDB\Database;
use PartDB\HTML;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

$action = 'default';
if (isset($_REQUEST['show_all'])) {
    $action = 'show_all';
}
if (isset($_REQUEST['show_active'])) {
    $action = 'show_active';
}
if (isset($_REQUEST['show_passive'])) {
    $action = 'show_passive';
}
if (isset($_REQUEST['show_electromechanic'])) {
    $action = 'show_electromechanic';
}
if (isset($_REQUEST['show_others'])) {
    $action = 'show_others';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Footprint-Bilder'));

try {
    $database = new Database();
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Execute actions
 *
 *********************************************************************************/

if (! $fatal_error) {
    switch ($action) {
        case 'show_all':
            $directories = array();
            $directories[] = BASE.'/img/footprints/';
            break;

        case 'show_active':
            $directories = array();
            $directories[] = BASE.'/img/footprints/Aktiv/';
            break;

        case 'show_passive':
            $directories = array();
            $directories[] = BASE.'/img/footprints/Passiv/';
            break;

        case 'show_electromechanic':
            $directories = array();
            $directories[] = BASE.'/img/footprints/Elektromechanik/';
            break;

        case 'show_others':
            $directories = findAllDirectories(BASE.'/img/footprints/');
            if (array_search(BASE.'/img/footprints/Aktiv', $directories) !== false) {
                unset($directories[array_search(BASE.'/img/footprints/Aktiv', $directories)]);
            }
            if (array_search(BASE.'/img/footprints/Passiv', $directories) !== false) {
                unset($directories[array_search(BASE.'/img/footprints/Passiv', $directories)]);
            }
            if (array_search(BASE.'/img/footprints/Elektromechanik', $directories) !== false) {
                unset($directories[array_search(BASE.'/img/footprints/Elektromechanik', $directories)]);
            }
            foreach ($directories as $key => $value) {
                $directories[$key] = $value.'/';
            }
            break;

        default:
            if ($config['tools']['footprints']['autoload']) {
                $directories = array(BASE.'/img/footprints/');
            } else {
                $directories = array();
            }
            break;
    }
}

//Give action to Template, so we can mark the active button
$html->setVariable("action", $action, "string");

/********************************************************************************
 *
 *   Get Footprints and set all HTML variables
 *
 *********************************************************************************/

if (count($directories) > 0) {
    $categories_loop = array();
    $categories = array();
    foreach ($directories as $directory) {
        $categories[] = rtrim($directory, "\\/");
        $categories = array_merge($categories, findAllDirectories($directory, true));
    }
    sort($categories);
    foreach ($categories as $category) {
        $pictures_loop = array();
        $pictures = findAllFiles($category.'/', false, '.png');
        foreach ($pictures as $filename) {
            $pictures_loop[] = array(   'title' => str_replace('.png', '', basename($filename)),
                'filename' => str_replace(BASE, BASE_RELATIVE, $filename));
        }

        if (count($pictures_loop) > 0) {
            $categories_loop[] = array( 'category_name' => str_replace(BASE, '', $category),
                'pictures_loop' => $pictures_loop);
        }
    }

    $html->setLoop('categories_loop', $categories_loop);
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

$html->printHeader($messages);

if (! $fatal_error) {
    $html->printTemplate('footprints');
}

$html->printFooter();
