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

	$Id: partinfo.php,v 1.5 2006/03/06 23:05:14 cl Exp $

	06/03/06:
		Added escape/unescape calls
*/
	include ("lib.php");
	partdb_init();

	/*
	 * 'action' is a hidden field in the form.
	 * 'r' is for remove, 'a' is for add.
	 * The 'instock' value has to be changed before the output begins.
	 */
	if ( strcmp ($_REQUEST["action"], "r") == 0 )
	{
		$query = "UPDATE parts SET instock=instock-". smart_escape($_REQUEST["n_less"]) ." WHERE id=". smart_escape($_REQUEST["pid"]) ." AND instock >= ". smart_escape($_REQUEST["n_less"]) ." LIMIT 1;";
		debug_print ($query);
		mysql_query ($query);
	}
	else if ( strcmp ($_REQUEST["action"], "a") == 0 )
	{
		$query = "UPDATE parts SET instock=instock+". smart_escape($_REQUEST["n_more"]) ." WHERE id=". smart_escape($_REQUEST["pid"]) ." LIMIT 1;";
		debug_print ($query);
		mysql_query ($query);
	}
?>

<html>
 <head>
  <body class="body">
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdtop">
		Detailinfo zu &quot;<?PHP print lookup_part_name ($_REQUEST["pid"]); ?>&quot;
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<script language="JavaScript" type="text/javascript">
		<!--
		function popUp(URL)
		{
		d = new Date();
		id = d.getTime();
		eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=1, scrollbars=1, location=1, statusbar=1, menubar=1, resizable=1, width=600, height=400');");
		}
		// -->
		</script>
		</head>
		<body>
		<table>
		<tr valign="top">
		<td>
		<table><?PHP
		$query = "SELECT parts.id,parts.name,parts.instock,parts.mininstock,footprints.name AS 'footprint',storeloc.name AS 'loc',suppliers.name AS 'supplier',parts.supplierpartnr,preise.preis,preise.ma,parts.comment FROM parts LEFT JOIN footprints ON parts.id_footprint=footprints.id LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id LEFT JOIN suppliers ON parts.id_supplier=suppliers.id LEFT JOIN preise ON parts.id=preise.part_id WHERE parts.id=". smart_escape($_REQUEST["pid"]) ." ORDER BY preise.ma DESC LIMIT 1;";
		debug_print ($query);
		$r = mysql_query ($query);
		while ( ($d = mysql_fetch_row ($r)) )
		{
		print "<tr><td><b>Name:</b></td><td>". smart_unescape($d[1]) ."</td></tr>";
		print "<tr><td><b>Vorhanden:</b></td><td>". smart_unescape($d[2]) ."</td></tr>";
		print "<tr><td><b>Min. Bestand:</b></td><td>". smart_unescape($d[3]) ."</td></tr>";
		print "<tr><td><b>Footprint:</b></td><td>". smart_unescape($d[4]) ."</td></tr>";
		print "<tr><td><b>Lagerort:</b></td><td>". smart_unescape($d[5]) ."</td></tr>";
		print "<tr><td><b>Lieferant:</b></td><td>". smart_unescape($d[6]) ."</td></tr>";
		print "<tr><td><b>Bestell-Nr.:</b></td><td>". smart_unescape($d[7]) ."</td></tr>";
		$d[8] = str_replace('.', ',', $d[8]);
		print "<tr><td><b>Preis:</b></td><td>". smart_unescape($d[8]);
		if ($d[9] == 1) {
		// if the price information has been added manually ...
		//print " (m) ";
		}
		print " &euro; &nbsp;</td></tr>";
		print "<tr><td valign=\"top\"><b>Kommentar:</b></td><td>". smart_unescape($d[10]) ."&nbsp;</td></tr>";
		}
		?>
		</table>
		</br>Angaben <a href="editpartinfo.php?pid=<?PHP print $_REQUEST["pid"]; ?>">ver&auml;ndern</a>
		</td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td>
		<table>
		<form method="post">
		<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
		<input type="hidden" name="action" value="r">
		<tr><td colspan="2">Teile entnehmen</td></tr>
		<tr valign="top">
		<td>Anzahl:</td><td><input type="text" size="3" name="n_less"></td>
		</tr><tr><td colspan="2" align="center"><input type="submit" value="Entnehmen!"></td></tr>
		</form>
		<tr><td colspan="2">&nbsp;</td></tr>
		<form method="post">
		<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
		<input type="hidden" name="action" value="a">
		<tr><td colspan="2">Teile hinzuf&uuml;gen</td></tr>
		<tr valign="top">
		<td>Anzahl:</td><td><input type="text" size="3" name="n_more"></td>
		</tr><tr><td colspan="2" align="center"><input type="submit" value="Hinzuf&uuml;gen!"></td></tr>
		</form>
		</table>
		</td>
		</tr>
		</table>
		<?PHP
		if (has_image($_REQUEST["pid"]))
		{
		print "</br><b>Bilder:</b><table><tr>\n";
		
		$pict_query = "SELECT pictures.id FROM pictures WHERE (pictures.part_id=". smart_escape($_REQUEST["pid"]) .") AND (pictures.pict_type='P');";
		debug_print ($pict_query);
		$r = mysql_query ($pict_query);

		while ( ($d = mysql_fetch_row ($r)) )
		{
			print "<td><a href=\"javascript:popUp('getimage.php?pict_id=". $d[0] ."')\"><img src=\"getimage.php?pict_id=". $d[0] ."&maxx=200&maxy=150\" alt=\"Zum Vergr&ouml;&szlig;ern klicken!\"></a></td>";
		}
		print "</tr></table>\n";
		}
		?>

		</td>
	</tr>
  </table>
 </body>
</html>
