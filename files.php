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

    $Id: files.php 518 2012-08-12 weinbauer73@gmail.com $
*/

require_once ('lib.php');
require_once ('class/interpreter.php');

$files =& new system\interpreter;

// parse files.ups script
$files -> check_script(-1);
$files -> clear_debug();
$files -> set_silent();
$files -> load_script('update/files.ups');
$files -> parse_script();
//if ( ! $files -> get_debug() ) $files -> show_report();
?>