<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

    printLib extension by F. Thiessen
    Version 0.2 (04.10.2014)
    http://fthiessen.de/

    Edited by J. Boehmer
    Version 0.3 (24.02.2016)
    http://jbtronics.wordpress.com

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
use PartDB\Label\PartLabel;
use PartDB\Log;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

$part_id            = isset($_REQUEST['pid'])               ? (integer)$_REQUEST['pid']             : 0;
$preset             = isset($_REQUEST['preset'])            ? (integer)$_REQUEST['preset']          : 0;

$action = 'default';
if (isset($_REQUEST["download"])) {
    $action = 'download';
}


$size_str = "50x30";
if (isset($_REQUEST['size'])) {
    $size_str  = $_REQUEST['size'];
}

$lang_str = "de";
//if(isset($_REQUEST['lang']))                  {$lang_str     = $_LANG['lang'];    }


/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/



try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = new User($database, $current_user, $log, 1); // admin
    //$part               = new Part($database, $current_user, $log, $part_id);
    //$footprint          = $part->get_footprint();
    //$storelocation      = $part->get_storelocation();
    //$manufacturer       = $part->get_manufacturer();
    //$category           = $part->get_category();
    //$all_orderdetails   = $part->get_orderdetails();

    $label = new PartLabel($database, $current_user, $log, $part_id);

    $label->setLines($label->getLinePresets()[$preset]);


    if ($action=="download") {
        $label->download();
    } else {
        $label->generate();
    }
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}
