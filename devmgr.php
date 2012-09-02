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

    $Id: devmgr.php 511 2012-08-05 weinbauer73@gmail.com $
*/

    require_once ('lib.php');

    $refreshnav = false;

    /*
     * In some cases a confirmation question has to be displayed.
     */
    $special_dialog = false;

    $action = 'default';
    if ( isset( $_REQUEST["add"]))        { $action = 'add';}
    if ( isset( $_REQUEST["delete"]))     { $action = 'delete';}
    if ( isset( $_REQUEST["rename"]))     { $action = 'rename';}
    if ( isset( $_REQUEST["new_parent"])) { $action = 'new_parent';}

    $dev_sel    = isset( $_REQUEST["dev_sel"])    ? $_REQUEST["dev_sel"]    : -1;
    $parentnode = isset( $_REQUEST["parentnode"]) ? $_REQUEST["parentnode"] : 0;

    if ( $action == 'add')
    {
        $dev_sel    = device_add( $_REQUEST["new_device"], $parentnode);
        $refreshnav = true;
    }


    if ( $action == 'delete')
    {
        /*
         * Delete a device.
         */
        if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) && $dev_sel >= 0)
        {

            $special_dialog = true;

            $html = new HTML;
            $html -> set_html_meta ( array('title'=>$title) );
            $html -> print_html_header();

            $array = array(
                'lookup_device_name'    =>  lookup_device_name($dev_sel),
                'size'          =>  $size,
                'name'          =>  $name,
                'dev_sel'       =>  $dev_sel
            );
            $html -> parse_html_template( 'devmgr', $array );

            $html -> print_html_footer();

        }
        else if (isset($_REQUEST["del_ok"]))
        {
            // the user said it's OK to delete the device
            device_delete( $dev_sel);
            $refreshnav = true;
        }
    }


    if ( $action == 'rename')
    {
        /* rename */
        device_rename( $dev_sel, $_REQUEST["new_name"]);
        $refreshnav = true;
    }


    if ( $action == 'new_parent')
    {
        /* resort */
        device_new_parent( $dev_sel, $_REQUEST["parentnode"]);
        $refreshnav = true;
    }

    $data       = device_select( $dev_sel);
    $name       = $data['name'];
    $parentnode = $data['parentnode'];

    $size       = min( devices_count(), 30);


    if ($special_dialog == false)
    {

        $html = new HTML;
        $html -> set_html_meta ( array('title'=>$title) );
        $html -> print_html_header();

        ob_start();
        device_buildtree( 0, 0, $parentnode);
        $devices1 = ob_get_contents();
        ob_end_clean();

        ob_start();
        device_buildtree( 0, 0, $dev_sel);
        $devices2 = ob_get_contents();
        ob_end_clean();

        $array = array(
            'device_buildtree_1'    =>  $devices1,
            'device_buildtree_2'    =>  $devices2,
            'refreshnav'        =>  $refreshnav
        );
        $html -> parse_html_template( 'devmgr', $array );

        $html -> print_html_footer();
    }

    $refreshnav = false;
?>
