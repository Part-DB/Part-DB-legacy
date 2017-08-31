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
        2012-09-13  kami89              - changed to OOP
*/

include_once('start_session.php');

use PartDB\HTML;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content


$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('3D Footprints'));

$dirs = findAllFiles(BASE.'/models/', true);

$html->setLoop("directories", $dir);

$html->printHeader($messages);

if (! $fatal_error) {
    $html->printTemplate('footprints3d');
}

$html->printFooter();
