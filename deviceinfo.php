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
	if ( strcmp ($_REQUEST["action"], "assignbytext") == 0 )
	{
//SELECT id FROM parts WHERE name='Teil1';UPDATE part_device SET quantity=quantity+1 WHERE id_part='A

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
		//deviceid
		//print "<input type=\"checkbox\" name=\"deviceid".$rowcount."\" value=\"" . smart_unescape($d[2]). "\"/>";
		//print "<input type=\"hidden\" name=\"selections\"  value=\"".$rowcount."\"/>";
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
?>

<html>
 <head>
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
			?>
		</td>
	</tr>
<?PHP
	if($showsearchedparts == 1)
	{
		print "<form methode=\"post\">";
		print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
		print "<input type=\"hidden\" name=\"action\"  value=\"assignbyselected\"/>";
		
		//Add empty table as space
		print "<table class=\"tablenone\"></br></table>";
		//Create table with found keywords
		print "<table class=\"table\"><tr><td class=\"tdtop\">";
		print "Teile Auswählen</td></tr>";
		print "<tr><td class=\"tdtext\">";
		print "<table>";
		$kw = '\'%'. mysql_escape_string($_REQUEST['newpartname']) .'%\'';
		$query = "SELECT parts.name, parts.comment, parts.id, footprints.name, parts.instock FROM ".
		"parts JOIN footprints ON (footprints.id = parts.id) ".
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
		//print "<input type=\"checkbox\" name=\"selectedid".$rowcount."\" value=\"" . smart_unescape($d[2]). "\"/>";
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
	}
?>	
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
		</head>
		<body>
		<table>
		<?PHP
		
		$rowcount = 0;	// $rowcount is used for the alternating bg colors
		
		print "<tr class=\"trcat\"><td>Teil</td><td>Footprint</td><td>Anzahl</td><td>-</td><td>+</td></tr>\n";
				
		$query = "SELECT parts.name, parts.comment, parts.id, footprints.name, part_device.quantity FROM parts JOIN part_device, footprints ON (parts.id = part_device.id_part and footprints.id = parts.id) WHERE id_device = ".$_REQUEST["deviceid"].";";
				 
				 
		debug_print($query);
		$result = mysql_query ($query);
		debug_print($result);
		
		debug_print($result);
		while ( $d = mysql_fetch_row ($result) )
		{
		$q = mysql_fetch_row ($quantity);
		// the alternating background colors are created here
		$rowcount++;
		if ( ($rowcount & 1) == 0 )
			print "<tr class=\"trlist1\">";
		else
			print "<tr class=\"trlist2\">";
		
		//print "<td class=\"tdrow1\">".smart_unescape($d[0])."</td>";
		print "<td class=\"tdrow1\"><a title=\"";
		print "Kommentar: " . smart_unescape($d[1]);
		print "\" href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[2]) ."');\">". smart_unescape($d[0]) ."</a></td>";
			
		print "<td class=\"tdrow2\">".smart_unescape($d[3])."</td>";
		print "<td class=\"tdrow3\">".smart_unescape($d[4])."</td>";
		
		print "<td class=\"tdrow4\"><form method=\"post\">";
		print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
		print "<input type=\"hidden\" name=\"partid\" value=\"".smart_unescape($d[2])."\"/>";
		print "<input type=\"hidden\" name=\"action\"  value=\"deassign\"/>";
		print "<input type=\"submit\" value=\"-\"/";
		if($d[4] <= 0)
		{
			print "disabled=\"disabled\"";
		}
		print "></form></td>";
		
		print "<td class=\"tdrow5\"><form method=\"post\">";
		print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
		print "<input type=\"hidden\" name=\"partid\" value=\"".smart_unescape($d[2])."\"/>";
		print "<input type=\"hidden\" name=\"action\"  value=\"assign\"/>";
		print "<input type=\"submit\" value=\"+\"/></form></td>";
			
	
		print "</tr>\n";
		}
		
		?>
		</table>
		</td>
	</tr>
  </table>
 </body>
</html>
