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

    $Id: catmgr.php 392 2012-03-03 06:30:03Z bubbles.red@gmail.com $

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

$catsel      = isset( $_REQUEST["catsel"]) ? $_REQUEST["catsel"] : -1;
$parentnode  = isset( $_REQUEST["parentnode"])  ? $_REQUEST["parentnode"] : 0;


if ( $action == 'add')
{
	category_add( $_REQUEST["new_category"], $parentnode);
	$refreshnav = true;
}


if ( $action == 'delete')
{
	/*
	* Delete a category.
	* Includes confirmation questions. Don't delete the
	* category when there are parts in it.
	*/
	if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) && $catsel >= 0)
	{
		$special_dialog = true;
		if ( parts_count_on_category( $catsel) != 0)
		{
			$tmpl = new vlibTemplate(BASE."/templates/catmgr.php/vlib_cat_delete.tmpl");
			$tmpl -> setVar('question_delete',true);
			$tmpl -> pparse();
		}
		else
		{
			$tmpl = new vlibTemplate(BASE."/templates/catmgr.php/vlib_cat_delete.tmpl");
			$tmpl -> setVar('nothing_delete',true);
			$tmpl -> setVar('category_name',category_get_name($catsel));
			$tmpl -> setVar('category_sel',$catsel);
			$tmpl -> pparse();
		}
	}
	else if (isset($_REQUEST["del_ok"]))
	{
	// the user said it's OK to delete the category
	category_del( $catsel);
			$refreshnav = true;
	}
}

if ( $action == 'rename')
{
	/* rename */
	category_rename( $catsel, $_REQUEST["new_name"]);
	$refreshnav = true;
}

if ( $action == 'new_parent')
{
	/* resort */
	category_new_parent( $catsel, $parentnode);
	$refreshnav = true;
}

$data       = category_select( $catsel);
$name       = $data['name'];
$parentnode = $data['parentnode'];

$size       = min( categories_count(), 30);

if ($special_dialog == false)
{

	/** edit: 20120715 Udo Neist **/

	$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
	$tmpl -> setVar('head_title', 'Kategorien');
	$tmpl -> setVar('head_charset', $http_charset);
	$tmpl -> setVar('head_theme', $theme);
	$tmpl -> setVar('head_css', $css);
	$tmpl -> setVar('head_menu', true);
	$tmpl -> pparse();

	$tmpl = new vlibTemplate(BASE."/templates/$theme/catmgr.php/vlib_cat_edit.tmpl");
	ob_start();
	categories_build_tree( 0, 0, $parentnode);
	$categories = ob_get_contents();
	ob_end_clean();
	$tmpl -> setVar('categories_build_tree_1', $categories);
	ob_start();
	categories_build_tree( 0, 0, $catsel);
	$categories = ob_get_contents();
	ob_end_clean();
	$tmpl -> setVar('categories_build_tree_2', $categories);
	$tmpl -> setVar('categories_refreshnav',$refreshnav);
	$tmpl -> setVar('categories_build_size',$size);
	$tmpl -> setVar('categories_build_name',$name);
	$tmpl -> pparse();

	$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
	$tmpl -> pparse();

	/** end: 20120715 Udo Neist **/

}
?>
