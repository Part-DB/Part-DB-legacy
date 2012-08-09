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
    include("config.php");
    partdb_init();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
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
        <?php 
            print parts_count_sum_value();
            print " ". $currency .PHP_EOL;
        ?>
        <br>

        <b>Mit Preis erfasste Bauteile:</b>
        <?php print parts_count_with_prices(); ?>
        <br>
        <br>

        <b>Anzahl der verschiedenen Bauteile:</b>
        <?php print parts_count(); ?>
        <br>

        <b>Anzahl der vorhandenen Bauteile:</b>
        <?php print parts_count_sum_instock(); ?>
        <br>
        <br>

        <b>Anzahl der Kategorien:</b>
        <?php print categories_count(); ?>
        <br>
        
        <?php if (! $disable_footprints) { ?>
            <b>Anzahl der Footprints:</b>
            <?php print footprint_count(); ?>
            <br>
        <?php } ?>

        <b>Anzahl der Lagerorte:</b>
        <?php print location_count(); ?>
        <br>

        <b>Anzahl der Lieferanten:</b>
        <?php print suppliers_count(); ?>
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

        function count_dir_entries( $dir) 
        { 
            $count = 0;

            $handle = opendir( $dir); 
            while ( $entry = readdir( $handle)) 
            { 
                if ( $entry != "." && $entry != ".." && $entry != ".svn") 
                { 
                    if ( is_dir( $dir.$entry))
                    { 
                        $count += count_dir_entries( $dir.$entry.'/'); 
                    }
                    else
                    { 
                        $count++;
                    } 
                }
            } 
            closedir( $handle); 
            return( $count);
        } 
        
        $dir = "tools/footprints/";
        echo count_dir_entries( $dir);

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
