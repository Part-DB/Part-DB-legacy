<?php
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

    $Id$
*/

  $sollDBVersion = 10; // Diese Version erwarten wir. Darf nur Incrementiert werden !
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
  Hierdurch ist sicher gestellt, das wir auch Updates überspringen können.
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

      case 5:
        $updateSteps[] = "ALTER TABLE  `devices` ADD  `parentnode` int(11) NOT NULL default '0' AFTER  `name` ;";
		break;

      case 6:
        $updateSteps[] = "ALTER TABLE  footprints ADD  parentnode INT(11) NOT NULL default '0' AFTER name;";
		break;

      case 7:
        $updateSteps[] = "ALTER TABLE  parts  ADD  obsolete boolean NOT NULL default false AFTER comment;";
		break;

      case 8:
        // footprints auf neues schema umbennenen
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_2KBB-R'                   WHERE name='2KBB-R';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_2KBB'                     WHERE name='2KBB';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_2KBP'                     WHERE name='2KBP';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_1010'                          WHERE name='1010';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_1012'                          WHERE name='1012';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_1014'                          WHERE name='1014';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_1212'                          WHERE name='1212';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_1214'                          WHERE name='1214';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_0405'                          WHERE name='0405';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_0505'                          WHERE name='0505';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_0605'                          WHERE name='0605';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_0807'                          WHERE name='0807';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_0808'                          WHERE name='0808';";
        $updateSteps[] = "UPDATE footprints SET name='ELKO_SMD_0810'                          WHERE name='0810';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-KOHLE_0204'                  WHERE name='0204';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-KOHLE_0207'                  WHERE name='0207';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-KOHLE_0309'                  WHERE name='0309';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-KOHLE_0414'                  WHERE name='0414';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-KOHLE_0617'                  WHERE name='0617';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-KOHLE_0922'                  WHERE name='0922';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_3202'                           WHERE name='3202';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_64W'                            WHERE name='64W';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_64Y'                            WHERE name='64Y';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_72-PT'                          WHERE name='72PT';";
        $updateSteps[] = "UPDATE footprints SET name='7-SEGMENT_1-20CM'                       WHERE name='7SEG-1';";
        $updateSteps[] = "UPDATE footprints SET name='7-SEGMENT_2'                            WHERE name='7SEG-2';";
        $updateSteps[] = "UPDATE footprints SET name='7-SEGMENT_3-TOT4301'                    WHERE name='7SEG-3';";
        $updateSteps[] = "UPDATE footprints SET name='7-SEGMENT_2-VQE'                        WHERE name='7SEG-VQE-3';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_147323-02'              WHERE name='AMP-147323-2';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_AK700-3-5'                WHERE name='AK70-3-5';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZ_ABRACON_ABS13'                    WHERE name='ABS13';";
        $updateSteps[] = "UPDATE footprints SET name='RESONATOR-ABRACON_ABM3B'                WHERE name='ABM3B';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-GERADE_HE14-02'         WHERE name='AMP-HE14S2';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-GERADE_HE14-03'         WHERE name='AMP-HE14S3';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-GERADE_HE14-04'         WHERE name='AMP-HE14S4';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-GERADE_HE14-05'         WHERE name='AMP-HE14S5';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-GERADE_HE14-06'         WHERE name='AMP-HE14S6';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-GERADE_HE14-07'         WHERE name='AMP-HE14S7';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-GERADE_HE14-08'         WHERE name='AMP-HE14S8';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-GERADE_HE14-09'         WHERE name='AMP-HE14S9';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-GERADE_HE14-10'         WHERE name='AMP-HE14S10';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-ABGEWINKELT_HE14-02'    WHERE name='AMP-HE14R2';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-ABGEWINKELT_HE14-03'    WHERE name='AMP-HE14R3';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-ABGEWINKELT_HE14-04'    WHERE name='AMP-HER4';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-ABGEWINKELT_HE14-05'    WHERE name='AMP-HE14R5';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-ABGEWINKELT_HE14-06'    WHERE name='AMP-HE14R6';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-ABGEWINKELT_HE14-07'    WHERE name='AMP-HE14R7';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-ABGEWINKELT_HE14-08'    WHERE name='AMP-HE14R8';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-ABGEWINKELT_HE14-09'    WHERE name='AMP-HE14R9';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP-ABGEWINKELT_HE14-10'    WHERE name='AMP-HE14R10';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-02'                  WHERE name='AMPMT-S2';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-03'                  WHERE name='AMPMT-S3';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-04'                  WHERE name='AMPMT-S4';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-05'                  WHERE name='AMPMT-S5';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-06'                  WHERE name='AMPMT-S6';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-07'                  WHERE name='AMPMT-S7';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-08'                  WHERE name='AMPMT-S8';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-09'                  WHERE name='AMPMT-S9';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-10'                  WHERE name='AMPMT-S10';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-AMP_MT-12'                  WHERE name='AMPMT-S12';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-02'                 WHERE name='ARK5MM-2';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-03'                 WHERE name='ARK5MM-3';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-04'                 WHERE name='ARK5MM-4';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-05'                 WHERE name='ARK5MM-5';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-06'                 WHERE name='ARK5MM-6';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-07'                 WHERE name='ARK5MM-7';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-08'                 WHERE name='ARK5MM-8';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-09'                 WHERE name='ARK5MM-9';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-10'                 WHERE name='ARK5MM-10';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-11'                 WHERE name='ARK5MM-11';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM508-12'                 WHERE name='ARK5MM-12';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-02'                 WHERE name='ARK350MM2';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-03'                 WHERE name='ARK350MM3';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-04'                 WHERE name='ARK350MM4';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-05'                 WHERE name='ARK350MM5';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-06'                 WHERE name='ARK350MM6';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-07'                 WHERE name='ARK350MM7';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-08'                 WHERE name='ARK350MM8';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-09'                 WHERE name='ARK350MM9';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-10'                 WHERE name='ARK350MM10';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-11'                 WHERE name='ARK350MM11';";
        $updateSteps[] = "UPDATE footprints SET name='SCHRAUBKLEMME_RM350-12'                 WHERE name='ARK350MM12';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_B25V'                           WHERE name='B25V';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_B25X'                           WHERE name='B25X';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_COAX-B35N61'                  WHERE name='B35N61';";
        $updateSteps[] = "UPDATE footprints SET name='TASTER_B3F-10XX1'                       WHERE name='B3F10XX1';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X05'              WHERE name='BL1X5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X06'              WHERE name='BL1X6';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X07'              WHERE name='BL1X7';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X08'              WHERE name='BL1X8';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X09'              WHERE name='BL1X9';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X10'              WHERE name='BL1X10';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X12'              WHERE name='BL1X12';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X13'              WHERE name='BL1X13';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X15'              WHERE name='BL1X15';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X17'              WHERE name='BL1X17';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X18'              WHERE name='BL1X18';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_1X20'              WHERE name='BL1X20';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X05'              WHERE name='BL2X5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X06'              WHERE name='BL2X6';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X07'              WHERE name='BL2X7';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X08'              WHERE name='BL2X8';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X09'              WHERE name='BL2X9';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X10'              WHERE name='BL2X10';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X12'              WHERE name='BL2X12';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X13'              WHERE name='BL2X13';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X15'              WHERE name='BL2X15';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X17'              WHERE name='BL2X17';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X18'              WHERE name='BL2X18';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE_2X20'              WHERE name='BL2X20';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X05'        WHERE name='BLF1X5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X06'        WHERE name='BLF1X6';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X07'        WHERE name='BLF1X7';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X08'        WHERE name='BLF1X8';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X09'        WHERE name='BLF1X9';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X10'        WHERE name='BLF1X10';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X12'        WHERE name='BLF1X12';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X13'        WHERE name='BLF1X13';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X15'        WHERE name='BLF1X15';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X17'        WHERE name='BLF1X17';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X18'        WHERE name='BLF1X18';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_1X20'        WHERE name='BLF1X20';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X05'        WHERE name='BLF2X5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X06'        WHERE name='BLF2X6';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X07'        WHERE name='BLF2X7';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X08'        WHERE name='BLF2X8';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X09'        WHERE name='BLF2X9';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X10'        WHERE name='BLF2X10';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X12'        WHERE name='BLF2X12';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X13'        WHERE name='BLF2X13';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X15'        WHERE name='BLF2X15';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X17'        WHERE name='BLF2X17';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X18'        WHERE name='BLF2X18';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-GERADE-FLACH_2X20'        WHERE name='BLF2X20';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_BNC-W'                        WHERE name='BNC';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X05'         WHERE name='BLW2X5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X06'         WHERE name='BLW2X6';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X07'         WHERE name='BLW2X7';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X08'         WHERE name='BLW2X8';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X09'         WHERE name='BLW2X9';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X10'         WHERE name='BLW2X10';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X12'         WHERE name='BLW2X12';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X13'         WHERE name='BLW2X13';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X15'         WHERE name='BLW2X15';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X17'         WHERE name='BLW2X17';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X18'         WHERE name='BLW2X18';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_2X20'         WHERE name='BLW2X20';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X02'         WHERE name='BLW1X2';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X05'         WHERE name='BLW1X5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X06'         WHERE name='BLW1X6';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X07'         WHERE name='BLW1X7';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X08'         WHERE name='BLW1X8';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X09'         WHERE name='BLW1X9';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X10'         WHERE name='BLW1X10';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X12'         WHERE name='BLW1X12';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X13'         WHERE name='BLW1X13';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X15'         WHERE name='BLW1X15';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X17'         WHERE name='BLW1X17';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X18'         WHERE name='BLW1X18';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSENLEISTE-ABGEWINKELT_1X20'         WHERE name='BLW1X20';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_CB417'                            WHERE name='CB417';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_CB429'                            WHERE name='CB429';";
        $updateSteps[] = "UPDATE footprints SET name='KERKO-SMD_0402'                         WHERE name='CAP-0402';";
        $updateSteps[] = "UPDATE footprints SET name='KERKO-SMD_0603'                         WHERE name='CAP-0603';";
        $updateSteps[] = "UPDATE footprints SET name='KERKO-SMD_0805'                         WHERE name='CAP-0805';";
        $updateSteps[] = "UPDATE footprints SET name='KERKO-SMD_1206'                         WHERE name='CAP-1206';";
        $updateSteps[] = "UPDATE footprints SET name='KERKO-SMD_1210'                         WHERE name='CAP-1210';";
        $updateSteps[] = "UPDATE footprints SET name='KERKO-SMD_1812'                         WHERE name='CAP-1812';";
        $updateSteps[] = "UPDATE footprints SET name='KERKO-SMD_1825'                         WHERE name='CAP-1825';";
        $updateSteps[] = "UPDATE footprints SET name='KERKO-SMD_2220'                         WHERE name='CAP-2220';";
        $updateSteps[] = "UPDATE footprints SET name='KERKO-SMD-ARRAY_4X0603-0612'            WHERE name='CAP-4x0603';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE_DCPOWERCONNECTOR'                WHERE name='BUxx';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-DICKSCHICHT_BPC10H'          WHERE name='BPC10H';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-DICKSCHICHT_BPC10V'          WHERE name='BPC10V';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-DICKSCHICHT_BPC3H'           WHERE name='BPC3H';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-DICKSCHICHT_BPC3V'           WHERE name='BPC3V';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-DICKSCHICHT_BPC5H'           WHERE name='BPC5H';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-DICKSCHICHT_BPC5V'           WHERE name='BPC5V';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-DICKSCHICHT_BPC7H'           WHERE name='BPC7H';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-DICKSCHICHT_BPC7V'           WHERE name='BPC7V';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DFS'                                 WHERE name='DFS';";
        $updateSteps[] = "UPDATE footprints SET name='KONDENSATOR_CTS_A_15MM'                 WHERE name='CTS-A-15';";
        $updateSteps[] = "UPDATE footprints SET name='KONDENSATOR_CTS_B_20MM'                 WHERE name='CTS-B-20';";
        $updateSteps[] = "UPDATE footprints SET name='KONDENSATOR_CTS_C_25MM'                 WHERE name='CTS-C-25';";
        $updateSteps[] = "UPDATE footprints SET name='KONDENSATOR_CTS_D_30MM'                 WHERE name='CTS-D-30';";
        $updateSteps[] = "UPDATE footprints SET name='RESONATOR-MURATA_CSTCE-G-A'             WHERE name='CSTCE-GA';";
        $updateSteps[] = "UPDATE footprints SET name='KARTENSLOT_CF-1'                        WHERE name='CF-CON';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZOSZILLATOR_CFPT-125'               WHERE name='CFPT125';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZOSZILLATOR_CFPT-126'               WHERE name='CFPT-126';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZOSZILLATOR_CFPT-37 '               WHERE name='CFPT37';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-CENTRONICS_F14'                  WHERE name='CENTRONICS-F14';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-CENTRONICS_F24'                  WHERE name='CENTRONICS-F24';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-CENTRONICS_F36'                  WHERE name='CENTRONICS-F36';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-CENTRONICS_F50'                  WHERE name='CENTRONICS-F50';";
        $updateSteps[] = "UPDATE footprints SET name='REEDRELAIS_SIL'                         WHERE name='CELDUC-SIL';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_CELDUC-SK-ABD'                   WHERE name='CELDUC-SK-ABD';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_CELDUC-SK-AL '                   WHERE name='CELDUC-SK-AL';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_CELDUC-SK-L  '                   WHERE name='CELDUC-SK-L';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_DIN41617-13'                  WHERE name='DIN41617-13';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_DIN41617-21'                  WHERE name='DIN41617-21';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_DIN41617-31'                  WHERE name='DIN41617-31';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_3S'                      WHERE name='DINMAB3S';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_4'                       WHERE name='DINMAB4';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_5'                       WHERE name='DINMAB5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_5S'                      WHERE name='DINMAB5S';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_5SV'                     WHERE name='DINMAB5SV';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_6'                       WHERE name='DINMAB6';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_6V'                      WHERE name='DINMAB6V';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_7S'                      WHERE name='DINMAB7S';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_7SV'                     WHERE name='DINMAB7SV';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_8S'                      WHERE name='DINMAB8S';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_8SN'                     WHERE name='DINMAB8SN';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_8SNV'                    WHERE name='DINMAB8SNV';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-DIN_MAB_8SV'                     WHERE name='DINMAB8SV';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SMA'                              WHERE name='DIODE-SMA';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SMB'                              WHERE name='DIODE-SMB';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SMC'                              WHERE name='DIODE-SMC';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP14'                               WHERE name='DIP14';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP14A4'                             WHERE name='DIP14A4';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP14A8'                             WHERE name='DIP14A8';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP16'                               WHERE name='DIP16';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP16A4'                             WHERE name='DIP16A4';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP16A8'                             WHERE name='DIP16A8';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP18'                               WHERE name='DIP18';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP02'                               WHERE name='DIP2';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP04'                               WHERE name='DIP4';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP06'                               WHERE name='DIP6';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP08'                               WHERE name='DIP8';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP08A4'                             WHERE name='DIP8A4';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP20'                               WHERE name='DIP20';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP22'                               WHERE name='DIP22';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP24'                               WHERE name='DIP24';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP24A12'                            WHERE name='DIP24A12';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP24W'                              WHERE name='DIP24W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP28'                               WHERE name='DIP28';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP28W'                              WHERE name='DIP28W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP32-3'                             WHERE name='DIP32';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP32W'                              WHERE name='DIP32W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP36W'                              WHERE name='DIP36W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP40W'                              WHERE name='DIP40W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP42W'                              WHERE name='DIP42W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP48W'                              WHERE name='DIP48W';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_DIP4S'                    WHERE name='DIP4S';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DIP52W'                              WHERE name='DIP52W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_DPAK-369C'                           WHERE name='DPAK369C';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO14'                             WHERE name='DO14';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO15'                             WHERE name='DO15';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO16'                             WHERE name='DO16';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO201'                            WHERE name='DO201';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO204AC'                          WHERE name='DO204';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO214AA'                          WHERE name='DO214AA';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO214AB'                          WHERE name='DO214AB';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO214AC'                          WHERE name='DO214AC';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO27'                             WHERE name='DO27';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO32'                             WHERE name='DO32';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO34'                             WHERE name='DO34';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO35'                             WHERE name='DO35';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO39'                             WHERE name='DO39';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO41'                             WHERE name='DO41';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_DO7'                              WHERE name='DO7';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_DK1A-L2-5V'                      WHERE name='DK1AL2';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D-PLATINENMONTAGE_W-09'             WHERE name='DSUB-F9';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D-PLATINENMONTAGE_W-15'             WHERE name='DSUB-F15';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D-PLATINENMONTAGE_W-25'             WHERE name='DSUB-F25';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D-PLATINENMONTAGE_W-37'             WHERE name='DSUB-F37';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_W-09'                             WHERE name='DSUB-F9D';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_W-09V'                            WHERE name='DSUB-F9DV';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_W-15'                             WHERE name='DSUB-F15D';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_W-15V'                            WHERE name='DSUB-F15DV';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_W-25'                             WHERE name='DSUB-F25D';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_W-25V'                            WHERE name='DSUB-F25DV';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_W-37'                             WHERE name='DSUB-F37D';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_W-37V'                            WHERE name='DSUB-F37DV';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D-PLATINENMONTAGE_M-09'             WHERE name='DSUB-M9';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D-PLATINENMONTAGE_M-15'             WHERE name='DSUB-M15';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D-PLATINENMONTAGE_M-25'             WHERE name='DSUB-M25';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D-PLATINENMONTAGE_M-37'             WHERE name='DSUB-M37';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_M-09'                             WHERE name='DSUB-M9D';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_M-09V'                            WHERE name='DSUB-M9DV';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_M-15'                             WHERE name='DSUB-M15D';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_M-15V'                            WHERE name='DSUB-M15DV';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_M-25'                             WHERE name='DSUB-M25D';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_M-25V'                            WHERE name='DSUB-M25DV';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_M-37'                             WHERE name='DSUB-M37D';";
        $updateSteps[] = "UPDATE footprints SET name='SUB-D_M-37V'                            WHERE name='DSUB-M37DV';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_ED16'                             WHERE name='ED16';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_ED22'                             WHERE name='ED22';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_ED26'                             WHERE name='ED26';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_ED38'                             WHERE name='ED38';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_ED43'                             WHERE name='ED43';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_EF12'                             WHERE name='EF12';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_EF16'                             WHERE name='EF16';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_EUROCARD-64M-2-L'             WHERE name='EUROCARD64M2L';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_EUROCARD-96M-3-L'             WHERE name='EUROCARD96M3L';";
        $updateSteps[] = "UPDATE footprints SET name='DREHSCHALTER-PANASONIC_EVQVX-11MM'      WHERE name='EVQVX-11MM';";
        $updateSteps[] = "UPDATE footprints SET name='DREHSCHALTER-PANASONIC_EVQVX-9MM'       WHERE name='EVQVX-9MM';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_F126'                             WHERE name='F126';";
        $updateSteps[] = "UPDATE footprints SET name='LOETOESE_FASTON-V'                      WHERE name='FASTON-V';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_FB100'                    WHERE name='FB100';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_FB15 '                    WHERE name='FB15';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_FB15A'                    WHERE name='FB15A';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_FB32 '                    WHERE name='FB32';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_FPCON65'                      WHERE name='FPCON65';";
        $updateSteps[] = "UPDATE footprints SET name='SICHERUNGSHALTER_Laengs'                WHERE name='FUSE1';";
        $updateSteps[] = "UPDATE footprints SET name='SICHERUNGSHALTER_Quer'                  WHERE name='FUSE2';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_GP20'                             WHERE name='GP20';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_G2RL-1'                          WHERE name='G2RL1';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_G2RL-1A'                         WHERE name='G2RL1A';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_G2RL-1A-E'                       WHERE name='G2RL1AE';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_G2RL-1-E'                        WHERE name='G2RL1E';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_G2RL-2'                          WHERE name='G2RL2';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_G2RL-2A'                         WHERE name='G2RL2A';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_G6D'                             WHERE name='G6D';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_GBU4'                     WHERE name='GBU4';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_JJM-1A'                          WHERE name='JJM1A';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_JJM-1C'                          WHERE name='JJM1C';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_JJM-2W'                          WHERE name='JJM2W';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZ_025MM'                            WHERE name='HC18';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZ_HC49'                             WHERE name='HC49';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZ_HC49-4H'                          WHERE name='HC49U';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_VIEWCOM_HS-1-25GY_50'      WHERE name='HS1-25GY-50';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_5MM-S'                            WHERE name='L5MM-S';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_KBU-4-6-8'                WHERE name='KBU46x8';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_KL195-25'                  WHERE name='KL195-25';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_KL195-38'                  WHERE name='KL195-38';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_KL195-50'                  WHERE name='KL195-50';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_KL195-63'                  WHERE name='KL195-63';";
        $updateSteps[] = "UPDATE footprints SET name='LED-ROT_0603'                           WHERE name='LED-0603';";
        $updateSteps[] = "UPDATE footprints SET name='LED-ROT_0805'                           WHERE name='LED-0805';";
        $updateSteps[] = "UPDATE footprints SET name='LED-ROT_1206'                           WHERE name='LED-1206';";
        $updateSteps[] = "UPDATE footprints SET name='LED-ROT_3MM'                            WHERE name='LED-3';";
        $updateSteps[] = "UPDATE footprints SET name='LED-ROT_5MM'                            WHERE name='LED-5';";
        $updateSteps[] = "UPDATE footprints SET name='LOETOESE_LSP'                           WHERE name='LSP10';";
        $updateSteps[] = "UPDATE footprints SET name='TASTER_LSH125'                          WHERE name='LSH125';";
        $updateSteps[] = "UPDATE footprints SET name='TASTER_LSH43'                           WHERE name='LSH43';";
        $updateSteps[] = "UPDATE footprints SET name='TASTER_LSH50'                           WHERE name='LSH50';";
        $updateSteps[] = "UPDATE footprints SET name='TASTER_LSH70'                           WHERE name='LSH70';";
        $updateSteps[] = "UPDATE footprints SET name='TASTER_LSH80'                           WHERE name='LSH80';";
        $updateSteps[] = "UPDATE footprints SET name='TASTER_LSH95'                           WHERE name='LSH95';";
        $updateSteps[] = "UPDATE footprints SET name='IC_LQFP64'                              WHERE name='LQFP64';";
        $updateSteps[] = "UPDATE footprints SET name='IC_LQFP48'                              WHERE name='LQFP48';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_LMI-L115-02'                  WHERE name='LMI-L115-2';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_LMI-L115-03'                  WHERE name='LMI-L115-3';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_LMI-L115-05'                  WHERE name='LMI-L115-5';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_LMI-L115-10'                  WHERE name='LMI-L115-10';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_LMI-L115-20'                  WHERE name='LMI-L115-20';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_10_1'               WHERE name='MATNLOK-926310-1';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_10_2'               WHERE name='MATNLOK-926310-2';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_10_3'               WHERE name='MATNLOK-926310-3';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_10_4'               WHERE name='MATNLOK-926310-4';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_10_5'               WHERE name='MATNLOK-926310-5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_11_1'               WHERE name='MATNLOK-926311-1';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_11_2'               WHERE name='MATNLOK-926311-2';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_11_3'               WHERE name='MATNLOK-926311-3';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_11_4'               WHERE name='MATNLOK-926311-4';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_11_5'               WHERE name='MATNLOK-926311-5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_12_1'               WHERE name='MATNLOK-926312-1';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_12_2'               WHERE name='MATNLOK-926312-2';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_12_3'               WHERE name='MATNLOK-926312-3';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_12_4'               WHERE name='MATNLOK-926312-4';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_12_5'               WHERE name='MATNLOK-926312-5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_13_1'               WHERE name='MATNLOK-926313-1';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_13_2'               WHERE name='MATNLOK-926313-2';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_13_3'               WHERE name='MATNLOK-926313-3';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_13_4'               WHERE name='MATNLOK-926313-4';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_13_5'               WHERE name='MATNLOK-926313-5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_14_1'               WHERE name='MATNLOK-926314-1';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_14_2'               WHERE name='MATNLOK-926314-2';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_14_3'               WHERE name='MATNLOK-926314-3';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_14_4'               WHERE name='MATNLOK-926314-4';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_14_5'               WHERE name='MATNLOK-926314-5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_15_1'               WHERE name='MATNLOK-926315-1';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_15_2'               WHERE name='MATNLOK-926315-2';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_15_3'               WHERE name='MATNLOK-926315-3';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_15_4'               WHERE name='MATNLOK-926315-4';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_15_5'               WHERE name='MATNLOK-926315-5';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_16_1'               WHERE name='MATNLOK-926316-1';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_16_2'               WHERE name='MATNLOK-926316-2';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_16_3'               WHERE name='MATNLOK-926316-3';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_16_4'               WHERE name='MATNLOK-926316-4';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MATNLOK_9263_16_5'               WHERE name='MATNLOK-926316-5';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_MELF'                             WHERE name='MELF';";
        $updateSteps[] = "UPDATE footprints SET name='IC_MBxS'                                WHERE name='MB2S';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_FEMALE-04'            WHERE name='MICROMATCH4F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_FEMALE-06'            WHERE name='MICROMATCH6F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_FEMALE-08'            WHERE name='MICROMATCH8F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_FEMALE-10'            WHERE name='MICROMATCH10F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_FEMALE-12'            WHERE name='MICROMATCH12F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_FEMALE-14'            WHERE name='MICROMATCH14F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_FEMALE-16'            WHERE name='MICROMATCH16F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_FEMALE-18'            WHERE name='MICROMATCH18F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_FEMALE-20'            WHERE name='MICROMATCH20F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_MALE-04'              WHERE name='MICROMATCH4M';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_MALE-06'              WHERE name='MICROMATCH6M';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_MALE-08'              WHERE name='MICROMATCH8M';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_MALE-10'              WHERE name='MICROMATCH10M';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_MALE-12'              WHERE name='MICROMATCH12M';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_MALE-14'              WHERE name='MICROMATCH14M';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_MALE-16'              WHERE name='MICROMATCH16M';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_MALE-18'              WHERE name='MICROMATCH18M';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_MALE-20'              WHERE name='MICROMATCH20M';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_SMD-04'               WHERE name='MICROMATCH-SMD4F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_SMD-06'               WHERE name='MICROMATCH-SMD6F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_SMD-08'               WHERE name='MICROMATCH-SMD8F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_SMD-10'               WHERE name='MICROMATCH-SMD10F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_SMD-12'               WHERE name='MICROMATCH-SMD12F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_SMD-14'               WHERE name='MICROMATCH-SMD14F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_SMD-16'               WHERE name='MICROMATCH-SMD16F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_SMD-18'               WHERE name='MICROMATCH-SMD18F';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE-MICROMATCH_SMD-20'               WHERE name='MICROMATCH-SMD20F';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZ_MM505'                            WHERE name='MM505';";
        $updateSteps[] = "UPDATE footprints SET name='IC_MLF28'                               WHERE name='MLF28';";
        $updateSteps[] = "UPDATE footprints SET name='IC_MLF32'                               WHERE name='MLF32';";
        $updateSteps[] = "UPDATE footprints SET name='IC_MLF44'                               WHERE name='MLF44';";
        $updateSteps[] = "UPDATE footprints SET name='IC_MLF64'                               WHERE name='MLF64';";
        $updateSteps[] = "UPDATE footprints SET name='LED_MINITOP'                            WHERE name='MINITOPLED';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_MINIMELF'                         WHERE name='MINIMELF';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_MICROMELF'                        WHERE name='MICROMELF';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE_RJ11'                            WHERE name='MODULAR-RJ11';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE_RJ11-SHLD'                       WHERE name='MODULAR-RJ11S';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE_RJ12'                            WHERE name='MODULAR-RJ12';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE_RJ12-SHLD'                       WHERE name='MODULAR-RJ12S';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE_RJ45'                            WHERE name='MODULAR-RJ45';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE_RJ45-SHLD'                       WHERE name='MODULAR-RJ45S';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-GERADE_PSL-02'        WHERE name='MOLEX-PSL2G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-GERADE_PSL-03'        WHERE name='MOLEX-PSL3G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-GERADE_PSL-04'        WHERE name='MOLEX-PSL4G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-GERADE_PSL-05'        WHERE name='MOLEX-PSL5G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-GERADE_PSL-06'        WHERE name='MOLEX-PSL6G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-GERADE_PSL-07'        WHERE name='MOLEX-PSL7G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-GERADE_PSL-08'        WHERE name='MOLEX-PSL8G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-GERADE_PSL-09'        WHERE name='MOLEX-PSL9G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-GERADE_PSL-10'        WHERE name='MOLEX-PSL10G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-02'   WHERE name='MOLEX-PSL2W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-03'   WHERE name='MOLEX-PSL3W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-04'   WHERE name='MOLEX-PSL4W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-05'   WHERE name='MOLEX-PSL5W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-06'   WHERE name='MOLEX-PSL6W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-07'   WHERE name='MOLEX-PSL7W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-08'   WHERE name='MOLEX-PSL8W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-09'   WHERE name='MOLEX-PSL9W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-10'   WHERE name='MOLEX-PSL10W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-03'             WHERE name='MOLEX53047-3';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-04'             WHERE name='MOLEX53047-4';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-05'             WHERE name='MOLEX53047-5';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-06'             WHERE name='MOLEX53047-6';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-07'             WHERE name='MOLEX53047-7';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-08'             WHERE name='MOLEX53047-8';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-09'             WHERE name='MOLEX53047-9';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-10'             WHERE name='MOLEX53047-10';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-11'             WHERE name='MOLEX53047-11';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-12'             WHERE name='MOLEX53047-12';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-13'             WHERE name='MOLEX53047-13';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-14'             WHERE name='MOLEX53047-14';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53047-15'             WHERE name='MOLEX53047-15';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-02'             WHERE name='MOLEX53048-2';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-03'             WHERE name='MOLEX53048-3';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-04'             WHERE name='MOLEX53048-4';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-05'             WHERE name='MOLEX53048-5';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-06'             WHERE name='MOLEX53048-6';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-07'             WHERE name='MOLEX53048-7';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-08'             WHERE name='MOLEX53048-8';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-09'             WHERE name='MOLEX53048-9';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-10'             WHERE name='MOLEX53048-10';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-11'             WHERE name='MOLEX53048-11';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-12'             WHERE name='MOLEX53048-12';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-13'             WHERE name='MOLEX53048-13';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-14'             WHERE name='MOLEX53048-14';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53048-15'             WHERE name='MOLEX53048-15';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-02'             WHERE name='MOLEX53261-2';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-03'             WHERE name='MOLEX53261-3';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-04'             WHERE name='MOLEX53261-4';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-05'             WHERE name='MOLEX53261-5';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-06'             WHERE name='MOLEX53261-6';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-07'             WHERE name='MOLEX53261-7';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-08'             WHERE name='MOLEX53261-8';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-09'             WHERE name='MOLEX53261-9';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-10'             WHERE name='MOLEX53261-10';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-11'             WHERE name='MOLEX53261-11';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-12'             WHERE name='MOLEX53261-12';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-13'             WHERE name='MOLEX53261-13';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-14'             WHERE name='MOLEX53261-14';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-MOLEX_53261-15'             WHERE name='MOLEX53261-15';";
        $updateSteps[] = "UPDATE footprints SET name='IC_MULTIWATT15'                         WHERE name='MULTIWATT15';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_MURATA_2012-LQH3C'                WHERE name='MURATA-2012-LQH3C';";
        $updateSteps[] = "UPDATE footprints SET name='RESONATOR-MURATA_CSTCC-G-A'             WHERE name='MURATA-CSTCC-G-A';";
        $updateSteps[] = "UPDATE footprints SET name='EMV-MURATA_NFE61P'                      WHERE name='MURATA-NFE61P';";
        $updateSteps[] = "UPDATE footprints SET name='IC_MSOP10'                              WHERE name='MSOP10';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE_PHONE-JACK'                      WHERE name='PHONE-JACK';";
        $updateSteps[] = "UPDATE footprints SET name='LASER_PDLD-PIGTAIL'                     WHERE name='PDLD';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE_PCPWR514M'                  WHERE name='PCPWR514M';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_PB-G90-1A-1'                     WHERE name='PBG90';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_P600'                             WHERE name='P600';";
        $updateSteps[] = "UPDATE footprints SET name='SCHALTREGLER_NME-S'                     WHERE name='NME-S';";
        $updateSteps[] = "UPDATE footprints SET name='SCHALTREGLER_NMA-D'                     WHERE name='NMA-D';";
        $updateSteps[] = "UPDATE footprints SET name='TRAFO-MYRRA_30-2'                       WHERE name='MYRRA-EI30';";
        $updateSteps[] = "UPDATE footprints SET name='TRAFO-MYRRA_38-2'                       WHERE name='MYRRA-EI38';";
        $updateSteps[] = "UPDATE footprints SET name='TRAFO-MYRRA_48-2'                       WHERE name='MYRRA-EI48';";
        $updateSteps[] = "UPDATE footprints SET name='TRAFO-MYRRA_66-2'                       WHERE name='MYRRA-EI66';";
        $updateSteps[] = "UPDATE footprints SET name='TRAFO-MYRRA_54-2'                       WHERE name='MYRRA-EL54';";
        $updateSteps[] = "UPDATE footprints SET name='TRAFO-MYRRA_60-2'                       WHERE name='MYRRA-EL60';";
        $updateSteps[] = "UPDATE footprints SET name='TRAFO-MYRRA_48-40'                      WHERE name='MYRRA-UI48';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X10'            WHERE name='PHSMD2X10';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X11'            WHERE name='PHSMD2X11';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X12'            WHERE name='PHSMD2X12';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X13'            WHERE name='PHSMD2X13';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X14'            WHERE name='PHSMD2X14';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X15'            WHERE name='PHSMD2X15';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X16'            WHERE name='PHSMD2X16';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X02'            WHERE name='PHSMD2X2';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X03'            WHERE name='PHSMD2X3';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X04'            WHERE name='PHSMD2X4';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X05'            WHERE name='PHSMD2X5';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X06'            WHERE name='PHSMD2X6';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X07'            WHERE name='PHSMD2X7';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X08'            WHERE name='PHSMD2X8';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-SMD_2X09'            WHERE name='PHSMD2X9';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_PT10-H'                         WHERE name='PT10H10';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PSO36'                               WHERE name='PSO36';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PSO20'                               WHERE name='PSO20';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PQFP100'                             WHERE name='PQFP100';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PQFP128'                             WHERE name='PQFP128';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PQFP160'                             WHERE name='PQFP160';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PQFP208'                             WHERE name='PQFP208';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PQFP240'                             WHERE name='PQFP240';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PQFP44'                              WHERE name='PQFP44';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PQFP48'                              WHERE name='PQFP48';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PLCC20'                              WHERE name='PLCC20';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PLCC28'                              WHERE name='PLCC28';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PLCC32'                              WHERE name='PLCC32';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PLCC44'                              WHERE name='PLCC44';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PLCC52'                              WHERE name='PLCC52';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PLCC68'                              WHERE name='PLCC68';";
        $updateSteps[] = "UPDATE footprints SET name='IC_PLCC84'                              WHERE name='PLCC84';";
        $updateSteps[] = "UPDATE footprints SET name='LED_PLCC2'                              WHERE name='PLCC2';";
        $updateSteps[] = "UPDATE footprints SET name='IC_QSOP16'                              WHERE name='QSOP16';";
        $updateSteps[] = "UPDATE footprints SET name='IC_QSOP20'                              WHERE name='QSOP20';";
        $updateSteps[] = "UPDATE footprints SET name='IC_QSOP24'                              WHERE name='QSOP24';";
        $updateSteps[] = "UPDATE footprints SET name='IC_QSOP28'                              WHERE name='QSOP28';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_0102-MLF'                WHERE name='RES-0102MLF';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_0204-MLF'                WHERE name='RES-0204MLF';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_0207-MLF'                WHERE name='RES-0207MLF';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_0402'                    WHERE name='RES-0402';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_0603'                    WHERE name='RES-0603';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_0805'                    WHERE name='RES-0805';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_1206'                    WHERE name='RES-1206';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_1210'                    WHERE name='RES-1210';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_1218'                    WHERE name='RES-1218';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_2010'                    WHERE name='RES-2010';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD_2512'                    WHERE name='RES-2512';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-SMD-ARRAY_4X0603-0612'       WHERE name='RES-4x0603';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_RB1A'                     WHERE name='RB1A';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_RAWA400-9P'                WHERE name='RAWA400-9P';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_RAWA400-8P'                WHERE name='RAWA400-8P';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_RAWA400-11P'               WHERE name='RAWA400-11P';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_RA37-3'                    WHERE name='RA37-3';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-ALU_RH10'                    WHERE name='RH10';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-ALU_RH100'                   WHERE name='RH100';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-ALU_RH100X'                  WHERE name='RH100X';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-ALU_RH25'                    WHERE name='RH25';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-ALU_RH250'                   WHERE name='RH250';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-ALU_RH5'                     WHERE name='RH5-304';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-ALU_RH50'                    WHERE name='RH50';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND-ALU_RH75'                    WHERE name='RH75';";
        $updateSteps[] = "UPDATE footprints SET name='DREHSCHALTER_DIP10-1'                   WHERE name='ROTARYDIP10-1';";
        $updateSteps[] = "UPDATE footprints SET name='DREHSCHALTER_DIP10'                     WHERE name='ROTARYDIP10';";
        $updateSteps[] = "UPDATE footprints SET name='DREHSCHALTER_DIP16-1'                   WHERE name='ROTARYDIP16-1';";
        $updateSteps[] = "UPDATE footprints SET name='DREHSCHALTER_DIP16'                     WHERE name='ROTARYDIP16';";
        $updateSteps[] = "UPDATE footprints SET name='RELAIS_RY2'                             WHERE name='RY2';";
        $updateSteps[] = "UPDATE footprints SET name='SD-KARTE_Schwarz'                       WHERE name='SD-CARD';";
        $updateSteps[] = "UPDATE footprints SET name='IC_BECK-SC12'                           WHERE name='SC12';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_S64Y'                           WHERE name='S64Y';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SHARP-S2xxEx'                        WHERE name='S2XXEX';";
        $updateSteps[] = "UPDATE footprints SET name='SCHIEBESCHALTER_SECME-1K2-RH'           WHERE name='SECME1K2RH';";
        $updateSteps[] = "UPDATE footprints SET name='SCHIEBESCHALTER_SECME-1K2-RL'           WHERE name='SECME1K2RL';";
        $updateSteps[] = "UPDATE footprints SET name='SCHIEBESCHALTER_SECME-1K2-SH'           WHERE name='SECME1K2SH';";
        $updateSteps[] = "UPDATE footprints SET name='SCHIEBESCHALTER_SECME-1K2-SL'           WHERE name='SECME1K2SL';";
        $updateSteps[] = "UPDATE footprints SET name='SCHIEBESCHALTER_SECME-1K2-SLB'          WHERE name='SECME1K2SLB';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_SFT1030'                          WHERE name='SFT1030';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_SFT1040'                          WHERE name='SFT1040';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_SFT1240'                          WHERE name='SFT1240';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_SFT830D'                          WHERE name='SFT830D';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_SFT830S'                          WHERE name='SFT830S';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_SFT840D'                          WHERE name='SFT840D';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL04'                       WHERE name='SIL4';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL05'                       WHERE name='SIL5';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL06'                       WHERE name='SIL6';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL07'                       WHERE name='SIL7';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL08'                       WHERE name='SIL8';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL09'                       WHERE name='SIL9';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL10'                       WHERE name='SIL10';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL11'                       WHERE name='SIL11';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL12'                       WHERE name='SIL12';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL13'                       WHERE name='SIL13';";
        $updateSteps[] = "UPDATE footprints SET name='WIDERSTAND_SIL14'                       WHERE name='SIL14';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-254-MC'              WHERE name='SK104-254MC';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-254-STIS'            WHERE name='SK104-254STIS';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-254-STS'             WHERE name='SK104-254STS';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-254-STSB'            WHERE name='SK104-254STSB';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-381-MC'              WHERE name='SK104-381MC';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-381-STIS'            WHERE name='SK104-381STIS';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-381-STS'             WHERE name='SK104-381STS';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-381-STSB'            WHERE name='SK104-381STSB';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-508-MC'              WHERE name='SK104-508MC';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-508-STIS'            WHERE name='SK104-508STIS';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-508-STS'             WHERE name='SK104-508STS';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-508-STSB'            WHERE name='SK104-508STSB';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-635-MC'              WHERE name='SK104-635MC';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-635-STIS'            WHERE name='SK104-635STIS';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-635-STS'             WHERE name='SK104-635STS';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK104-635-STSB'            WHERE name='SK104-635STSB';";
        $updateSteps[] = "UPDATE footprints SET name='TASTER_SKHH-3MM'                        WHERE name='SKHH-V4x3Y';";
        $updateSteps[] = "UPDATE footprints SET name='GLEICHRICHTER_SKBB'                     WHERE name='SKBB';";
        $updateSteps[] = "UPDATE footprints SET name='KUEHLKOERPER_SK96-84'                   WHERE name='SK96-84';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_SMA-JH'                       WHERE name='SMA-JH';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER_SMA-JV'                       WHERE name='SMA-JV';";
        $updateSteps[] = "UPDATE footprints SET name='TRAFO-SMD_LP-500X'                      WHERE name='SMLP500x';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_SMSL-1305'                        WHERE name='SMSL1305';";
        $updateSteps[] = "UPDATE footprints SET name='TRAFO-SMD_SL2'                          WHERE name='SMSL2';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD123-1'                         WHERE name='SOD123A';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD123-3'                         WHERE name='SOD123B';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD123-5'                         WHERE name='SOD123C';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD57'                            WHERE name='SOD57';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD61-A'                          WHERE name='SOD61A';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD61-B'                          WHERE name='SOD61B';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD61-C'                          WHERE name='SOD61C';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD61-D'                          WHERE name='SOD61D';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD61-E'                          WHERE name='SOD61E';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD64'                            WHERE name='SOD64';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD80'                            WHERE name='SOD80';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD81'                            WHERE name='SOD81';";
        $updateSteps[] = "UPDATE footprints SET name='DIODE_SOD87'                            WHERE name='SOD87';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO14'                                WHERE name='SOIC14';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO16'                                WHERE name='SOIC16';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO16W'                               WHERE name='SOIC16W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO18W'                               WHERE name='SOIC18W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO20W'                               WHERE name='SOIC20W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO24W'                               WHERE name='SOIC24W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO28W'                               WHERE name='SOIC28W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO32-400'                            WHERE name='SOIC32';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO32-525'                            WHERE name='SOIC32W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SO08'                                WHERE name='SOIC8';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SOT143'                              WHERE name='SOT143';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SOT223'                              WHERE name='SOT223';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SOT23-5'                             WHERE name='SOT23-5';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SOT23-6'                             WHERE name='SOT23-6';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SOT23'                               WHERE name='SOT23';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SOT363'                              WHERE name='SOT363';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SQFP100'                             WHERE name='SQFP14X20';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SQFP64'                              WHERE name='SQFP64';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SSOP14'                              WHERE name='SSOP14';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SSOP16'                              WHERE name='SSOP16';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SSOP20'                              WHERE name='SSOP20';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SSOP24'                              WHERE name='SSOP24';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SSOP28'                              WHERE name='SSOP28';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SSOP30'                              WHERE name='SSOP30';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SSOP48'                              WHERE name='SSOP48';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SSOP56'                              WHERE name='SSOP56';";
        $updateSteps[] = "UPDATE footprints SET name='IC_SSOP56DL'                            WHERE name='SSOP56DL';";
        $updateSteps[] = "UPDATE footprints SET name='BUZZER_TDB'                             WHERE name='TDB';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_T18'                            WHERE name='T18';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_T7-YA'                          WHERE name='T7YA';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_T7-YB'                          WHERE name='T7YB';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX14'                           WHERE name='TEX14';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX16'                           WHERE name='TEX16';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX18'                           WHERE name='TEX18';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX20'                           WHERE name='TEX20';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX22'                           WHERE name='TEX22';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX24'                           WHERE name='TEX24';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX24W'                          WHERE name='TEX24W';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX28'                           WHERE name='TEX28';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX28W'                          WHERE name='TEX28W';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX32W'                          WHERE name='TEX32W';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX40W'                          WHERE name='TEX40W';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX40WW'                         WHERE name='TEX40WW';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX42W'                          WHERE name='TEX42W';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX48W'                          WHERE name='TEX48W';";
        $updateSteps[] = "UPDATE footprints SET name='SOCKEL_TEX64WW'                         WHERE name='TEX64W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO126'                               WHERE name='TO126';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO18'                                WHERE name='TO18';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO18D'                               WHERE name='TO18D';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO202'                               WHERE name='TO202';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO218'                               WHERE name='TO218';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO220'                               WHERE name='TO220-3';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO220-5'                             WHERE name='TO220-5';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO247'                               WHERE name='TO247';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO252'                               WHERE name='TO252';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO263'                               WHERE name='TO263';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO3'                                 WHERE name='TO3';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO39-4'                              WHERE name='TO39-4';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO39'                                WHERE name='TO39';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO51'                                WHERE name='TO51';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO52'                                WHERE name='TO52';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO66'                                WHERE name='TO66';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO72-3'                              WHERE name='TO72-3';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO72-4'                              WHERE name='TO72-4';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO92-2'                              WHERE name='TO92-2';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO92'                                WHERE name='TO92-3';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TO92-G4'                             WHERE name='TO92-3G';";
        $updateSteps[] = "UPDATE footprints SET name='LASER_TORX173'                          WHERE name='TORX173';";
        $updateSteps[] = "UPDATE footprints SET name='LASER_TOTX173'                          WHERE name='TOTX173';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TQFP100'                             WHERE name='TQPP100';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TQFP112'                             WHERE name='TQFP112';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TQFP144'                             WHERE name='TQFP144';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TQFP32'                              WHERE name='TQFP32';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TQFP44'                              WHERE name='TQFP44';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TQFP64'                              WHERE name='TQFP64';";
        $updateSteps[] = "UPDATE footprints SET name='BUCHSE_RJ45-SHLD-LED'                   WHERE name='TRJ19201';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_TSM-4YJ'                        WHERE name='TSM4YJ';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_TSM-4YL'                        WHERE name='TSM4YL';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_TSM-4ZJ'                        WHERE name='TSM4ZJ';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_TSM-4ZL'                        WHERE name='TSM4ZL';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_TS53-YJ'                        WHERE name='TS53YJ';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMER_TS53-YL'                        WHERE name='TS53YL';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSOP32'                              WHERE name='TSSOP32W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSOP48'                              WHERE name='TSSOP48W';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSOP86'                              WHERE name='TSSOP86';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSSOP08'                             WHERE name='TSSOP8';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSSOP14'                             WHERE name='TSSOP14';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSSOP16'                             WHERE name='TSSOP16';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSSOP20'                             WHERE name='TSSOP20';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSSOP24'                             WHERE name='TSSOP24';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSSOP28'                             WHERE name='TSSOP28';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSSOP48'                             WHERE name='TSSOP48';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSSOP56'                             WHERE name='TSSOP56';";
        $updateSteps[] = "UPDATE footprints SET name='IC_TSSOP64'                             WHERE name='TSSOP64';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_TYCO_H38'                         WHERE name='TYCO-H38';";
        $updateSteps[] = "UPDATE footprints SET name='TRIMMKONDENSATOR-SCHWARZ_TZ03F'         WHERE name='TZ03F';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER-USB_A-1'                      WHERE name='USB-A1';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER-USB_A-2'                      WHERE name='USB-A2';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER-USB_B-1'                      WHERE name='USB-B1';";
        $updateSteps[] = "UPDATE footprints SET name='VERBINDER-USB_B-2'                      WHERE name='USB-B2';";
        $updateSteps[] = "UPDATE footprints SET name='IC_UMAX10'                              WHERE name='UMAX10';";
        $updateSteps[] = "UPDATE footprints SET name='IC_UMAX08'                              WHERE name='UMAX8';";
        $updateSteps[] = "UPDATE footprints SET name='IC_VSO40'                               WHERE name='VSO40';";
        $updateSteps[] = "UPDATE footprints SET name='IC_VSO56'                               WHERE name='VSO56';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_102'                    WHERE name='WAGO233-102';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_103'                    WHERE name='WAGO233-103';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_104'                    WHERE name='WAGO233-104';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_105'                    WHERE name='WAGO233-105';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_106'                    WHERE name='WAGO233-106';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_107'                    WHERE name='WAGO233-107';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_108'                    WHERE name='WAGO233-108';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_109'                    WHERE name='WAGO233-109';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_110'                    WHERE name='WAGO233-110';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_112'                    WHERE name='WAGO233-112';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_116'                    WHERE name='WAGO233-116';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_124'                    WHERE name='WAGO233-124';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_136'                    WHERE name='WAGO233-136';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_148'                    WHERE name='WAGO233-148';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_202'                    WHERE name='WAGO233-202';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_203'                    WHERE name='WAGO233-203';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_204'                    WHERE name='WAGO233-204';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_205'                    WHERE name='WAGO233-205';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_206'                    WHERE name='WAGO233-206';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_207'                    WHERE name='WAGO233-207';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_208'                    WHERE name='WAGO233-208';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_209'                    WHERE name='WAGO233-209';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_210'                    WHERE name='WAGO233-210';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_212'                    WHERE name='WAGO233-212';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_216'                    WHERE name='WAGO233-216';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_224'                    WHERE name='WAGO233-224';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_236'                    WHERE name='WAGO233-236';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_248'                    WHERE name='WAGO233-248';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_402'                    WHERE name='WAGO233-402';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_403'                    WHERE name='WAGO233-403';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_404'                    WHERE name='WAGO233-404';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_405'                    WHERE name='WAGO233-405';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_406'                    WHERE name='WAGO233-406';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_407'                    WHERE name='WAGO233-407';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_408'                    WHERE name='WAGO233-408';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_409'                    WHERE name='WAGO233-409';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_410'                    WHERE name='WAGO233-410';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_412'                    WHERE name='WAGO233-412';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_416'                    WHERE name='WAGO233-416';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_424'                    WHERE name='WAGO233-424';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_436'                    WHERE name='WAGO233-436';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_448'                    WHERE name='WAGO233-448';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_502'                    WHERE name='WAGO233-502';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_503'                    WHERE name='WAGO233-503';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_504'                    WHERE name='WAGO233-504';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_505'                    WHERE name='WAGO233-505';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_506'                    WHERE name='WAGO233-506';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_507'                    WHERE name='WAGO233-507';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_508'                    WHERE name='WAGO233-508';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_509'                    WHERE name='WAGO233-509';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_510'                    WHERE name='WAGO233-510';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_512'                    WHERE name='WAGO233-512';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_516'                    WHERE name='WAGO233-516';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_524'                    WHERE name='WAGO233-524';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_536'                    WHERE name='WAGO233-536';";
        $updateSteps[] = "UPDATE footprints SET name='KLEMME-WAGO-233_548'                    WHERE name='WAGO233-548';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_733-02'                WHERE name='WAGO733-332';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_733-03'                WHERE name='WAGO733-333';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_733-04'                WHERE name='WAGO733-334';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_733-05'                WHERE name='WAGO733-335';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_733-06'                WHERE name='WAGO733-336';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_733-07'                WHERE name='WAGO733-337';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_733-08'                WHERE name='WAGO733-338';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_733-09'                WHERE name='WAGO733-340';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_733-10'                WHERE name='WAGO733-342';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-02'                WHERE name='WAGO734-132';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-03'                WHERE name='WAGO734-133';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-04'                WHERE name='WAGO734-134';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-05'                WHERE name='WAGO734-135';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-06'                WHERE name='WAGO734-136';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-07'                WHERE name='WAGO734-137';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-08'                WHERE name='WAGO734-138';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-09'                WHERE name='WAGO734-139';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-10'                WHERE name='WAGO734-140';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-11'                WHERE name='WAGO734-142';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-12'                WHERE name='WAGO734-143';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-13'                WHERE name='WAGO734-146';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-14'                WHERE name='WAGO734-148';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-15'                WHERE name='WAGO734-150';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-WAGO_734-16'                WHERE name='WAGO734-154';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_WE612SV'                          WHERE name='WE612SV';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_WE622MV'                          WHERE name='WE622MV';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_WE632LV'                          WHERE name='WE632LV';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_WE642XV'                          WHERE name='WE642XV';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_PD_S'                             WHERE name='WED-S';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_PD_L'                             WHERE name='WEPD-L';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_PD_M'                             WHERE name='WEPD-M';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_PD_XL'                            WHERE name='WEPD-XL';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_PD_XXL'                           WHERE name='WEPD-XXL';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_PD4'                              WHERE name='WEPD4';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_PDM'                              WHERE name='WEPDM';";
        $updateSteps[] = "UPDATE footprints SET name='SPULE_WESV'                             WHERE name='WESV';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X03'         WHERE name='WS6G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X05'         WHERE name='WS10G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X07'         WHERE name='WS14G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X08'         WHERE name='WS16G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X10'         WHERE name='WS20G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X13'         WHERE name='WS26G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X17'         WHERE name='WS34G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X20'         WHERE name='WS40G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X25'         WHERE name='WS50G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-GERADE-RAHMEN_2X32'         WHERE name='WS64G';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-ABGEWINKELT-RAHMEN_2X05'    WHERE name='WS10W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-ABGEWINKELT-RAHMEN_2X07'    WHERE name='WS14W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-ABGEWINKELT-RAHMEN_2X08'    WHERE name='WS16W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-ABGEWINKELT-RAHMEN_2X10'    WHERE name='WS20W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-ABGEWINKELT-RAHMEN_2X13'    WHERE name='WS26W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-ABGEWINKELT-RAHMEN_2X17'    WHERE name='WS34W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-ABGEWINKELT-RAHMEN_2X20'    WHERE name='WS40W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-ABGEWINKELT-RAHMEN_2X25'    WHERE name='WS50W';";
        $updateSteps[] = "UPDATE footprints SET name='STIFTLEISTE-ABGEWINKELT-RAHMEN_2X32'    WHERE name='WS64W';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZOSZILLATOR_DIP8'                   WHERE name='XTAL-DIP8';";
        $updateSteps[] = "UPDATE footprints SET name='QUARZOSZILLATOR_DIP14'                  WHERE name='XTAL-DIP14';";
        $updateSteps[] = "UPDATE footprints SET name='KARTENSLOT_SD'                          WHERE name='YAMAICHI-FPS';";
        $updateSteps[] = "UPDATE footprints SET name=''                                       WHERE name='';";
		break;
      
      case 9:
        $updateSteps[] = "ALTER TABLE `parts` ADD `description` mediumtext AFTER `name`;";
        $updateSteps[] = "ALTER TABLE `parts` ADD `visible`     boolean NOT NULL AFTER `obsolete`;";
        break;

/*
      case 10:
        $updateSteps[] = "INSERT INTO internal SET keyName='test', keyValue='muh';";
        break;
      case 11:
        $updateSteps[] = ""; //INSERT INTO internal SET keyName='test2', keyValue='muh2';";
        break;
      case 12:
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
