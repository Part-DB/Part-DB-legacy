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

    $Id: update.php 511 2012-08-12 weinbauer73@gmail.com $

    Edits:

    2012-08-29 weinbauer73@gmail.com
    - move class update to namespace system/interpreter
*/

require_once ('lib.php');
require_once ('class/interpreter.php');

$update =& new system\interpreter;
// $update -> set_hash('sha256'); -> default!

// downloading a list of updates from url and get some files
$update -> download_list( 'ftp://' );
$update -> download_file( 'part-db_up515.zip' );

// parse an update script
$update -> check_script(515);
$update -> load_script('update/update.ups');
$update -> parse_script();
if ( ! $update -> get_debug() ) $update -> show_report();
?>