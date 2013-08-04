<?php
/*
    ---------------------------------------------------------------------------------------

    phpBookWorm: Filemanager

    Copyright: CC-BY-SA 3.0, 2012-2013
    Author: Udo Neist (webmaster@singollo.de, GPG-Fingerprint 4A8F B229 2AE9 9634 04D1 E2F0 21F2 E27D FE97 D87F)

    Part: class.fileio.php

    Info: Webschnittstelle (Down- und Upload)

    ---------------------------------------------------------------------------------------

    27.10.2012: Erste Version (teilweise übernommen von ChemDB34 (c) by Udo Neist) (Udo Neist)
    03.01.2013: Ergängzung um getFiles() und readDir() (Udo Neist)
    04.01.2013: Kopfzeilen geändert (Udo Neist)
    26.01.2013: aboutFile() geändert (Udo Neist)
    03.03.2013: readDir() um eine Option "onlydir" erweitert (Udo Neist)
*/


class fileIO {

    // Realer Name, liegt so als Datei vor
    private $filename;
    /*
        Name des eigentlichen Downloads
        Das Forum, der Downloadmanager und auch das Dokumentenmanagement speichern verschiedene
    */
    private $dfile;
    private $mime;
    private $dir;
    private $string;
    private $files;

    var $error;
    var $errno;

    function __construct() {
    /*
        Initialisiert die Variablen
    */

        settype($this -> filename, "string");
        settype($this -> dfile, "string");
        settype($this -> mime, "string");
        settype($this -> string, "string");
        settype($this -> dir, "string");
        settype($this -> error, "string");
        settype($this -> erno, "integer");
        settype($this -> files, "array");

        $this -> filename = '';
        $this -> dfile = '';
        $this -> dir = '.';
        $this -> string = '';
        unset($this->error);
        unset($this->errno);

        $this -> clearFiles();

    }

    function setFileName($filename='',$dfilename='') {
    /*
        Setzt den Dateinamen bzw. die URL und optional den Namen der Datei für den Download.

        * Variablen
        $filename (string): File oder URL
        $dfilename (string): Dateinamen für Download (optional)

        * Rückgabe
        keine
    */
        settype($filename, "string");
        settype($dfilename, "string");

        $this -> filename = $filename;
        $this -> dfile = $dfilename;
    }

    function setMimeType($mime='') {
    /*
        Setzt den Mime-TysetFileNamep.

        * Variablen
        $mime (string): Mime-Typ

        * Rückgabe
        keine
    */
        settype($mime, "string");

        $this -> mime = $mime;
    }

    function setString($string='') {
    /*
        Setzt $this->string.

        * Variablen
        $string (string): String zum Schreiben

        * Rückgabe
        keine
    */
        settype($mime, "string");

        $this -> string = $string;
    }

    function getString() {
    /*
        Gibt den String aus readFile zurück.

        * Variablen
        keine

        * Rückgabe
        $this->string (string): Inhalt
    */
        return $this -> string;
    }

    function getFiles()  {
    /*
        Gibt das Array aus readDir zurück.

        * Variablen
        keine

        * Rückgabe
        $this->files (array): Inhalt
    */
        return $this -> files;
    }

    function clearFiles()  {
    /*
        Löscht das Array für readDir().

        * Variablen
        keine

        * Rückgabe
        $this->files (array): Inhalt
    */
        $this -> files = array();
    }

    function aboutFile() {
    /*
        Liest Infos über das angeforderte File
    */

        clearstatcache();

        $info = array();

        $finfo = finfo_open(FILEINFO_CONTINUE);
        $info['mime'] = finfo_file($finfo, $this -> filename);
        finfo_close($finfo);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $info['mimetype'] = finfo_file($finfo, $this -> filename);
        finfo_close($finfo);

        $info['size'] = filesize($this -> filename);
        $info['date'] = date('d.m.Y H:i',filectime($this -> filename));

        $perms = fileperms($this -> filename);
        if ($perms) {
            $ts=array(
                0140000=>'ssocket',
                0120000=>'llink',
                0100000=>'-file',
                0060000=>'bblock',
                0040000=>'ddir',
                0020000=>'cchar',
                0010000=>'pfifo'
            );
            $t=decoct($perms & 0170000); // File Encoding Bit
            $info['mode'] =(array_key_exists(octdec($t),$ts))?$ts[octdec($t)]{0}:'u';
            $info['mode'].=(($perms&0x0100)?'r':'-').(($perms&0x0080)?'w':'-');
            $info['mode'].=(($perms&0x0040)?(($perms&0x0800)?'s':'x'):(($perms&0x0800)?'S':'-'));
            $info['mode'].=(($perms&0x0020)?'r':'-').(($perms&0x0010)?'w':'-');
            $info['mode'].=(($perms&0x0008)?(($perms&0x0400)?'s':'x'):(($perms&0x0400)?'S':'-'));
            $info['mode'].=(($perms&0x0004)?'r':'-').(($perms&0x0002)?'w':'-');
            $info['mode'].=(($perms&0x0001)?(($perms&0x0200)?'t':'x'):(($perms&0x0200)?'T':'-'));
        }

        return $info;
    }

    function readFile() {
    /*
        Liest das angebene File und gibt den Inhalt zurück

        * Variablen
        $this->filename (string): Dateinamen oder URL (mit http://)

        * Rückgabe
        $this->string (string): Inhalt

        * Anmerkung
        Ist allow_url_fopen=off, dann wird Fehler 255 zurückgegeben.
    */

        $this -> setString();

        unset($this->error);
        unset($this->errno);

        // Prüft auf URL und absoluten Pfad
        if (!stristr($this->filename,"http://")) {
            if (substr($this->filename,0,1) != '/') $this->filename = TEMP.'/'.$this->filename;
            $this->filename = 'file://'.$this->filename;
        }

        if (function_exists('curl_init')) {
            // Die Session initialisieren
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->filename);
            // Session Optionen setzen
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            // Ausführen der Aktionen
            $this->string = curl_exec($ch);
            // Fehlercodes
            $this->error = curl_error ($ch);
            $this->errno = curl_errno ($ch);
            // Session beenden
            curl_close($ch);
            if ($this->errno = 0) {
                return true;
            }else{
                return false;
            }
        }elseif(ini_get('allow_url_fopen')=='1'){
            $handler = @fopen($this->filename,'rb');
            if (!$handler) {
                $this->error = "Error: can't open handler.";
                $this->errno = 2;
                return false;
            }
            $this->string = fgets($handler);
            fclose($handler);
            $this->errno = 0;
            return true;
        }else{
            $this->error = "Error: can't open page.";
            $this->errno = 255;
            return false;
        }
    }

    function writeFile() {
    /*
        Schreibt ein angegebenes File

        * Variablen
        $this->filename (string): Dateinamen (lokal)
        $this->string (string): Inhalt

        * Rückgabe
        Statusinfo (true oder false)
    */

        unset($this->error);
        unset($this->errno);

        // Erstmal auf Dateinamen prüfen (weitere Checks folgen)
        if (!$this->filename) {
            $this->error = 'No filename';
            $this->errno = 1;
            return false;
        }

        // Filehandler öffnen
        $handler = @fopen($this->filename,'wb');
        if (!$handler) {
            $this->error = "Error: can't open handler.";
            $this->errno = 2;
            return false;
        }

        fwrite($handler,$this->string);
        fclose($handler);
        $this->errno = 0;
        return true;
    }

    function showFile($mode='') {
    /*
        Sucht das angegebene File und bietet es zum Download an.

        * Variablen
        $mode (string): Datei als Download ($mode='attachment') anbieten oder anzeigen (default)
        $this->filename (string): Filename mit kompletten Pfad
        $this->dfile (string): Filename für den Download (optional)
        $this->mime (string): Mimetyp

        * Rückgabe
        Datei oder Fehler (false)

        Basis-Funktion eines Downloadmanagers. Angabe des MIME-Typen ist erforderlich.
    */

        if (is_readable($this->filename)) {

            ob_clean();

            header('HTTP/1.1 200 OK');
            header('Status: 200 OK');
            header("Cache-Control: public, max-age=0"); // HTTP/1.1
            header("Pragma: public");
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
            header('Content-Length: '.filesize($this->filename));
            header('Content-Transfer-Encoding: binary');
            header('Content-type: '.$this->mime);
            header('Content-Disposition: '.(($mode=='attachment')?'attachment':'inline').';filename="'.(($this->dfile)?$this->dfile:$this->filename).'"');
            readfile($this->filename);
            exit();
        }else{
            return false;
        }
    }

    function readDir($path = '.',$search = '',$exclude = '', $options = array('mime'=>array(),'recursive'=>true,'onlyfiles'=>false,'onlydir'=>false,'stripdir'=>false)) {
    /*
        Durchforstet das übergebene Verzeichnis rekursiv nach Dateien

        * Variablen
        $this -> files (array): Container für Dateien
        $path (string): Suchpfad (Default: .)
        $search (string): Suchmuster für preg_match()
        $exclude (string): Suchmuster für preg_match() zum Ausschliessen von Dateien/Verzeichnissen
        $options (array):
            recursive (bool): Rekursiv suchen
            onlyfiles (bool): Nur Dateien
            onlydir (bool): Nur Verzeichnisse, Optionen für Dateien sind unwirksam
            stripdir (bool): Gibt nur den Filenamen zurück
            mime (string): Mimetyp

        * Rückgabe
        $this -> files (array): Liste der Ergebnisse

        * Anforderung
        Kein Datenbanklink erforderlich!

        * Anmerkung
        Der Punkt ist das aktuelle Verzeichnis, ".." kennt man als "Verzeichnis höher" und "dir/" als "Verzeichnis tiefer".

    */

        if (!$path) return false;
        // $this->files wird initialisiert, falls noch nicht geschehen
        if (!is_array($this -> files)) $this -> clearFiles();

        // Pfad setzen
        $dir = dir($path);
        // Pfad durchsuchen
        while (($file = $dir->read()) !== false) {
            // Verzeichnisebenen überspringen
            if ($file =='.' || $file =='..' || preg_match('/'.$exclude.'/i',$file)==1) continue;
            if (is_dir($path."/".$file) && $options['onlydir']===true) $this -> files[] = (($options['stripdir']===true)?$file:$path."/".$file);
            if (is_dir($path."/".$file) && $options['recursive']===true) {
                // Falls Verzeichnis, dann rekursiv die Funktion nochmal aufrufen
                $this -> readDir($path."/".$file,$search,$exclude,$options);
            }else{
                if ($options['onlydir']===false && count($options['mime'])>0 && is_file($path."/".$file)) {
                    if (function_exists(finfo_file)) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                        $mime = finfo_file($finfo, $path."/".$file);
                        finfo_close($finfo);
                        $mime = explode('; ',$mime);
                        if (!in_array($mime[0],$options['mime'])) continue;
                    }elseif (function_exists(mime_content_type)) {
                        if (!in_array(mime_content_type($path."/".$file),$options['mime'])) continue;
                    }
                }
                if (preg_match('/'.$search.'/i',$file==1)) {
                    // Bei Treffer in $search und nicht ausgeschlossen durch $exclude wird das File in das Array $this->files aufgenommen,
                    // aber abhängig von $options['onlydir'] bzw. $options['onlyfiles'] wird entweder alles, nur Files oder nur Verzeichnisse.
                    if ($options['onlyfiles']===true && !is_dir($path."/".$file)) $this -> files[] = (($options['stripdir']===true)?$file:$path."/".$file);
                    if ($options['onlyfiles']===false) $this -> files[] = (($options['stripdir']===true && !is_dir($path."/".$file))?$file:$path."/".$file);
                }
            }
        }
        // Verzeichnishandler schliessen
        $dir->close();
    }

// Ende der Klasse

}
?>
