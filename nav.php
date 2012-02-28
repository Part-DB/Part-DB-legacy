<?php
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

    $Id$
*/
    include('config.php');
    include('lib.php');
    partdb_init();


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Navigation</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <script type="text/javascript" src="dtree.js"></script>
</head>
<body class="body">

<div class="outer">
    <h2>Suche
        <span id="homelink">
        <a href="startup.php" target="content_frame"><img src="img/partdb/partdb.png" alt="Home"></a>
        </span>
    </h2>
    <div class="inner">
        <form action="search.php" method="get" target="content_frame">
            <input type="text" name="keyword" size="17" maxlength="20">
            <input type="submit" name="s" value="Los!"><br>
            <div class="search_selection">
                <p><input type="checkbox" name="search_nam" value="true" checked>Name</p>
                <p><input type="checkbox" name="search_loc" value="true" checked>Lagerort</p>
                <p><input type="checkbox" name="search_com" value="true" checked>Kommentar</p>
                <p><input type="checkbox" name="search_sup" value="true"        >Lieferant</p>
                <p><input type="checkbox" name="search_fpr" value="true" checked>Footprint</p>
                <p><input type="checkbox" name="search_snr" value="true"        >Bestellnr.</p>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>

<div class="outer">
    <h2>Kategorien</h2>
    <div class="inner">
        <div class="dtree">
            <script type="text/javascript">
                cat_tree = new dTree('cat_tree');
                cat_tree.add(0,-1,'');
                <?php categories_build_navtree(); ?>
                document.write(cat_tree);
            </script>
            <br>
            <a href="javascript:cat_tree.openAll();">Alle Anzeigen</a> | <a href="javascript:cat_tree.closeAll();">Alle Schliessen</a>
        </div>
    </div>
</div>

<?php
    if (! $disable_devices) {
?>
<div class="outer">
    <h2>Baugruppen</h2>
    <div class="inner">
        <div class="dtree">
            <script type="text/javascript">
                dev_tree = new dTree('dev_tree');
                dev_tree.add(0,-1,'&Uuml;bersicht','device.php','','content_frame');
                <?php devices_build_navtree(); ?>
                document.write( dev_tree);
            </script>
            <br>
            <a href="javascript:dev_tree.openAll();">Alle Anzeigen</a> | <a href="javascript:dev_tree.closeAll();">Alle Schliessen</a>
        </div>
    </div>
</div>

<?php
    }
?>

<div class="outer">
    <h2>Verwaltung / Tools</h2>
    <div class="inner">
        <div class="dtree inner">
            <script type="text/javascript">
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
                    menue.add(10,6,'Hilfe','help.php"','','content_frame');
                <?php } ?>

                menue.add(11,0,'Bearbeiten','','','');
                <?php if (! $disable_devices) { ?>
                    menue.add(12,11,'Baugruppen','devmgr.php"','','content_frame');
                <?php } ?>
                menue.add(13,11,'Lagerorte','locmgr.php"','','content_frame');
                menue.add(14,11,'Footprints','fpmgr.php"','','content_frame');
                menue.add(15,11,'Kategorien','catmgr.php"','','content_frame');
                menue.add(16,11,'Lieferanten','supmgr.php','','content_frame');

                <?php if (! $disable_config) { ?>
                    menue.add(17,0,'Konfiguration','','','');
                    menue.add(18,17,'System',    'config_system.php', '', 'content_frame');
                    menue.add(19,17,'Datenbank', 'config_page.php', '', 'content_frame');
                <?php } ?>
                document.write(menue);
            </script>
            <br>
            <a href="javascript:menue.openAll();">Alle Anzeigen</a> | <a href="javascript:menue.closeAll();">Alle Schliessen</a>
        </div>
    </div>
</div>

</body>
</html>
