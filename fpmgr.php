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
    include('lib.php');
    partdb_init();

    /*
     * In some cases a confirmation question has to be displayed.
     */
    $special_dialog = false;

    /*
     * this is the dispatcher ...
     */
    $action = 'default';
    if ( isset( $_REQUEST["add"]))                              { $action = 'add';}
    if ( isset( $_REQUEST["delete"]))                           { $action = 'delete';}
    if ( isset( $_REQUEST["rename"]))                           { $action = 'rename';}
    if ( isset( $_REQUEST["new_filename"]))                     { $action = 'new_filename';}
    if ( isset( $_REQUEST["new_parent"]))                       { $action = 'new_parent';}
    if ( isset( $_REQUEST["save_proposed_filenames"]))          { $action = 'save_proposed_filenames';}
    if ( isset( $_REQUEST["save_all_proposed_filenames"]))      { $action = 'save_proposed_filenames';}

    $footprint_sel = isset( $_REQUEST["footprint_sel"]) ? $_REQUEST["footprint_sel"] : -1;
    $parentnode    = isset( $_REQUEST["parentnode"])    ? $_REQUEST["parentnode"] : 0;

    if ( $action == 'add')
    {
        footprint_add( $_REQUEST["new_footprint"], $_REQUEST["new_footprint_filename"], $parentnode);
    }
   

    if ( $action == 'delete')
    {
        /*
         * Delete a footprint
         * Don't delete when there are parts use this footprint
         */
        $result = parts_select_footprint( $footprint_sel); 
        $ncol   = mysql_num_rows( $result);
        if ( $ncol > 0)
        {
            $special_dialog = true;

            // catch up to three examples, where footprint is in use
            for ($i = 0; ($i < $ncol) and ($i < 3); $i++)
            {
                $data      = mysql_fetch_assoc( $result);
                $example[] = $data['name'];
            }
            $example = implode( ', ', $example);

            // give warning message
            print "<html><body>".
                "<div style=\"text-align:center;\">".
                "<div style=\"color:red;font-size:x-large;\">Footprint kann nicht gel&ouml;scht werden!</div>";
            if ($ncol == 1)
                print "Es gibt noch 1 Teil, das diesen Footprint verwendet: ". $example;
            else
                print "Es gibt noch ". $ncol ." Teile, die diesen Footprint verwenden (z.B.: ". $example. ").";
            print "<form method=\"get\" action=\"\">".
                "<input type=\"submit\" value=\"OK\">".
                "</form></div>".
                "</body></html>";
        }
        else
        {
            // delete footprint
            footprint_del( $footprint_sel);
        }
    }


    if ( $action == 'rename')
    {
        footprint_rename( $footprint_sel, $_REQUEST["new_name"]);
    }
   
    if ( $action == 'new_filename')
    {
        footprint_new_filename( $footprint_sel, $_REQUEST["new_filename_edit"]);
    }

    if ( $action == 'new_parent')
    {
        footprint_new_parent( $footprint_sel, $parentnode);
    }
    
    if ( $action == 'save_proposed_filenames')
    {
        $query  = "SELECT id, name, filename FROM footprints";
        $result = mysql_query( $query) or die( mysql_error());
        while ( $data = mysql_fetch_assoc( $result))
        {
            $name = smart_unescape($data['name']);
            $filename = smart_unescape($data['filename']);
            $id = smart_unescape($data['id']);
            
            if ((file_exists(smart_unescape($filename)) == false) && ($filename != ""))
            {
                $checkbox_checked = isset( $_REQUEST["filename_checkbox_".$id]) ? $_REQUEST["filename_checkbox_".$id] : false;
                
                if (($checkbox_checked == true) || (isset($_REQUEST["save_all_proposed_filenames"])))
                {
                    if (isset($_REQUEST["proposed_filename_".$id]))
                    {
                        footprint_new_filename($id, $_REQUEST["proposed_filename_".$id]);
                    }
                }
            }
            
        }
    }
    
    function list_defect_filenames($id_array)
    {
        $files = find_all_files("tools/footprints/", ".png");
        $files = array_merge($files, find_all_files("media/", ".png"));
                
        $row_odd = true;
        foreach ($id_array as $id)
        {
            $data = footprint_select($id);
            
            $name = smart_unescape($data['name']);
            $filename = smart_unescape($data['filename']);
            
            if ((file_exists($filename) == false) && ($filename != ""))
            {
                $proposed_filenames = search_for_files($files, basename($filename, ".png"));
                          
                // the alternating background colors are created here
                print "<tr class=\"".( $row_odd ? 'trlist_odd': 'trlist_even')."\">". PHP_EOL;
                $row_odd = ! $row_odd;
                
                // checkboxes + footprint names
                print "<td class=\"tdrow0\">";
                print "<input type=\"checkbox\" ";
                foreach ($proposed_filenames as $file)
                {
                    if (strtolower(basename($file, ".png")) == strtolower(basename($filename, ".png")))
                    {
                        print "checked ";
                        break;
                    }
                }
                print "name=\"filename_checkbox_".$id."\">".footprint_get_full_path($id);
                print "</td>\n";
                
                // broken footprint filenames
                print "<td class=\"tdrow1\">";
                print "<FONT COLOR=\"#FF0000\">".$filename."</FONT>";
                print "</td>\n";
                
                // proposed filenames
                print "<td class=\"tdrow0\">";
                
                if (count($proposed_filenames) == 0)
                {
                    print "<input type=\"hidden\" name=\"proposed_filename_".$id."\" value=\"".$proposed_filenames[0]."\">";
                    print "<FONT COLOR=\"#FF0000\">Dateiname l&ouml;schen und sp&auml;ter selber von Hand setzen.</FONT>";
                }
                else
                {
                    print "<select name=\"proposed_filename_".$id."\">";
                    print "<option value=\"\">Dateiname l&ouml;schen und sp&auml;ter selber von Hand setzen.</option>";
  
                    $has_one_selected = false;
                    foreach ($proposed_filenames as $file)
                    {
                        print "<option ";
                        if ((strtolower(basename($file, ".png")) == strtolower(basename($filename, ".png"))) && ($has_one_selected == false))
                        {
                            print "selected ";
                            $has_one_selected = true;
                        }
                        print "value=\"".$file."\">".$file."</option>";
                    }
                    print "</select>";
                }
                
                print "</td>\n";
            }
            
        }
    }

    $data       = footprint_select( $footprint_sel);
    $name       = $data['name'];
    $filename   = $data['filename'];
    $parentnode = $data['parentnode'];

    $size       = min( footprint_count(), 25);
   
    /*
     * Don't show the default text when there's a msg.
     */
    if ($special_dialog == false)
    {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Footprints</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<div class="outer">
    <h2>Footprint anlegen</h2> 
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td>&Uuml;bergeordnete Footprinthierarchie ausw&auml;hlen:</td>
                    <td>
                        <select name="parentnode">
                        <option value="0">root node</option>
                        <?PHP footprint_build_tree( 0, 0, $parentnode); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Neuer Footprint-Name:</td>
                    <td>
                        <input type="text"     name="new_footprint">
                    </td>
                </tr>
                <tr>
                    <td>Neuer Footprint-Dateiname:</td>
                    <td>
                        <input type="text"     name="new_footprint_filename">
                        <input type="submit"   name="add" value="Anlegen">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>


<div class="outer">
    <h2>Footprints umbenennen/l&ouml;schen/sortieren</h2>
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td rowspan="5">
                        Zu bearbeitenden <br> Footprint w&auml;hlen:<br>
                        <select name="footprint_sel" size="<?php print $size;?>" onChange="this.form.submit()">
                        <?php footprint_build_tree( 0, 0, $footprint_sel, true); ?>
                        </select>
                        <br>
                        (rot = ohne Bild)
                    </td>
                    <td>
                        Neuer Name:<br>
                        <input type="text"   name="new_name" value="<?php print $name; ?>">
                        <input type="submit" name="rename" value="Umbenennen">
                    </td>
                </tr>
                <tr>
                    <td>
                        Neuer Dateiname:<br>
                        <input type="text"   name="new_filename_edit" value="<?php print $filename; ?>">
                        <input type="submit" name="new_filename" value="Umbenennen">
                    </td>
                </tr>
                <tr>
                    <td>
                        Bild:<br>
                        <img align="middle" height="70" src="<?php print $filename ?>" alt="">
                    </td>
                </tr>
                <tr>
                    <td>
                        Neuer &uuml;bergeordneter Footprint:<br>
                        <select name="parentnode">
                        <option value="0">root node</option>
                        <?php footprint_build_tree( 0, 0, $parentnode); ?>
                        </select>
                        <input type="submit" name="new_parent" value="Umsortieren">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="delete" value="L&ouml;schen">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<div class="outer">
    <h2>Fehlerhafte Dateinamen</h2>
    <div class="inner">
        <?php
            $defect_filename_id_array = footprint_get_defect_filename_ids();
            if (count($defect_filename_id_array) > 0)
            {
        ?>
                <br>Die Dateinamen der folgenden Footprints konnten keiner Bilddatei zugeordnet werden.<br>
                Bitte &uuml;berpr&uuml;fen bzw. korrigieren Sie die vorgeschlagenen Dateien kurz, um sie dann zu &uuml;bernehmen.<br>
                Bereits gesetzte Haken bedeuten, dass f&uuml;r die jeweiligen Footprints exakt gleichnamige Dateien gefunden wurden.<br><br>
                <form action="" method="POST">
                    <TABLE>
                        <tr class="trcat">
                            <td>Footprint-Name</td>
                            <td>Fehlerhafter Dateiname</td>
                            <td>Vorgeschlagene Dateinamen</td>
                        </tr>
                        <?php list_defect_filenames($defect_filename_id_array); ?>
                    </TABLE>
                    <br> 
                    Vorgeschlagene Dateinamen &uuml;bernehmen:
                    <input type="submit" name="save_proposed_filenames" value="Nur die markierten">   
                    <input type="submit" name="save_all_proposed_filenames" value="Alle">    
                </form>
        <?php
            }
            else
                print "Keine fehlerhaften Dateinamen gefunden.";
        ?>
    </div>
</div>

</body>
</html>
<?php } ?>
