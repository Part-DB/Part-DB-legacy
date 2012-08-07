<?PHP
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

    $Id: editpartinfo.php 511 2012-08-04 weinbauer73@gmail.com $
*/

    require_once ('lib.php');

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
    $tmpl -> setVar('head_title', $title);
    $tmpl -> setVar('head_charset', $http_charset);
    $tmpl -> setVar('head_theme', $theme);
    $tmpl -> setVar('head_css', $css);
    $tmpl -> setVar('head_menu', true);
    $tmpl -> setVar('head_popup', true);
    $tmpl -> setVar('head_validate', true);
    $tmpl -> pparse();

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
        $p_obsolete  = ( isset( $_REQUEST["p_obsolete"]) ? (bool)$_REQUEST["p_obsolete"] : false);
        $p_visible   = ( isset( $_REQUEST["p_visible"])  ? (bool)$_REQUEST["p_visible"]  : false);
        part_update( $pid,
            $_REQUEST["p_category"],
            $_REQUEST["p_name"],
            $_REQUEST["p_description"],
            $_REQUEST["p_instock"],
            $_REQUEST["p_mininstock"],
            $_REQUEST["p_comment"],
            $p_obsolete,
            $p_visible,
            $_REQUEST["p_footprint"],
            $_REQUEST["p_storeloc"],
            $_REQUEST["p_supplier"],
            $_REQUEST["p_supplierpartnr"]);

        print "<script>window.close();</script>\n";
    }
    else if ( strcmp ($action, "ds_add") == 0 )
    {
        // add ds_path if requested (use_ds_path)
        $ds     = ( strcmp( $_REQUEST["use_ds_path"], "true") == 0 ) ? $_REQUEST["ds_path"] : '';
        $ds_url = $_REQUEST["ds_url"];
        datasheet_add( $pid, $ds.$ds_url);

    }
    else if ( strcmp ($action, "ds_del") == 0 )
    {
        datasheet_delete( $_REQUEST["ds_id"]);

    }
    else if (strcmp ($action, "part_del") == 0)
    {
        if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
        {
            /* display the confirmation text */

            $special_dialog = 1;
            $tmpl = new vlibTemplate(BASE."/templates/$theme/editpartinfo.php/vlib_editpartinfo_del.tmpl");
            $tmpl -> setVar('del_part',true);
            $tmpl -> setVar('part_get_name',part_get_name($pid));
            $tmpl -> pparse();
        }
        else if (isset($_REQUEST["del_ok"]))
        {
            /* the user said it's OK to delete the part ... */
            part_delete( $pid);

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
            picture_set_default( $pid, $_REQUEST["default_img"]);
        }
        /* check if the user wants to delete an image */
        if (isset($_REQUEST["del_img"]))
        {
            $img_del_id_array = $_REQUEST["del_img"];
            if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
            {
                /* print the confirmation text */
                $special_dialog = 1;
                $tmpl = new vlibTemplate(BASE."/templates/$theme/editpartinfo.php/vlib_editpartinfo_del.tmpl");
                $tmpl -> setVar('del_part',false);
                $tmpl -> setVar('pid',$pid);
                $images=array();
                for ($i = 0; $i < count( $img_del_id_array); $i++)
                {
                    $images[]['images']=$img_del_id_array[$i];
                }
                $tmpl -> setLoop('img_del_id_array',$img_del_id_array);
                $tmpl -> pparse();
            }
            else if (isset($_REQUEST["del_ok"]))
            {
                /* user OK'd the action ...*/
                for ($i = 0; $i < count($img_del_id_array); $i++)
                {
                    // delete only the images, the thumbsnails will expire automatically
                    picture_delete( $img_del_id_array[$i]);
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
            picture_add( $pid, $fname);
        }
    }
    else if ( strcmp ($action, "price_del") == 0 )
    {
        /*
         * If everythink is OK (DB consistency, no bugs in the
         * software, ...) every part only has one price "tag".
         * So we add LIMIT 1 to protect from run-away queries.
         */
        price_delete( $pid);
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
        if ( preg_match("/^[-+]?[0-9]*\.?[0-9]+/", $_REQUEST["price"]) == true)
        {
            $_REQUEST["price"] = str_replace(',', '.', $_REQUEST["price"]);
            /* Before adding the new price, delete the old one! */
            price_delete( $pid);
            price_add( $pid, $_REQUEST["price"]);
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
    <script type="text/javascript" src="validatenumber.js"></script>
    <script type="text/javascript">
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
    <h2>&Auml;ndere Detailinfos von &quot;<?PHP print part_get_name( $pid); ?>&quot;</h2>
    <div class="inner">

        <form action="" method="get">
            <input type="hidden" name="pid" value="<?PHP print $pid; ?>">
            <table>
                <?php

                $result = parts_select( $pid);
                while ( ($data = mysql_fetch_assoc( $result)) )
                {
                    ?>
                    <tr>
                        <td><b>Name:</b></td>
                        <td><input name='p_name' size='20' value='<?php print smart_unescape( $data['name']); ?>'></td>
                    </tr>
                    <tr>
                        <td><b>Beschreibung:</b></td>
                        <td><input name='p_description' size='40' value='<?php print smart_unescape( $data['description']); ?>'></td>
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
                            <?php suppliers_build_list( $data['id_supplier']); ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Bestell-Nr.:</b></td>
                        <td><input name='p_supplierpartnr' value='<?php print smart_unescape( $data['supplierpartnr']); ?>'></td>
                    </tr>
                    <tr>
                        <td><b>obsolet:</b></td>
                        <td><input type="checkbox" name="p_obsolete" value="true"<?php print $data['obsolete'] ? 'checked' : ''; ?>></td>
                    </tr>
                    <tr>
                        <td><b>&ouml;ffentlich sichtbar:</b></td>
                        <td><input type="checkbox" name="p_visible" value="true"<?php print $data['visible'] ? 'checked' : ''; ?>></td>
                    </tr>
                    <tr>
                        <td valign='top'><b>Kommentar:</b></td>
                        <td><textarea name='p_comment' rows=2 cols=20><?php print smart_unescape( $data['comment']); ?></textarea></td>
                    </tr>
                    <tr>
                        <td><b>Kategorie:</b></td>
                        <td><select name='p_category'>
                            <option value="0">root node</option>
                            <?php categories_build_tree( 0, 1, part_get_category_id( $pid)); ?>
                            </select>
                        </td>
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
    </div>
</div>

<div class="outer">
    <h2>Preisinfos</h2>
    <div class="inner">
        <?php
            $r = price_select( $pid);
            if ( mysql_num_rows( $r) > 0)
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
                <input type="hidden" name="pid" value="<?php print $pid; ?>">
                <input type="hidden" name="action" value="price_del"><br>
                <input type="submit" value="L&ouml;sche Preisinfo!">
                </form>
            <?php
            }
            else
            {
            print "Keine Preisinfos vorhanden!<br>";
            }
            ?>
            <br>
            <form action="" method="get">
            <input type="hidden" name="pid" value="<?php print $pid; ?>">
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
            <?php
            if (picture_exists( $pid))
            {
                // there's at least one picture
                ?>
                <form action="" method="get">
                <input type="hidden" name="pid" value="<?php print $pid; ?>">
                <input type="hidden" name="action" value="img_mgr">
                <tr>
                <td>&nbsp;</td><td>&quot;Master-Bild&quot;</td><td>L&ouml;schen</td>
                </tr>
                <?php
                $r_img = pictures_select( $pid);
                $ncol  = mysql_num_rows( $r_img);
                for ($i = 0; $i < $ncol; $i++)
            {
                $d_img  = mysql_fetch_assoc( $r_img);
                $img_id = $d_img['id'];
                print "<tr>". PHP_EOL;
                print "<td><a href=\"javascript:popUp('getimage.php?pict_id=". smart_unescape( $img_id). "');\"><img src=\"getimage.php?pict_id=". smart_unescape( $img_id). "&maxx=200&maxy=150\"></a></td>". PHP_EOL;
                print "<td><input type=\"radio\" name=\"default_img\" value=\"". smart_unescape( $img_id) ."\"></td>". PHP_EOL;
                print "<td><input type=\"checkbox\" name=\"del_img[]\" value=\"". smart_unescape( $img_id). "\"></td>". PHP_EOL;
                print "</tr>". PHP_EOL;
            }
            ?>
            <tr><td><input type="submit" value="F&uuml;hre &Auml;nderungen durch!"></td></tr>
            </form>
            <?php
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
            <input type="hidden" name="pid" value="<?php print $pid; ?>">
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

            <?php
            // check for existing datasheets
            $result = datasheet_select( $pid);
            $ncol = mysql_num_rows($result);
            if ( $ncol > 0)
            {
                print "<form action=\"\" method=\"get\">";
                print "<select name=\"ds_id\" size=\"5\">";
                for ($i = 0; $i < $ncol; $i++)
                {
                    $d = mysql_fetch_assoc( $result);
                    $sel = ($i == 0) ? 'selected' : '';
                    print "<option ". $sel ." value=\"". smart_unescape( $d['id']) ."\">". smart_unescape( $d['datasheeturl']) ."</option>\n";
                }
                ?>
                </select>
                <input type="hidden" name="pid" value="<?php print $pid; ?>">
                <input type="hidden" name="action" value="ds_del">&nbsp;&nbsp;&nbsp;
                <input type="submit" value="Ausgew&auml;hltes l&ouml;schen!">
                </form>
            <?php
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
            <input type="hidden" name="pid"    value="<?php print $pid; ?>">
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
            <input type="hidden" name="pid" value="<?php print $pid; ?>">
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

<?php
    }

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
    $tmpl -> pparse();
?>
