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

use PartDB\HTML;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Check if all installation steps are done. If not, go to the installer.
 *
 *********************************************************************************/

if ((! is_array($config['installation_complete']))
    || in_array(false, $config['installation_complete'], true)) { // is at least one array item 'false'?...
    header('Location: install.php'); // ...then go to the installation page
    exit;
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], $config['page_title']);
$html->setVariable('title', $config['page_title'], 'string');

/********************************************************************************
 *
 *   Check if client is a mobile device
 *
 *********************************************************************************/

/*$mobile = false;
if (isset($_SERVER["HTTP_USER_AGENT"]))
{
    $agents = array(
        'Windows CE', 'Pocket', 'Mobile',
        'Portable', 'Smartphone', 'SDA',
        'PDA', 'Handheld', 'Symbian',
        'WAP', 'Palm', 'Avantgo',
        'cHTML', 'BlackBerry', 'Opera Mini',
        'Nokia', 'PSP', 'J2ME'
    );

    foreach ($agents as $agent)
    {
        if (strpos($_SERVER["HTTP_USER_AGENT"], $agent))
        {
            $mobile = true;
            break;
        }
    }
}

$html->set_variable('mobile', $mobile, 'boolean');*/

/********************************************************************************
 *
 *   Generate HTML Output
 *
 *********************************************************************************/

if (count($messages) == 0) {
    $html->setMeta(array('frameset' => true));
}

$html->printHeader($messages, 'index.php', '', true);

if ((! $fatal_error) && (count($messages) == 0)) {
    $html->printTemplate('frameset');
}

$html->printFooter();
