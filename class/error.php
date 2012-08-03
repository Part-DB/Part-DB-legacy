<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

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

    $Id: class/error.php 510 2012-08-03 weinbauer73@gmail.com $

*/

class _exception {

	function __construct() {
	
		/*
		*	Tests of available classes
		*/
	
		$classes = array('vlibTemplate','vlibMimeMail');
		$text = array();
		foreach ($classes as $class) if (class_exists($class)===false) $text[]=$class;
		if (count($text)>0) $this -> throwClassError($text,basename(__FILE__));

	}
	
	function throwClassError( $e, $script ) {

		/*
		*	Shows class loading error
		*
		*	Variables: $e (array) with error code(s), $script as filename
		*/

		settype($e,'array');
		settype($script,'string');

		$array=array('Error in script <em>"'.$script.'"</em>:');
		foreach($e as $key => $value) {
			$e[$key]='Class <em">"'.$value.'"</em> doesn`t exists, not loadable or faulty.';
		}
		$this -> throwError(array_merge($array,$e),'Nothing to do :-(');
	}
	
	function throwFunctionError( $e, $script ) {

		/*
		*	Shows function loading error
		*
		*	Variables: $e (array) with error code(s), $script as filename
		*/

		settype($e,'array');
		settype($script,'string');

		$array=array('Error in script <em>"'.$script.'"</em>:');
		foreach($e as $key => $value) {
			$e[$key]='Function <em">"'.$value.'"</em> doesn`t exists, not loadable or faulty.';
		}
		$this -> throwError(array_merge($array,$e),'Nothing to do :-(');
	}
	
	function throwError( $e='', $variable ) {

		/*
		*	Shows error
		*
		*	Variables: $e (string oder array) with error code(s), $array with variables
		*/

		global $theme, $title;

		settype($e,'array');

		$this -> header();

		// loading template
		$tmpl =& new vlibTemplate(BASE."/templates/$theme/vlib_error.tmpl");
		if (is_array($e)) {
			if (strlen($e['SQL'])>0 && strlen($e['SQLError'])>0) {
				$tmpl -> setVar('line',$e['line']);
				$tmpl -> setVar('class',$e['class']);
				$tmpl -> setVar('SQL',$e['SQL']);
				$tmpl -> setVar('SQLError',$e['SQLError']);
			}else{
				$tmpl -> setVar('Error',$e[0]);
				if ( is_array($variable) && count($variable)>0 )
				{
					$tmpl -> newloop('variables');
					foreach ($variable as $key=>$value) $tmpl -> addRow(array('key'=>$key,'value'=>$value));
					$tmpl -> addLoop();
				}
				else
				{
					$tmpl -> setVar('var_dump',$variable);
				}
				$tmpl -> newloop('loop');
				for ($i=1;$i<count($e);$i++) $tmpl -> addRow(array('Errors'=>$e[$i]));
				$tmpl -> addLoop();
			}
		}elseif (strlen($e)>0) {
				$tmpl -> setVar('Error',$e);
		}
		$error = $tmpl->grab();
		// shows error
		echo $error;
		// mail to...
//		if ($conf['sys']['debug']=='true' && $this -> mail($error)) echo '<br>Die Fehlermeldung wurde einem Administrator automatisch zugesendet.';
	
		$this -> footer();

		exit;
	}
	
	private function header() {

		/*
		*	Prints header of site
		*/

		global $http_charset, $theme, $title;

		$tmpl =& new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
		$tmpl -> setVar('head_title', $title);
		$tmpl -> setVar('head_charset', $http_charset);
		$tmpl -> setVar('head_theme', $theme);
		$tmpl->pparse();

	}
	
	private function footer() {

		global $theme;

		$tmpl =& new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
		$tmpl->pparse();

	}
	
	private function mail($body='') {
		/*
		*	Mail to admin or master user
		*/

/*		global $conf;
	
		$mail =& new vlibMimeMail;
		// Mail-From setzen
		$mail->from($conf['sys']['email'],'Chemikalienverwaltung');
		// Mail-To setzen
		$mail->to($conf['sys']['email'],'Chemikalienverwaltung');
		// Subject
		$mail -> subject('ChemDB - Fehlermeldung vom Rechner '.$_SERVER['REMOTE_ADDR'].(($_SESSION['chem_var']['username'])?' / Angemeldeter User: '.$_SESSION['chem_var']['username']:''));
	
		$tmplbody = "<tmpl><body><strong>Chemikalienverwaltung: Fehlermeldung</strong><br>".$body."Mfg.<br>Chemikalienverwaltung</body></tmpl>";
		$mail->tmplbody(utf8_decode($tmplbody));
	
		// Setze die Priorität auf Hoch
		$mail->priority(2);
		// Versenden
		return $mail -> send();*/
	}

}

?>