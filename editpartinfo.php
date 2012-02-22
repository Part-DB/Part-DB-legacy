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

    /*
     * If there's a confirmation question or if the part has been
     * deleted, don't output the normal dialog but something else.
     * special_dialog == 0: output normal stuff
     * special_dialog != 0: don't output normal stuff
     */
    $special_dialog = 0;

    // set action to default, if not exists
    $action = ( isset( $_REQUEST["action"]) ? $_REQUEST["action"] : 'default');
    $pid    = ( isset( $_REQUEST["pid"])    ? $_REQUEST["pid"]    : '-1');


    if ( strcmp ($action, "edit") == 0 )
    {
        $query = 
            "UPDATE parts ".
            "SET name=".        smart_escape($_REQUEST["p_name"])           .",".
            "instock=".         smart_escape($_REQUEST["p_instock"])        .",".
            "mininstock=".      smart_escape($_REQUEST["p_mininstock"])     .",".
            "id_footprint=".    smart_escape($_REQUEST["p_footprint"])      .",".
            "id_storeloc=".     smart_escape($_REQUEST["p_storeloc"])       .",".
            "id_supplier=".     smart_escape($_REQUEST["p_supplier"])       .",".
            "supplierpartnr=".  smart_escape($_REQUEST["p_supplierpartnr"]) .",".
            "comment=".         smart_escape($_REQUEST["p_comment"])        ." ".
            "WHERE id=".        smart_escape($pid)                          ." ".
            "LIMIT 1;";
        debug_print ($query);
        mysql_query ($query);
        print "<script>window.close();</script>\n";
    }
    else if ( strcmp ($action, "edit_category") == 0 )
    {
        $query = "UPDATE parts SET id_category=". smart_escape($_REQUEST["p_category"]) ." WHERE id=". smart_escape( $pid) ." LIMIT 1;";
        debug_print ($query);
        mysql_query ($query);
        print "<script>window.close();</script>\n";
    }
    else if ( strcmp ($action, "ds_add") == 0 )
    {
        // add ds_path if requested (use_ds_path)
        $ds     = ( strcmp( $_REQUEST["use_ds_path"], "true") == 0 ) ? $_REQUEST["ds_path"] : '';
        $ds_url = $_REQUEST["ds_url"];
        $query = "INSERT INTO datasheets (part_id,datasheeturl) VALUES (". smart_escape( $pid) .",". smart_escape($ds.$ds_url) .");";
        debug_print ($query);
        mysql_query ($query);

    }
    else if ( strcmp ($action, "ds_del") == 0 )
    {
        // delete datasheet from local directory
        $query = "SELECT datasheeturl FROM datasheets WHERE id=". smart_escape($_REQUEST["ds_id"]) ." LIMIT 1;";
        $r = mysql_query( $query);
        $d = mysql_fetch_row ($r);
        $filename = "/home/eparts". $d[0];
        while( is_file( $filename) == true)
        {
            chmod( $filename, 0666);
            unlink( $filename);
        }

        $query = "DELETE FROM datasheets WHERE id=". smart_escape($_REQUEST["ds_id"]) ." LIMIT 1;";
        debug_print ($query);
        mysql_query ($query);

    }
    else if (strcmp ($action, "part_del") == 0)
    {
        if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
        {
            /* display the confirmation text */
            $special_dialog = 1;
            print "<html><body class=\"body\"><link rel=\"StyleSheet\" href=\"css/partdb.css\" type=\"text/css\" /><div style=\"text-align:center;\">\n";
            print "<table class=\"table\">\n";
            print "<tr><td class=\"tdtop\"><div style=\"color:red;\">M&ouml;chten Sie das Bauteil &quot;". lookup_part_name( $pid) ."&quot; wirklich l&ouml;schen? </td></tr>\n";
            print "<tr><td class=\"tdtext\"><table><tr><td></div>Der L&ouml;schvorgang ist irreversibel!</td></tr>\n";
            print "<tr><td><form action=\"\" method=\"post\"><input type=\"hidden\" name=\"pid\" value=\"". $pid ."\"></td></tr>\n";
            print "<tr><td><input type=\"hidden\" name=\"action\" value=\"part_del\"><input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen!\"><input type=\"submit\" name=\"del_ok\" value=\"L&ouml;schen!\"></td></tr>\n";
            print "</table></td></tr></table></form></div></body></html>\n";
        }
        else if (isset($_REQUEST["del_ok"]))
        {
            /* the user said it's OK to delete the part ... */
            // no LIMIT here because every part can have multiple datasheets
            $query = "DELETE FROM datasheets WHERE part_id=". smart_escape( $pid) .";";
            debug_print ($query);
            mysql_query ($query);
            $query = "DELETE FROM parts WHERE id=". smart_escape( $pid). " LIMIT 1";
            debug_print ($query);
            mysql_query ($query);
            $special_dialog = 1;
            print "<script>window.close();</script>";
        }
    }
    else if ( strcmp ($action, "img_mgr") == 0 )
    {
        /*
         * Set the default ("master") picture.
         * The master picture is the picture whose thumbnail
         * is shown in the part list.
         */
        if (isset($_REQUEST["default_img"]))
        {
            $query = "UPDATE pictures SET pict_masterpict=0 WHERE part_id=". smart_escape( $pid) .";";
            debug_print ($query);
            mysql_query ($query);
            $query = "UPDATE pictures SET pict_masterpict=1 WHERE id=". smart_escape($_REQUEST["default_img"]) .";";
            debug_print ($query);
            mysql_query ($query);
        }   
        /* check if the user wants to delete an image */
        if (isset($_REQUEST["del_img"]))
        {
            $img_del_id_array = $_REQUEST["del_img"];
            if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
            {
                /* print the confirmation text */
                $special_dialog = 1;
                print "<html><body class=\"body\"><link rel=\"StyleSheet\" href=\"css/partdb.css\" type=\"text/css\"/>";
                print "<table class=\"table\">";
                print "<tr><td class=\"tdtop\"><div style=\"color:red\">M&ouml;chten Sie das ausgew&auml;hlte Bild/die ausgew&auml;hlen Bilder wirklich l&ouml;schen?</div></td></tr>";
                print "<tr><td class=\"tdtext\"><table><tr><td>Der L&ouml;schvorgang ist irreversibel!</td></tr>";
                print "<tr><td><form action=\"\" method=\"post\"><input type=\"hidden\" name=\"pid\" value=\"". $pid ."\"><input type=\"hidden\" name=\"action\" value=\"img_mgr\">";
                for ($i = 0; $i < count($img_del_id_array); $i++)
                {
                    print "<input type=\"hidden\" name=\"del_img[]\" value=\"". $img_del_id_array[$i] ."\">";
                    print "<input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen!\"><input type=\"submit\" name=\"del_ok\" value=\"L&ouml;schen!\"></form></div></body></html>";
                }
                print "</form></td></tr></table></td></tr></table>";
            }
            else if (isset($_REQUEST["del_ok"]))
            {
                /* user OK'd the action ...*/
                for ($i = 0; $i < count($img_del_id_array); $i++)
                {
                    // delete only the images, the thumbsnails will expire automatically
                    $query = "DELETE FROM pictures WHERE id=". smart_escape($img_del_id_array[$i]) ." LIMIT 1;";
                    debug_print ($query);
                    mysql_query ($query);
                }
            }
        }
    }
    else if ( strcmp ($action, "img_add") == 0 )
    {
        if (is_uploaded_file($_FILES['uploaded_img']['tmp_name']))
        {
            /*
             * split the file name into its parts and create
             * a unique filename.
             */
            $a = explode(".",$_FILES['uploaded_img']['name']);
            $fname = "img_";
            $fname .= md5_file($_FILES['uploaded_img']['tmp_name']);
            if (($a[count($a)-1] == "jpg") || ($a[count($a)-1] == "jpeg"))
            {
                $fname .= ".jpg";
            }
            else if ($a[count($a)-1] == "gif")
            {
                $fname .= ".gif";
            }
            else if ($a[count($a)-1] == "png")
            {
                $fname .= ".png";
            }
            // FIXME: Some error handling required (for example:
            // unknown file type etc. pp.
            move_uploaded_file($_FILES['uploaded_img']['tmp_name'], "img/".$fname);
            chmod ("img/" .$fname, 0664);
            $query = "INSERT INTO pictures (part_id,pict_fname) VALUES (". smart_escape( $pid) .",". smart_escape($fname) .")";
            debug_print($query);
            mysql_query($query);
        }
    }
    else if ( strcmp ($action, "price_del") == 0 )
    {
        /*
         * If everythink is OK (DB consistency, no bugs in the
         * software, ...) every part only has one price "tag".
         * So we add LIMIT 1 to protect from run-away queries.
         */
        $query = "DELETE FROM preise WHERE part_id=". smart_escape( $pid) ." LIMIT 1;";
        debug_print($query);
        mysql_query($query);
    }
    else if ( strcmp ($action, "price_add") == 0 )
    {
        /*
         * See if the price is a valid (floating point) number ...
         * Actually this code snippet only checks for the beginning,
         * not the entire text. I'm no RegEx expert, so maybe someone
         * could replace this with a better one!
         * (http://www.regular-expressions.info/floatingpoint.html)
         */
        if (preg_match("/^[-+]?[0-9]*\.?[0-9]+/", $_REQUEST["price"]) == true)
        {
            $_REQUEST["price"] = str_replace(',', '.', $_REQUEST["price"]);
            /* Before adding the new price, delete the old one! */
            $query = "DELETE FROM preise WHERE part_id=". smart_escape( $pid) ." LIMIT 1;";
            debug_print($query);
            mysql_query($query);
            $query = "INSERT INTO preise (part_id,ma,preis,t) VALUES (". smart_escape( $pid) .", 1, ". smart_escape($_REQUEST["price"]) .", NOW());";
            debug_print($query);
            mysql_query($query);
        }
    }
    if ($special_dialog == 0)
    {   
    ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Angaben ver&auml;ndern</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <script type="text/javascript" src="popup.php"></script>
    <script type="text/javascript">

        function validateNumber(evt) 
        {
            var theEvent = evt || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode( key );
            var regex = /[0-9]|\./;
            if( !regex.test(key) ) 
            {
                theEvent.returnValue = false;
                if(theEvent.preventDefault) theEvent.preventDefault();
            }
        }
    
        function switch_ds_path() 
        {
            if(document.ds.use_ds_path.checked)
            {
                document.ds.ds_path.disabled=false;
                document.getElementById('URL').style.display='none';
                document.getElementById('file').style.display='block';
            }
            else
            {
                document.ds.ds_path.disabled=true;
                document.getElementById('URL').style.display='block';
                document.getElementById('file').style.display='none';
            }
        }
    </script> 
</head>
<body class="body" onload="switch_ds_path()">

<div class="outer">
    <h2>&Auml;ndere Detailinfos von &quot;<?PHP print lookup_part_name( $pid); ?>&quot;</h2>
    <div class="inner">

        <form action="" method="get">
            <input type="hidden" name="pid" value="<?PHP print $pid; ?>">
            <table>
                <?php  
                $query = "SELECT".
                    " parts.id,".
                    " parts.name,".
                    " parts.instock,".
                    " parts.mininstock,".
                    " parts.id_footprint,".
                    " parts.id_storeloc,".
                    " parts.id_supplier AS 'supplier',".
                    " parts.supplierpartnr,".
                    " parts.comment".
                    " FROM parts".
                    " WHERE parts.id=". smart_escape( $pid) ." LIMIT 1;";
                $result = mysql_query ($query);
                while ( ($data = mysql_fetch_assoc( $result)) )
                {
                    ?>
                    <tr>
                        <td><b>Name:</b></td>
                        <td><input name='p_name' size='40' value='<?php print smart_unescape( $data['name']); ?>'></td>
                    </tr>
                    <tr>
                        <td><b>Vorhanden:</b></td>
                        <td><input name='p_instock' size='5' onkeypress="validateNumber(event)" value='<?php print smart_unescape( $data['instock']); ?>'></td>
                    </tr>
                    <tr>
                        <td><b>Min. Bestand:</b></td>
                        <td><input name='p_mininstock' size='5' onkeypress="validateNumber(event)" value='<?php print smart_unescape( $data['mininstock']); ?>'></td>
                    </tr>
                    <tr>
                        <td><b>Footprint:</b></td>
                        <td><select name='p_footprint'>
                            <option value=""></option>
                            <?php footprint_build_tree( 0, 1, $data['id_footprint']); ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Lagerort:</b></td>
                        <td><select name='p_storeloc'>
                            <option value=""></option>
                            <?php location_tree_build( 0, 1, $data['id_storeloc'], false); ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Lieferant:</b></td>
                        <td><select name='p_supplier'>
                            <option value=""></option>
                            <?php suppliers_build_list( $data['supplier']); ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Bestell-Nr.:</b></td>
                        <td><input name='p_supplierpartnr' value='<?php print smart_unescape( $data['supplierpartnr']); ?>'></td>
                    </tr>
                    <tr>
                        <td valign='top'><b>Kommentar:</b></td>
                        <td><textarea name='p_comment' rows=2 cols=20><?php print smart_unescape( $data['comment']); ?></textarea></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td>
                        <input type="hidden" name="action" value="edit">
                        <input type="submit" value="&Auml;ndern!">
                    </td>
                </tr>
            </table>
            </form>

            <form  action="" method="get">
            <table>
                <tr>
                    <td>
                        <b>Kategorie:</b>
                        <input type="hidden" name="pid" value="<? print $pid; ?>">
                    </td>
                    <td>
                        <select name='p_category'>
                        <option value="0">root node</option>
                        <?php categories_build_tree( 0, 1, part_get_category_id( $pid)); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" name="action" value="edit_category">
                        <input type="submit" value="&Auml;ndern!">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<div class="outer">
    <h2>Preisinfos</h2>
    <div class="inner">
        <?PHP
            $q = "SELECT id,preis,ma FROM preise WHERE part_id=". smart_escape( $pid) ." ORDER BY ma DESC;";
            $r = mysql_query($q);
            if (mysql_num_rows($r) > 0)
            {
                /*
                * There's some information in the table ...
                * Because we assume that only one entry is possible,
                * we display the manual entry, if there's one manual
                * and one automatically added entry entry.
                */
                $d = mysql_fetch_row($r);
                $d[1] = str_replace('.', ',', $d[1]);
                print "<b>Preis:</b> ". smart_unescape($d[1]);
                if ($d[2] == 1)
                {
                    //print " (manuell)";
                    include( "config.php");
                    print " ".$currency."<br>";
                }
            ?>
                <form action="" method="get">
                <input type="hidden" name="pid" value="<?PHP print $pid; ?>">
                <input type="hidden" name="action" value="price_del"><br>
                <input type="submit" value="L&ouml;sche Preisinfo!">
                </form>
            <?PHP
            }
            else
            {
            print "Keine Preisinfos vorhanden!<br>";
            }
            ?>
            <br>
            <form action="" method="get">
            <input type="hidden" name="pid" value="<?PHP print $pid; ?>">
            <input type="hidden" name="action" value="price_add">
            <b>Preis:</b> <input type="text" name="price" size="8"><br><br>
            <input type="submit" value="Preiseingabe!">
            </form>
    </div>
</div>


<div class="outer">
    <h2>Bilder</h2>
    <div class="inner">
        <table>
            <tr><td>
            <?PHP
            if (has_image( $pid))
            {
                // there's at least one picture
                ?>
                <form action="" method="get">
                <input type="hidden" name="pid" value="<?PHP print $pid; ?>">
                <input type="hidden" name="action" value="img_mgr">
                <tr>
                <td>&nbsp;</td><td>&quot;Master-Bild&quot;</td><td>L&ouml;schen</td>
                </tr>
                <?PHP
                $query = "SELECT id FROM pictures WHERE ((pictures.pict_type='P') AND (pictures.part_id=". smart_escape( $pid) .")) ORDER BY pictures.pict_masterpict DESC, pictures.id ASC;";
                $r_img = mysql_query($query);
                $ncol = mysql_num_rows($r_img);
                for ($i = 0; $i < $ncol; $i++)
            {
                $d_img = mysql_fetch_row($r_img);
                print "<tr>\n";
                print "<td><a href=\"javascript:popUp('getimage.php?pict_id=". smart_unescape($d_img[0]). "');\"><img src=\"getimage.php?pict_id=". smart_unescape($d_img[0]). "&maxx=200&maxy=150\"></a></td>\n";
                print "<td><input type=\"radio\" name=\"default_img\" value=\"". smart_unescape($d_img[0]) ."\"></td>\n";
                print "<td><input type=\"checkbox\" name=\"del_img[]\" value=\"". smart_unescape($d_img[0]). "\"></td>\n";
                print "</tr>\n";
            }
            ?>
            <tr><td><input type="submit" value="F&uuml;hre &Auml;nderungen durch!"></td></tr>
            </form>
            <?PHP
            }
            else
            {
                // no pictures for this part
                print "Kein Bild vorhanden!";
            }
            ?>
            </td></tr>
            <tr><td>
            Hier k&ouml;nnen Sie Bilder hochladen. Im Moment werden JPG, PNG und GIF Dateien unterst&uuml;tzt.
            <form enctype="multipart/form-data" action="" method="post">
            <input type="hidden" name="pid" value="<?PHP print $pid; ?>">
            <input type="hidden" name="action" value="img_add">
            <input type="file" name="uploaded_img">
            <input type="submit" value="Lade Bild hoch!">
            </form>
            </td></tr>
        </table>
    </div>
</div>


<div class="outer">
    <h2>Datenbl&auml;tter</h2>
    <div class="inner">
        <table>
            <tr><td>

            <?PHP
            // check for existing datasheets
            $query = "SELECT id,datasheeturl FROM datasheets WHERE part_id=". smart_escape( $pid) .";";
            $r = mysql_query($query);
            $ncol = mysql_num_rows($r);
            if ($ncol > 0)
            {
                print "<form action=\"\" method=\"get\">";
                print "<select name=\"ds_id\" size=\"5\">";
                for ($i = 0; $i < $ncol; $i++)
                {
                    $d = mysql_fetch_row ($r);
                    $sel = ($i == 0) ? 'selected' : '';
                    print "<option ". $sel ." value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
                }
            ?>
                </select>
                <input type="hidden" name="pid" value="<?PHP print $pid; ?>">
                <input type="hidden" name="action" value="ds_del">&nbsp;&nbsp;&nbsp;
                <input type="submit" value="Ausgew&auml;hltes l&ouml;schen!">
                </form>
            <?PHP
            }
            ?>
            
            </td></tr>
            <tr><td>
            <form action="" method="get" name="ds">
            <?php require( "config.php"); ?>
            <input type="checkbox" name="use_ds_path" value="true" onclick="switch_ds_path()" <?php print ($use_datasheet_path ? 'checked' : ''); ?> >Pfad verwenden:&nbsp;&nbsp;&nbsp;
            <input type="text"     name="ds_path"     value="<?php print $datasheet_path; ?>" size="40" <?php print ($use_datasheet_path ? '' : 'disabled'); ?> ><br>
            <div id="URL" style="float:left">URL</div><div id="file" style="float:left">Dateinamen</div>&nbsp;des hinzuf&uuml;genden Datenblattes:<br>
            <input type="text"   name="ds_url" value="" size="40">
            <input type="hidden" name="pid"    value="<?PHP print $pid; ?>">
            <input type="hidden" name="action" value="ds_add">&nbsp;&nbsp;&nbsp;
            <input type="submit" value="Hinzuf&uuml;gen!">
            </form>
            Hinweis:<br>
            Wenn das Datenblatt unter C:\datasheets\foo.pdf zu finden ist, geben Sie als URL file:///C:/datasheets/foo.pdf an.<br>
            Dies scheint allerdings nicht mit allen Browser-Versionen und Acrobat-Reader-Versionen zu funktionieren.
            </td></tr>
        </table>
    </div>
</div>


<div class="outer">
    <h2>Bauteil l&ouml;schen</h2>
    <div class="inner">
        <form action="" method="get">
            <table>
            <tr><td>
            <input type="hidden" name="pid" value="<?PHP print $pid; ?>">
            <input type="hidden" name="action" value="part_del">
            </tr>
            <tr>
            <td>
            Der L&ouml;schvorgang ist nicht r&uuml;ckg&auml;ngig zu machen!
            <p><input type="submit" value="L&ouml;sche Teil!">
            </td>
            </tr>
            </table>
        </form>
    </div>
</div>

</body>
</html>

<?PHP
    }
?>
