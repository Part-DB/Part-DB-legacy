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

use PartDB\Attachement;
use PartDB\Category;
use PartDB\Database;
use PartDB\Device;
use PartDB\Footprint;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Manufacturer;
use PartDB\Part;
use PartDB\Storelocation;
use PartDB\Supplier;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Statistik'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = new User($database, $current_user, $log, 1); // admin
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Set all HTML variables
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $noprice_parts              = Part::getNoPriceParts($database, $current_user, $log);
        $count_of_parts_with_price  = Part::getCount($database) - count($noprice_parts); // :-)

        $html->setVariable('parts_count_with_prices', $count_of_parts_with_price, 'integer');
        $html->setVariable('parts_count_sum_value', Part::getSumPriceInstock($database, $current_user, $log, true), 'string');

        $html->setVariable('parts_count', Part::getCount($database), 'integer');
        $html->setVariable('parts_count_sum_instock', Part::getSumCountInstock($database), 'integer');

        $html->setVariable('categories_count', Category::getCount($database), 'integer');
        $html->setVariable('footprint_count', Footprint::getCount($database), 'integer');
        $html->setVariable('location_count', Storelocation::getCount($database), 'integer');
        $html->setVariable('suppliers_count', Supplier::getCount($database), 'integer');
        $html->setVariable('manufacturers_count', Manufacturer::getCount($database), 'integer');
        $html->setVariable('devices_count', Device::getCount($database), 'integer');
        $html->setVariable('attachements_count', Attachement::getCount($database), 'integer');

        $html->setVariable('footprint_picture_count', count(findAllFiles(BASE.'/img/footprints/', true)), 'integer');
        $html->setVariable('iclogos_picture_count', count(findAllFiles(BASE.'/img/iclogos/', true)), 'integer');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
        $fatal_error = true;
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

$html->printHeader($messages);

if (! $fatal_error) {
    $html->printTemplate('statistics');
}

$html->printFooter();
