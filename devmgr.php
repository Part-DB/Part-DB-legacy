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
    
	$refreshnav = false;
	
    /*
     * In some cases a confirmation question has to be displayed.
     */
    $special_dialog = false;
	
    $action = 'default';
    if ( isset( $_REQUEST["add"]))        { $action = 'add';}
    if ( isset( $_REQUEST["delete"]))     { $action = 'delete';}
    if ( isset( $_REQUEST["rename"]))     { $action = 'rename';}
    if ( isset( $_REQUEST["new_parent"])) { $action = 'new_parent';}

    $dev_sel    = isset( $_REQUEST["dev_sel"])    ? $_REQUEST["dev_sel"]    : -1;
    $parentnode = isset( $_REQUEST["parentnode"]) ? $_REQUEST["parentnode"] : 0;

    if ( $action == 'add')
    {
        $dev_sel    = device_add( $_REQUEST["new_device"], $parentnode);
		$refreshnav = true;
    }
    
    
    if ( $action == 'delete')
    {
        /*
         * Delete a device.
         */
        if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) && $dev_sel >= 0)
        {
            $special_dialog = true;
                print "<html><body>".
                    "<div style=\"text-align:center;\">".
                    "<div style=\"color:red;font-size:x-large;\">M&ouml;chten Sie die Baugruppe &quot;". lookup_device_name( $dev_sel) ."&quot; wirklich l&ouml;schen?</div>".
                    "Der L&ouml;schvorgang ist irreversibel!".
                    "<form action=\"\" method=\"post\">".
                    "<input type=\"hidden\" name=\"dev_sel\" value=\"". $dev_sel ."\">".
                    "<input type=\"hidden\" name=\"delete\"  value=\"x\">".
                    "<input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen\">".
                    "<input type=\"submit\" name=\"del_ok\"  value=\"L&ouml;schen\">".
                    "</form></div>".
                    "</body></html>";
        }
        else if (isset($_REQUEST["del_ok"]))
        {
            // the user said it's OK to delete the device
            device_delete( $dev_sel);
			$refreshnav = true;
        }
    }
   

    if ( $action == 'rename')
    {
        /* rename */
        device_rename( $dev_sel, $_REQUEST["new_name"]);
		$refreshnav = true;
    }
   

    if ( $action == 'new_parent')
    {
        /* resort */
        device_new_parent( $dev_sel, $_REQUEST["parentnode"]);
        $refreshnav = true;
    }

    $data       = device_select( $dev_sel);
    $name       = $data['name'];
    $parentnode = $data['parentnode'];

    $size       = min( devices_count(), 30);
   

    if ($special_dialog == false)
    {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Baugruppen</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<script type="text/javascript">
	<?php
	if ( $refreshnav)
	{
		$refreshnav = false;
		print "parent.frames._nav_frame.location.reload();";		
	}
	?>
</script>


<div class="outer">
    <h2>Baugruppe anlegen</h2>
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td>&Uuml;bergeordnete Baugruppe ausw&auml;hlen:</td>
                    <td>
                        <select name="parentnode">
                        <option value="0">root node</option>
                        <?php device_buildtree( 0, 0, $parentnode); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Name der neuen Baugruppe</td>
                    <td>
                        <input type="text"   name="new_device">
                        <input type="submit" name="add" value="Anlegen">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>


<div class="outer">
    <h2>Baugruppe umbennenen/l&ouml;schen/sortieren</h2>
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td>
                        W&auml;hlen Sie die zu bearbeitende Baugruppe:<br>
                    </td>
                    <td>
                        Was soll mit der ausgew&auml;hlten Baugruppe geschehen?
                    </td>
                </tr>
                <tr>
                    <td rowspan="3">
                        <select name="dev_sel" size="<?php print $size;?>" onChange="this.form.submit()">
                        <?php device_buildtree( 0, 0, $dev_sel); ?>
                        </select>
                    </td>
                    <td>
                        Neuer Name:<br>
                        <input type="text"   name="new_name" value="<?php print $name; ?>">
                        <input type="submit" name="rename" value="Umbenennen">
                    </td>
                </tr>
                <tr>
                    <td>
                        Neue &Uuml;berbaugruppe:<br>
                        <select name="parentnode">
                        <option value="0">root node</option>
                        <?php device_buildtree( 0, 0, $parentnode); ?>
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

</body>
</html>

<?PHP
    }
?>
