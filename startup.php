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

	$Id: startup.php,v 1.4 2006/05/28 10:28:57 cl Exp $

	28/05/06
		If all requirements regarding (locs, footprints, categories
		and suppliers) are met (at least one of each), hide the
		warning. Only if something's wrong the warning pops up, now
		the text color has been changed to red. Some people suggested
		this long ago ...
*/
	include("lib.php");
	partdb_init();

	/*
	 * This variable determines wheater the user is reminded to add
	 * add least one loc, one footprint, one category and one supplier.
	 */
	$display_warning = 0;

	$q = "SELECT id FROM storeloc LIMIT 1;";
	//debug_print($q);
	$r = mysql_query($q);
	if (! mysql_num_rows($r))
	{
		$display_warning |= 1;
	}

	$q = "SELECT id FROM footprints LIMIT 1;";
	debug_print($q);
	$r = mysql_query($q);
	if (! mysql_num_rows($r))
	{
		$display_warning |= 1;
	}

	$q = "SELECT id FROM categories LIMIT 1;";
	debug_print($q);
	$r = mysql_query($q);
	if (! mysql_num_rows($r))
	{
		$display_warning |= 1;
	}

	$q = "SELECT id FROM suppliers LIMIT 1;";
	debug_print($q);
	$r = mysql_query($q);
	if (! mysql_num_rows($r))
	{
		$display_warning |= 1;
	}
?>
<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdstartup">
		<img src="img/partdb/partdb.png"></img><b>Part-DB V0.1.3 RW</b><img src="img/partdb/partdb.png"></img>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

<table class="table">
	<tr>
	<tr>
		<td class="tdtop">
		Lizenz
		</td>
	</tr>
		<td class="tdtext">
			Part-DB, Copyright (C) 2005 of Christoph Lechner. Part-DB is published under the GPL, so it comes with ABSOLUTELY NO WARRANTY, click <a href="readme/gpl.txt">here</a> for details. This is free software, and you are welcome to redistribute it under certain conditions. Click <a href="readme/gpl.txt">here</a> for details.</br>
			</br> 
			The first Author's Homepage <a href="http://www.cl-projects.de/">http://www.cl-projects.de/</a></br>
			</br>
			Author since 2009 by K.Jacobs - <a href="http://www.grautier.com/">http://grautier.com</a></br>
			</br>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

<table class="table">
	<tr>
		<td class="tdtop">
		Changelog
		</td>
	</tr>
	<tr>
		<td class="tdtextsmall">
			**.08.2009
			- FIX nav.php (µC.net)</br>
			- FIX var für Footprints (µC.net)</br>
			23.05.2009</br>
			- FIX Stats</br>
			21.05.2009</br>
			- ADD Footprint Bilder + Tools </br>
			13.05.2009</br>
			- MOD CSS Template</br>
			- ADD JS Men&uuml;</br>
			11.05.2009</br>
			- FIX Komma/Punkt Prob. beim Preis.</br>
			10.05.2009</br>
			- ADD help.php (Kleine Beschreibungen)</br>
			25.04.2009</br>
			- MOD Style ist Komplet</br>
			18.04.2009</br>
			- MOD Style partinfo.php und newpart.php
			- ADD Bilder nach Footprints in "../img/footprints abgelegte Bilder (footprintname.jpg) weden automatisch angezeigt wen kein anderes Bild gesetzt ist.</br>
			16.04.2009</br>
			- MOD Bilderupload die daten werden mit img_ + MD5 gespeichert somit wird verhindert das ein Bild doppelt gespeichert wird.</br>
			- MOD Style catmgr.php, fpmgr.php, subpmgr.php, locmgr.php.</br>
			15.04.2009</br>
			- ADD More Stats</br>
			- MOD Style openlist.php, submgr.php</br>
			- MOV Fucking Code to /dev/null</br>
			- FIX Many BUGs</br>
			- FIX Bilder Upload und Anzeige</br>
			14.04.2009</br>
			- ADD openlist.php &Ouml;ffentliche Liste aller Bauteile</br>
			13.04.2009</br>
			- MOD Style startup.php, nav.php</br>
			- ADD Bauteile in den Kategorien Verschieben. (www.mikrocontroller.net Wiki)</br>
			- ADD SUB Kategorien Ausblenden (www.mikrocontroller.net Wiki)</br>
			08.04.2009</br>
			- ADD Patch Sortierung (Mikrocontroller.net Wiki)</br>
			- ADD Suche f&uuml;r Parts &uuml;ber AllDataSheet und Reichelt</br>
			</br>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

<?PHP
	if ($display_warning)
	{
?>

<table class="table">
	<tr>
		<td class="tdtext">
		Beachten Sie bitte, dass Sie vor der Verwendung der <tt>part-db</tt> jeweils mindestens</br>
		einen Lagerort</br>
		ein Footprint</br>
		eine Kategorie</br>
		und einen Lieferanten</br>
		hinzuf&uuml;gen m&uuml;ssen. Die Tools hierf&uuml;r finden Sie links.</br>
  	</tr>
</table>

<?PHP
	}
?>

  </head>
 </body>
</html>


