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
    include("lib.php");
	include("config.php");
    partdb_init();
    
    // set action to default, if not exists
    $action        = isset( $_REQUEST['action'])   ? $_REQUEST['action']   : 'default';
	$deviceid      = isset( $_REQUEST['deviceid']) ? $_REQUEST['deviceid'] : 0; 

    $confirmdelete = 0;
    $refreshnav    = 0;
	
    if( strcmp( $action, "createdevice") == 0)  //add a new device
    {
        device_add( $_REQUEST["newdevicename"], $deviceid);
		$refreshnav = 1;
    }
    
    if( strcmp($action, "confirmeddelete") == 0)
    {
        device_delete( $deviceid);
		$refreshnav = 1;
    }
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

	<script language="JavaScript" type="text/javascript">
		<?PHP
		if($refreshnav == 1)
		{
			$refreshnav = 0;
			print "parent.frames._nav_frame.location.reload();";		
		}
		?>
	</script>

<div class="outer">
    <h2>Neues Ger&auml;t erzeugen</h2>
    <div class="inner">
        <form method="post" action="">
            Ger&auml;tenamen
            <input type="text" name="newdevicename" size="10" maxlength="50" >
            <input type="hidden" name="action" value="createdevice">
            <input type="submit" value="OK">
        </form> 
    </div>
</div>

<?php
if(strcmp( $action, "deletedevice") == 0)
{
    print "<br>";
    print "<table class=\"table\">";
    print "<tr><td class=\"tdtop\">Ger&auml;t \"".$_REQUEST["devicename"]."\" wirklich l&ouml;schen?</td></tr>";
    print "<tr><td class=\"tdtext\">";
    print "<form method=\"post\" action=\"\">";
            print "<input type=\"hidden\" name=\"action\"  value=\"confirmeddelete\"/>";
            print "<input type=\"hidden\" name=\"deviceid\" value=\"". $deviceid ."\"/>";
            print "<input type=\"submit\" style=\"height: 1.5em; width: 5em\" value=\"Ja\">";
    print "</form>";
    print "<form method=\"post\" action=\"\">";
            print "<input type=\"hidden\" name=\"action\"  value=\"\"/>";
            print "<input type=\"hidden\" name=\"deviceid\" value=\"". $deviceid ."\"/>";
            print "<input type=\"submit\" style=\"height: 1.5em; width: 5em\" value=\"Nein\">";
    print "</form></td>";
    print "</tr></table>";
}
?>
    
<div class="outer">
    <h2>Ger&auml;te</h2>
    <div class="inner">
        <table>
        <?php
            
            $result = devices_select( $deviceid);
            $rowcount = 0;  // $rowcount is used for the alternating bg colors
            
            print "<tr class=\"trcat\">".
                "<td>Name</td>".
                "<td>Anzahl Teile</td>".
                "<td>Anzahl Einzelteile</td>".
                "<td>Preis</td>".
                "<td>L&ouml;schen</td>".
                "</tr>\n";
            
            while ( $d = mysql_fetch_assoc( $result))
            {
                
                // the alternating background colors are created here
                $rowcount++;
                print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">";
                
                print "<td class=\"tdrow1\"><a href=\"deviceinfo.php?deviceid=". smart_unescape( $d['id']) ."\">". smart_unescape( $d['name']) ."</a></td>\n";
                print "<td class=\"tdrow2\">". smart_unescape( $d['parts']) ."</td>\n";
                print "<td class=\"tdrow3\">". smart_unescape( $d['pieces']) ."</td>\n";
                print "<td class=\"tdrow3\">". smart_unescape( $d['value']) ."&nbsp". $currency."</td>\n";
                print "<td class=\"tdrow3\">";
                
                print "<form method=\"post\" action=\"\">";
                print "<input type=\"hidden\" name=\"action\"  value=\"deletedevice\">";
                print "<input type=\"hidden\" name=\"deviceid\" value=\"". smart_unescape( $d['id']) ."\">";
                print "<input type=\"hidden\" name=\"devicename\" value=\"". smart_unescape($d['name']) ."\">";
                print "<input type=\"submit\" value=\"L&ouml;schen\">";
                print "</form>";
                
                print "</td>";
                print "</tr>". PHP_EOL;
            }
        ?>
        </table>
    </div>
</div>

</body>
</html>
