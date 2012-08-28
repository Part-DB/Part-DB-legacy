<?
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

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

    $Id: getimage.php 511 2012-08-04 weinbauer73@gmail.com $
*/

    require_once ('lib.php');

    if (isset($_REQUEST["pid"]))
    {
        $pict_id_query = "SELECT pictures.id FROM pictures WHERE (pictures.part_id=". smart_escape($_REQUEST["pid"]). ") AND (pictures.pict_type='P') ORDER BY pictures.pict_masterpict DESC LIMIT 1;";
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
        $part_id_query = "SELECT pictures.part_id FROM pictures WHERE (pictures.id=". smart_escape($_REQUEST["pict_id"]). ") AND (pictures.pict_type='P') LIMIT 1;";
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
    
    $pict_query = "SELECT pictures.pict_fname FROM pictures WHERE (pictures.id=". smart_escape($pictID). ") LIMIT 1;";
    debug_print ($pict_query);
    $r = mysql_query ($pict_query); 
    if (mysql_num_rows($r))
    {
        $d = mysql_fetch_row($r);
        $pict_fname = "img/".smart_unescape($d[0]); // filename is only hex, but it doesn't hurt anyway
    }
    else
        print "No picture";

    $f = fopen($pict_fname, 'rb');
    header("content-type: image/jpeg");
    fpassthru($f);
?>
