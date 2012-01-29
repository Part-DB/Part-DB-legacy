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

  $sollDBVersion = 2; // Diese Version erwarten wir. Darf nur Incrementiert werden !
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
      print "your Database version ".$ver." is outdated an will now be updated to ".$sollDBVersion."<br>";
      print "Get lock of database<br>";
      $query = "LOCK TABLE internal;";
      $result = mysql_query($query);
      
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
                print "Step:".$query." ok<br>";
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
          $query = "UPDATE internal set keyValue = $strVer;";
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
      $query = "UNLOCK TABLES";
      $result = mysql_query($query);
      if ($error == 0)
        print "Update Finished<br>";
      else
        print "Update Failed<br>";
    }
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
        break;
/*
      case 2:
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
