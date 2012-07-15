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

    $Id: nav.php 433 2012-05-09 22:13:52Z bubbles.red@gmail.com $
*/

require_once ('lib.php');

$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
$tmpl -> setVar('head_title', $title);
$tmpl -> setVar('head_charset', $http_charset);
$tmpl -> setVar('head_css', $css);
$tmpl -> setVar('head_menu', true);
$tmpl -> pparse();

$tmpl = new vlibTemplate(BASE."/templates/$theme/nav.php/vlib_menu.tmpl");
$tmpl -> setVar('enable_devices', ! $disable_devices);
$tmpl -> setVar('enable_help', ! $disable_help);
$tmpl -> setVar('enable_config', ! $disable_config);
ob_start();
categories_build_navtree();
$javascript = ob_get_contents();
ob_end_clean();
$tmpl -> setVar('categories_build_navtree', $javascript);
ob_start();
devices_build_navtree();
$javascript = ob_get_contents();
ob_end_clean();
$tmpl -> setVar('devices_build_navtree', $javascript);
$tmpl -> pparse();

$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
$tmpl -> pparse();
?>
