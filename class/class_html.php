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

    $Id: class_html.php 501 2012-07-29 weinbauer73@gmail.com $

*/

class HTML
{

	private $meta;
	private $template;

	private $variable;
	private $loop;

	private $debug;

	function __construct()
	{

		/* Hier werden die Standardwerte für die Klassenvariablen gesetzt */

		// Variablentyp festlegen
		settype ($this->meta,'array');
		settype ($this->variable,'array');
		settype ($this->template,'string');

		// Variablen erzeugen
		$this->meta = array();
		$this->variable = array();
		$this->template = '';

		// String
		$this->meta['theme']='standard';
		$this->meta['title']='';
		$this->meta['http_charset']='utf-8';
		$this->meta['css']='';

		// Boolean
		$this->meta['menu']=true;
		$this->meta['head_popup']=false;
		$this->meta['util_functions']=false;
		$this->meta['clear_default_text']=false;
		$this->meta['validate']=false;
		$this->meta['id']=false;

		// Fehlersuche
		$this->debug=false;
	}

	/** Funktionen für Variablen **/

	function set_html_meta ( $meta = array() )
	{

		/* 
		*	Im Array $meta werden die Konfigurations- oder userspezifischen Daten übergeben, die die Standardwerte überschreiben
		*	Es werden keine Prüfungen durchgeführt!
		*/

		$this->meta = $meta;

		return 0;
	}

	function set_html_variable ( $key = '', $var = '', $type = '', $format = array() )
	{

		/*
		*	Setzt eine Variable
		*
		*	Ist $type mit 'boolean', 'integer', 'float' oder 'string' angegeben, wird der Variablentyp gesetzt.
		*	Wird $format['format'] mit 'nf' übergeben und der Typ ist 'integer' oder 'float', so wird nach number_format() formatiert. Die Standardwerte entsprechen dem deutschen Format.
		*	Gibt man dagegen $format['format'] mit 'sf' an, so wird der String durch sprintf() und der Formatangabe in $format['printf'] formatiert.
		*
		*/



		if ( strlen($key)==0 || strlen($var) ==0 ) return 1; // Fehlercode 1: Variable nicht angeben

		if ( in_array( $type, array('boolean', 'integer', 'float', 'string')) )
		{
			settype( $var,$type );
			settype( $this->variable[$key],$type );
		}

		if ( in_array( $type, array('integer', 'float') ) && is_numeric($var) && $format['format'] == 'nf' && count($format) > 1 )
		{

			/*
			*	number_format()
			*
			*	Definiert die Ausgabe nach deutschem Standard, falls keine anderen Angaben vorhanden sind
			*/

			if ( ! $format['dec_point'] ) $format['dec_point'] =',';
			if ( ! $format['thousand'] ) $format['thousand'] ='.';

			$var = number_format($var,$format['decimal'],$format['dec_point'],$format['thousand']);
		}

		
		if ( $format['format'] == 'sf' && strlen($format['printf']) > 0 )
		{

			/*
			*	sprintf()
			*
			*	Formatiert nach den Regeln von sprintf() -> http://php.net/manual/de/function.sprintf.php
			*/

			$var = sprintf( $var, $fomat['printf'] );
		}

		$this->variable[$key] = $var;

		return 0;
	}

	function unset_html_variable ( $key = '' )
	{

		/* Löscht eine Variable */

		if ( strlen($key) == 0 ) return 1; // Fehlercode 1: Variable nicht angeben

		$this->variable[$key] = '';
		unset( $this->variable[$key] );

		return 0;
	}

	function clr_html_variable ()
	{

		/* Löscht das ganze Array */

		unset ( $this->variable );
		$this->variable = array();

		return 0;
	}


	function set_html_loop ( $key = '', $array = array() )
	{

		/* Setzt ein Array für Loops */

		if ( strlen($key)==0 || count($array) ==0 ) return 1; // Fehlercode 1: Variable nicht angeben

		$this->loop[$key] = $array;

		return 0;
	}

	function unset_html_loop ( $key = '' )
	{

		/* Löscht ein Loop */

		if ( strlen($key) == 0 ) return 1; // Fehlercode 1: Variable nicht angeben

		$this->loop[$key] = array();
		unset( $this->loop[$key] );

		return 0;
	}

	function clr_html_loop ()
	{

		/* Löscht alle Loops */

		unset ( $this->loop );
		$this->loop = array();

		return 0;
	}

	/** Ausgabefunktionen **/

	function print_html_header ()
	{

		/* HTML-Header */

		if ( !is_array($this->meta) || count ($this->meta) == 0 || strlen($this->meta['theme'])==0 ) return 1; // Fehlercode 1: Metadaten nicht angegeben, mindestens $this->meta['theme'] ist notwendig!
		if ( ! is_readable(BASE."/templates/".$this->meta['theme']."/vlib_head.tmpl") ) return 2; // Fehlercode 2: Datei nicht gefunden

		$tmpl = new vlibTemplate(BASE."/templates/".$this->meta['theme']."/vlib_head.tmpl");
		$tmpl -> setVar('head_title', $this->meta['title']);
		$tmpl -> setVar('head_charset', $this->meta['http_charset']);
		$tmpl -> setVar('head_theme', $this->meta['theme']);

		if ( strcasecmp('templates/'.$this->meta['theme'].'/partdb.css',$this->meta['css']) )
		{
			$tmpl -> setVar('head_css', $this->meta['css']);
		}

		$tmpl -> setVar('head_menu', $this->meta['menu']);
		$tmpl -> setVar('head_util_functions', $this->meta['util_functions']);
		$tmpl -> setVar('head_clear_default_text', $this->meta['clear_default_text']);
		$tmpl -> setVar('head_validate', $this->meta['validate']);
		$tmpl -> setVar('hide_id', $this->meta['id']);
		$tmpl -> pparse();

		return 0;

	}

	function print_html_footer ()
	{

		/* Footer ausgeben */

		if ( !is_array($this->meta) || count ($this->meta) == 0 || strlen($this->meta['theme'])==0 ) return 1; // Fehlercode 1: Metadaten nicht angegeben, $this->meta['theme'] ist notwendig!
		if ( ! is_readable(BASE."/templates/".$this->meta['theme']."/vlib_foot.tmpl") ) return 2; // Fehlercode 2: Datei nicht gefunden

		$tmpl = new vlibTemplate(BASE."/templates/".$this->meta['theme']."/vlib_foot.tmpl");
		$tmpl -> pparse();

		return 0;

	}

	function load_html_template ( $template = '' )
	{

		/*
		*	Definiert das Template für nachfolgende Funktionen.
		*
		*	Variablen
		*	$template: gekürzter Name für das Template. Scriptname und "vlib_" sowie ".tmpl" müssen weggelassen werden!
		*
		*/

		$this->template = '';

		if ( strlen($template) == 0 ) return 1; // Fehlercode 1: Variablen nicht angeben
		if ( ! is_readable(BASE."/templates/".$this->meta['theme']."/".basename($_SERVER['PHP_SELF'])."/vlib_".$template.".tmpl") ) return 2; // Fehlercode 2: Datei nicht gefunden

		$this->template = BASE."/templates/".$this->meta['theme']."/".basename($_SERVER['PHP_SELF'])."/vlib_".$template.".tmpl";
		$this->clr_html_loop();
		$this->clr_html_variable();

		if ( $this->debug ) echo "loading template ".$this->template."...<br>";

		return 0;

	}

	function parse_html_template ( $template = '', $array = array() )
	{

		/*
		*	Meta-Funktion, fasst die Funktionen load_html_template(), set_html_*() und print_html_template() zusammen.
		*
		*	Variable: $array[$key]=>$value
		*	Loop: $array[$index][$key]=>$value
		*
		*	Erkennt automatisch anhand von $value ob Loop oder Variable.
		*/

		if ( count($array) == 0) return 1; // Fehlercode 1: Variablen nicht angeben
		if ( $this->load_html_template($template) > 0 ) return 2; // Fehlercode 2: Datei nicht gefunden

		if ( count($array) >0 )
		{
			while ( list( $key, $value ) = each( $array ) )
			{
				if ( is_array($value) )
				{
					if ( $this->debug ) echo "set loop $key...<br>";
					$this->set_html_loop( $key, $value );
				}
				else
				{
					if ( $this->debug ) echo "set variable $key...<br>";
					$this->set_html_variable( $key, $value );
				}
			}
		}

		if ( $this->print_html_template()==2 ) echo $this->template;
		

		return 0;

	}

	function parse_html_table ( $array = array() )
	{

		/* Alias für parse_html_template(), übergibt aber einen festen Dateinamen */

		$this->parse_html_template( 'table', $array );

	}

	function print_html_template ()
	{

		/*
		*	Gibt das zuvor mit load_html_template() und set_html_*() bzw. parse_html_*() definierte Template aus.
		*/

		if ( ! is_readable($this->template) ) return 2; // Fehlercode 2: Datei nicht gefunden

		if ( $this->debug )
		{
			echo "<br>loading template: ".$this->template;
			echo "<br>variables: ";
			print_r($this->variable);
			echo "<br>loop: ";
			print_r($this->loop);
		}

		$tmpl = new vlibTemplate($this->template);

		if ( count($this->variable) >0 ) while ( list( $key, $value ) = each( $this->variable ) ) $tmpl -> setVar ( $key, $value );
		if ( count($this->loop) >0 ) while ( list( $key, $loop ) = each( $this->loop ) ) $tmpl -> setLoop ( $key, $loop );

		$tmpl->pparse();
	}

	/** Misc **/

	function print_error_state( $error = 0 )
	{

		/* Gibt Fehlercodes der Funktionen als Klartext zurück */

		$errorcodes = array ('0' => 'success','1' => 'no variable found','2' => 'file not found');
		return $errorcodes[$error];

	}

}

?>