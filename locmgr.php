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

    $Id: locmgr.php 392 2012-03-03 06:30:03Z bubbles.red@gmail.com $

*/

    require_once ('lib.php');

    /*
     * In some cases a confirmation question has to be displayed.
     */
    $special_dialog = false;

    /*
     * this is the dispatcher ...
     */
    $action = 'default';
    if ( isset( $_REQUEST["add"]))        { $action = 'add';}
    if ( isset( $_REQUEST["delete"]))     { $action = 'delete';}
    if ( isset( $_REQUEST["rename"]))     { $action = 'rename';}
    if ( isset( $_REQUEST["new_parent"])) { $action = 'new_parent';}
    if ( isset( $_REQUEST["update"]))     { $action = 'update';}

    $series       = isset( $_REQUEST["series"]) ? (bool)$_REQUEST["series"] : false;
    $location_sel = isset( $_REQUEST["location_sel"]) ? $_REQUEST["location_sel"] : -1;
    $parentnode   = isset( $_REQUEST["parentnode"])   ? $_REQUEST["parentnode"] : 0;


    if ( $action == 'add')
    {
        if ( $series)
        {
            // add location series
            $start  = $_REQUEST["series_start"];
            $end    = $_REQUEST["series_end"];
            // determine the width of second argument
            $width  = strlen( (string) $end);
            $format = "%0". (int)$width ."s";

            foreach( range( $start, $end) as $index)
            {
                $new_location = $_REQUEST["new_location"]. sprintf( $format, $index);
                location_add( $new_location, $parentnode);
            }
        }
        else
        {
            // add a location
            location_add( $_REQUEST["new_location"], $parentnode);
        }
    }

    if ( $action == 'delete')
    {
        /*
         * Delete a location.
         * Includes confirmation questions. Don't delete the
         * location when there are parts in this location.
         */
        if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) && $location_sel >= 0)
        {
            $special_dialog = true;
            if ( parts_count_on_storeloc( $location_sel) > 0)
            {
                print "<html><body>".
                    "<div style=\"text-align:center;\">".
                    "<div style=\"color:red;font-size:x-large;\">Lagerort kann nicht gel&ouml;scht werden!</div>".
                    "Es gibt noch Teile, die diesen Lagerort als Ort eingetragen haben.".
                    "<form method=\"get\" action=\"\">".
                    "<input type=\"submit\" value=\"OK\">".
                    "</form></div>".
                    "</body></html>";
            }
            else
            {
                print "<html><body>".
                    "<div style=\"text-align:center;\">".
                    "<div style=\"color:red;font-size:x-large;\">M&ouml;chten Sie den Lagerort &quot;". smart_unescape( location_get_name( $location_sel)) ."&quot; wirklich l&ouml;schen?</div>".
                    "Der L&ouml;schvorgang ist irreversibel!".
                    "<form action=\"\" method=\"post\">".
                    "<input type=\"hidden\" name=\"location_sel\" value=\"". $location_sel ."\">".
                    "<input type=\"hidden\" name=\"delete\"  value=\"x\">".
                    "<input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen\">".
                    "<input type=\"submit\" name=\"del_ok\"  value=\"L&ouml;schen\">".
                    "</form></div>".
                    "</body></html>";
            }
        }
        else if (isset($_REQUEST["del_ok"]))
        {
            // the user said it's OK to delete the location
            location_delete( $location_sel);
        }
    }

    if ( $action == 'rename')
    {
        location_rename( $location_sel, $_REQUEST["new_name"]);
    }


    if ( $action == 'new_parent')
    {
        /* resort */
        location_new_parent( $location_sel, $parentnode);
    }

    if ( $action == 'update')
    {
        $value = isset( $_REQUEST["is_full"]) ? $_REQUEST["is_full"] == "true" : false;
        location_mark_as_full( $location_sel, $value);
    }

    $data       = location_select( $location_sel);
    $name       = $data['name'];
    $parentnode = $data['parentnode'];

    $size       = min( location_count(), 30);



    /*
     * Don't show the default text when there's a msg.
     */
    if ($special_dialog == false)
    {

        $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
        $tmpl -> setVar('head_title', 'Lagerorte');
        $tmpl -> setVar('head_charset', $http_charset);
        $tmpl -> setVar('head_theme', $theme);
        $tmpl -> setVar('head_css', $css);
        $tmpl -> pparse();

?>
<script type="text/javascript">
    function switch_series()
    {
        if(document.create.series.checked)
        {
            document.create.series_start.disabled=false;
            document.create.series_end.disabled=false;
        }
        else
        {
            document.create.series_start.disabled=true;
            document.create.series_end.disabled=true;
        }
    }
</script>


<div class="outer">
    <h2>Lagerorte anlegen</h2>
    <div class="inner">
        <form action="" method="post" name="create">
            <table>
                <tr>
                    <td>&Uuml;bergeordneten Lagerort ausw&auml;hlen:</td>
                    <td>
                        <select name="parentnode">
                        <option value="0">root node</option>
                        <?PHP location_tree_build( 0, 0, $parentnode); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Neuer Lagerort:</td>
                    <td>
                        <input type="text"     name="new_location">
                        <input type="submit"   name="add" value="Anlegen">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="series" value="true" onclick="switch_series()">Serie erzeugen&nbsp;&nbsp;
                    </td>
                    <td>
                        von <input type="text" name="series_start" size="4" value="1" disabled>
                        bis <input type="text" name="series_end"   size="4" value="3" disabled>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>


<div class="outer">
    <h2>Lagerorte umbenennen/l&ouml;schen/sortieren</h2>
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td rowspan="4">
                        Zu bearbeitenden Lagerort w&auml;hlen:<br>
                        <select name="location_sel" size="<?php print $size;?>" onChange="this.form.submit()">
                        <?PHP location_tree_build( 0, 0, $location_sel); ?>
                        </select>
                    </td>
                    <td>
                        Neuer Name:<br>
                        <input type="text" name="new_name" value="<?php print $name; ?>">
                        <input type="submit" name="rename" value="Umbenennen">
                    </td>
                </tr>
                <tr>
                    <td>
                        Neuer &uuml;bergeordneter Lagerort:<br>
                        <select name="parentnode">
                        <option value="0">root node</option>
                        <?PHP location_tree_build( 0, 0, $parentnode); ?>
                        </select>
                        <input type="submit" name="new_parent" value="Umsortieren">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="is_full" value="true">voll<br>
                        <input type="checkbox" name="is_full" value="false">noch Platz<br>
                        <input type="submit"   name="update"  value="Markieren">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="delete" value="L&ouml;schen">
              file:///mnt/server.venus.prv/www/htdocs/part-db/locmgr.php      </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php
        $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
        $tmpl -> pparse();
   }
?>
