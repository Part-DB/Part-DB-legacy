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

	$Id: showparts.php,v 1.11 2006/05/23 21:47:14 cl Exp $

	ChangeLog
	
	07/03/06:
		Added escape/unescape stuff
*/
	include ("lib.php");
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
	else if(strcmp($_REQUEST["action"], "an") == 0)	//add number of parts
	{
		$query = "UPDATE parts SET instock=instock+". smart_escape($_REQUEST["toadd"]) ." WHERE id=". smart_escape($_REQUEST["pid"]) ." LIMIT 1;";
		debug_print($query);
		mysql_query($query);
	}
	
	function findallsubcategories ($cid)
	{
		$rv = "id_category=". smart_escape($cid);
		
		$query = "SELECT id FROM categories WHERE parentnode=". smart_escape($cid) .";";
		debug_print ($query."<br>");
		$result = mysql_query ($query);
		while ( ( $d = mysql_fetch_row ($result) ) )
		{
			$rv = $rv . " OR " . findallsubcategories (smart_unescape($d[0]));
		}

		return ($rv);
	}
?>
<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdtop">
		Sonstiges
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
			eval("page" + id + " = window.showModalDialog(URL,'"+id+"','dialogWidth:645px;dialogHeight:485px');");
			location.reload(true);
			}
			// -->
		 </script>
		<?PHP
		print "<form action=\"showparts.php\" methode=\"post\">";
		print "<input type=\"hidden\" name=\"cid\" value=\"".$_REQUEST["cid"]."\">";
		print "<input type=\"hidden\" name=\"type\" value=\"index\">";
		if (! isset($_REQUEST["nosubcat"]) )
		{
		print "<input type=\"hidden\" name=\"nosubcat\" value=\"1\">";
		print "<input type=\"submit\" name=\"s\" value=\"Unterkategorien ausblenden\">";
		}
		else
		print "<input type=\"submit\" name=\"s\" value=\"Unterkategorien einblenden\">";
		print "</form>";

		if ( strcmp ($_REQUEST["type"], "toless") == 0 )
		{
		print "<form method=\"get\"><input type=\"hidden\" name=\"cid\" value=\"0\"><input type=\"hidden\" name=\"type\" value=\"toless\">\nLieferant(en):<select name=\"sup_id\">";

		if (! isset($_REQUEST["sup_id"]) )
			print "<option selected value=\"0\">Alle</option>";
		else
			print "<option value=\"0\">Alle</option>";
			
		$query = "SELECT id,name FROM suppliers ORDER BY name ASC;";
		$r = mysql_query ($query);
		
		$ncol = mysql_num_rows ($r);
		while ( ($d = mysql_fetch_row($r)) )
		{
		if ($d[0] == $_REQUEST["sup_id"])
		print "<option selected value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
		else
		print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
		}
		print "</select><input type=\"submit\" value=\"W&auml;hle Lieferanten!\"></form>\n";
		}
		else if (strcmp ($_REQUEST["type"], "noprice") == 0)
		{
		//print "<h2>Teile ohne Preis</h2>";
		}
		else if (strcmp ($_REQUEST["type"], "showpending") == 0)
		{
		//print "<h2>Ausstehende Bestellungen</h2>";
		}
		else
		{
		?>
		<a href="javascript:popUp('newpart.php?cid=<?PHP print $_REQUEST["cid"]; ?>');">Neues Teil in dieser Kategorie</a>
		<?PHP  	} ?>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

<table class="table">
	<tr>
		<td class="tdtop">
		Anzeige der Kategorie &quot;<?PHP print lookup_category_name ($_REQUEST["cid"]); ?>&quot;
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<table>
		<?PHP
		//ADD by TheBorg
		if (! isset($_REQUEST["nosubcat"]) )
			$catclause = findallsubcategories ($_REQUEST["cid"]);
		else
			$catclause = "id_category=".$_REQUEST["cid"];

		if ( (strcmp ($_REQUEST["type"], "index") == 0) || (strcmp ($_REQUEST["type"], "noprice") == 0) )
		{
		print "<tr  class=\"trcat\"><td></td><td>Name</td><td>Vorh./\r\n</br>Min.Best.</td><td>Footprint</td><td>Lagerort</td><td>Datenbl&auml;tter</td><td>-</td><td>+</td></tr>";

		/* the only difference is the query */
		if (strcmp ($_REQUEST["type"], "index") == 0)
			$query = "SELECT parts.id,parts.name,parts.instock,parts.mininstock,footprints.name AS 'footprint',storeloc.name AS 'loc',parts.comment FROM parts LEFT JOIN footprints ON parts.id_footprint=footprints.id LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id WHERE (". $catclause .") ORDER BY name ASC;";
		else
			$query = "SELECT parts.id,parts.name,parts.instock,parts.mininstock,footprints.name AS 'footprint',storeloc.name AS 'loc',parts.comment FROM parts LEFT JOIN footprints ON parts.id_footprint=footprints.id LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id LEFT JOIN preise ON parts.id=preise.part_id WHERE (". $catclause .") AND (preise.id IS NULL) ORDER BY name ASC;";

		debug_print ($query);
		$result = mysql_query ($query);

		$rowcount = 0;
		while ( $d = mysql_fetch_row ($result) )
		{
			$rowcount++;
			if ( ($rowcount % 2) == 0 )
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
			print "<td class=\"tdrow1\"><a title=\"";
			print "Kommentar: " . smart_unescape($d[6]);
			print "\" href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\">". smart_unescape($d[1]) ."</a></td>";
			print "<td class=\"tdrow2\">". smart_unescape($d[2]) ."/". smart_unescape($d[3]) ."</td>";
			print "<td class=\"tdrow3\">". smart_unescape($d[4]) ."</td><td class=\"tdrow4\">". smart_unescape($d[5]) . "</td>";
			print "<td class=\"tdrow5\">";
			$test = ($d[1]) ;
			$query = "SELECT datasheeturl FROM datasheets WHERE part_id=". smart_escape($d[0]) ." ORDER BY datasheeturl ASC;";
			$result_ds = mysql_query($query);
			$dnew = mysql_fetch_row ($result_ds); #)
			if($dnew[0] == NULL)
			{
			// Mit ICONS 
			print "<a title=\"alldatasheet.com\"href=\"http://www.alldatasheet.com/view.jsp?Searchword=". smart_unescape ($test) ."\" target=\"_blank\"><img class=\"catbild\" src=\"img/partdb/ads.png\"></a>";
			print "<a title=\"Reichelt.de\"href=\"http://www.reichelt.de/?ACTION=4;START=0;SHOW=1;SEARCH=". smart_unescape ($test) ."\" target=\"_blank\"><img class=\"catbild\" src=\"img/partdb/reichelt.png\"></a>";
			// Ohne ICONS
			print "<a href=\"http://search.datasheetcatalog.net/key/". smart_unescape ($test) ."\" target=\"_blank\">DC </a>";
			// print "<a href=\"http://www.alldatasheet.com/view.jsp?Searchword=". smart_unescape ($test) ."\" target=\"_blank\">AllDataSheet, </a>";
			// print "<a href=\"http://www.reichelt.de/?ACTION=4;START=0;SHOW=1;SEARCH=". smart_unescape ($test) ."\" target=\"_blank\">Reichelt"</a>;
			}
			else 
			{
				print "<a href=\"". smart_unescape($dnew[0]) ."\">Datenblatt</a> ";
			}
			print "</td>";
			
			//build the "-" button, only if more then 0 parts on stock
			print "<form method=\"post\"><td class=\"tdrow6\">";
			print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape($d[0])."\"/>";
			print "<input type=\"hidden\" name=\"action\"  value=\"r\"/>";
			print "<input type=\"submit\" value=\"-\"";
			if($d[2]<=0)
			{
				print " disabled=\"disabled\" ";
			}
			print "/></td></form>";
			
			//build the "+" button
			print "<form method=\"post\"><td class=\"tdrow7\">";
			print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape($d[0])."\"/>";
			print "<input type=\"hidden\" name=\"action\"  value=\"a\"/>";
			print "<input type=\"submit\" value=\"+\"/></td></form>";
			
			print "<tr>\n";
		}
		}
		else if ( strcmp ($_REQUEST["type"], "showpending") == 0 )
		{
		print "<tr class=\"trcat\"><td></td><td>Name</td><td>Ausstehend</td><td>Vorhanden</td><td>Min. Bestand</td><td>Footprint</td><td>Lagerort</td><td>Datenbl&auml;tter</td></tr>";

		$query = "SELECT parts.id,parts.name,SUM(pending_orders.quantity),parts.instock,parts.mininstock,footprints.name AS 'footprint',storeloc.name AS 'loc' FROM parts LEFT JOIN footprints ON parts.id_footprint=footprints.id LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id INNER JOIN pending_orders ON parts.id=pending_orders.part_id WHERE (". $catclause .") GROUP BY (pending_orders.part_id) ORDER BY name ASC;";

		debug_print ($query);
		$result = mysql_query ($query);

		$rowcount = 0;
		while ( $d = mysql_fetch_row ($result) )
		{
			$rowcount++;
			if ( ($rowcount % 2) == 0 )
				print "<tr class=\"trlist1\">";
			else
				print "<tr class=\"trlist2\">";

			if (has_image($d[0]))
			{
				print "<td  class=\"tdrow0\"><a href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\"><img src=\"gettn.php?pid=". smart_unescape($d[0]) . "\" alt=\"". smart_unescape($d[1]) ."\"></a></td>";
			}
			else
			{
				print "<td class=\"tdrow0\"><img class=\"catbild\" src=\"img/dummytn.png\"></td>";
			}
			print "<td class=\"tdrow1\"><a href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\">". smart_unescape($d[1]) ."</a></td><td class=\"tdrow2\">". smart_unescape($d[2]) ."</td><td class=\"tdrow3\">". smart_unescape($d[3]) ."</td><td class=\"tdrow4\">". smart_unescape($d[4]) ."</td><td class=\"tdrow5\">". smart_unescape($d[5]) . "</td><td class=\"tdrow6\">". smart_unescape($d[6]) . "</td>";
			print "<td  class=\"tdrow6\">";
			$query = "SELECT datasheeturl FROM datasheets WHERE part_id=". smart_escape($d[0]) ." ORDER BY datasheeturl ASC;";
			$result_ds = mysql_query($query);
			while ( $d_ds = mysql_fetch_row ($result_ds) )
			{
				print "<a href=\"". smart_unescape($d_ds[0]) ."\">Datenblatt</a> ";
			}
			print "</td>";
			print "<tr>\n";
		}

		}
		else if ( strcmp ($_REQUEST["type"], "toless") == 0 )
		{
		/*
		 * All supplier IDs are positive integers, thus 0 (which
		 * stands for "all suppliers") is no valid supplier ID!
		 * Show the entire list.
		 */
		if ( (! isset($_REQUEST["sup_id"]) ) || ($_REQUEST["sup_id"] == "0") )
		{
			$query = "SELECT SUM((parts.mininstock-parts.instock)*preise.preis) FROM parts LEFT JOIN preise ON parts.id=preise.part_id WHERE (". $catclause .") AND (parts.instock < parts.mininstock);";
		}
		else
		{
			$query = "SELECT SUM((parts.mininstock-parts.instock)*preise.preis) FROM parts LEFT JOIN preise ON parts.id=preise.part_id WHERE (". $catclause .") AND (parts.instock < parts.mininstock) AND (parts.id_supplier=". smart_escape($_REQUEST["sup_id"]) .");";
		}

		debug_print ($query);
		$result = mysql_query ($query);
		$d = mysql_fetch_row ($result);
		print "<tr><td colspan=\"4\">Wert der zu bestellenden Artikel: ".$d[0]."&euro;</td></tr>";

		/****/
		print "<tr class=\"trcat\"><td>Name</td><td>Footprint</td><td>Bestellmenge</td><td>Lieferant</td><td>Bestell-Nr.</td><td>Lagerort</td><td>Hinzuf&uuml;gen</td></tr>";
		if ( (! isset($_REQUEST["sup_id"]) ) || ($_REQUEST["sup_id"] == "0") )
		{
		/*$query = "
		SELECT 
		parts.id,
		parts.name,
		parts.instock,
		parts.mininstock,
		footprints.name AS 'footprint',
		storeloc.name AS 'loc',
		parts.comment 
		FROM parts 
		LEFT JOIN footprints ON parts.id_footprint=footprints.id 
		LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id 
		WHERE (". $catclause .") ORDER BY name ASC;";
		*/
			//$query = "SELECT parts.id,parts.name,footprints.name AS 'footprint',parts.mininstock-parts.instock AS 'diff',suppliers.name AS 'supplier',parts.supplierpartnr FROM parts LEFT JOIN footprints ON parts.id_footprint=footprints.id LEFT JOIN suppliers ON parts.id_supplier=suppliers.id WHERE (". $catclause .") AND (parts.instock < parts.mininstock) ORDER BY name ASC;";
			$query = 
				"SELECT ".
				"parts.id,".
				"parts.name,".
				"footprints.name AS 'footprint',".
				"parts.mininstock-parts.instock AS 'diff',".
				"suppliers.name AS 'supplier',".
				"parts.supplierpartnr,".
				"parts.instock,parts.mininstock,".
				"storeloc.name AS 'loc'".
				" FROM parts ".
				" LEFT JOIN footprints ON parts.id_footprint=footprints.id".
				" LEFT JOIN suppliers ON parts.id_supplier=suppliers.id".
				" LEFT JOIN pending_orders ON parts.id=pending_orders.part_id".
				" LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
				" WHERE (pending_orders.id IS NULL) AND (parts.instock < parts.mininstock) AND (". $catclause .") ".
				"UNION ".
				"SELECT ".
				"parts.id,".
				"parts.name,".
				"footprints.name AS 'footprint',".
				"parts.mininstock-parts.instock-SUM(pending_orders.quantity),".
				"suppliers.name AS 'supplier',".
				"parts.supplierpartnr,".
				"parts.instock,parts.mininstock,".
				"storeloc.name AS 'loc'".
				" FROM parts ".
				" INNER JOIN pending_orders ON (parts.id=pending_orders.part_id)".
				" LEFT JOIN footprints ON parts.id_footprint=footprints.id".
				" LEFT JOIN suppliers ON parts.id_supplier=suppliers.id".
				" LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
				" WHERE (". $catclause .") ".
				"GROUP BY (pending_orders.part_id) ".
				"HAVING (parts.instock + SUM(pending_orders.quantity)  < parts.mininstock) ".
				"ORDER BY name ASC ";
		}
		else
		{
			$query = "SELECT ".
			"parts.id,".
			"parts.name,".
			"footprints.name AS 'footprint',".
			"parts.mininstock-parts.instock AS 'diff',".
			"suppliers.name AS 'supplier',".
			"parts.supplierpartnr,".
			"parts.instock,".
			"parts.mininstock,".
			"storeloc.name AS 'loc'".
			" FROM parts".
			" LEFT JOIN footprints ON parts.id_footprint=footprints.id".
			" LEFT JOIN suppliers ON parts.id_supplier=suppliers.id".
			" LEFT JOIN pending_orders ON parts.id = pending_orders.part_id".
			" LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
			" WHERE (". $catclause .") AND (pending_orders.id IS NULL) AND (parts.instock < parts.mininstock) AND (parts.id_supplier = ". smart_escape($_REQUEST["sup_id"]) .")". 
			" UNION".
			" SELECT ".
			"parts.id,".
			"parts.name,". 
			"footprints.name AS 'footprint',". 
			"parts.mininstock - parts.instock - SUM( pending_orders.quantity ) ,". 
			"suppliers.name AS 'supplier',". 
			"parts.supplierpartnr," .
			"parts.instock,". 
			"parts.mininstock,".
			"storeloc.name AS 'loc'".
			" FROM parts ".
			" INNER JOIN pending_orders ON ( parts.id = pending_orders.part_id ) ". 
			" LEFT JOIN footprints ON parts.id_footprint = footprints.id ".
			" LEFT JOIN suppliers ON parts.id_supplier = suppliers.id ".
			" LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
			" WHERE (". $catclause .") AND (parts.id_supplier = ". smart_escape($_REQUEST["sup_id"]) .") GROUP BY (pending_orders.part_id) HAVING (parts.instock + SUM( pending_orders.quantity ) < parts.mininstock) ORDER BY name ASC;";
		}
		debug_print ($query);
		//print(smart_unescape($query));
		$result = mysql_query ($query);

		$rowcount = 0;
		while ( $d = mysql_fetch_row ($result) )
		{
			$rowcount++;
			if ( ($rowcount % 2) == 0 )
				print "<tr class=\"trlist1\">";
			else
				print "<tr class=\"trlist2\">";

			print "<td class=\"tdrow1\"><a href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\">". smart_unescape($d[1]) ."</a></td><td class=\"tdrow3\">". smart_unescape($d[2]) ."</td><td class=\"tdrow4\">". smart_unescape($d[3]) ."</td><td class=\"tdrow1\">". smart_unescape($d[4]) ."</td><td class=\"tdrow1\">". smart_unescape($d[5]) . "</td><td class=\"tdrow1\">". smart_unescape($d[8]) . "</td>";
			//show text box with number to add and the add button
			print "<td class=\"tdrow2\"><form method=\"post\">";
			print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape($d[0])."\"/>";
			print "<input type=\"hidden\" name=\"action\"  value=\"an\"/>";
			print "<input type=\"text\" style=\"width:25px;\" name=\"toadd\" value=\"" . smart_unescape($d[3]) . "\"/>";
			print "<input type=\"submit\" value=\"Add\"/></form></td>";
			
			print "</tr>\n";
		}
		}
		?>
		</table>
		</td>
	</tr>
</table>

  </head>
 </body>
</html>
