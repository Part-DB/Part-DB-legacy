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

    $Id: supmgr.php 392 2012-03-03 06:30:03Z bubbles.red@gmail.com $

*/

    require_once ('lib.php');

    $action = 'default';
    if ( isset( $_REQUEST["add"]))    { $action = 'add';}
    if ( isset( $_REQUEST["delete"])) { $action = 'delete';}
    if ( isset( $_REQUEST["rename"])) { $action = 'rename';}

    $supplier_sel = isset( $_REQUEST["supplier_sel"]) ? $_REQUEST["supplier_sel"] : -1;

    if ( $action == 'add')
    {
        supplier_add( $_REQUEST['new_supplier']);
    }

    if ( $action == 'delete')
    {
        supplier_delete( $supplier_sel);
    }

    if ( $action == 'rename')
    {
        supplier_rename( $supplier_sel, $_REQUEST["new_name"]);
    }

    $data = supplier_select( $supplier_sel);

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
    $tmpl -> setVar('head_title', 'Lieferanten');
    $tmpl -> setVar('head_charset', $http_charset);
    $tmpl -> setVar('head_theme', $theme);
    $tmpl -> setVar('head_css', $css);
    $tmpl -> pparse();

    $tmpl = new vlibTemplate(BASE."/templates/$theme/supmgr.php/vlib_supmgr.tmpl");
    $tmpl -> setVar('size', min(suppliers_count(), 30));
    ob_start();
    suppliers_build_list($supplier_sel);
    $list = ob_get_contents();
    ob_end_clean();
    $tmpl -> setVar('suppliers_build_list', $list);
    $tmpl -> setVar('name', $data['name']);
    $tmpl -> pparse();

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
    $tmpl -> pparse();
?>
