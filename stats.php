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

	$Id: stats.php,v 1.1 2005/08/08 18:00:53 cl Exp $
*/
	include('lib.php');
	partdb_init();
?>
<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdtop">
		Statistik
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<b>Wert aller mit Preis erfassten Bauteile:</b>
		<?PHP
		$query = "SELECT SUM(preise.preis*parts.instock) FROM parts LEFT JOIN preise ON parts.id=preise.part_id ORDER BY name ASC;";
		$r = mysql_query ($query);
		$d = mysql_fetch_row ($r);
		print $d[0];
		?>&euro;</br>

		<b>Mit Preis erfasste Bauteile:</b>
		<?PHP
		$i = 0;
		$query = "SELECT preis FROM preise;";
		debug_print($query);
		$r = mysql_query ($query);
		while ( $d = mysql_fetch_row ($r) )
		{
		  $i++;
		}
		print $i;
		?></br>

		<b>Anzahl der Kategorien:</b>
		<?PHP
		$i = 0;
		$query = "SELECT id,name FROM categories WHERE parentnode=". smart_escape($pid) ." ORDER BY categories.name ASC;";
		debug_print($query);
		$r = mysql_query ($query);
		while ( $d = mysql_fetch_row ($r) )
		{
		  $i++;
		}
		print $i;
		?></br>

		<b>Anzahl der verschidenen Bauteile:</b>
		<?PHP
		$i = 0;
		$query = "SELECT name FROM parts;";
		debug_print($query);
		$r = mysql_query ($query);
		while ( $d = mysql_fetch_row ($r) )
		{
		  $i++;
		}
		print $i;
		?></br>

		<b>Anzahl der vorhandenen Bauteile:</b>
		<?PHP
		$query = "SELECT SUM(instock) FROM parts;";
		$r = mysql_query ($query);
		$d = mysql_fetch_row ($r);
		print $d[0];
		?></br>

		<b>Anzahl der Hochgeladenen Bilder:</b>
		<?PHP
		$dir = "img/";
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) 
		{
		  $files[] = $filename;
		}
		echo count($files)- 2;
		unset($files);
		?></br>

		<b>Anzahl der Footprint Bilder:</b>
		<?PHP
		$dir = "tools/footprints/";
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) 
		{
		  $files[] = $filename;
		}
		echo count($files)- 2;
		unset($files);
		?></br>

		<b>Anzahl der Hersteller Logos:</b>
		<?PHP
		$dir = "tools/iclogos/";
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) 
		{
		  $files[] = $filename;
		}
		echo count($files)- 2;
		unset($files);
		?></br>
		</td>
	</tr>
</table>

  </head>
 </body>
</html>
