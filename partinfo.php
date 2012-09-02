<?PHP
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
    MERCHANTABIL7ITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA

    $Id: partinfo.php 511 2012-08-05 weinbauer73@gmail.com $
*/

require_once ('lib.php');

/*
* 'action' is a hidden field in the form.
* The 'instock' value has to be changed before the output begins.
*/

// set action to default, if not exists
$action = ( isset( $_REQUEST["action"]) ? $_REQUEST["action"] : 'default');

if ( $action == "dec")
{
    parts_stock_decrease( $_REQUEST["pid"], $_REQUEST["n_less"]);
}

if ( $action == "inc")
{
    parts_stock_increase( $_REQUEST["pid"], $_REQUEST["n_more"]);
}

$html = new HTML;
$html -> set_html_meta ( array('title'=>'Neues Teil','popup'=>true) );
$html -> print_html_header();

$data = mysql_fetch_assoc( parts_select( $_REQUEST["pid"]) );
$loop = array (
    'name'          =>  smart_unescape( $data['name']),
    'description'       =>  smart_unescape( $data['description']),
    'id_category'       =>  part_get_category_path( $data['id_category']),
    'instock'       =>  smart_unescape( $data['instock']),
    'mininstock'        =>  smart_unescape( $data['mininstock']),
    'id_footprint'      =>  part_get_footprint_path( $data['id_footprint']),
    'link'          =>  ((file_exists(smart_unescape($data['footprint_filename'])))?smart_unescape($data['footprint_filename']):''),
    'part_get_location_path'=>  part_get_location_path( $data['id_storeloc']),
    'location_is_full'  =>  (($data['location_is_full'] == 1 )?' [voll]':''),
    'supplier'      =>  smart_unescape( $data['supplier']),
    'supplierpartnr'    =>  smart_unescape( $data['supplierpartnr']),
    'obsolete'      =>  (($data['obsolete'])?'ja':'nein'),
    'price'         =>  smart_unescape(str_replace('.', ',', $data['price'])),
    'currency'      =>  smart_unescape($currency),
    'comment'       =>  nl2br( smart_unescape( $data['comment']))
);

if ( picture_exists( $_REQUEST["pid"]) )
{
    $picture=array();
    while ($data = mysql_fetch_assoc( pictures_select( $_REQUEST["pid"]) )) $picture[] = array('id'=>$data['id']);
}

$array = array(
    'part_get_name'     =>  part_get_name( $_REQUEST["pid"] ),
    'suppliers_build_list'  =>  $list,
    'name'          =>  $data['name'],
    'loop'          =>  $loop,
    'pid'           =>  $_REQUEST["pid"],
    'picture'       =>  $picture,
);

$html -> parse_html_template( 'partinfo', $array );
$html -> print_html_footer();

?>
