<?php
/*
    ---------------------------------------------------------------------------------------

    phpBookWorm: Filemanager


    Copyright: CC-BY-SA 3.0, 2012-2013
    Author: Udo Neist (webmaster@singollo.de, GPG-Fingerprint 4A8F B229 2AE9 9634 04D1 E2F0 21F2 E27D FE97 D87F)

    Part: conf.php

    Info: Konfigurationsdatei

    ---------------------------------------------------------------------------------------

    06.04.2013: Minimale Konfiguration für Filemanager (Udo Neist)
    15.09.2013: Konfiguration von Part-DB wird importiert (Udo Neist)

*/

require_once ('../start_session.php');

require (BASE.'/lib/vlib/vlibTemplate.php');
require (BASE.'/lib/vlib//vlibDate.php');
require (BASE.'/lib/vlib//vlibMimeMail.php');
require (BASE.'/filemanager/lib/class/class.xhr.php');
require (BASE.'/filemanager/lib/class/class.fileio.php');

/*

// Testet auf Browser
function browser_info($agent=null) {
	if (!in_array('HTTP_USER_AGENT',$_SERVER) || strlen($_SERVER['HTTP_USER_AGENT'])==0) return false;
	$known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko', 'chrome');
	$agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
	$pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';

	if (!preg_match_all($pattern, $agent, $matches)) return array();
	$i = count($matches['browser'])-1;
	return array($matches['browser'][$i] => $matches['version'][$i]);
}

$ua = browser_info();

if (($ua['firefox'] && $ua['firefox']<1.5) || ($ua['msie'] && $ua['msie'] <= 6) || ($ua['opera'] && $ua['opera'] < 9)) {
	$html -> header('vlib_old_browser.html');
	$html -> footer();
	die();
}

*/

/*
*   Definiert ein paar Verzeichnisse
*
    $dirs['dir']: Basisverzeichnis
    $dirs['mimegfx']: Verzeichnis der Grafiken für Mimetypen

    $dirs['mime'][x]: Mimetypen, die angezeigt werden. Dabei ist "x" einfach ein Namen für die Aufzählung (z.B. "office" für alle Dokumenttypen, "gfx" für Grafiken). 
                      "x" wird in vlib_filemanager.html definiert.
*/
$dirs['root']    = false; // Hauptverzeichnis erlaubt ja/nein
$dirs['dir']     = BASE.'/img'; // Hauptverzeichnis
$dirs['mimegfx'] = 'gfx/mimetypes/';

$dirs['dirs'][]  = array('path'=>'labels','type'=>'image','name'=>'Labels');
$dirs['dirs'][]  = array('path'=>'iclogos','type'=>'image','name'=>'IC-Logos');
$dirs['dirs'][]  = array('path'=>'footprints','type'=>'image','name'=>'Footprints');

/*
*   Vordefinierte Sammlungen
*/
$dirs['mime']['office']= array ('application/pdf','text/plain','application/msword','application/rtf','application/vnd.ms-excel');
$dirs['mime']['gfx']  = array ('image/png','image/gif','image/jpeg');
$dirs['mime']['all']= array ();

$mode['userootdir']=true;
$mode['mode'][$dirs['dir']]='rw';

$_SESSION['logged']=true; // Für die Testsuite
?>
