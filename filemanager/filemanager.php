<?php
/*
	---------------------------------------------------------------------------------------

	phpBookWorm: Filemanager


    Copyright: CC-BY-SA 3.0, 2012-2013
    Author: Udo Neist (webmaster@singollo.de, GPG-Fingerprint 4A8F B229 2AE9 9634 04D1 E2F0 21F2 E27D FE97 D87F)

	Part: filemanager.php

	Info: Beispielaufruf, benötigt vlibTemplate-Engine

	---------------------------------------------------------------------------------------

    17.10.2012: Erste Version (Udo Neist)
    18.10.2012: Zielverzeichnis wird jetzt per Mime-Typ direkt ausgewählt (Udo Neist)
                Anzeige der hochgeladenen Daten pro Session (Udo Neist)
    27.12.2012: Kleinere Bugs beseitigt (Udo Neist)
    03.01.2012: Klassen werden in inc/conf.php geladen (Udo Neist)
                Umbenennung nach filemanager.php (Udo Neist)
    04.01.2013: Kopfzeilen geändert (Udo Neist)
                Nur angemeldete User dürfen den Filemanager verwenden (Udo Neist)
    08.01.2013: searchFiles() verarbeitet jetzt auch das vorhergehende Verzeichnis korrekt (Udo Neist)
    15.01.2013: Zugriffsrecht geändert (Udo Neist)
    26.01.2013: Download ergänzt (Udo Neist)
                Copy, Move, Rename, Delete als Befehle eingebaut (Udo Neist)
    03.03.2013: Token für Zugriffsschutz (Udo Neist)
    31.03.2013: Kleinere Erweiterung für den internen Viewer (Udo Neist)
    06.04.2013: Erlaubt jetzt auch die Nutzung des Hauptverzeichnisses $dirs['dir'] bei searchFiles() (Udo Neist)
                Kommentare ergänzt oder geändert (Udo Neist)
                GeSHi für Syntax-Highlightning integriert (Udo Neist)
    07.04.2013: Kleinere Anpassung für eine Integration in eine andere Webseite (Udo Neist)
    28.07.2013: Verzeichnisliste (Udo Neist)
*/

/*
*   Aufruf der Konfiguration
*/
require_once ('inc/conf.php');

// Korrektur des Hauptverzeichnisses, falls leer
if ($dirs['dir']=='' || $dirs['dir']=='/') $dirs['dir']='.';
// Wenn keine Unterverzeichnisse definiert wurden, dann wird das Hauptverzeichnis freigeschaltet
if (count($dirs['dirs'])==0) $dirs['root']=true;

if (file_exists('lib/libs/geshi/geshi.php')) {
    include_once ('lib/libs/geshi/geshi.php');
    include_once ('lib/libs/geshi/mime2geshi.php');
}

/*
*   Erlaubt Uploads nur für eingeloggte User
*/
if ($_SESSION['logged'] && $_FILES) {
    $ajax = new xhr;
    if (!$ajax -> setUploadDir($dirs['dir'].'/'.$_POST['target'])) {
        header("Status: 403 Forbidden");
        exit;
    }else{
        $ajax -> moveUploadFile();
    }
}

// Falls nicht eingeloggt oder Token fehlerhaft, dann Fehler 403 zurückgeben
if ($_SESSION['logged'] && array_key_exists('get_token',$_POST)) {
        $_SESSION['token']=md5(mt_rand());
        echo json_encode(array('token'=>$_SESSION['token']));
}else if (strlen($_REQUEST['token'])<>32 || $_REQUEST['token']<>$_SESSION['token'] || !$_SESSION['logged']) {
        header("Status: 403 Forbidden");
        exit;
}

function searchFiles() {
    /*
        Sucht nach Dateien
    */

    global $dirs,$mode;

    if (strlen($_GET['list'])==0) {
        if ($mode['userootdir']===false) {
            echo "<div><ul class='list'><li class='hover'>Zugriff nicht erlaubt.</li></ul></div>";
            exit;
        }else{
            $_GET['list']='/';
        }
    }
    if (substr($_GET['list'],1,1)!="/") $_GET['list']='/'.$_GET['list'];

    $tmpl = new vlibTemplateCache('templates/vlib/vlib_filemanager_list.html');
    $tmpl -> setVar('hbase',$dirs['dir']);
    $tmpl -> setVar('hpath',$_GET['list']);
    $tmpl -> setVar('htype',$_GET['type']);

    $fileIO = new fileIO;
    $fileIO -> readDir($dirs['dir'].$_GET['list'],'','cached',array('mime'=>$dirs['mime'][$_GET['type']],'recursive'=>false,'onlydir'=>false,'onlyfiles'=>false,'stripdir'=>true));
    $files = $fileIO -> getFiles();

    if (count($files)>0) {
        asort($files);
        $array=array();
        if (strpos($_GET['list'],'/')>0) {
            $dir = explode('/',$_GET['list']);
            if (count($dir)>1) {
                $last = array_pop($dir);
                $array['dir'][]=basename(implode('/',$dir));
            }
        }
        foreach($files as $file) {
            if (is_dir($file)) {
                // Directory
                $array['dir'][]=str_replace($dirs['dir'].'/','',$file);
            }else{
                // File
                $array['file'][]=$file;
            }
        }

        foreach($array as $type => $file) {
            $tmpl -> newLoop($type);
            foreach($file as $name) {
               $tmpl -> addRow(
                    array(
                        'name'      =>  (($type=='dir' && strlen($_GET['list'])>strlen($name))?'..':basename($name)),
                        'path'      =>  (($type=='dir')?$name:''),
                        'b64name'   =>  base64_encode( preg_replace('{^/|\?.*}','',str_replace(BASE,BASE_RELATIVE,$dirs['dir']).'/'.$_GET['list'].'/'.$name ) )
                    )
                );
            }
            $tmpl -> addLoop();
        }
        $tmpl -> setVar('type',$_GET['list']);
    }else{
        $tmpl -> setVar('nofile',true);
    }
    $tmpl -> pparse();
}

function searchDirs() {
    /*
        Sucht nach Verzeichnissen
    */

    global $dirs;

    $array=array();

    if ($dirs['dir']=='.') {
        for ($i=0; $i<count($dirs['dirs']); $i++) {
            $array['dir'][]=$dirs['dirs'][$i]['path'];
        }
    }else{
        $fileIO = new fileIO;
        $fileIO -> readDir($dirs['dir'],'','cached',array('recursive'=>true,'onlydir'=>true,'stripdir'=>false));
        $files = $fileIO -> getFiles();
        if (count($files)>0) {
            asort($files);
            foreach($files as $file) {
                if (is_dir($file)) {
                    // Directory
                    $array['dir'][]=str_replace($dirs['dir'].'/','',$file);
                }
            }
        }
    }
    echo json_encode($array);

}

function getFile($file,$mime) {
    /*
        Download einer Datei
    */

    global $dirs;

    $file = base64_decode($file);
    $mime = base64_decode($mime);

    $fileIO = new fileIO;
    $fileIO -> setFileName($file,basename($file));
    $fileIO -> setMimeType($mime);
    $fileIO -> showFile('attachment');
}

/*
*   Kommandos
*/

if (array_key_exists('list',$_GET)) {

    searchFiles();

}elseif (array_key_exists('dir',$_GET)) {

    searchDirs();

}elseif (array_key_exists('lsdir',$_GET)) {

    $tmpl = new vlibTemplateCache('templates/vlib/vlib_filemanager_listdir.html');
    $tmpl -> setVar('root',(($dirs['root'])?1:0));
    $tmpl -> newLoop("listdir");
    $max = ((count($dirs['dirs'])>5)?5:count($dirs['dirs']));
    for ($i=0; $i<$max; $i++) {
        $tmpl -> addRow(
            array(
                'path'      =>  $dirs['dirs'][$i]['path'],
                'type'      =>  $dirs['dirs'][$i]['type'],
                'name'      =>  $dirs['dirs'][$i]['name'],
            )
        );
    }
    $tmpl -> addLoop(); 
    $tmpl -> pparse();

}elseif (class_exists('GeSHi') && array_key_exists('geshi',$_GET) && strlen($_GET['geshi'])>0 && strlen($_GET['mime'])>0) {

    $fileIO = new fileIO;
    $fileIO -> setFileName(BASE.'/'.$_GET['geshi']);
    $fileIO -> readFile();

    $geshi = new GeSHi($fileIO -> getString(), $mime2geshi[$_GET['mime']]);
    $geshi -> set_header_type(GESHI_HEADER_DIV);
    $geshi -> enable_line_numbers(GESHI_FANCY_LINE_NUMBERS,1);
    $geshi -> set_line_style('background: #fcfcfc;', 'background: #f0f0f0;');

    echo $geshi->parse_code();

}elseif (array_key_exists('cmd',$_GET) && strlen($_GET['cmd'])>0) {

    $data = base64_decode($_GET['cmd']);
    $data = stripslashes($data);
    $data = json_decode($data,true);

    $array = array(
        'command'       => $data['command'],
        'dir_source'    => $data['dir_source'],
        'dir_target'    => $data['dir_target'],
        'source'        => $data['source'],
        'target'        => $data['target'],
    );

    $file['source'] = $dirs['dir'].'/'.$data['dir_source'].'/'.$data['source'];
    $file['target'] = $dirs['dir'].'/'.$data['dir_target'].'/'.$data['target'];

    if ($data['command']=='rename') {
        $array['status'] = ((
            rename ($dirs['dir'].'/'.$data['dir_source'].'/'.$data['source'],$dirs['dir'].'/'.$data['dir_source'].'/'.$data['target'])
        )?'ok':'error');
    }
    if ($data['command']=='move') {
        $array['status'] = ((
            rename ($dirs['dir'].'/'.$data['dir_source'].'/'.$data['source'],$dirs['dir'].'/'.$data['dir_target'].'/'.$data['source'])
        )?'ok':'error');
    }
    if ($data['command']=='copy') {
        $array['status'] = ((
            copy ($dirs['dir'].'/'.$data['dir_source'].'/'.$data['source'],$dirs['dir'].'/'.$data['dir_target'].'/'.$data['target'])
        )?'ok':'error');
    }
    if ($data['command']=='delete') $array['status'] = ((unlink ($dirs['dir'].'/'.$data['dir_source'].'/'.$data['source']))?'ok':'error');

    echo json_encode($array);

}elseif (array_key_exists('get',$_GET) && strlen($_GET['get'])>0) {

    getFile($_GET['get'],$_GET['mime']);

}elseif (array_key_exists('info',$_GET) && strlen($_GET['info'])>0) {
    
    $fileIO = new fileIO;
    $fileIO -> setFileName(DOCUMENT_ROOT.'/'.base64_decode($_GET['info']));
    $dir = dirname(base64_decode($_GET['info']));
    if ($dirs['dir']!='.' && (substr($dir,0,1)=='.' || substr($dir,0,1)=='/')) {
        header ('HTTP/1.0 404 Not Found');
    }elseif ($info = $fileIO -> aboutFile()) {
        $tmpl = new vlibTemplateCache('templates/vlib/vlib_filemanager_info.html');
        $file = basename(base64_decode($_GET['info']));
        $tmpl -> setVar('name',((strlen($file)>20)?substr($file,0,15).'...'.substr($file,-5):$file));
        $tmpl -> setVar('longname',basename($file));
        $tmpl -> setVar('file','/'.base64_decode($_GET['info']));
        $tmpl -> setVar('filename',$_GET['info']);
        $tmpl -> setVar('mime',$info['mime']);
        $tmpl -> setVar('mimetype',$info['mimetype']);
        $tmpl -> setVar('mimetype_gfx',((is_file('gfx/mimetypes/'.str_replace('/','-',$info['mimetype']).'.png'))?$dirs['mimegfx'].'/'.str_replace('/','-',$info['mimetype']).'.png':false));
        $tmpl -> setVar('b64mimetype',base64_encode($info['mimetype']));
        $tmpl -> setVar('date',$info['date']);
        $tmpl -> setVar('mode',$info['mode']);
        $tmpl -> setVar('size',round($info['size']/1024,2));
        $tmpl -> setVar('gfx',((strpos($info['mime'],'image')!=false)?true:false));
        $tmpl -> pparse();
    }

}
?>