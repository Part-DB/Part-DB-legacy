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

    $Id: orderparts.php 511 2012-08-05 weinbauer73@gmail.com $
*/

require_once ('lib.php');

// set action to default, if not exists
$action       = ( isset( $_REQUEST['action'])    ? $_REQUEST['action']   : 'default');
$cid          = ( isset( $_REQUEST['cid'])       ? $_REQUEST['cid']      : '');
$sup_id       = ( isset( $_REQUEST['sup_id'])    ? $_REQUEST['sup_id']   : 0);
$deviceid     = ( isset( $_REQUEST['deviceid)']) ? $_REQUEST['deviceid'] : '');

if ( strcmp( $action, "an") == 0) //add number of parts
{
    parts_stock_increase( $_REQUEST["pid"], $_REQUEST["toadd"]);
}

$html = new HTML;
$html -> set_html_meta ( array('title'=>'Neues Teil','menu'=>true,'popup'=>true) );
$html -> print_html_header();

$result = parts_select_order($sup_id);
$rowcount = 0;
$loop = array();
while ($data = mysql_fetch_assoc($result))
{
    $rowcount++;
    $loop[]['even_odd'] = is_odd($rowcount);
    $loop[]['tdrow0'] = print_table_image( $data['id'], $data['name'], $data['footprint_filename']);
    $loop[]['id'] = smart_unescape($data['id']);
    $loop[]['name'] = smart_unescape($data['name']);
    $loop[]['footprint'] = smart_unescape($data['footprint']);
    $loop[]['diff'] = smart_unescape($data['diff']);
    $loop[]['supplier'] = smart_unescape($data['supplier']);
    $loop[]['supplierpartnr'] = smart_unescape($data['supplierpartnr']);
    $loop[]['loc'] = smart_unescape($data['loc']);
}

$orders_sum = parts_order_sum($sup_id);

$array = array (
    'selected' => ((isset($_REQUEST["sup_id"]))?false:true),
    'suppliers_build_list' => suppliers_build_list($sup_id),
    'category_get_name' => category_get_name($cid),
    'currency' => $currency,
    'order_value' => (($orders_sum>0)?$orders_sum:' - '),
    'loop1' => $loop,
    'loop2' => PrintsFormats("format"),
    'deviceid' => $deviceid,
    'spacer' => ((strcmp ($action, "createbom"))?";":$_REQUEST["spacer"])
);

if ( strcmp($action, "createbom") == 0 )
{
    $result = parts_select_order($sup_id);
    $loop=array();
    while ( $data = mysql_fetch_assoc( $result))
    {
        $loop[]['GenerateBOMResult']=GenerateBOMResult(
                    $_REQUEST["format"],     //$Format
                    $_REQUEST["spacer"],     //$Spacer
                    $data['name'],           //$PartName
                    $data['supplierpartnr'], //$SupNr
                    $data['supplier'],       //$SupName
                    $data['diff'],           //$Quantity
                    $data['instock'],        //$Instock
                    $data['price']           //$Price
                    );
    }
    $array = array_merge($array,array(
        'createbom'     =>  true,
        'nrows'         =>  mysql_num_rows($result) + 6,
        'GenerateBOMHeadline'   =>  GenerateBOMHeadline( $_REQUEST["format"], $_REQUEST["spacer"] ),
        'loop3'         =>  $loop
        )
    );
}

$html -> parse_html_template( 'orderparts', $array );

$html -> print_html_footer();

?>
