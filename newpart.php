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

	$Id: newpart.php,v 1.5 2006/03/06 23:05:14 cl Exp $

	04/03/06:
		Added escape/unescape calls
    
  04/12/09:
    Added "Remember Values" checkbox. If checked, the selected values for 'supplier', 'footprint'
    and 'storage location' will rest after submitting the Form. (Added by Julian Oberacker, "juliano" @ mikrocontroller.net)
*/

	include ("lib.php");
	partdb_init();

	if ( strcmp ($_REQUEST["a"], "add") == 0 )
	{
		/* some sanity checks */
		if ( (strcmp ($_REQUEST["p_footprint"], "X") == 0) || (strcmp ($_REQUEST["p_storeloc"], "X") == 0) || (strcmp ($_REQUEST["p_supplier"], "X") == 0) )
		{
			print "<h1>Fehler</h1>";
		}
		else
		{
			$query = "INSERT INTO parts (id_category,name,instock,mininstock,comment,id_footprint,id_storeloc,id_supplier,supplierpartnr) VALUES (". smart_escape($_REQUEST["cid"]) .",". smart_escape($_REQUEST["p_name"]) .",". smart_escape($_REQUEST["p_instock"]) .",". smart_escape($_REQUEST["p_mininstock"]) .",". smart_escape($_REQUEST["p_comment"]) .",". smart_escape($_REQUEST["p_footprint"]) .",". smart_escape($_REQUEST["p_storeloc"]) .",". smart_escape($_REQUEST["p_supplier"]) .",". smart_escape($_REQUEST["p_supplierpartnr"]) .");";
			
			debug_print ($query);
			mysql_query ($query);
		}
	}
?>
<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdtop">
		Neues Teil in der Kategorie &quot;<?PHP print lookup_category_name ($_REQUEST["cid"]); ?>&quot;
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<table>
		<form method="post">
		<input type="hidden" name="a" value="add">
		<input type="hidden" name="cid" value="<?PHP print $_REQUEST["cid"]; ?>">
		<table>
		<tr>
		<td>Name des Bauteils:</td>
		<td><input type="text" name="p_name"></td>
		</tr><tr>
		<td>Anzahl der vorr&auml;tigen Teile dieses Typs:</td>
		<td><input type="text" name="p_instock" <?PHP if($_REQUEST["rememberInstoc"]) print "value=\"". $_REQUEST["p_instock"] ."\">"; 
                                                  else print "value=\"0\">"; ?></td>
		</tr><tr>
		<td>Minimaler Lagerbestand:</td>
		<td><input type="text" name="p_mininstock" <?PHP if($_REQUEST["rememberMininstoc"]) print "value=\"". $_REQUEST["p_mininstock"] ."\">"; 
                                                  else print "value=\"0\">"; ?></td>
		</tr><tr>
		<td>Footprint:</td>
		<td>
		<select name="p_footprint">
		<option value="X"></option>
		<?PHP
		$query = "SELECT id,name FROM footprints ORDER BY name ASC;";
		$r = mysql_query ($query);
		$ncol = mysql_num_rows ($r);
		for ($i = 0; $i < $ncol; $i++)
		{
		$d = mysql_fetch_row ($r);
    if($_REQUEST["rememberFootprint"] && ($_REQUEST["p_footprint"] == smart_unescape($d[0]))) print "<option selected value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
		else print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
		}
		?>
		</select>
		</td>
		</tr><tr>
		<td>Lagerort:</td>
		<td>
		<select name="p_storeloc">
		<option value="X"></option>
		<?PHP
		$query = "SELECT id,name FROM storeloc ORDER BY name ASC;";
		$r = mysql_query ($query);
		$ncol = mysql_num_rows ($r);
		for ($i = 0; $i < $ncol; $i++)
		{
		$d = mysql_fetch_row ($r);
		if($_REQUEST["rememberStoreloc"] && ($_REQUEST["p_storeloc"] == smart_unescape($d[0]))) print "<option selected value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
    else print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
		}
		?>
		</select>
		</td>
		</tr><tr>
		<td>Lieferant:</td>
		<td>
		<select name="p_supplier">
		<option value="X"></option>
		<?PHP
		$query = "SELECT id,name FROM suppliers ORDER BY name ASC;";
		$r = mysql_query ($query);
		$ncol = mysql_num_rows ($r);
		for ($i = 0; $i < $ncol; $i++)
		{
		$d = mysql_fetch_row ($r);
    if($_REQUEST["rememberSupplier"] && ($_REQUEST["p_supplier"] == smart_unescape($d[0]))) print "<option selected value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
    else print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
		}
		?>
		</select>
		</td>
		</tr><tr>
		<td>Bestell-Nr.:</td>
		<td><input type="text" name="p_supplierpartnr"></td>
		</tr><tr>
		<td valign="top">Kommentar:</td>
		<td><textarea name="p_comment"></textarea></td>
		</tr><tr><td colspan="2"><input type="submit"><br>
    Werte merken f&uuml;r:<br>
      <input type="checkbox" name="rememberFootprint" value="1" <?PHP if($_REQUEST["rememberFootprint"]) print "checked = \"checked\""; ?>>Footprint<br>
      <input type="checkbox" name="rememberSupplier" value="1" <?PHP if($_REQUEST["rememberSupplier"]) print "checked = \"checked\""; ?>>Lieferant<br>
      <input type="checkbox" name="rememberStoreloc" value="1" <?PHP if($_REQUEST["rememberStoreloc"]) print "checked = \"checked\""; ?>>Lagerort<br>
      <input type="checkbox" name="rememberInstoc" value="1" <?PHP if($_REQUEST["rememberInstoc"]) print "checked = \"checked\""; ?>>Vorr&auml;tig<br>
      <input type="checkbox" name="rememberMininstoc" value="1" <?PHP if($_REQUEST["rememberMininstoc"]) print "checked = \"checked\"";?>>Minimalbestand
      </td></tr>
		</table>
		</td>
	</tr>
</table>

  </head>
 </body>
</html>
