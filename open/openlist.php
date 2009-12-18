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

	06/03/06
		Added escape/unescape calls
*/
	include('../lib.php');
	partdb_init();
?>
<html>
 <body class="body">
  <table class="table">
   <link rel="StyleSheet" href="../css/partdb.css" type="text/css" />

	<tr>
		<td class="tdtop">
			<b>&Ouml;ffentliche Liste</b>
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<table>
			<?PHP
			// execute the SQL query (DON'T USE smart_escape HERE, because
			// it breaks the query)
			$kw = '\'%'. mysql_escape_string($_REQUEST['keyword']) .'%\'';
			$query = "SELECT parts.id,parts.name,parts.instock,parts.mininstock,footprints.name AS 'footprint',storeloc.name AS 'loc',parts.id_category FROM parts LEFT JOIN footprints ON parts.id_footprint=footprints.id LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id WHERE (parts.name LIKE ".$kw.") OR (parts.comment LIKE ".$kw.") OR (parts.supplierpartnr LIKE ".$kw.") ORDER BY parts.id_category,parts.name ASC;";
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
			print "<tr><td></br><b>". show_bt($d[6]) ."</b></td></tr>\n";
			print "<tr><td>Name</td><td>Vorhanden</td><td>Min. Bestand</td><td>Footprint</td></tr>\n";
			$prevcat = $d[6];
			$rowcount = 0;
			}
		
			// the alternating background colors are created here
			$rowcount++;
			if ( ($rowcount & 1) == 0 )
				print "<tr class=\"trlist1\">";
			else
				print "<tr class=\"trlist2\">";
				print "<td>". smart_unescape($d[1]) ."</td><td>". smart_unescape($d[2]) ."</td><td>". smart_unescape($d[3]) ."</td><td>". smart_unescape($d[4]) ."</td></td>";
				print "</tr>\n";
			}
			?>
			</table>
		</td>
	</tr>
  </table>
 </body>
</html>
