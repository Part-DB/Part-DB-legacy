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

	$Id: search.php,v 1.3 2006/03/09 15:08:09 cl Exp $

	06/03/06	Added escape/unescape calls
	05/12/09	Edit Parts over Popup (k.jacobs)
*/
	include('lib.php');
	partdb_init();
	
	if(strcmp($_REQUEST["action"], "r") == 0)  //remove one part
	{
		$query = "UPDATE parts SET instock=instock-1 WHERE id=" . smart_escape($_REQUEST["pid"]) . " AND instock >= 1 LIMIT 1;";
		debug_print($query);
		mysql_query($query);
	}
	else if(strcmp($_REQUEST["action"], "a") == 0)	//add one part
	{
		$query = "UPDATE parts SET instock=instock+1 WHERE id=" . smart_escape($_REQUEST["pid"]) . " LIMIT 1;";
		debug_print($query);
		mysql_query($query);
	}
	
?>
<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdtop">
		Suchergebnis - Sie suchten nach &quot;<?PHP print $_REQUEST['keyword']; ?>&quot;
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
		<table class="table">
		<?PHP
		// execute the SQL query (DON'T USE smart_escape HERE, because
		// it breaks the query)
		$kw = '\'%'. mysql_escape_string($_REQUEST['keyword']) .'%\'';
		$query = "SELECT parts.id,parts.name,parts.instock,parts.mininstock,footprints.name AS 'footprint',storeloc.name AS 'loc',parts.id_category,parts.comment FROM parts LEFT JOIN footprints ON parts.id_footprint=footprints.id LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id WHERE (parts.name LIKE ".$kw.") OR (parts.comment LIKE ".$kw.") OR (parts.supplierpartnr LIKE ".$kw.") OR (storeloc.name LIKE ".$kw.") ORDER BY parts.id_category,parts.name ASC;";
		debug_print ($query."<br>");
		$result = mysql_query ($query);
	
		$rowcount = 0;	// $rowcount is used for the alternating bg colors
		$prevcat = -1;	// $prevcat remembers the previous category. -1 is
			// an invalid category id.
		while ( $d = mysql_fetch_row ($result) )
		{
		if ($prevcat != $d[6])
		{
			/* this part is in a different category than
			   the previous. */
			print "<td class=\"tdtop\" colspan=\"7\">Treffer in der Kategorie ". show_bt($d[6]) ."</td>";
			print "<tr class=\"trcat\"><td></td><td>Name</td><td>Vorh./\r\n</br>Min.Best.</td><td>Footprint</td><td>Lagerort</td><td>-</td><td>+</td></tr>\n";
			$prevcat = $d[6];
			$rowcount = 0;
		}
		// the alternating background colors are created here
		$rowcount++;
		if ( ($rowcount & 1) == 0 )
			print "<tr class=\"trlist1\">";
		else
			print "<tr class=\"trlist2\">";
		
		if (has_image($d[0]))
		{
			print "<td class=\"tdrow0\"><a href=\"javascript:popUp('getimage.php?pid=". smart_unescape($d[0]) . "')\"><img class=\"catbild\" src=\"getimage.php?pid=". smart_unescape($d[0]) . "\" alt=\"". smart_unescape($d[1]) ."\"></a></td>";
		}
		else
		{
			//Footprintbilder
			if(is_file("tools/footprints/" . smart_unescape($d[4]) . ".png"))
			{
			print "<td class=\"tdrow0\"><a href=\"javascript:popUp('tools/footprints/". smart_unescape($d[4]) . ".png')\"><img class=\"catbild\" src=\"tools/footprints/". smart_unescape($d[4]) .".png\"></a></td>";
			}
			else
			{
			print "<td class=\"tdrow0\"><img class=\"catbild\" src=\"img/partdb/dummytn.png\"></td>";
			}
		}
		print "<td class=\"tdrow1\"><a title=\"Kommentar: " . smart_unescape($d[7]) . "\"";
		print "href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\">". smart_unescape($d[1]) ."</a></td>";
		print "<td class=\"tdrow2\">". smart_unescape($d[2]) ."/". smart_unescape($d[3]) ."</td>";
		print "<td class=\"tdrow3\">". smart_unescape($d[4]) ."</td>";
		print "<td class=\"tdrow4\">". smart_unescape($d[5]) . "</td>";
		
		//build the "-" button, only if more then 0 parts on stock
		print "<form method=\"post\"><td class=\"tdrow5\">";
		print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape($d[0])."\"/>";
		print "<input type=\"hidden\" name=\"action\"  value=\"r\"/>";
		print "<input type=\"submit\" value=\"-\"";
		if($d[2]<=0)
		{
			print " disabled=\"disabled\" ";
		}
		print "/></td></form>";
			
		//build the "+" button
		print "<form method=\"post\"><td class=\"tdrow6\">";
		print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape($d[0])."\"/>";
		print "<input type=\"hidden\" name=\"action\"  value=\"a\"/>";
		print "<input type=\"submit\" value=\"+\"/></td></form>";
		print "</tr>\n";
		}
		?>
		</table>
		</td>
	</tr>
</table>

  </head>
 </body>
</html>
