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

	$Id: locmgr.php,v 1.7 2006/04/12 12:27:29 cl Exp $

	28/02/06
		Added escape/unescape functions where required

	06/04/06
		Now it is possible to edit the name of the location.
		Some sanity checks are new, too! The base line is to
		avoid database corruption, therefore we refuse to
		delete locs with parts in them.
*/
	include('lib.php');
	partdb_init();

	/*
	 * In some cases a confirmation question has to be displayed.
	 */
	$special_dialog = 0;

	/*
	 * this is the dispatcher ...
	 */
	if (isset($_REQUEST["btn_add"]))
	{
		// add a location
		$query = "INSERT INTO storeloc (name) VALUES (". smart_escape($_REQUEST["locname"]) .");";
		debug_print ($query);
		mysql_query ($query);
	}
	else if (isset($_REQUEST["btn_del"]))
	{
		/*
		 * Delete a location.
		 * Includes confirmation questions. Don't delete the
		 * location when there are parts in this location.
		 */
		if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
		{
			$query = "SELECT COUNT(*) FROM parts WHERE id_storeloc=". smart_escape($_REQUEST["locsel"]) .";";
			debug_print($query);
			$r = mysql_query($query);
			$d = mysql_fetch_row($r); // COUNT(*) queries always give a result!
			if ($d[0] != 0)
			{
				$special_dialog = 1;
				print "<html><body><div style=\"text-align:center;\"><div style=\"color:red;font-size:x-large;\">Lagerort kann nicht gel&ouml;scht werden!</div>Es gibt noch Teile, die diesen Lagerort als Ort eingetragen haben.<form method=\"get\" action=\"locmgr.php\"><input type=\"submit\" value=\"OK!\"></form></div></body></html>";
			}
			else
			{
				$special_dialog = 1;
				print "<html><body><div style=\"text-align:center;\"><div style=\"color:red;font-size:x-large;\">M&ouml;chten Sie den Lagerort &quot;". lookup_location_name($_REQUEST["locsel"]) ."&quot; wirklich l&ouml;schen?</div>Der L&ouml;schvorgang ist irreversibel!<form action=\"locmgr.php\" method=\"post\"><input type=\"hidden\" name=\"locsel\" value=\"". $_REQUEST["locsel"] ."\"><input type=\"hidden\" name=\"btn_del\" value=\"x\"><input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen!\"><input type=\"submit\" name=\"del_ok\" value=\"L&ouml;schen!\"></form></div></body></html>";
			}
		}
		else if (isset($_REQUEST["del_ok"]))
		{
			// the user said it's OK to delete the location
			$query = "DELETE FROM storeloc WHERE id=". smart_escape($_REQUEST["locsel"]) ." LIMIT 1;";
			debug_print ($query);
			mysql_query ($query);
		}
	}
	else if (isset($_REQUEST["btn_rn"]))
	{
		$query = "UPDATE storeloc SET name=". smart_escape($_REQUEST["nn"]) ." WHERE id=". smart_escape($_REQUEST["locsel"]) ." LIMIT 1;";
		debug_print ($query);
		mysql_query ($query);
	}

	/*
	 * Don't show the default text when there's a msg.
	 */
	if ($special_dialog == 0)
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Navigation</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<table class="table">
	<tr>
		<td class="tdtop">
		Lagerorte bearbeiten
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<form method="get">
			<table>
			<tr>
			<td valign="top">
			Zu bearbeitenden Lagerort w&auml;hlen:<br>
			<select name="locsel" size="8">
			<?PHP
			$query = "SELECT id,name FROM storeloc ORDER BY name ASC;";
			debug_print($query);
			$r = mysql_query ($query);
	
			$ncol = mysql_num_rows ($r);
			for ($i = 0; $i < $ncol; $i++)
			{
			$d = mysql_fetch_row ($r);
			if ($i == 0)
			print "<option selected value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
			else
			print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
			}
			?>
			</select>
			</td><td valign="top">
			<table>
			<tr>
			<td>
			<input type="submit" name="btn_del" value="L&ouml;schen">
			</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			<td>
			Neuer Name:<br>
			<input type="text" name="nn">
			<input type="submit" name="btn_rn" value="Umbenennen!">
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</form>
			<form method="post">
			Neuer Lagerort:<input type="text" name="locname">
			<input type="submit" name="btn_add" value="Anlegen">
			</form>
		</td>
	</tr>
</table>

 </body>
</html>

<?PHP
	}
?>
