<?php
/*
    $Id: nav.php 377 2012-02-27 23:21:10Z bubbles.red@gmail.com $
*/
    include( 'db_update.php');
    partdb_init();

    require( 'config.php');
    
    $action = ( isset( $_REQUEST["action"]) ? $_REQUEST["action"] : 'default');
    
    
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Systemeinstellungen</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>

<body class="body">

<div class="outer">
    <h2>Systemeinstellungen aus config.php (momentan nur Ansicht)</h2>
    <div class="inner">
        <table>
            <tr>
                <td>HTTP-Zeichensatz:</td>
                <td><?php print $http_charset; ?></td>
            </tr>

            <tr>
                <td>W&auml;hrungssymbol:</td>
                <td><?php print $currency; ?></td>
            </tr>

            <tr>
                <td>Pfad f&uuml;r Datenbl&auml;tter:</td>
                <td><?php print $datasheet_path; ?></td>
            </tr>

            <tr>
                <td>Pfad f&uuml;r Datenbl&auml;tter als Standard verwenden:</td>
                <td><?php print ( $use_datasheet_path) ? "ja" : "nein"; ?></td>
            </tr>

            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td>Updateliste auf Startseite verstecken:</td>
                <td><?php print ( $disable_update_list) ? "ja" : "nein"; ?></td>
            </tr>

            <tr>
                <td>Baugruppenfunktion abschalten:</td>
                <td><?php print ( $disable_devices) ? "ja" : "nein"; ?></td>
            </tr>

            <tr>
                <td>Bauteil-ID in Listen verstecken:</td>
                <td><?php print ( $hide_id) ? "ja" : "nein"; ?></td>
            </tr>

            <tr>
                <td>Hilfefunktion abschalten:</td>
                <td><?php print ( $disable_help) ? "ja" : "nein"; ?></td>
            </tr>

            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <tr>
                <td>modale Dialoge verwenden:</td>
                <td><?php print ( $use_modal_dialog) ? "ja" : "nein"; ?></td>
            </tr>

            <tr>
                <td>Dialogbreite:</td>
                <td><?php print $dialog_width; ?></td>
            </tr>

            <tr>
                <td>Dialogh&ouml;he:</td>
                <td><?php print $dialog_height; ?></td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
