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

	$Id: nav.php,v 1.7 2006/03/06 23:05:14 cl Exp $

	04/03/06:
		Added escape/unescape calls
*/
	include('config.php');
	include('lib.php');
	partdb_init();

	/* This recursive procedure builds the tree of categories.
	   There's nothing special about it, so no more comments.
	   Warning: Infinite recursion can occur when the DB is
	   corrupted! But normally everything should be fine. */
	function buildtree ($pid, $parentId)
	{
		$query = "SELECT id,name FROM categories WHERE parentnode=". smart_escape($pid) ." ORDER BY categories.name ASC;";
		$r = mysql_query ($query);
		while ( $d = mysql_fetch_row ($r) )
		{
			print "d.add(". smart_unescape($d[0]) .",". smart_unescape($pid) .",'". smart_unescape($d[1]) ."','showparts.php?cid=". smart_unescape($d[0]) ."&type=index\"','','_content_frame');\n";
			buildtree ($d[0], $pid);
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Navigation</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <script type="text/javascript" src="dtree.js"></script>
</head>
<body class="body">

<table class="table">
	<tr>
		<td class="tdtop">
        Suche
		</td>
	</tr>

	<tr>
		<td class="tdtext">
		<form action="search.php" method="get" target="_content_frame">
            <input type="text" name="keyword" size="20" maxlength="20">
            <input type="submit" name="s" value="Los!"><br>
            <table>
            <tr><td valign="top">
            <input type="checkbox" name="search_nam" value="true" checked>Name<br>
            <input type="checkbox" name="search_com" value="true" checked>Kommentar<br>
            <input type="checkbox" name="search_fpr" value="true" checked>Footprint<br>
            </td><td valign="top">
            <input type="checkbox" name="search_loc" value="true" checked>Lagerort<br>
            <input type="checkbox" name="search_sup" value="true"        >Lieferant<br>
            <input type="checkbox" name="search_snr" value="true"        >Bestellnr.<br>
            </td></tr></table>
		</form>
		</td>
	</tr>
</table>

<br>

<table class="table">
	<tr>
		<td class="tdtop">
        Kategorien
		</td>
	</tr>

	<tr>
		<td class="tdtext">
		 <!-- <base href="" target="_content_frame"> -->
		  <div class="dtree">
			<script type="text/javascript">
				<!--
				d = new dTree('d');
				d.add(0,-1,'');
				<?PHP buildtree (0, 0); ?>
				document.write(d);
				//-->
			</script>
			<br>
			<a href="javascript: d.openAll();">Alle Anzeigen</a> | <a href="javascript: d.closeAll();">Alle Schliessen</a>
		  </div>
		 <!-- </base> -->
		</td>
	</tr>
</table>

<?php
    if (! $disable_devices) {
?>
<br>

<table class="table">
	<tr>
		<td class="tdtop">
		Baugruppen
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<a href="device.php" target="_content_frame">Baugruppen</a>
		</td>
	</tr>
</table>

<?php
    }
?>

<br>


<table class="table">
	<tr>
		<td class="tdtop">
		Tools
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<a href="tools/label.php" target="_content_frame">Labels</a><br>
		<a href="tools/footprints.php" target="_content_frame">Footprints</a><br>
		<a href="tools/iclogos.php" target="_content_frame">IC-Logos</a>
		</td>
	</tr>
</table>

<br>

<table class="table">
	<tr>
		<td class="tdtop">
		Zeige
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<a href="showparts.php?cid=0&amp;type=toless" target="_content_frame">Zu bestellende Teile</a><br>
		<a href="showparts.php?cid=0&amp;type=noprice" target="_content_frame">Teile ohne Preis</a><br>
		<a href="stats.php" target="_content_frame">Statistik</a><br>
        <?php if (! $disable_help) { ?>
		<a href="help.php" target="_content_frame">Hilfe</a>
        <?php } ?>
		</td>
	</tr>
</table>

<br>

<table class="table">
	<tr>
		<td class="tdtop">
		Bearbeiten
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<a href="locmgr.php" target="_content_frame">Lagerorte</a><br>
		<a href="fpmgr.php" target="_content_frame">Footprints</a><br>
		<a href="catmgr.php" target="_content_frame">Kategorien</a><br>
		<a href="supmgr.php" target="_content_frame">Lieferanten</a><br>
		</td>
	</tr>
</table>

</body>
</html>
