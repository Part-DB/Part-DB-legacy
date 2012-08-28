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

    $Id: supmgr.php 511 2012-08-05 weinbauer73@gmail.com $

    Edits:

    20120828 weinbauer73@gmail.com
	- suppliers_build_list() returns an array
	- javascript selector now use "input.value = object[this.selectedIndex].text" instead of submit form
*/

require_once ('lib.php');

$action = 'default';
if ( isset( $_REQUEST["add"]))    { $action = 'add'; }
if ( isset( $_REQUEST["delete"])) { $action = 'delete'; }
if ( isset( $_REQUEST["rename"])) { $action = 'rename'; }

$supplier_sel = isset( $_REQUEST["supplier_sel"]) ? $_REQUEST["supplier_sel"] : -1;

if ( $action == 'add')
{
	supplier_add( $_REQUEST['new_supplier'] );
}

if ( $action == 'delete')
{
	supplier_delete( $supplier_sel );
}

if ( $action == 'rename')
{
	supplier_rename( $supplier_sel, $_REQUEST["new_name"] );
}

$data = supplier_select( $supplier_sel );

$html = new HTML;
$html -> set_html_meta ( array('title'=>'Lieferanten','menu'=>true) );
$html -> print_html_header();

$array = array(
	'size'=>min(suppliers_count(), 30),
	'suppliers_build_list'=>suppliers_build_list( $supplier_sel ),
	'name'=>$data['name']
);

$html -> parse_html_template( 'supmgr', $array );
$html -> print_html_footer();

?>
