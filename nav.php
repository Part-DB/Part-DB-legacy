<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

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

    $Id: nav.php 510 2012-08-03 weinbauer73@gmail.com $

*/

require_once ('lib.php');

$html = new HTML;
$html -> set_html_meta ( array('title'=>$title, 'menu'=>true) );
$html -> print_html_header();

$html -> load_html_template('menu');
$html -> set_html_variable('enable_devices', ! $disable_devices);
$html -> set_html_variable('enable_help', ! $disable_help);
$html -> set_html_variable('enable_config', ! $disable_config);

ob_start();
categories_build_navtree();
$javascript = ob_get_contents();
ob_end_clean();
$html -> set_html_variable('categories_build_navtree', $javascript);
ob_start();
devices_build_navtree();
$javascript = ob_get_contents();
ob_end_clean();

$html -> set_html_variable('devices_build_navtree', $javascript);
$html -> print_html_template();
$html -> print_html_footer();

?>
