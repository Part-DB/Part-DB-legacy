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

    $Id: gettn.php,v 1.4 2006/05/28 10:41:25 cl Exp $

    Portions of the code are (c) by S. Bechtold,
    http://de.php.net/manual/en/function.imagepng.php
*/
    include ("lib.php");
    partdb_init();
    
    // Standard height & width
    // The user can set them
    if (!isset($_REQUEST["maxx"]))
        $maxX = 32;
    else
        $maxX = $_REQUEST["maxx"];
        
    if (!isset($_REQUEST["maxy"]))
        $maxY = 32;
    else
        $maxY = $_REQUEST["maxy"];
    
    if (isset($_REQUEST["pid"]))
    {
        $pict_id_query = "SELECT pictures.id FROM pictures WHERE (pictures.part_id='". mysql_escape_string ($_REQUEST["pid"]). "') AND (pictures.pict_type='P') ORDER BY pictures.pict_masterpict DESC, pictures.id ASC LIMIT 1;";
        debug_print ($pict_id_query);
        $r = mysql_query($pict_id_query);
        if (mysql_num_rows($r))
        {
            $d = mysql_fetch_row($r);
            $pictID = $d[0];
        }
        else
        {
            print "No picture!!!";
        }
        $partID = $_REQUEST["pid"];
    }
    else if (isset($_REQUEST["pict_id"]))
    {
        $part_id_query = "SELECT pictures.part_id FROM pictures WHERE (pictures.id='". mysql_escape_string ($_REQUEST["pict_id"]). "') AND (pictures.pict_type='P') LIMIT 1;";
        debug_print ($part_id_query);
        $r = mysql_query($part_id_query);
        if (mysql_num_rows($r))
        {
            $d = mysql_fetch_row($r);
            $partID = $d[0];
        }
        else
        {
            print "No picture!!!";
        }
        $pictID = $_REQUEST["pict_id"];
    }
    else
        print "No picture!!!";
    
    $pict_query = "SELECT pictures.pict_fname FROM pictures WHERE (pictures.id='". mysql_escape_string ($pictID). "') LIMIT 1;";
    debug_print ($pict_query);
    $r = mysql_query ($pict_query); 
    if (mysql_num_rows($r))
    {
        $d = mysql_fetch_row($r);
        $pict_fname = "img/" $d[0];
    }
    else
        print "No picture";

    /* see if there's a current thumbnail in the cache */
    $cache_query = "SELECT pictures.id,pictures.pict_fname FROM pictures WHERE ((DATE_SUB(NOW(), INTERVAL 1 DAY) < pictures.tn_t) AND (pictures.tn_obsolete = 0) AND (pictures.tn_pictid='". mysql_escape_string ($pictID). "') AND (pictures.pict_type='T') AND (pictures.pict_width='". mysql_escape_string($maxX) ."') AND (pictures.pict_height='". mysql_escape_string($maxY) ."'));";
    debug_print ($cache_query);
    $r = mysql_query ($cache_query);
    if (mysql_num_rows($r) == 0)
    {
        /*
         * no cache hit or cache contents expired
         * -> recreate the thumbnail
         */
        $pict_cache_fname = md5($pict_fname ."#". time() ."#". $maxX ."#". $maxY ."#". $pictID) .".png";
        $img_data = getImageSize($pict_fname);
        switch ($img_data[2])
        {
            case IMG_GIF:
                // GIF image
                $orig_image = imageCreateFromGIF($pict_fname);
                break;
            case IMG_JPG:
                // JPEG image
                $orig_image = imageCreateFromJPEG($pict_fname);
                break;
            case IMG_PNG:
                // PNG image
                $orig_image = imageCreateFromPNG($pict_fname);
                break;
        }

        // calculate zoom ratio for X and Y and zoom for the picture to
        // fit in the box
        $zoomX = $img_data[0] / $maxX;
        $zoomY = $img_data[1] / $maxY;
        $zoom = max($zoomX, $zoomY);
        $newX = $img_data[0] / $zoom;
        $newY = $img_data[1] / $zoom;
        $posX = ($maxX - $newX) / 2;
        $posY = ($maxY - $newY) / 2;

        // create a image w given sizes
        $theimage = imageCreateTrueColor($maxX, $maxY);

        $cols = explode(",", "0,0,0");
        $bgcolor = imageColorAllocate($theimage, trim($cols[0]), trim($cols[1]), trim($cols[2]));
        imageFill($theimage, 0, 0, $bgcolor);
    
        imageCopyResampled($theimage, $orig_image, $posX, $posY, 0, 0, $newX, $newY, $img_data[0], $img_data[1]);

        imagePNG($theimage, .$pict_cache_fname);

        imageDestroy($theimage);
        imageDestroy($orig_image);

        /*
         * Clean the thumbnail cache.
         * There's the possibility of race conditions because most
         * browsers try to get many pictures at the same time. But
         * deleting a file that does not exist is no problem.
         * Delete only 100 expired thumbnails at once, because file-
         * systems maybe need a long time on a heavy-loaded system.
         */
        $cache_exp_query = "SELECT id,pict_fname FROM pictures WHERE (((DATE_SUB(NOW(), INTERVAL 1 DAY) > pictures.tn_t) OR (tn_obsolete != 0)) AND (pict_type='T')) LIMIT 100;";
        debug_print($cache_exp_query);
        $r_exp = mysql_query($cache_exp_query);
        while ( ($d = mysql_fetch_row($r_exp)) )
        {
            unlink(.$d[1]);
            $del_query = "DELETE FROM pictures WHERE id=". smart_escape($d[0]) ." LIMIT 1;";
            debug_print($del_query);
            mysql_query($del_query);
        }
        
        $cache_update_query = "INSERT INTO pictures (part_id,pict_fname,pict_width,pict_height,pict_type,tn_t,tn_pictid) VALUES ('". mysql_escape_string($partID) ."','". $pict_cache_fname ."','". mysql_escape_string($maxX) ."','". mysql_escape_string($maxY). "','T',NOW(),'". mysql_escape_string($pictID). "');";
        debug_print($cache_update_query);
        mysql_query($cache_update_query);
    }
    else
    {
        $d = mysql_fetch_row($r);
        $pict_cache_fname = $d[1];
    }

    $f = fopen(.$pict_cache_fname, 'rb');
    header("content-type: image/png");
    fpassthru($f);
?>
