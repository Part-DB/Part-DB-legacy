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
            $errors[] = _('Die Log-Datei kann nicht gelesen werden!');
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Debugging</title>
    <meta http-equiv="content-type" content="text/html; charset=<?php print $config['html']['http_charset']; ?>">
    <?php if ($autorefresh) print '<meta http-equiv="refresh" content="5" >'; ?>

    <!-- For maximum stability of this page, we don't use templates! So we include the stylesheet directly here... -->

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>

</head>

<body>
    <!-- Reload the navigation frame because of the debugging buttons at the top of the navigation frame -->
    <!-- <script type="text/javascript">
        location.reload();
    </script> -->

    <div class="container">
        <div class="panel panel-primary">
        <div class="panel-heading">Debug-Konsole</div>
        <div class="panel-body">
            <form action="" method="post">
                <?php
                    if ($config['debug']['enable'])
                    {
                        print '<div class="form-group"><label><span style="color: #008000; ">Debugging ist aktiviert</span></label>' . "\n";
                        print '<div class="col-md-12"><button class="btn btn-primary" type="submit" name="disable">Deaktivieren</button>' . "\n";
                        print '<button class="btn btn-default" type="submit" name="disable_and_delete">Deaktivieren und Log-Datei löschen</button>' . "\n";
                        print '</div></div><br>';
                        print '<div class="form-group"><label class="control-label">Testeintrag erzeugen:</label>' . "\n";
                        print '<div class="col-md-5"><input type="text" class="form-control" name="new_type" value="warning"></div>' . "\n";
                        print '<div class="col-md-5"><input type="text" class="form-control" name="new_text" class="form-control" value="Testeintrag"></div>' . "\n";
                        print '<div class="col-md-2"><button class="btn btn-primary" type="submit" name="add">Hinzuf&uuml;gen</button></div></div>' . "\n";
                        print '<hr><button type="submit" class="btn btn-default" name="clear">Log leeren</button>' . "\n";
                        print '<button type="submit" name="download" class="btn btn-default">Log als XML-Datei herunterladen</button>' . "\n";

                        if ($autorefresh)
                        {
                            print '<hr><button type="submit" name="stop_autorefresh" class="btn btn-default" >Autorefresh deaktivieren</button>';
                        }
                        else
                        {
                            print '<input type="hidden" name="autorefresh_disabled">';
                            print '<hr><button type="submit" class="btn btn-default" name="start_autorefresh">Autorefresh aktivieren</button>';
                        }
                    }
                    else
                    {
                        print '<strong><span style="color: #ff0000; ">Debugging ist deaktiviert</span></strong><br>';
                        print '<div class="form-group"><label>Administratorpasswort zum aktivieren:</label> ';
                        print '<div class="input-group"><input class="form-control" type="password" name="admin_password" value="">' . "\n";
                        print '<div class="input-group-btn"><button class="btn btn-primary" type="submit" name="enable">Aktivieren</button></div></div></div>';
                    }

                    if (count($errors) > 0)
                    {
                        print '<div class="alert alert-danger"><strong><span style="color: #ff0000; ">';
                        foreach ($errors as $error)
                            print $error.'<br>';
                        print '</font></strong></div>';
                    }
                ?>
            </form>
        </div>
    </div>

        <?php if ($config['debug']['enable']) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">Debug-Log</div>
        <div class="panel-body">
            Folgende Log-Typen werden hervorgehoben:
            "<span style="color: darkgreen; ">success</span>",
            "<span style="color: darkorange; ">warning</span>",
            "<strong><span style="color: red; ">error</span></strong>"
            "<strong><span style="color: blue; ">temp</span></strong>"<br>
            Zus&auml;tzliche Informationen k&ouml;nnen angezeigt werden,
            indem man mit der Maus &uuml;ber den entsprechenden Eintrag f&auml;hrt.
            <select name="debug_log" size="25" class="form-control">
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
    </div>




</body>
</html>


