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

    $Id: class/update.php 515 2012-08-11 weinbauer73@gmail.com $

    Edits:

    2012-08-28 weinbauer73@gmail.com
    - Functions f_*() declared as private

    2012-08-29 weinbauer73@gmail.com
    - move class update to namespace system/interpreter

    2012-08-30 weinbauer73@gmail.com
    - checksum/hash added
*/

namespace system;

require_once('pack.php');

class interpreter
{

    /*
    *
    *   Download
    /
    */

    private $url = '';
    private $lof = array();

    /*
    *   Errorcodes:
    *
    *   0: OK
    *   1: File or Directory not found
    *   2: Error backup/rollback
    *   3: Cannot chmod
    *   4: mkdir failed
    *   100: Failed to execute command
    *   110: Checksum error
    *   200: Parse Error: nothing to parse
    */

    private $errno = 0;

    /*
    *
    *   Parser
    *
    */

    private $script_pattern = ' :: ';
    private $script_checksum = 'sha256';
    private $script_name = '';
    private $script_path = '';
    private $script_critical = true;
    private $script = '';

    /*
    *
    *   Backup
    *
    */

    private $backup_files = array();

    /*
    *
    *   Log
    *
    */

    private $debug = false;
    private $silent = false;
    private $log = array();
    private $report = array();

    function __construct ()
    {

        /*
        *   Konstruktur
        */

        // Download
        settype($url,'string');
        settype($lof,'array');
        // Error
        settype($errno,'integer');
        // Parser
        settype($script_pattern,'string');
        settype($script_checksum,'string');
        settype($script_name,'string');
        settype($script_path,'string');
        settype($script_critical,'boolean');
        settype($script,'string');
        // Cleanup
        settype($backup_files,'array');
        // Log
        settype($log,'array');
        settype($report,'array');
        settype($debug,'boolean');
        settype($silent,'boolean');

    }

    /* Download */

    function download_list ( $url = '' )
    {

        /*
        *   ToDo: Lädt die Liste der verfügbaren Updates von der angegebenen URL und gibt diese als Array zurück.
        */

        return array();

    }

    function download_file ( $updatefile = '' )
    {

        /*
        *   ToDo: Lädt das Update-Archiv.
        */

        return true;

    }

    /* Interpreter */

    function check_script ( $revision = 0 )
    {

        /*
        *   Prüft, ob das passende Archiv zur angegebenen Revision existiert.
        */

        //if ( $revision == 0 || ! is_readable (BASE.'/'.$this -> script_path.'/part-db_upd_rev'.$revision.'.zip') ) return false;
        $this -> revision = $revision;

    }

    function load_script ( $file = '' )
    {

        /*
        *   Lädt das Updatescript
        */

        if ( $this -> revision == 0 ) return false;

        echo 'loading '.$file.'...<br>';
        $this -> clear_errno();
        if ( $this -> check_file_exists( $file ) ) return $this -> errno;
        $this -> script = file ($file,FILE_IGNORE_NEW_LINES);
        if ( count( $this -> script ) > 0 )
        {
            $this -> script_path = dirname ($file);
            $this -> script_name = basename ($file);
        }
        return;

    }

    function parse_script ()
    {
        $this -> clear_errno();
        $this -> script_critical = true;
        if ( count( $this -> script ) == 0 ) return $this -> set_errno(2);
        echo 'executing '.$this -> script_path.'/'.$this -> script_name.'...<br>';
        /* Splittet die Zeile in Command / Option / Checksumme (md5, sha1 etc.) */
        $lines = array();
        foreach ($this -> script as $row)
        {
            $line = explode($this -> script_pattern,$row);
            $lines[] = array('command'=>$line[0],'option'=>$line[1]);
        }

        $this -> clear_log();

        echo 'using '.$this -> script_checksum.' as hash-algorithm<br>';
        $this -> f_log('using '.$this -> script_checksum.' as hash-algorithm');

        foreach ($lines as $row=>$command)
        {
            if ( $command['command'][0] <> '#' && $command['command'][0] <> ';')
            {

                if ( ! $this -> silent ) echo 'executing '.$command['command'].' => '.$command['option'].'<br>';

                /* Befehle mit Rollback */
                if ( $command['command'] == 'copy' && $this -> f_copy ($command['option']) == 100 ) $this -> file_rollback($command['option']);
                if ( $command['command'] == 'delete' && $this -> f_delete ($command['option']) >0 ) $this -> file_rollback($command['option']);
                if ( $command['command'] == 'sql')
                {
                    $log = $this -> f_sql ($command['option']);
                    if ( is_array($log) )
                    {
                        foreach ($log as $errline => $errcode) $this -> f_log('sql-error: '.$errcode['sql'].' -> '.$errcode['error']);
                        $this -> db_rollback();
                    }
                }

                /* Befehle ohne Rollback, da ein Fehlschlag keine Änderung mit sich bringt */
                if ( $command['command'] == 'chmod' ) $this -> f_chmod ($command['option']);
                if ( $command['command'] == 'mkdir' ) $this -> f_mkdir ($command['option']);
                if ( $command['command'] == 'rmdir' ) $this -> f_rmdir ($command['option']);
                if ( $command['command'] == 'unpack' ) $this -> f_unpack ($command['option']);
                if ( $command['command'] == 'sync' ) $this -> f_sync ($command['option']);

                /* Log */
                if ( $command['command'] == 'log' ) $this -> f_log ('comment: '.$command['option']);

                /* Setzt oder Löscht die Fehlerbehandlung */
                if ( $command['command'] == 'critical' && ( $command['option'] == 'true' ||  $command['option'] == '1' ) ) $this -> f_critical ();
                if ( $command['command'] == 'critical' && ( $command['option'] == 'false' ||  $command['option'] == '0' ) ) $this -> f_uncritical ();
                if ( ! $this -> script_critical && $this -> errno > 0 ) $this -> clear_errno();

                $this -> show_log();
                $this -> log = array();

                if ( $this -> errno > 0 )
                {
                    $this -> f_log('something getting wrong, stop executing script!');
                    echo '<br><span style="color:red;">something getting wrong, stop executing script!</span><br>';
                    break;
                }
            }
        }

        if ( $this -> errno == 0 ) echo '<br><span style="color:green;">executing of script: success</span><br>';
        $this -> cleanup();
        echo '<span style="color:blue;">done!</span><br>';

        return $this -> errno;

    }

    /* Primäre Funktionen */

    private function f_copy ( $options = '' )
    {

        /*
        *   Kopiert die Datei aus dem Updateverzeichnis ins normale Verzeichnis
        *
        *   ToDo: Code für Checksumme
        */

        $this -> clear_errno();

        // Erster Test, ob String $file nicht leer ist.
        if ( $options == '' )
        {
            $this -> f_log('copy file '.str_replace(BASE,'',$source).' to '.str_replace(BASE,'',$target).': failed -> file not specified');
            return $this -> set_errno(1);
        }
        // Prüft auf Leerzeichen, da dort eine Checksumme hinterlegt sein könnte.
        $line = explode(' ',$options);
        $file = (( count( $line )>0 )?$line[0]:$options);
        // Quelle
        $source = BASE.'/'.$this -> script_path.'/files/'.$file;
        // Ziel
        $target = BASE.'/'.$file;
        // Checksumme
        if ( $line[1] )
        {
            $checksum = hash_file( $this -> script_checksum, $source );
            // Bei Checksummenfehler mit Fehlercode 110 zurück
            if ( $checksum <> $line[1] ) { return $this -> set_errno(110); }
        }

        // Test auf Quelle
        if ( $this -> check_file_exists( $source ) )
        {
            $this -> f_log('copy file '.str_replace(BASE,'',$source).' to '.str_replace(BASE,'',$target).': failed -> file not found');
            return $this -> set_errno(1);
        }

        // Backup des Targets ohne Auswertung der Rückgabe, da auch noch nicht existerende Ziele behandelt werden müssen.
        $this -> file_backup( $file );
        // Logfile
        if ( ! $this -> silent ) echo '&nbsp;-&nbspcopying '.str_replace(BASE,'',$source).' to '.str_replace(BASE,'',$target).(($checksum)?' (checksum ok) ':'').'<br>';
        // Kopiert Quelle nach Ziel. Bei Fehler wird Fehlercode 100 ("Failed to execute command") zurückgegeben.
        $err = copy ($source, $target);
        $this -> f_log('copy file '.str_replace(BASE,'',$source).' to '.str_replace(BASE,'',$target).(($checksum)?' (checksum ok) ':'').': '.((!$err)?'failed':'success'));
        return ((!$err)?$this -> set_errno(100):0);
    }

    private function f_delete ( $file='' )
    {

        /*
        *   Alias für file_backup(). Das Löschen erfolgt erst durch die Funktion cleanup().
        */

        return $this -> file_backup ($file);

    }

    private function f_mkdir ( $options = '' )
    {

        /*
        *   Erstellt ein Verzeichnis mit den angebenen Rechten
        */

        $this -> clear_errno();

        // Prüft auf Leerzeichen, da File/Rechte-Kombination
        $line = explode(' ',$options);
        if ( count($line) == 0 )
        {
            $this -> f_log('mkdir '.$line[0].': failed -> nothing specified');
            return $this -> set_errno(1);
        }
        if ( strlen($line[0]) == 0 )
        {
            $this -> f_log('mkdir '.$line[0].': failed -> directory not specified');
            return $this -> set_errno(4);
        }
        $line[1] = (string)$line[1];
        if ( strlen($line[1]) == 0 )
        {
            $this -> f_log('mkdir '.$line[0].': failed -> mode not specified');
            if ( ! $this -> silent ) echo '&nbsp;-&nbsp;mkdir "'.$line[0].'/"<br>';
            $err = mkdir ( BASE.'/'.$line[0], octdec($line[1]) );
        }
        else
        {
            if ( strlen( $line[1]) <= 4 ) $line[1] = substr('0000'.$line[1],-4);
            if ( ! $this -> silent ) echo '&nbsp;-&nbsp;mkdir "'.$line[0].'/" with mode '.$line[1].'<br>';
            $err = mkdir ( BASE.'/'.$line[0] );
        }
        $this -> f_log('mkdir '.$line[0].': '.((!$err)?'failed':'success'));
        return $err;

    }

    private function f_rmdir ( $directory = '')
    {

        /*
        *   Löscht ein Verzeichnis
        */

        $this -> clear_errno();

        if ( $this -> check_dir_exists( $directory ) )
        {
            $this -> f_log('rmdir '.$line[0].': failed -> file not specified');
            return $this -> set_errno(1);
        }
        if ( ! $this -> silent ) echo '&nbsp;-&nbsp;rmdir "'.$line[0].'/"<br>';
        $err = rmdir ($directory);
        $this -> f_log('rmdir '.$line[0].': '.((!$err)?'failed':'success'));
        return $err;

    }

    private function f_chmod ( $options = '')
    {

        /*
        *   Ändert die Rechte einer Datei oder eines Verzeichnisses
        */

        $this -> clear_errno();

        // Prüft auf Leerzeichen, da File/Rechte-Kombination
        $line = explode(' ',$options);
        if ( count($line) == 0 )
        {
            $this -> f_log('chmod '.$line[0].': failed -> file not specified');
            return $this -> set_errno(1);
        }
        $line[1] = (string)$line[1];
        if ( strlen($line[1]) == 0 )
        {
            $this -> f_log('chmod '.$line[0].' to '.$line[1].': failed -> mode not specified');
            return $this -> set_errno(3);
        }
        if ( strlen( $line[1]) <= 4 ) $line[1] = substr('0000'.$line[1],-4);

        if ( $this -> check_dir_exists ( BASE.'/'.$line[0] ) && $this -> check_file_exists( BASE.'/'.$line[0] ) ) return $this -> errno;

        if ( ! $this -> silent ) echo '&nbsp;-&nbsp;chmod '.BASE.'/'.$line[0].' to '.$line[1].'<br>';
        $err = chmod ( BASE.'/'.$line[0], octdec($line[1]) );
        $this -> f_log('chmod '.((is_dir(BASE.'/'.$line[0]))?'directory "'.$line[0].'/"':'file "'.$line[0].'"').' to '.$line[1].': '.((!$err)?'failed':'success'));
        return ((!$err)?$this -> set_errno(3):0);

    }

    private function f_sql ( $sqlscript = '' )
    {
        /*
        *   Liest das SQL-Script aus und gibt es an die Datenbank weiter.
        *
        *   Gibt die Fehlermeldungen zurück
        */

        if ( $sqlscript == '' || ! is_readable(BASE.'/'.$this -> script_path.'/files/'.$sqlscript) ) return false;

        if ( ! $this -> silent ) echo '&nbsp;-&nbsp;loading sql-script '.BASE.'/'.$this -> script_path.'/files/'.$sqlscript.'...<br>';
        if ( ! $this -> silent ) echo '&nbsp;-&nbsp;execute sql-script '.$sqlscript.'...<br>';

        $sqlcontent = explode(';',file_get_contents(BASE.'/'.$this -> script_path.'/files/'.$sqlscript));
        $error = array();
        for ( $i=0; $i<count($sqlcontent)-1; $i++ )
        {
            $this -> f_log('executing sql: '.trim($sqlcontent[$i]));
            mysql_query(trim($sqlcontent[$i]));
            $this -> f_log('sql state: '.((mysql_error())?'error => '.mysql_error():'ok'));
            if ( mysql_error() && $this -> script_critical )
            {
                $this -> f_log('stop executing sql-script!');
                break;
            }
        }
        return ((count($error)==0)?true:$error);
    }

    private function f_log ( $text = '' )
    {

        /*
        *   Schreibt ein Text in ein Logfile oder in die entsprechende Tabelle. Sollte auch von den anderen f_*()-Funktionen genutzt werden!
        */

        if ( $this -> debug ) $this -> log[]=$text;
        $this -> report[]=$text;

    }

    private function f_unpack ( $file = '' )
    {

        /*
        *   Extrahiert ein Archiv
        */

        if ( $this -> check_file_exists( $file ) ) return  $this -> set_errno(1);
        $packer = new pack();
        $packer -> unzip (BASE.'/'.$this -> script_path.'/files/'.$file);

    }

    private function f_sync ( $options = '' )
    {

        /*
        *   ToDo: Synchronisiert die angegebene Tabelle.Spalte mit dem Verzeichnis rekursiv mit (true=fullpath, rekursiv) oder ohne Pfadangabe (false=filename, flat)
        */

    }

    private function f_critical ()
    {

        /*
        *   Setzt das Flag für systemkritische Befehle. Wenn das Flag gesetzt ist, bricht das Update sofort ab.
        */

        $this -> script_critical = true;

    }

    private function f_uncritical ()
    {

        /*
        *   Löscht das Flag für systemkritische Befehle. Wenn das Flag gelöscht ist, wird der nächste Befehl ausgeführt.
        */

        $this -> script_critical = false;

    }

    /* Weitere Funktionen */

    function set_hash ($hash = 'sha256')
    {

        /*
        *   Setzt die Hash-Funktion für f_copy()
        *
        *   Standard ist sha256
        */

        if ( in_array( $hash,hash_algos() ) )
        {
            $this -> script_checksum = $hash;
            return true;
        }
        else
        {
            return false;
        }
    }

    function clear_debug ()
    {

        /*
        *   Löscht das Debug-Flag
        */

        $this -> debug = false;
        return;

    }

    function set_debug ()
    {

        /*
        *   Setzt das Debug-Flag
        */

        $this -> debug = true;
        return;

    }

    function get_silent ()
    {

        /*
        *   Gibt das Silent-Flag zurück
        */

        return $this -> silent;

    }

    function clear_silent ()
    {

        /*
        *   Löscht das Silent-Flag
        */

        $this -> silent = false;
        return;

    }

    function set_silent ()
    {

        /*
        *   Setzt das Silent-Flag
        */

        $this -> silent = true;
        return;

    }

    function get_debug ()
    {

        /*
        *   Gibt das Debug-Flag zurück
        */

        return $this -> debug;

    }

    function show_report ()
    {

        /*
        *   Zeigt das gesamte Log an
        */

        if ( count( $this -> report ) > 0 )
        {
            echo 'report:<br><ul>';
            foreach ($this -> report as $errline) echo '<li>'.$errline.'</li>';
            echo '</ul>';
        }
    }

    /* Private Funktionen */

    private function clear_errno ()
    {

        /*
        *   Löscht die Fehlernummer
        */

        $this -> errno = 0;
        return 0;

    }

    private function set_errno ( $errno = 0 )
    {

        /*
        *   Setzt die Fehlernummer
        */

        $this -> errno = $errno;
        return $errno;

    }

    private function file_backup ( $file= '' )
    {

        /*
        *   Backup file
        */

        $this -> errno = 0;
        if ( $this -> check_file_exists( $file ) ) return  $this -> set_errno(1);
        if ( ! $this -> silent ) echo '&nbsp;-&nbsp;backup '.$file.'<br>';
        // Die Datei für cleanup() merken
        $this -> backup_files[] = $file;

        $err = copy( BASE.'/'.$file,BASE.'/'.$file.".bak" );
        $this -> f_log('backup '.$file.': '.((!$err)?'failed':'success'));
        return ((!$err)?$this -> set_errno(2):0);

    }

    private function file_rollback ( $file= '' )
    {
        /*
        *   Rollback file
        */

        if ( $this -> check_file_exists( $file ) ) return  $this -> set_errno(1);
        if ( ! $this -> silent ) echo '&nbsp;-&nbsp;rollback '.$file.'<br>';
        $err = rename( BASE.'/'.$file.".bak",BASE.'/'.$file );
        $this -> f_log('rollback '.$file.': '.(($err)?'failed':'success'));

    }

    private function db_backup ()
    {
        /*
        *   ToDo: Erzeugt ein Backup der Datenbank
        */
    }

    private function db_rollback ()
    {
        /*
        *   ToDo: Spielt das Backup der Datenbank bei Fehler zurück
        */
    }

    private function check_dir_exists ( $directory = '' )
    {

        /*
        *   Prüft, ob das angegebene Verzeichnis existiert  
        */

        $this -> errno = (($directory == '' || ! is_dir($directory))?1:0);
        return $this -> errno;

    }

    private function check_file_exists ( $file = '' )
    {

        /*
        *   Prüft, ob die angegebene Datei existiert    
        */

        $this -> errno = (($file == '' || ! is_file($file))?1:0);
        return $this -> errno;

    }

    private function cleanup ()
    {

        /*
        *   Lösche alle Backups
        */

        echo '<br>at least executing cleanup...<br>';
        foreach ( $this->backup_files as $file)
        {
            if ( ! $this -> silent ) echo '&nbsp;-&nbsp;delete backup of file '.$file.'<br>';
            unlink (BASE.'/'.$file.'.bak');
        }

        /*
        *   ToDo
        /
        *   - Spielt das Backup der Datenbank zurück, wenn ein Fehler aufgetreten ist.
        */

        echo "<br>";
    }

    private function clear_log ()
    {

        /*  
        *   Löscht das Log
        */

        $this -> log = array();
        $this -> report = array();

    }

    private function show_log ()
    {

        /*
        *   Zeigt das Log an
        */

        if ( $this -> debug && count( $this -> log ) > 0 )
        {
            echo 'log:<br><ul>';
            foreach ($this -> log as $errline) echo '<li>'.$errline.'</li>';
            echo '</ul>';
        }

    }
}
?>

