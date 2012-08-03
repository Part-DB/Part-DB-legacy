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

    $Id: class/html.php 510 2012-08-03 weinbauer73@gmail.com $

*/

require_once ('error.php');

class HTML
{

	private $meta;
	private $template;

	private $variable;
	private $loop;

	private $grab;
	private $debug;

	function __construct()
	{

		/* check for loaded classes */
		$e =& new _exception;

		$classes = array('vlibTemplate');
		$text = array();
		foreach ($classes as $class) if (class_exists($class)===false) $text[]=$class;
		if (count($text)>0) $e -> throwClassError($text,basename(__FILE__));

		/* default values ​​set for the class variables */

		global $http_charset, $theme, $css;

		// specify the variable type
		settype ($this->meta,'array');
		settype ($this->variable,'array');
		settype ($this->template,'string');
		settype ($this->grab,'boolean');
		settype ($this->debug,'boolean');

		// specify the variable type of array $this->meta
		settype ($this->meta['theme'],'string');
		settype ($this->meta['title'],'string');
		settype ($this->meta['http_charset'],'string');
		settype ($this->meta['css'],'string');
		settype ($this->meta['menu'],'boolean');
		settype ($this->meta['head_popup'],'boolean');
		settype ($this->meta['util_functions'],'boolean');
		settype ($this->meta['clear_default_text'],'boolean');
		settype ($this->meta['validate'],'boolean');
		settype ($this->meta['id'],'boolean');

		// default variable type
		$this->meta = array();
		$this->variable = array();
		$this->template = '';

		// strings
		$this->meta['theme']=(($theme<>'standard' && is_readable(BASE."/templates/$theme/partdb.css"))?$theme:'standard');
		$this->meta['title']='';
		$this->meta['http_charset']=(($http_charset)?$http_charset:'utf-8');
		$this->meta['css']=((is_readable(BASE."/css/custom-$theme.css"))?"css/custom-$theme.css":"templates/$theme/partdb.css");

		// boolean
		$this->meta['menu']=true;
		$this->meta['head_popup']=false;
		$this->meta['util_functions']=false;
		$this->meta['clear_default_text']=false;
		$this->meta['validate']=false;
		$this->meta['id']=false;
		$this->grab=false;

		// to debug
		$this->debug=false;

	}

	/** Funktionen für Variablen **/

	function set_html_meta ( $meta = array() )
	{

		/* 
		*	In $meta, the configuration or user-specific data is passed that override the default values.
		*	There are no tests conducted!
		*/

		settype($meta,'array');

		foreach ($meta as $key=>$value)
		{
			$this->meta[$key] = $value;
		}

		return 0;
	}

	function set_html_variable ( $key = '', $var = '', $type = '', $format = array() )
	{

		/*
		*	Sets a variable in the template
		*
		*	If $type to 'boolean', 'integer', 'float' or 'string' is specified, the variable type is set on it.
		*	Setting $format['format'] to 'nf' and $type is 'integer' or 'flot', the variable is parsed by number_format(). The default values ​​correspond to the German format.
		*	However, if $format['format'] specify with 'sf', the string with the format specified in $format['printf'] is formatted by sprintf().
		*
		*/

		settype($key,'string');
		settype($var,'string');
		settype($type,'string');
		settype($format,'array');

		try
		{
			if ( strlen($key) == 0 ) throw new Exception(1);  // Error code 1: variable not set
		}
		catch (Exception $error)
		{
			$this -> print_error_state( $error->getTrace(), $error -> getMessage(), '$key => $var, optional $type and $format' );
		}

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
			*	The default values ​​correspond to the German format.
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
			*	Formatted according to the rules of sprintf() -> http://php.net/manual/de/function.sprintf.php
			*/

			$var = sprintf( $var, $fomat['printf'] );
		}

		$this->variable[$key] = $var;

		return 0;
	}

	function unset_html_variable ( $key = '' )
	{

		/* unset variable */

		settype($key,'string');

		try
		{
			if ( strlen($key)==0 ) throw new Exception(1);  // Error code 1: variable not set
		}
		catch (Exception $error)
		{
			$this -> print_error_state( $error->getTrace(), $error -> getMessage(), '$key' );
		}

		$this->variable[$key] = '';
		unset( $this->variable[$key] );

		return 0;
	}

	function clr_html_variable ()
	{

		/* unset array $this->variable */

		unset ( $this->variable );
		$this->variable = array();

		return 0;
	}

	function set_html_loop ( $key = '', $array = array() )
	{

		/* using $array for a loop */

		settype($array,'array');
		settype($key,'string');

		try
		{
			if ( strlen($key) == 0 ) throw new Exception(1);  // Error code 1: variable not set
		}
		catch (Exception $error)
		{
			$this -> print_error_state( $error->getTrace(), $error -> getMessage(), '$key => $array' );
		}

		$this->loop[$key] = $array;

		return 0;
	}

	function unset_html_loop ( $key = '' )
	{

		/* delete loop */

		settype($key,'string');

		try
		{
			if ( strlen($key) == 0 ) throw new Exception(1);  // Error code 1: variable not set
		}
		catch (Exception $error)
		{
			$this -> print_error_state( $error->getTrace(), $error -> getMessage(), '$key' );
		}

		$this->loop[$key] = array();
		unset( $this->loop[$key] );

		return 0;
	}

	function clr_html_loop ()
	{

		/* clear all loops */

		unset ( $this->loop );
		$this->loop = array();

		return 0;
	}

	/** Ausgabefunktionen **/

	function set_grab()
	{
		$this->grab = true;
	}

	function unset_grab()
	{
		$this->grab = false;
	}

	function print_html_header ()
	{

		/* HTML-header */

		try
		{
			if ( !is_array($this->meta) || count ($this->meta) == 0 || strlen($this->meta['theme'])==0 ) throw new Exception(1);  // Error code 1: variable not set
			if ( ! is_readable(BASE."/templates/".$this->meta['theme']."/vlib_head.tmpl") ) throw new Exception(2); // Error code 2: file not found
		}
		catch (Exception $error)
		{
			$this -> print_error_state( $error->getTrace(), $error -> getMessage(), (($error -> getMessage()==1)?'$theme':'file templates/'.$this->meta['theme'].'/vlib_head.tmpl' ));
		}

		$tmpl =& new vlibTemplate(BASE."/templates/".$this->meta['theme']."/vlib_head.tmpl");
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
		$tmpl -> setVar('head_popup', $this->meta['popup']);
		$tmpl -> setVar('hide_id', $this->meta['hide_id']);

		if ( ! $this->grab )
		{
			$tmpl -> pparse();
			return 0;
		}
		else
		{
			return $tmpl -> grab();
		}

	}

	function print_html_footer ()
	{

		/* HTML-footer */

		try
		{
			if ( !is_array($this->meta) || count ($this->meta) == 0 || strlen($this->meta['theme'])==0 ) throw new Exception(1); // Error code 1: Metadaten nicht angegeben, $this->meta['theme'] ist notwendig!
			if ( ! is_readable(BASE."/templates/".$this->meta['theme']."/vlib_foot.tmpl") ) throw new Exception(2); // Error code 2: file not found
		}
		catch (Exception $error)
		{
			$this -> print_error_state( $error->getTrace(), $error -> getMessage(), (($error -> getMessage()==1)?'$theme':'file templates/'.$this->meta['theme'].'/vlib_foot.tmpl' ));
		}

		$tmpl =& new vlibTemplate(BASE."/templates/".$this->meta['theme']."/vlib_foot.tmpl");
		$tmpl -> setVar('html_body', $this->meta['body']);
		$tmpl -> pparse();

		return 0;

	}

	function load_html_template ( $template = '', $use_scriptname = true )
	{

		/*
		*	Defines the template for subsequent functions.
		*
		*	Variables
		*	$template: abbreviated name for the template. Script name, "vlib_" and ".tmpl" must be left out!
		*
		*/

		settype($template,'string');
		settype($use_scriptname,'boolean');

		try
		{
			if ( strlen($template) == 0 ) throw new Exception(1); // Error code 1: variable not set
		}
		catch (Exception $error)
		{
			$this -> print_error_state( $error->getTrace(), $error -> getMessage(), '$template');
		}

		$this->template = '';

		if ( $use_scriptname === true )
		{
			if ( ! is_readable(BASE."/templates/".$this->meta['theme']."/".basename($_SERVER['PHP_SELF'])."/vlib_".$template.".tmpl") ) return 2; // Error code 2: file not found
			$this->template = BASE."/templates/".$this->meta['theme']."/".basename($_SERVER['PHP_SELF'])."/vlib_".$template.".tmpl";
		}
		else
		{
			if ( ! is_readable(BASE."/templates/".$this->meta['theme']."/vlib_".$template.".tmpl") ) return 2; // Error code 2: file not found
			$this->template = BASE."/templates/".$this->meta['theme']."/vlib_".$template.".tmpl";
		}
		$this->clr_html_loop();
		$this->clr_html_variable();

		if ( $this->debug ) echo "loading template ".$this->template."...<br>";

		return 0;

	}

	function parse_html_template ( $template = '', $array = array(), $use_scriptname = true)
	{

		/*
		*	Meta function! Summarizes the functions load_html_template(), set_html_*() and print_html_template().
		*
		*	Variables: $array[$key]=>$value
		*	Loops: $array[$index][$key]=>$value
		*
		*	Detects based on $value is whether variable or loop.
		*/

		settype($template,'string');
		settype($array,'array');
		settype($use_scriptname,'boolean');

		try
		{
			if ( count($array) == 0) throw new Exception(1); // Error code 1: variable not set
			if ( $this->load_html_template($template,$use_scriptname) > 0 ) throw new Exception(2); // Error code 2: file not found
		}
		catch (Exception $error)
		{
			$this -> print_error_state( $error->getTrace(), $error -> getMessage(), (($error -> getMessage()==1)?'$array':'file templates/'.$this->meta['theme'].'/vlib_'.$template.'.tmpl' ));
		}

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

		/* Alias for parse_html_template(). Template is set to "vlib_table.tmpl". */

		settype($array,'array');

		return $this->parse_html_template( 'table', $array, false);

	}

	function print_html_template ()
	{

		/*
		*	Print template. Use load_html_template(), set_html_*() or parse_html_*() for creating pages.
		*/

		try
		{
			if ( ! is_readable($this->template) ) throw new Exception(2); // Error code 2: file not found
		}
		catch (Exception $error)
		{
			$this -> print_error_state( $error->getTrace(), $error -> getMessage() );
		}

		if ( $this->debug )
		{
			echo "<br>loading template: ".$this->template;
			echo "<br>variables: ";
			print_r($this->variable);
			echo "<br>loop: ";
			print_r($this->loop);
		}

		$tmpl =& new vlibTemplate($this->template);

		if ( count($this->variable) >0 ) while ( list( $key, $value ) = each( $this->variable ) ) $tmpl -> setVar ( $key, $value );
		if ( count($this->loop) >0 ) while ( list( $key, $loop ) = each( $this->loop ) ) $tmpl -> setLoop ( $key, $loop );

		if ( ! $this->grab )
		{
			$tmpl -> pparse();
			return 0;
		}
		else
		{
			return $tmpl -> grab();
		}

	}

	/** Misc **/

	function set_debug()
	{
		$this->debug = true;
	}

	function unset_debug()
	{
		$this->debug = false;
	}

	private function get_error_state( $errno = 0 )
	{

		/* returns the error code as plain text */

		settype($errno,'integer');

		$errorcodes = array (0 => 'success',1 => 'no variable found',2 => 'file not found');
		return $errorcodes[$errno];

	}

	private function print_error_state ( $debug=array(), $error = 0, $variable = '' )
	{

		/* throw error */

		settype($debug,'array');
		settype($error,'integer');
		settype($variable,'string');

		global $title;

		$text = "args:\n\n";
		ob_start();
		var_dump($debug[0]['args']);
		$text .= ob_get_contents();
		ob_end_clean();

		$text .="\n\$_REQUEST:\n\n";
		ob_start();
		var_dump($_REQUEST);
		$text .= ob_get_contents();
		ob_end_clean();

		$e =& new _exception;
		$e -> throwError( array(
			'<strong>Error</strong><br><br>system name: '.$title.'.<br><br>running <em>'.basename($debug[0]['file']).'</em> at line #<em>'.$debug[0]['line'].'</em> error code: <em>'.$this->get_error_state($error).'</em>',
			'using class <em>'.$debug[0]['class'].'()</em>',
			'function name: <em>'.$debug[0]['function'].'()</em>',
			'missing or wrong argument: <em>'.$variable.'</em>'
			),
			$text
		);
	}


}

?>