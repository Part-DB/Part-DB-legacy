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

	$Id: $

*/
	include ("lib.php");
	partdb_init();
	$showsearchedparts = 0;
	$notallinstock = 0;
	$bookstate = 0;
	$bookerrorstring = "";
	if ( strcmp ($_REQUEST["action"], "assignbytext") == 0 )
	{

		$query = "SELECT id FROM parts WHERE name=". smart_escape($_REQUEST["newpartname"]) .";";
		debug_print ($query);
		$result = mysql_query ($query);
		$nParts = mysql_num_rows($result);
		if( $nParts == 1)
		{
			//Check if part is allready assigned
			$partid = mysql_fetch_row ($result);
			$query = "SELECT * FROM part_device WHERE id_part=". smart_escape($partid[0]) ." AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
			debug_print ($query);
			$result = mysql_query ($query);
			$nDevices = mysql_num_rows($result);
			if( $nDevices == 0)
			{
				//now add a part to the device			
				$query = "INSERT INTO part_device (id_part,id_device,quantity) VALUES (". smart_escape($partid[0]) .",". smart_escape($_REQUEST["deviceid"]) .",1);";
				debug_print ($query);
				mysql_query ($query);
			}
			else
			{
				//Increment the part quantity
				$query = "UPDATE part_device SET quantity=quantity+1 WHERE id_part=" . smart_escape($partid[0]) . " AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
				debug_print($query);
				mysql_query($query);
			}
		}
		else
		{
			$showsearchedparts = 1;
		}
	}
	else if ( strcmp ($_REQUEST["action"], "assignbyselected") == 0 )
	{
		$rowcount = $_REQUEST["selections"];
		while($rowcount)
		{
			if($_REQUEST["selectedid".$rowcount] && $_REQUEST["selectedquantity".$rowcount])
			{
				$query = "INSERT INTO part_device (id_part,id_device,quantity) VALUES (". smart_escape($_REQUEST["selectedid".$rowcount]) .",". smart_escape($_REQUEST["deviceid"]) .",".smart_escape($_REQUEST["selectedquantity".$rowcount]).");";
				debug_print ($query);
				mysql_query ($query);
			}
			$rowcount--;
		}
	}
	else if ( strcmp ($_REQUEST["action"], "assign") == 0 )
	{
		//Increment the part quantity
		$query = "UPDATE part_device SET quantity=quantity+1 WHERE id_part=" . smart_escape($_REQUEST["partid"]) . " AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
		debug_print($query);
		mysql_query($query);
	}
	else if ( strcmp ($_REQUEST["action"], "deassign") == 0 )
	{
		$query = "UPDATE part_device SET quantity=quantity-1 WHERE id_part=" . smart_escape($_REQUEST["partid"]) . " AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
		debug_print($query);
		mysql_query($query);
	}
	else if ( strcmp ($_REQUEST["action"], "remove") == 0 )
	{
		$query = "DELETE FROM part_device WHERE id_part=" . smart_escape($_REQUEST["partid"]) . " AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
		debug_print($query);
		mysql_query($query);
	}
	else if ( strcmp ($_REQUEST["action"], "bookparts") == 0 )
	{
		//First check if enough parts are in stock
		$query = "SELECT parts.instock, part_device.quantity, parts.name FROM parts JOIN part_device ON part_device.id_part = parts.id WHERE part_device.id_device = ".$_REQUEST["deviceid"].";";
		debug_print ($query);
		$result = mysql_query ($query);
		debug_print ($result);
		
		$enoughinstock = 0;
		$bookstate = 2;	//no parts in device
		if(mysql_num_rows($result)>0)
			$enoughinstock = 1;
			
		while ( $d = mysql_fetch_row ($result) )
		{
			$needed = $d[1]*$_REQUEST["bookmultiplikator"];
			if($d[0] < $needed)
			{
				$enoughinstock = 0;
				$bookstate = 3;	//not enough parts in stock
				$bookerrorstring = $bookerrorstring.$d[2]." Benötigt: ".$needed." Im Lager: ".$d[0]."<BR>";
			}
		}		
		if($enoughinstock)
		{
			$query = "UPDATE parts JOIN part_device ON part_device.id_part = parts.id SET parts.instock = parts.instock - (part_device.quantity*".
			$_REQUEST["bookmultiplikator"].") ".
			"WHERE part_device.id_device = ".$_REQUEST["deviceid"].";";
			debug_print ($query);
			$result = mysql_query ($query);
			if($result)
				$bookstate = 1;	//success
			else
				$bookstate = 4;	//querry error
			debug_print ($result);
		}
	}
?>

<html>
 <head>
 </head>
  <body class="body">
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />
<table class="table">
	
	<tr>
		<td class="tdtop">
		Teile per Name Zuordnen
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<?PHP
			print "<form methode=\"post\">";
			print "<input type=\"text\" name=\"newpartname\"/>";
			print "<input type=\"hidden\" name=\"action\"  value=\"assignbytext\"/>";
			print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
			print "<input type=\"submit\" value=\"Hinzufügen\"/></form>";
			
			print "<form methode=\"post\">";
			print "<input type=\"hidden\" name=\"action\"  value=\"refresh\"/>";
			print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
			print "<input type=\"submit\" value=\"Aktualisieren\"/></form>";
			?>
		</td>
		</tr>
		
	<?PHP
	if($showsearchedparts == 1)
	{	
		print "<tr>";
		print "<td class=\"tdtext\">";
		print "<form methode=\"post\">";
		print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
		print "<input type=\"hidden\" name=\"action\"  value=\"assignbyselected\"/>";
		print "<table>";
		$kw = '\'%'. mysql_escape_string($_REQUEST['newpartname']) .'%\'';
		$query = "SELECT parts.name, parts.comment, parts.id, footprints.name, parts.instock FROM ".
		"parts JOIN footprints ON (footprints.id = parts.id_footprint) ".
		"WHERE parts.name LIKE ".$kw.
		" AND parts.id NOT IN(SELECT part_device.id_part FROM part_device WHERE part_device.id_device=".$_REQUEST["deviceid"].");";
		debug_print ($query);
		$result = mysql_query ($query);
		$nParts = mysql_num_rows($result);
		$rowcount = 0;
		print "<tr class=\"trcat\"><td>Anzahl</td><td>Teil</td><td>Footprint</td><td>Lagernd</td>\n";
		while ( $d = mysql_fetch_row ($result) )
		{
		$q = mysql_fetch_row ($quantity);
		$rowcount++;
		if ( ($rowcount & 1) == 0 )
			print "<tr class=\"trlist1\">";
		else
			print "<tr class=\"trlist2\">";
		
		print "<td class=\"tdrow0\" >";
		print $rowcount;
		print "<input type=\"hidden\" name=\"selectedid".$rowcount."\" value=\"" . smart_unescape($d[2]). "\"/>";
		print "<input type=\"text\" size=\"3\" onkeypress=\"validateNumber(event)\" name=\"selectedquantity".$rowcount."\" value=\"0\"/>";
		
		print "</td>";
		print "<td class=\"tdrow1\"><a title=\"";
		print "Kommentar: " . smart_unescape($d[1]);
		print "\" href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[2]) ."');\">". smart_unescape($d[0]) ."</a></td>";
			
		print "<td class=\"tdrow2\">".smart_unescape($d[3])."</td>";
		print "<td class=\"tdrow3\">".smart_unescape($d[4])."</td>";
	
		print "</tr>\n";
		}
		
		
		print "</td></tr></table>";
		print "<input type=\"hidden\" name=\"selections\"  value=\"".$rowcount."\"/>";
		print "<input type=\"submit\" value=\"Hinzufügen\"/>";
		print "</table>";
		print "</form>";
		print "</td>";
		print "</tr>";
	}
	?>
</table>
<table class="tablenone">
</br>
</table>
<table class="table">
	<tr>
		<td class="tdtop">
		Zugeordnete Teile zu &quot;<?PHP print lookup_device_name ($_REQUEST["deviceid"]); ?>&quot;
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
		
		function validateNumber(evt) 
		{
		  var theEvent = evt || window.event;
		  var key = theEvent.keyCode || theEvent.which;
		  key = String.fromCharCode( key );
		  var regex = /[0-9]|\./;
		  if( !regex.test(key) ) {
			theEvent.returnValue = false;
			if(theEvent.preventDefault) theEvent.preventDefault();
		  }
		}
		// -->
		</script>
		
		<table>
		<?PHP
		$rowcount = 0;	
		print "<tr class=\"trcat\"><td>Teil</td><td>Footprint</td><td>Anzahl</td><td>Lagernd</td><td>Lagerort</td><td>Lieferant</td><td>Einzelpreis</td><td>Entfernen</td><td>-</td><td>+</td></tr>\n";
				
		$query = "SELECT parts.name, parts.comment, parts.id, footprints.name, part_device.quantity, parts.instock, storeloc.name, suppliers.name, preise.preis ".
		"FROM parts ".
		"JOIN part_device, footprints, storeloc, suppliers ".
		"ON (parts.id = part_device.id_part AND footprints.id = parts.id_footprint AND storeloc.id = parts.id_storeloc AND suppliers.id = parts.id_supplier) ".
		"LEFT JOIN preise ON (preise.part_id = parts.id)".
		"WHERE id_device = ".$_REQUEST["deviceid"]." ORDER BY parts.id_category,parts.name ASC;";
		debug_print($query);
		$result = mysql_query ($query);
		$sumprice = 0;
		while ( $d = mysql_fetch_row ($result) )
		{
		$q = mysql_fetch_row ($quantity);
		
		$rowcount++;
		if ( ($rowcount & 1) == 0 )
			print "<tr class=\"trlist1\">";
		else
			print "<tr class=\"trlist2\">";
		
		print "<td class=\"tdrow0\"><a title=\"";
		print "Kommentar: " . smart_unescape($d[1]);
		print "\" href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[2]) ."');\">". smart_unescape($d[0]) ."</a></td>";
			
		print "<td class=\"tdrow1\">".smart_unescape($d[3])."</td>";
		print "<td class=\"tdrow1\">".smart_unescape($d[4])."</td>";
		
		print "<td ";
		if($d[4] <= $d[5])
		{
			print "class=\"tdrow1\"";
		}
		else
		{
			$notallinstock = 1;
			print "class=\"tdrowred\"";
		}
		print ">".smart_unescape($d[5])."</td>";
		print "<td class=\"tdrow1\">".smart_unescape($d[6])."</td>";
		print "<td class=\"tdrow1\">".smart_unescape($d[7])."</td>";
		print "<td class=\"tdrow1\">";
		if($d[8])
			print smart_unescape($d[8]);
		else
			print "-.-";
		print "€</td>";
		//Build the sum
		$sumprice += $d[8];
		print "<td class=\"tdrow1\"><form method=\"post\">";
		print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
		print "<input type=\"hidden\" name=\"partid\" value=\"".smart_unescape($d[2])."\"/>";
		print "<input type=\"hidden\" name=\"action\"  value=\"remove\"/>";
		print "<input type=\"submit\" value=\"Entfernen\"/></form></td>";
		
		print "<td class=\"tdrow1\"><form method=\"post\">";
		print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
		print "<input type=\"hidden\" name=\"partid\" value=\"".smart_unescape($d[2])."\"/>";
		print "<input type=\"hidden\" name=\"action\"  value=\"deassign\"/>";
		print "<input type=\"submit\" value=\"-\"/";
		if($d[4] <= 0)
		{
			print "disabled=\"disabled\"";
		}
		print "></form></td>";
		
		print "<td class=\"tdrow7\"><form method=\"post\">";
		print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
		print "<input type=\"hidden\" name=\"partid\" value=\"".smart_unescape($d[2])."\"/>";
		print "<input type=\"hidden\" name=\"action\"  value=\"assign\"/>";
		print "<input type=\"submit\" value=\"+\"/></form></td>";
		print "</tr>\n";
		}
		
		$rowcount++;
		if ( ($rowcount & 1) == 0 )
			print "<tr class=\"trlist1\">";
		else
			print "<tr class=\"trlist2\">";
		print "<td class=\"tdrow0\" colspan=\"6\"></td><td class=\"tdrow0\">Gesamtpreis:".$sumprice."€</td><td class=\"tdrow0\" colspan=\"3\"></td>";
		print "</tr>";
		
		$query = "SELECT parts.name, parts.comment, parts.id, footprints.name, part_device.quantity, parts.instock, storeloc.name, suppliers.name, preise.preis ".
		"FROM parts ".
		"JOIN part_device, footprints, storeloc, suppliers ".
		"ON (parts.id = part_device.id_part AND footprints.id = parts.id_footprint AND storeloc.id = parts.id_storeloc AND suppliers.id = parts.id_supplier) ".
		"LEFT JOIN preise ON (preise.part_id = parts.id)".
		"WHERE id_device = ".$_REQUEST["deviceid"]." ORDER BY parts.id_category,parts.name ASC;";
		debug_print($query);
		$result = mysql_query ($query);
		
		?>
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
		BOM Generierung
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<form method="post">
				<table>
				<?PHP
				print "<tr class=\"trcat\"><td><input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
				print "<input type=\"hidden\" name=\"action\"  value=\"createbom\"/>";
				
				print "Lieferant:</td><td><select name=\"sup_id\">";
				if (! isset($_REQUEST["sup_id"]) )
					print "<option selected value=\"0\">Alle</option>";
				else
					print "<option value=\"0\">Alle</option>";
				
				$query = "SELECT id,name FROM suppliers ORDER BY name ASC;";
				$r = mysql_query ($query);
				
				$ncol = mysql_num_rows ($r);
				$lieferanten;
				while ( ($d = mysql_fetch_row($r)) )
				{
				$lieferanten = $lieferanten . smart_unescape($d[0]);
				if ($d[0] == $_REQUEST["sup_id"])
					print "<option selected value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
				else
					print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
				}
				print "</select>";
				print "<tr class=\"trcat\"><td>";
				print "Format:</td><td><select name=\"format\">";
				if (! isset($_REQUEST["format"]) )
					print "<option selected value=\"0\">CVS</option>";
				else
					print "<option value=\"0\">CVS</option>";
				print "</select>";
				print "</td></tr><tr class=\"trcat\"><td>";
				print "Trennzeichen:</td><td><input type=\"text\" name=\"spacer\" size=\"3\" value=\"";
				if ( strcmp ($_REQUEST["action"], "createbom"))
					print ";";
				else
					print $_REQUEST["spacer"];
				print "\"/></td></tr>";
				
				print "<tr class=\"trcat\"><td>Multiplikator:</td><td><input type=\"text\" name=\"multiplikator\" size=\"3\" onkeypress=\"validateNumber(event)\" value=\"";
				if ( strcmp ($_REQUEST["action"], "createbom"))
					print "1";
				else
					print $_REQUEST["multiplikator"];
				print "\"/></tr>";
				
				
				print "</td></tr>";
				print "<tr class=\"trcat\"><td>Nur benötigtes Material bestellen:</td><td><input type=\"checkbox\" name=\"onlyneeded\" ";
				if ( strcmp ($_REQUEST["action"], "createbom"))
				{
					print "checked=\"checked\"";
				}
				else
				{
					if(isset($_REQUEST["onlyneeded"]))
						print "checked=\"checked\"";
				}
				print "\"></tr></td>";
				print "<tr><td><input type=\"submit\" value=\"Ausführen\"/></tr></td>";
				
				print "<tr><td colspan=\"4\">";
				
				if ( strcmp ($_REQUEST["action"], "createbom") == 0 )
				{
					
					$query = "SELECT parts.supplierpartnr, part_device.quantity, storeloc.name, suppliers.name, parts.name, parts.instock ".
					"FROM parts ".
					"JOIN part_device, footprints, storeloc, suppliers ".
					"ON (parts.id = part_device.id_part AND footprints.id = parts.id_footprint AND storeloc.id = parts.id_storeloc AND suppliers.id = parts.id_supplier) ".
					"WHERE id_device = ".$_REQUEST["deviceid"]." ORDER BY parts.id_category,parts.name ASC;";
					if($_REQUEST["sup_id"]!=0)
					{
						$query = $query . " AND parts.id_supplier = ".$_REQUEST["sup_id"];
					}
					$query = $query . ";";
					
					$result = mysql_query ($query);
					$nrows = mysql_num_rows($result)+6;
					
					print "<textarea name=\"sql_query\" rows=\"".$nrows."\" cols=\"40\" dir=\"ltr\" >";
					debug_print($query);
					print "______________________________\r\n";
					print "Bestell-Liste:\r\n";
					print "\r\n\r\n";
					while ( $d = mysql_fetch_row ($result) )
					{
						$q = mysql_fetch_row ($quantity);
						$order = 1;
						$orderstring = "";
						//print partnr.
						$orderstring = $orderstring.smart_unescape($d[0]);
						//print spacer
						$orderstring = $orderstring.$_REQUEST["spacer"];
						//print quantity
						if(isset($_REQUEST["onlyneeded"]))
						{
							$quant = (smart_unescape($d[1])*$_REQUEST["multiplikator"]);
							if( $quant > $d[5])	//Check if instock is greater
							{
								$orderstring = $orderstring.($quant-$d[5])."\r\n";
							}
							else
							{
								$order = 0;
							}
						}
						else
						{
							$orderstring = $orderstring.(smart_unescape($d[1])*$_REQUEST["multiplikator"])."\r\n";
						}
						if($order)
							print $orderstring;
					}
					print "</textarea>";
				}
				print "</td></tr>";
				?>
				</table>
			</form>
		</td>
	</tr>
  </table>
  
  <table class="tablenone">
</br>
</table>
<table class="table">
	<tr>
		<td class="tdtop">
		Benötigte Teile Abfassen
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<form method="post">
				<table>
					
					<?PHP
					print "<tr class=\"trcat\"><td>Multiplikator:</td><td><input type=\"text\" name=\"bookmultiplikator\" size=\"3\" onkeypress=\"validateNumber(event)\" value=\"";
					if ( strcmp ($_REQUEST["action"], "bookparts"))
						print "1";
					else
						print $_REQUEST["bookmultiplikator"];
					print "\"/><td></tr>";
					print "<tr><td><input type=\"submit\" value=\"Ausführen\"";
					if($notallinstock)
					{
						print "disabled=\"disabled\"";
					}
					print "/>";
					print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
					print "<input type=\"hidden\" name=\"action\"  value=\"bookparts\"/>";
					print "</td>";
					if($bookstate > 1)	//success
					{
					print "<td class=\"tdtextsmall\">";
					if($bookstate == 2)	//no parts in device
						print "Keine Teile zum Gerät zugeordnet.";
					else if($bookstate == 3)	//not enough parts in stock
						print "<B>Nicht genug Teile verfügbar.<BR>Teil/e:<BR></B>" . $bookerrorstring;
					else if($bookstate == 4)	//querry error
						print "Fehler.";
					print "</td>";
					}
					print "</tr>";
					?>
				</table>
			</form>
		</td>
	</tr>
  </table>
  
 </body>
</html>
