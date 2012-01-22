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
	
		
	if(strcmp($_REQUEST["action"], "createdevice") == 0)  //add a new part
	{
		$query = "INSERT INTO devices (name) VALUES (". smart_escape($_REQUEST["newdevicename"]) .");";
	   
		debug_print ($query);
		$r = mysql_query ($query);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Footprints</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<table class="table">
	<tr>
		<td class="tdtop">
		Neues Gerät erzeugen
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<form method="post" action="">
			Gerätenamen
			<input type="text" name="newdevicename" size="10" maxlength="50" >
			<input type="hidden" name="action" value="createdevice">
			<input type="submit" value="OK">
			</form>	
		</td>
	</tr>
</table>

<br>

<table class="table">
	<tr>
		<td class="tdtop">
		Geräte
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<table >

		<?PHP
		$query = "SELECT devices.id, devices.name, SUM(part_device.quantity), COUNT(part_device.quantity) ".
		"FROM devices LEFT JOIN part_device ".
		"ON (devices.id =  part_device.id_device) GROUP BY part_device.id_device ORDER BY devices.name ASC;";
		debug_print($query);
		$result = mysql_query ($query);
		debug_print($result);
	
		$rowcount = 0;	// $rowcount is used for the alternating bg colors
		
		print "<tr class=\"trcat\"><td>Name</td><td>Anzahl Teile</td><td>Anzahl Einzelteile</td></tr>\n";
		
		while ( $d = mysql_fetch_row ($result) )
		{
		
		// the alternating background colors are created here
		$rowcount++;
		if ( ($rowcount & 1) == 0 )
			print "<tr class=\"trlist1\">";
		else
			print "<tr class=\"trlist2\">";
		
		print "<td class=\"tdrow1\"><a href=\"deviceinfo.php?deviceid=". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) . "</a></td>\n";
		print "<td class=\"tdrow2\">". smart_unescape($d[2]) ."</td>\n";
		print "<td class=\"tdrow3\">". smart_unescape($d[3]) ."</td>\n";
		
		print "</tr>\n";
		}
		?>
		</table>
		</td>
	</tr>
</table>

</body>
</html>
