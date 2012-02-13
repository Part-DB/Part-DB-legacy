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
            print "d.add(". smart_unescape($d[0]) .",". smart_unescape($pid) .",'". smart_unescape($d[1]) ."','showparts.php?cid=". smart_unescape($d[0]) ."&type=index\"','','content_frame');\n";
            buildtree ($d[0], $pid);
        }
    }

    function baugruppentree ($pid, $parentId)
    {    
        $query = "SELECT devices.id, devices.name ".
        "FROM devices ".
        "ORDER BY devices.id ASC;";
        //debug_print($query);
        $result = mysql_query ($query);
          
        while ( $d = mysql_fetch_row ($result))
        {      
            // part-db/deviceinfo.php?deviceid=1
	    $id = $d[0];
	    $id++;
            print "baugruppen.add($id,1,'". smart_unescape($d[1]) ."','deviceinfo.php?deviceid=". smart_unescape($d[0]) ."','','content_frame');\n";
            //baugruppentree ($d[0], $pid);
        }
    }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Navigation</title>
    <?php print_http_charset(); ?>
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
        <form action="search.php" method="get" target="content_frame">
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
         <!-- <base href="" target="content_frame"> -->
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
            <a href="javascript:d.openAll();">Alle Anzeigen</a> | <a href="javascript:d.closeAll();">Alle Schliessen</a>
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
          <div class="dtree">
            <script type="text/javascript">
                <!--
                baugruppen = new dTree('baugruppen');
                baugruppen.add(0,-1,'');
                baugruppen.add(1,0,'Verwaltung','device.php"','','content_frame');
                <?PHP baugruppentree (0, 0); ?>
                document.write(baugruppen);
                //-->
            </script>
            <br>
            <a href="javascript:baugruppen.openAll();">Alle Anzeigen</a> | <a href="javascript:baugruppen.closeAll();">Alle Schliessen</a>
          </div>
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
        Verwaltung / Tools
        </td>
    </tr>
    <tr>
        <td class="tdtext">
         <!-- <base href="" target="content_frame"> -->
          <div class="dtree">
            <script type="text/javascript">
                <!--
                menue = new dTree('menue');
                menue.add(0,-1,'');
                menue.add(1,0,'Tools','','','');
                menue.add(2,1,'Import','import.php"','','content_frame');
                menue.add(3,1,'Labels','tools/label.php"','','content_frame');
                menue.add(4,1,'Footprints','tools/footprints.php"','','content_frame');
                menue.add(5,1,'IC-Logos','tools/iclogos.php"','','content_frame');

                menue.add(6,0,'Zeige','','','');
                menue.add(7,6,'Zu bestellende Teile','orderparts.php"','','content_frame');
                menue.add(8,6,'Teile ohne Preis','nopriceparts.php"','','content_frame');
                menue.add(9,6,'Statistik','stats.php"','','content_frame');
                <?php if (! $disable_help) { ?>
                menue.add(10,6,'Hilfe','help.php"','','content_frame')
                <?php } ?>

                menue.add(11,0,'Bearbeiten','','','');
                menue.add(12,11,'Lagerorte','locmgr.php"','','content_frame')
                menue.add(13,11,'Footprints','fpmgr.php"','','content_frame')
                menue.add(14,11,'Kategorien','catmgr.php"','','content_frame')
                menue.add(15,11,'Lieferanten','supmgr.php"','','content_frame')
                document.write(menue);
                //-->
              </script>
            <br>
            <a href="javascript:menue.openAll();">Alle Anzeigen</a> | <a href="javascript:menue.closeAll();">Alle Schliessen</a>
          </div>
         <!-- </base> -->
        </td>
    </tr>
</table>

</body>
</html>
