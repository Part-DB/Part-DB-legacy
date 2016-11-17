<?php
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

    Changelog (sorted by date):
        [DATE]      [NICKNAME]          [CHANGES]
        2012-09-01  kami89              - created
*/

    include_once('start_session.php');

    $errors = array();

    if (isset($_REQUEST["add"]))
    {
        try
        {
            debug($_REQUEST['new_type'], $_REQUEST['new_text'], __FILE__, __LINE__, __METHOD__, false);
        }
        catch (Exception $exception)
        {
            $errors[] = $exception->getMessage();
        }
    }
    elseif (isset($_REQUEST["clear"]))
    {
        try
        {
             create_debug_log_file(); // override the existing debug log with a new, empty debug log
        }
        catch (Exception $exception)
        {
            $errors[] = $exception->getMessage();
        }
    }
    elseif (isset($_REQUEST["download"]))
    {
        if (is_readable(DEBUG_LOG_FILENAME))
        {
            send_file(DEBUG_LOG_FILENAME);
            // TODO: how can we re-activate the autorefresh now?!
        }
        else
            $errors[] = 'Die Log-Datei kann nicht gelesen werden!';
    }
    elseif (isset($_REQUEST["enable"]))
    {
        try
        {
            set_debug_enable(true, $_REQUEST['admin_password']);
            header('Location: system_debug.php');
        }
        catch (Exception $exception)
        {
            $errors[] = $exception->getMessage();
        }
    }
    elseif (isset($_REQUEST["disable"]) || isset($_REQUEST["disable_and_delete"]))
    {
        try
        {
            set_debug_enable(false);

            if (isset($_REQUEST["disable_and_delete"]))
                delete_debug_log_file();

            header('Location: system_debug.php');
        }
        catch (Exception $exception)
        {
            $errors[] = $exception->getMessage();
        }
    }

    if ($config['debug']['enable'] && (count($errors) == 0) && ( ! isset($_REQUEST['stop_autorefresh']))
            && (( ! isset($_REQUEST['autorefresh_disabled'])) || (isset($_REQUEST['start_autorefresh']))))
        $autorefresh = true;
    else
        $autorefresh = false;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Debugging</title>
    <meta http-equiv="content-type" content="text/html; charset=<?php print $config['html']['http_charset']; ?>">
    <?php if ($autorefresh) print '<meta http-equiv="refresh" content="5" >'; ?>

    <!-- For maximum stability of this page, we don't use templates! So we include the stylesheet directly here... -->
    <style type="text/css">
        .body {     background-color: #cdcdcd;
                    font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;}

        .outer {    background-color: #ffffff;
                    margin-top:    10px;
                    margin-bottom: 15px;
                    border: none;
                    border-bottom: 1px solid #000000;
                    border-left:   1px solid #000000;
                    padding:   11px;
                    font-size: 12px;}

        .outer h2 { background-color: #BABABA;
                    margin-top:    0px;
                    margin-bottom: 1px;
                    border-bottom: 2px solid #F76B02;
                    border-left:   1px solid #F76B02;
                    padding: 1px;
                    font-size:   15px;
                    font-weight: bold;}

        .inner {    background-color: #F2F2F2;
                    border-bottom: 3px solid #d0d0d0;
                    border-left:   1px solid #d0d0d0;
                    border-right:  1px solid #d0d0d0;
                    padding: 1px;}
    </style>
</head>

<body class="body">

    <!-- Reload the navigation frame because of the debugging buttons at the top of the navigation frame -->
    <script type="text/javascript">
        parent.frames.navigation_frame.location.reload();
    </script>

    <div class="outer">
        <h2>Debug-Konsole</h2>
        <div class="inner">
            <form action="" method="post">
                <?php
                    if ($config['debug']['enable'])
                    {
                        print '<strong><font color="#008000">Debugging ist aktiviert</font></strong><br>';
                        print '<input type="submit" name="disable" value="Deaktivieren">';
                        print '<input type="submit" name="disable_and_delete" value="Deaktivieren und Log-Datei löschen">';
                        print '<hr>Testeintrag erzeugen:';
                        print '<input type="text" name="new_type" value="warning">';
                        print '<input type="text" name="new_text" value="Testeintrag">';
                        print '<input type="submit" name="add" value="Hinzuf&uuml;gen">';
                        print '<hr><input type="submit" name="clear" value="Log leeren">';
                        print '<input type="submit" name="download" value="Log als XML-Datei herunterladen">';

                        if ($autorefresh)
                        {
                            print '<hr><input type="submit" name="stop_autorefresh" value="Autorefresh deaktivieren">';
                        }
                        else
                        {
                            print '<input type="hidden" name="autorefresh_disabled">';
                            print '<hr><input type="submit" name="start_autorefresh" value="Autorefresh aktivieren">';
                        }
                    }
                    else
                    {
                        print '<strong><font color="#ff0000">Debugging ist deaktiviert</font></strong><br>';
                        print 'Administratorpasswort zum aktivieren: ';
                        print '<input type="password" name="admin_password" value="">';
                        print '<input type="submit" name="enable" value="Aktivieren"><br>';
                    }

                    if (count($errors) > 0)
                    {
                        print '<br><br><strong><font color="#ff0000">';
                        foreach ($errors as $error)
                            print $error.'<br>';
                        print '</font></strong>';
                    }
                ?>
            </form>
        </div>
    </div>

<?php if ($config['debug']['enable']) { ?>
    <div class="outer">
        <h2>Debug-Log</h2>
        <div class="inner">
            Folgende Log-Typen werden hervorgehoben:
            "<font color="darkgreen">success</font>",
            "<font color="darkorange">warning</font>",
            "<strong><font color="red">error</font></strong>"
            "<strong><font color="blue">temp</font></strong>"<br>
            Zus&auml;tzliche Informationen k&ouml;nnen angezeigt werden,
            indem man mit der Maus &uuml;ber den entsprechenden Eintrag f&auml;hrt.
            <select name="debug_log" size="25" style="width:100%" autocomplete="off">
                <?php
                    $logs = get_debug_log_elements();
                    foreach ($logs as $log)
                    {
                        switch (strtolower($log['type']))
                        {
                            case 'success':
                                $style = ' style="color:darkgreen"';
                                break;
                            case 'warning':
                                $style = ' style="color:darkorange"';
                                break;
                            case 'error':
                                $style = ' style="color:red;font-weight:bold"';
                                break;
                            case 'temp':
                                $style = ' style="color:blue;font-weight:bold"';
                                break;
                            default:
                                $style = ' ';
                                break;
                        }

                        $hint = 'in file '.$log['file'];
                        $hint .= ' on line '.$log['line'];
                        if ($log['function'] != '')
                            $hint .= ' in function '.$log['function'];

                        print '<option'.$style.' title="'.$hint.'">';
                        print '['.$log['datetime'].'] '.$log['type'].': '.$log['message'];
                        print "</option>\n";
                        $index++;
                    }
                ?>
                <option selected></option>
            </select>
        </div>
    </div>
<?php } ?>
</body>
</html>


