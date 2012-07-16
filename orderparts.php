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

    $Id: orderparts.php 475 2012-07-02 16:09:22Z kami89@gmx.ch $

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

	/** edit: 20120716 Udo Neist **/

	$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
	$tmpl -> setVar('head_title', 'Neues Teil');
	$tmpl -> setVar('head_charset', $http_charset);
	$tmpl -> setVar('head_css', $css);
	$tmpl -> setVar('head_menu', true);
	$tmpl -> setVar('head_popup', true);
	$tmpl -> pparse();

	$tmpl = new vlibTemplate(BASE."/templates/$theme/orderparts.php/vlib_orderparts.tmpl");
	$tmpl -> setVar('selected', ((isset($_REQUEST["sup_id"]))?false:true));
	$tmpl -> setLoop('suppliers_build_list',suppliers_build_list($sup_id));
	$tmpl -> setVar('category_get_name', category_get_name($cid));
	$tmpl -> setVar('currency', $currency);
	$orders_sum = parts_order_sum($sup_id);
	$tmpl -> setVar('order_value', (($orders_sum>0)?$orders_sum:' - '));

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
	$tmpl -> setLoop('loop1',$loop);
	$tmpl -> setLoop('loop2',PrintsFormats("format"));

	$tmpl -> setVar('deviceid', $deviceid);
	$tmpl -> setVar('spacer', ((strcmp ($action, "createbom"))?";":$_REQUEST["spacer"]));
	if ( strcmp($action, "createbom") == 0 )
	{
		$tmpl -> setVar('createbom', true);

		$result = parts_select_order($sup_id);
		$tmpl -> setVar('nrows', mysql_num_rows($result) + 6);
		$tmpl -> setVar('GenerateBOMHeadline', GenerateBOMHeadline( $_REQUEST["format"], $_REQUEST["spacer"]));
		$loop=array();
		while ( $data = mysql_fetch_assoc( $result))
		{
			//function GenerateBOMResult($Format,$Spacer,$PartName,$SupNr,$SupName,$Quantity,$Instock,$Price)
			$loop[]['GenerateBOMResult']=GenerateBOMResult(
                                            $_REQUEST["format"],     //$Format
                                            $_REQUEST["spacer"],     //$Spacer
                                            $data['name'],           //$PartName
                                            $data['supplierpartnr'], //$SupNr
                                            $data['supplier'],       //$SupName
                                            $data['diff'],           //$Quantity
                                            $data['instock'],        //$Instock
                                            $data['price']);         //$Price
		}
		$tmpl -> setLoop('loop3',$loop);
	}
	$tmpl -> pparse();

	$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
	$tmpl -> pparse();

	/** end: 20120716 Udo Neist **/

?>
