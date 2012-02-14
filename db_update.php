<?PHP
/*
Diese Datei enthält Funktionen zum Prüfen der aktuellen Datenbank Version,
die notwendige Datenbank Version sowie alle Infos um die Änderungen durchzuführen

Bei einer Änderung sind 2 Punkte zu ändern:
- incrementieren von "$sollDBVersion" um 1
- eintragen der notwendigen Änderungen in der Funktion "setDBUpdateSteps()".
  ACHTUNG: Die Nummer beim case ist um eins niedriger als bei "$sollDBVersion"
           Die Nummer gibt hier die Version an die aktuell in der Datenbank vorliegt !
           
Der Ablauf ist wie folgt:
1) Feststellen der aktuellen Version
2) Wenn $sollDBVersion gleich aktueller Version Fertig
3) sonst Update der Datenbank um 1 Version
3) gehe zu 2)

So können updates problemlos übersprungen werden. Updates werden Schritt für Schritt 
nachgeholt.
*/

  $sollDBVersion = 5; // Diese Version erwarten wir. Darf nur Incrementiert werden !
                      // Achtung, diese Nummer-"1" muss es in der Funktion setDBUpdateSteps()
                      // geben, sonst wird mit einem Fehler abgebrochen !

  include ("lib.php");
  partdb_init();

  /*
  ermittelt die aktuelle Datenbank Version und liefert diese als Return Wert
  */
  function getDBVersion()
  {
    $curVersion = 0;  // default: Fehler, kann nicht ermittelt werden
    $query = "SELECT keyValue FROM internal WHERE keyName LIKE 'dbVersion'";
    
    $result = mysql_query ($query);
    if ($result != false)
    {
      $row = mysql_fetch_row($result);
      $curVersion = intval($row[0]);
    }
    else
    {
      $curVersion = 1;  // Ist noch nicht hinterlegt, erst mal für dbUpdate vorbereiten
    }
    
    return $curVersion;
  }

  /*
  ermittelt ob die Datenbank Automatisch upgedaten werden soll
  true wenn Automatisches update gewünscht
  
  Ist noch kein passender Eintrag in der Datenbank, wird er Angelegt
  */
  function getDBAutomaticUpdateActive()
  {
    $isActive = false;  // By default disabled
    $query = "SELECT keyValue FROM internal WHERE keyName LIKE 'dbAutoUpdate'";
    
    $result = mysql_query ($query);
    if ($result != false)
    {
      if (mysql_num_rows($result) == 0)
      {
        $query = "INSERT INTO internal SET keyName='dbAutoUpdate', keyValue='0'";
        mysql_query($query);
      }
      else
      {
        $row = mysql_fetch_row($result);
        if (intval($row[0]) != 0)
          $isActive = true;
      }
    }
    
    return $isActive;
  }
  
  /*
  setzt den Auto update Status des Datenbank updates
  */
  function setDBAutomaticUpdateActive($active)
  {
    $query = "UPDATE internal SET keyValue=";
    if ($active)
      $query = $query."1";
    else
      $query = $query."0";
    
    $query = $query." WHERE keyName LIKE 'dbAutoUpdate'";
    
    $result = mysql_query ($query);
    if ($result == false)
    {
      print "Update failed error=".mysql_error()."<br>";
    }
  }

  /*
  liefert die Datenbankversion, die wir erwarten
  */
  function getSollDBVersion()
  {
    global $sollDBVersion;
    return $sollDBVersion;
  }

  /*
  Prüft die Datenbank Version und liefert true wenn update notwendig
  sonst false
  */
  function checkDBUpdateNeeded()
  {
    global $sollDBVersion;
    $dbVer = getDBVersion();
    if ($dbVer != $sollDBVersion)
      return true;

    return false;
  }
  
  /*
  Funktion führt alle notwendigen Updates aus, bis letzte Version erreicht
  */
  function doDBUpdate()
  {
    global $sollDBVersion;
    $error = 0;
    if (checkDBUpdateNeeded())
    {
      $ver = intval(getDBVersion());
      if ($ver > $sollDBVersion)
      {
        print "WARNUNG: Ihre Datenbank Version ".$ver." ist neuer als diese Version von partDB unterst&uuml;tzte v".$sollDBVersion.", Update abgebrochen<br>";
        return;
      }
      
      print "your Database version ".$ver." is outdated an will now be updated to ".$sollDBVersion."<br>";
      print "Get lock of database<br>";
      $query = "SELECT GET_LOCK('UpdatePartDB', 3);";  // get exclusive database access
      $result = mysql_query($query);
      
      if ($result == false)
      {
        print "It seem that there is an database update already going on, aborting, try again later<br>";
        print mysql_error()."<br>";
        break;
      }

      while($ver < $sollDBVersion)
      {
        
        $steps = setDBUpdateSteps($ver);
        if (count($steps) > 0)
        {
          if (strlen($steps[0]) > 0)
          {
            foreach($steps as $query)
            {
              $results = mysql_query($query);
              if ($results == false)
              {
                print mysql_error()."<br>";
                $error = 1;
              }
              else
              {
                print "Step: ".$query." ok<br>";
              }
            };
          }
          else
          {
            print "Skipping empty update<br>";
          }
        }
        else  // Fehler, update von dieser Version ist nicht definiert
        {
          print "Fehler, update von dieser Version ist nicht definiert. Check setDBUpdateSteps() f&uuml;r Version ".$ver."<br>";
          $error = 1;
        }
        
        if ($error == 0)
        {
          $strVer = "".($ver+1);
          $query = "UPDATE internal set keyValue = $strVer WHERE keyName = 'dbVersion';";
          $result = mysql_query($query);
        }
        else
        {
          print "Update failed, aborting<br>";
          break;
        }
        
        $ver = getDBVersion();
      };
      
      print "Unlocking Database<br>";
      $query = "SELECT RELEASE_LOCK('UpdatePartDB');";
      $result = mysql_query($query);
    }
    if ($error == 0)
      print "Update Finished<br>";
    else
      print "Update Failed<br>";
  }
  
  /*
  hier werden die einzelnen Schritte festgelegt um von der in "$ver" übergebenen
  Version zur nächsten zu kommen. 
  IMMER nur einen Schritt.
  Hierdurch ist sicher gestellt, das wir auch Updates Überspringen können.
  */
  function setDBUpdateSteps($ver)
  {
    $updateSteps = array();
    switch($ver)
    {
      case 1:
        // tabelle existiert noch nicht, anlegen und mit Leben füllen
        $updateSteps[] = "CREATE TABLE internal (keyName CHAR(30) CHARACTER SET ASCII UNIQUE NOT NULL, keyValue CHAR(30));";
        $updateSteps[] = "INSERT INTO internal SET keyName='dbVersion', keyValue='0';"; // nur beim Anlegen sonst nie nehmen !
        // devices anlegen
        $updateSteps[] = "CREATE TABLE `devices` (".
            "`id` int(11) NOT NULL auto_increment,".
            "`name` mediumtext NOT NULL,".
            " PRIMARY KEY  (`id`)".
            ") ENGINE=MyISAM;";
        // part_device anlegen
        $updateSteps[] = "CREATE TABLE `part_device` (".
            "`id_part` int(11) NOT NULL default '0',".
            "`id_device` int(11) NOT NULL default '0',".
            "`quantity` int(11) NOT NULL default '0',".
            " PRIMARY KEY  (`id_part`)".
            ") ENGINE=MyISAM;";
        break;

      case 2:
        $updateSteps[] = "ALTER TABLE  `part_device` ADD  `mountname` mediumtext NOT NULL AFTER  `quantity` ;";
        break;

      case 3:
        $updateSteps[] = "ALTER TABLE  `storeloc` ADD  `parentnode` int(11) NOT NULL default '0' AFTER  `name` ;";
        $updateSteps[] = "ALTER TABLE  `storeloc` ADD  `is_full` boolean NOT NULL default false AFTER `parentnode` ;";
        break;

      case 4:
        $updateSteps[] = "ALTER TABLE  `part_device` DROP PRIMARY KEY;";
        break;
/*
      case 5:
        $updateSteps[] = "INSERT INTO internal SET keyName='test', keyValue='muh';";
        break;
      case 3:
        $updateSteps[] = ""; //INSERT INTO internal SET keyName='test2', keyValue='muh2';";
        break;
      case 4:
        $updateSteps[] = "DELETE FROM internal WHERE keyName='test2'";
        break;
*/
      default:
        print "FEHLER: unbekannte Version $ver <br>";
        break;
    }
    
    return $updateSteps;
  }

?>
