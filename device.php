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
<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdtop">
		Neues Gerät erzeugen
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<form method="post">
			Gerätenamen
			<input type="edit" name="newdevicename" width="10" maxlength="20" >
			<input type="hidden" name="action" value="createdevice">
			<input type="submit" value="OK"> </br>
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
		Geräte
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
		<table >

		<?PHP
		$query = "SELECT id, name FROM devices;";
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
		
		print "<td class=\"tdrow1\"><a href=\"javascript:popUp('deviceinfo.php?deviceid=". smart_unescape($d[0]) ."');\">". smart_unescape($d[1]) . "</td>";
		print "<td class=\"tdrow2\">Teile</td>";
		print "<td class=\"tdrow3\">Einzelteile</td>";
		
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
