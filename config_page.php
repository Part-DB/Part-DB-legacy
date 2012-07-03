<?php
/*
    $Id$
*/
    include( "config.php");
    include ("db_update.php");
    partdb_init();
    
    $action = 'default';
    if ( isset( $_REQUEST["db_update"]))          { $action = 'db_update';}
    if ( isset( $_REQUEST["set_auto_update"]))    { $action = 'set_auto_update';}
    if ( isset( $_REQUEST["backup"]))             { $action = 'backup';}
    if ( isset( $_REQUEST["download_file"]))      { $action = 'download_file';}
    if ( isset( $_REQUEST["delete_file"]))        { $action = 'delete_file';}
    
    $selected_backup_file = isset( $_REQUEST["selected_backup_file"]) ? $_REQUEST["selected_backup_file"] : "";
    $backup_path = isset($db_backup_path) ? $db_backup_path : "backup/";
    

    if ($action == "backup")
    {
        $backup_file = $database .'_'. date("Y-m-d_H:i:s") . '.sql';
        $command = "mysqldump --opt -h $mysql_server -u $db_user -p $db_password $database > $backup_path$backup_file";

        exec($command);
    }

    if ($action == "download_file")
    {
        if ($selected_backup_file != "")
        {      
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=".$selected_backup_file);
            header("Content-Length:". filesize($backup_path.$selected_backup_file));

            readfile($backup_path.$selected_backup_file);
        }
    }
    
    if ($action == "delete_file")
    {
        if ($selected_backup_file != "")
        {      
            $command = "rm $backup_path$selected_backup_file";
            
            exec($command);
        }
    }
    
    function list_backup_files( $path)
    {
        $handle = opendir($path);
        
        while($file = readdir($handle))
        {
                $file_array[] = $file;
        }

        rsort($file_array);

        foreach($file_array as $file)
        {
            if (($file != '.') && ($file != '..') && ($file != '.svn') && (!(is_dir($path.$file))))
            {
                print "<option value=\"". smart_unescape( $file) . "\">".
                      smart_unescape( $file) ."</option>\n";

            }
        }

        closedir($handle); 
    }
    
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Datenbankupdate</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>

<body class="body">
<div class="outer">
    <h2>Datenbank Status / Update</h2>
    <div class="inner">
    <table class="table">
      <tr>
        <td class="tdtext">
          <?php print "Datenbank Version ". getDBVersion(); ?>
        </td>
        <td class="tdtext">
          <?php print "Ben&ouml;tigte Version ". getSollDBVersion(); ?>
        </td>
        <td class="tdtext">
          <?php print ( checkDBUpdateNeeded() == true) ? "Update notwendig" : "up-to-date"; ?>
        </td>
      </tr>
      <tr>
        <td class="tdtext">
          <?php
            if ($action == "db_update")
            {
              print "Updateverlauf<br>";
              doDBUpdate();
            }
          ?>
        </td>
        <td class="tdtext">
          <?php
            if ($action == "set_auto_update")
            {
              if (isset($_REQUEST["active"]))
              {
                if ($_REQUEST["active"] == true)
                {
                  setDBAutomaticUpdateActive(true);
                }
              }
              else
              {
                setDBAutomaticUpdateActive(false);
              }
            }
          ?>
          <form action="" method="post">
          <?php
            print "<input type=\"checkbox\" name=\"active\" value=\"active\"";
            if (getDBAutomaticUpdateActive())
            {
              print " checked";
            }
            print ">Automatisches Update<br>";
          ?>
          <input type="submit" name="set_auto_update" value="&Uuml;bernehmen">
          </form>
        </td>
        <td class="tdtext">
          <form action="" method="post">
          <input type="submit" name="db_update" value="Jetzt Datenbank Updaten">
          </form>
        </td>
      </tr>
    </table>
    </div>
</div>

<div class="outer">
    <h2>Datenbank-Backup</h2>
    <div class="inner">
    <form action="" method="post">
    <table class="table">
      <tr>
        <td class="tdtext">
          Hinweis: Damit die Backups erstellt werden k&ouml;nnen, muss der Benutzer "www-data" im Backup-Ordner Schreibrechte haben.
        </td>
      </tr>
      <tr>
        <td class="tdtext">
          Jetzt eine Datenbanksicherung durchf&uuml;hren:
          <input type="submit" name="backup" value="Datenbank-Backup erstellen">
        </td>
      </tr>
      <tr>
        <td class="tdtext">
            Existierende Datenbanksicherungen:
            <select name="selected_backup_file" size=1>
                        <?php list_backup_files($backup_path); ?>
            </select>
            <input type="submit" name="download_file" value="Download">
            <input type="submit" name="delete_file" value="L&ouml;schen">
        </td>
      </tr>
    </table>
    </form>
    </div>
</div>

</body>
</html>
