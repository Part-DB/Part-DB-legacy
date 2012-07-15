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

    $Id: partinfo.php 480 2012-07-07 12:23:24Z kami89@gmx.ch $

*/

    require_once ('lib.php');

    /*
     * 'action' is a hidden field in the form.
     * The 'instock' value has to be changed before the output begins.
     */

    // set action to default, if not exists
    $action = ( isset( $_REQUEST["action"]) ? $_REQUEST["action"] : 'default');

    if ( $action == "dec")
    {
        parts_stock_decrease( $_REQUEST["pid"], $_REQUEST["n_less"]);
    }

    if ( $action == "inc")
    {
        parts_stock_increase( $_REQUEST["pid"], $_REQUEST["n_more"]);
    }

	$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
	$tmpl -> setVar('head_title', 'Neues Teil');
	$tmpl -> setVar('head_charset', $http_charset);
	$tmpl -> setVar('head_css', $css);
	$tmpl -> setVar('head_popup', true);
	$tmpl -> pparse();

?>
<div class="outer">
    <h2>Detailinfo zu &quot;<?PHP print part_get_name( $_REQUEST["pid"]); ?>&quot;</h2>
    <div class="inner">

        <table>
        <tr valign="top">
        <td>
        <table>
        <?php
        $result = parts_select( $_REQUEST["pid"]);
        while ( $data = mysql_fetch_assoc( $result))
        {
            ?>
            <tr><td><b>Name:</b></td><td><?php         print smart_unescape( $data['name']); ?></td></tr>
            <tr><td><b>Beschreibung:</b></td><td><?php print smart_unescape( $data['description']); ?></td></tr>
            <tr><td><b>Kategorie:</b></td><td><?php    print part_get_category_path( $data['id_category']); ?></td></tr>
            <tr><td><b>Vorhanden:</b></td><td><?php    print smart_unescape( $data['instock']); ?></td></tr>
            <tr><td><b>Min. Bestand:</b></td><td><?php print smart_unescape( $data['mininstock']); ?></td></tr>

            <tr><td><b>Footprint:</b></td><td><?php    print part_get_footprint_path( $data['id_footprint']); ?>
            <?php
            // footprint
            $link = smart_unescape( $data['footprint_filename']);
            if ( file_exists($link))
            {
                print "<img align=\"middle\" height=\"70\" src=\"". $link ."\" alt=\"\">";
            }
            ?>
            </td></tr>

            <tr><td><b>Lagerort:</b></td><td><?php    print part_get_location_path( $data['id_storeloc']). (( $data['location_is_full'] == 1 ) ? ' [voll]' : ''); ?></td></tr>
            <tr><td><b>Lieferant:</b></td><td><?php   print smart_unescape( $data['supplier']); ?></td></tr>
            <tr><td><b>Bestell-Nr.:</b></td><td><?php print smart_unescape( $data['supplierpartnr']) ?></td></tr>
            <tr><td><b>obsolet:</b></td><td><?php     print $data['obsolete'] ? 'ja' : 'nein'; ?></td></tr>

            <?php
            include( "config.php");
            $preis = str_replace('.', ',', $data['price']);
            ?>
            <tr><td><b>Preis:</b></td><td><?php print smart_unescape( $preis) ." ". $currency; ?> &nbsp;</td></tr>
            <tr><td valign="top"><b>Kommentar:</b></td><td><?php print nl2br( smart_unescape( $data['comment'])); ?>&nbsp;</td></tr>

            <?php
        }
        ?>
        </table>
        <br>Angaben <a href="editpartinfo.php?pid=<?PHP print $_REQUEST["pid"]; ?>">ver&auml;ndern</a>
        </td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>
        <table>
        <form action="" method="post">
        <input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
        <input type="hidden" name="action" value="dec">
        <tr><td colspan="2">Teile entnehmen</td></tr>
        <tr valign="top">
        <td>Anzahl:</td><td><input type="text" size="3" name="n_less" value="1"></td>
        </tr><tr><td colspan="2" align="center"><input type="submit" value="Entnehmen!"></td></tr>
        </form>
        <tr><td colspan="2">&nbsp;</td></tr>
        <form action="" method="post">
        <input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
        <input type="hidden" name="action" value="inc">
        <tr><td colspan="2">Teile hinzuf&uuml;gen</td></tr>
        <tr valign="top">
        <td>Anzahl:</td><td><input type="text" size="3" name="n_more" value="1"></td>
        </tr><tr><td colspan="2" align="center"><input type="submit" value="Hinzuf&uuml;gen!"></td></tr>
        </form>
        </table>
        </td>
        </tr>
        </table>
        <?php
        if ( picture_exists( $_REQUEST["pid"]))
        {
            print "<br><b>Bilder:</b><br>". PHP_EOL;

            $result = pictures_select( $_REQUEST["pid"]);

            while ($data = mysql_fetch_assoc( $result))
            {
                $link = "getimage.php?pict_id=". $data['id'];
                print "<a href=\"javascript:popUp('". $link ."')\">".
                    "<img src=\"". $link ."&maxx=200&maxy=150\" alt=\"Zum Vergr&ouml;&szlig;ern klicken!\">".
                    "</a><br>". PHP_EOL;
            }
        }

        ?>
    </div>
</div>
<?php
	$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
	$tmpl -> pparse();
?>
