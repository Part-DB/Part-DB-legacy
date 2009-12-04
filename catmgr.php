<?PHP
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

	$Id: catmgr.php,v 1.5 2006/05/08 19:03:15 cl Exp $

	ChangeLog

	04/03/2006
		Added some comments and some escape/unescape stuff. Tested!
	
	02/05/2006
		Some people told me it would be useful to add functionality
		for deleting and renaming categories.
		Security stuff is there too, so you cannot delete categories
		which aren't empty.
*/
	include('lib.php');
	partdb_init();
	
	/*
	 * In some cases a confirmation question has to be displayed.
	 */
	$special_dialog = 0;
	

	if (isset($_REQUEST["btn_add"]))
	{
		$query = "INSERT INTO categories (name,parentnode) VALUES (". smart_escape($_REQUEST["a_cn"]) .",". smart_escape($_REQUEST["a_pc"]) .");";
		debug_print($query);
		mysql_query ($query);
	}
	else if (isset($_REQUEST["btn_del"]))
	{
		/*
		 * Delete a category.
		 * Includes confirmation questions. Don't delete the
		 * category when there are parts in it.
		 */
		if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
		{
			$query = "SELECT COUNT(*) FROM parts WHERE id_category=". smart_escape($_REQUEST["catsel"]) .";";
			debug_print($query);
			$r = mysql_query($query);
			$d = mysql_fetch_row($r); // COUNT(*) queries always give a result!
			if ($d[0] != 0)
			{
				$special_dialog = 1;
				print "<html><body><div style=\"text-align:center;\"><div style=\"color:red;font-size:x-large;\">Kategorie kann nicht gel&ouml;scht werden!</div>Es gibt noch Teile, die in dieser Kategorie eingetragen sind.<form method=\"get\" action=\"catmgr.php\"><input type=\"submit\" value=\"OK!\"></form></div></body></html>";
			}
			else
			{
				$special_dialog = 1;
				print "<html><body><div style=\"text-align:center;\"><div style=\"color:red;font-size:x-large;\">M&ouml;chten Sie die Kategorie &quot;". lookup_category_name($_REQUEST["catsel"]) ."&quot; wirklich l&ouml;schen?</div>Der L&ouml;schvorgang ist irreversibel!<form action=\"catmgr.php\" method=\"post\"><input type=\"hidden\" name=\"catsel\" value=\"". $_REQUEST["catsel"] ."\"><input type=\"hidden\" name=\"btn_del\" value=\"x\"><input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen!\"><input type=\"submit\" name=\"del_ok\" value=\"L&ouml;schen!\"></form></div></body></html>";
			}
		}
		else if (isset($_REQUEST["del_ok"]))
		{
			// the user said it's OK to delete the category
			$query = "DELETE FROM categories WHERE id=". smart_escape($_REQUEST["catsel"]) ." LIMIT 1;";
			debug_print ($query);
			mysql_query ($query);
		}
	}
	else if (isset($_REQUEST["btn_rn"]))
	{
		/* rename */
		$query = "UPDATE categories SET name=". smart_escape($_REQUEST["nn"]) ." WHERE id=". smart_escape($_REQUEST["catsel"]) ." LIMIT 1";
		debug_print($query);
		mysql_query($query);
	}
	
	/*
	 * The buildtree function creates a tree for <select> tags.
	 * It recurses trough all categories (and subcategories) and
	 * creates the tree. Deeper levels have more spaces in front.
	 * As the top-most category (it doesn't exist!) has the ID 0,
	 * you have to supply cid=0 at the very beginning.
	 */
	function buildtree ($cid, $level)
	{
		$query = "SELECT id,name FROM categories WHERE parentnode=". smart_escape($cid) .";";
		$r = mysql_query ($query);
		while ( $d = mysql_fetch_row ($r) )
		{
			print "<option value=\"". smart_unescape($d[0]) . "\">";
			for ($i = 0; $i < $level; $i++) print "&nbsp;&nbsp;&nbsp;";
			print smart_unescape($d[1]) ."</option>";

			// do the same for the next level.
			buildtree ($d[0], $level + 1);
		}
	}

	if ($special_dialog == 0)
	{
?>
<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdtop">
		Kategorie Anlegen
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<form method="post">
			<table>
			<tr>
			<td>&Uuml;bergeordnete Kategorie ausw&auml;hlen:</td>
			<td>
			<select name="a_pc">
			<option value="0">root node</option>
			<?PHP buildtree(0, 1); ?>
			</select>
			</td>
			</tr><tr>
			<td>Name der neuen Kategorie</td>
			<td><input type="text" name="a_cn"></td>
			</tr><tr>
			<td colspan="2"><center><input type="submit" name="btn_add" value="Anlegen!"></center></td>
			</tr>
			</table>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

  <table class="table">
	<tr>
		<td class="tdtop">
		Kategorie Bearbeiten/L&ouml;schen
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			</form>
			<form method="post">
			<table border="0">
			<tr>
			<td valign="top">
			W&auml;hlen Sie die zu bearbeitende Kategorie:<br>
			<select name="catsel" size="10">
			<?PHP buildtree(0, 1); ?>
			</select>
			</td><td valign="top">
			Was soll mit der ausgew&auml;hlten Kategorie geschehen???
			<table border="0">
			<tr>
			<td>
			<input type="submit" name="btn_del" value="L&ouml;schen">
			</td>
			</tr><tr><td>&nbsp;</td></tr><tr>
			<td>
			Neuer Name:<br>
			<input type="edit" name="nn">
			<input type="submit" name="btn_rn" value="Umbenennen!">
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</form>
		</td>
	</tr>
  </table>

  </head>
 </body>
</html>

<?PHP
	}
?>
