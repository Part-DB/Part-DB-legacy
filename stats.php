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

    $Id$
*/
    include('lib.php');
    partdb_init();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/.dtd">
<html>
<head>
    <title>Statistik</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<div class="outer">
    <h2>Statistik</h2>
    <div class="inner">
        <b>Wert aller mit Preis erfassten Bauteile:</b>
        <?PHP
        include('config.php');
        $query = "SELECT SUM(preise.preis*parts.instock) FROM parts LEFT JOIN preise ON parts.id=preise.part_id ORDER BY name ASC;";
        $r = mysql_query ($query);
        $d = mysql_fetch_row ($r);
        print $d[0]." ".$currency;
        ?><br>

        <b>Mit Preis erfasste Bauteile:</b>
        <?PHP
        $i = 0;
        $query = "SELECT preis FROM preise;";
        $r = mysql_query ($query);
        while ( $d = mysql_fetch_row ($r) )
        {
          $i++;
        }
        print $i;
        ?><br>
        <br>

        <b>Anzahl der verschiedenen Bauteile:</b>
        <?php print parts_count(); ?>
        <br>

        <b>Anzahl der vorhandenen Bauteile:</b>
        <?PHP
        $query = "SELECT SUM(instock) FROM parts;";
        $r = mysql_query ($query);
        $d = mysql_fetch_row ($r);
        print $d[0];
        ?><br>
        <br>

        <b>Anzahl der Kategorien:</b>
        <?php print categories_count(); ?>
        <br>

<?php
    if (! $disable_devices) {
?>
        <b>Anzahl der Baugruppen:</b>
        <?php print devices_count(); ?>
        <br>
<?php
    }
?>

        <br>

        <b>Anzahl der hochgeladenen Bilder:</b>
        <?PHP
        $dir = "img/";
        $dh  = opendir($dir);
        while (false !== ($filename = readdir($dh))) 
        {
          $files[] = $filename;
        }
        echo count($files)- 2;
        unset($files);
        ?><br>

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
        ?><br>

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
        ?>
    </div>
</div>

</body>
</html>
