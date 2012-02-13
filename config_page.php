<?php
    include ("db_update.php");
    partdb_init();
    
    $action = ( isset( $_REQUEST["action"]) ? $_REQUEST["action"] : 'default');
    
    
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Import</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">
  <table class="table">
      <tr>
          <td class="tdtop">
          Datenbank Status / Update
          </td>
          <td class="tdtop">
          </td>
          <td class="tdtop">
          </td>
      </tr>
      <tr>
        <td class="tdtext">
          <?php
            print "Datenbank Version ".getDBVersion()."<br>";
          ?>
        </td>
        <td class="tdtext">
          <?php
            print "Ben&ouml;tigte Version ".getSollDBVersion()."<br>";
          ?>
        </td>
        <td class="tdtext">
          <?php
            if (checkDBUpdateNeeded() == true)
              print "Update Notwendig<br>";
            else
              print "Up to date<br>";
          ?>
        </td>
      </tr>
      <tr>
        <td class="tdtext">
          <?php
            if (strcmp ($action, "db_update") == 0)
            {
              doDBUpdate();
            }
          ?>
        </td>
        <td class="tdtext">
        </td>
        <td class="tdtext">
          <form action="" method="post">
          <input type="hidden" name="action" value="db_update">
          <input type="submit" value="Jetzt Datenbank Updaten">
          </form>
        </td>
      </tr>
  </table>
</body>
</html>
