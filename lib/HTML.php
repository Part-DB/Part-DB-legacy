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
*/

namespace PartDB;

use Exception;
use PartDB\Tools\PDBDebugBar;
use Smarty;

/**
 * @file HTML.php
 * @brief class HTML
 *
 * @class HTML
 * @brief Class HTML
 *
 * This class is used for generating HTML output with the template system "smarty".
 *
 * @author weinbauer73
 *
 * @todo    In the PHP scripts, BASE_RELATIVE has no slash at the end,
 *          but in the template files this constant has another name and has a slash at the end.
 *          It would be better if that constant was identical with BASE_RELATIVE.
 */
class HTML
{
    /********************************************************************************
     *
     *   Attributes
     *
     *********************************************************************************/

    /** @var array Meta variables for the HTML header */
    private $meta               = array();
    /** @var string[] The filenames (without extension) of all JavaScript files which will be included
     *  in the header. The files must be stored in "/javascript/". */
    private $javascript_files   = array();
    /** @var string OnLoad string for the HTML body */
    private $body_onload        = '';

    /** @var array variables for the HTML template */
    private $variables          = array();
    /** @var array loops for the HTML template */
    private $loops              = array();
    /** @var string  */
    private $redirect_url       = "";

    /********************************************************************************
     *
     *   Constructor / Destructor
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @param string    $theme              the name of the theme (the directory name in "/templates/")
     * @param string    $custom_css_file    @li the filename of a custom CSS file, like "greenway.css"
     *                                      @li the file must exist in "/templates/custom_css/"
     *                                      @li If this param is an empty string, the default CSS of the
     *                                          chosen theme will be used.
     * @param string    $page_title         If you want, you can set the page title directly here.
     *                                      Otherwise, you can set it later with HTML::set_meta().
     *
     * @throws Exception if there was an error
     */
    public function __construct($theme, $custom_css_file = '', $page_title = '', $autorefresh = 0)
    {
        // specify the variable type
        settype($this->meta, 'array');
        settype($this->javascript_files, 'array');
        settype($this->body_onload, 'string');
        settype($this->variables, 'array');
        settype($this->loops, 'array');

        // specify the variable type of array $this->meta
        settype($this->meta['theme'], 'string');
        settype($this->meta['title'], 'string');
        settype($this->meta['custom_css'], 'string');
        settype($this->meta['frameset'], 'boolean');
        settype($this->meta['autorefresh'], 'integer');

        // check passed parameters
        if (($theme != 'standard') && (! is_readable(BASE.'/templates/'.$theme."/partdb.css"))) {
            debug('warning', 'Template "'.$theme.'" coult not be found! '.
                'Use "standard" template instead...', __FILE__, __LINE__, __METHOD__);
            $theme = 'standard';
        }
        if ((! is_string($custom_css_file))
            || (($custom_css_file != '') && (! is_readable(BASE.'/templates/custom_css/'.$custom_css_file)))) {
            debug('warning', 'Custom CSS file "'.$custom_css_file.'" coult not be found! '.
                'Use standard instead...', __FILE__, __LINE__, __METHOD__);
            $custom_css_file = '';
        }
        if (! is_string($page_title)) {
            debug('warning', 'Page title "'.$page_title.'" is no string!', __FILE__, __LINE__, __METHOD__);
            $page_title = '';
        }

        $this->meta['theme']        = $theme;
        $this->meta['custom_css']   = $custom_css_file;
        $this->meta['title']        = $page_title;
        $this->meta['frameset']     = false;
        $this->meta['autorefresh'] = $autorefresh;
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     *  Set some meta variables for the HTML header
     *
     * @param array $meta       @li all variables which you want to set
     *                          @li Example: array('title' => 'foo')
     *
     * @throws Exception if the parameter is no array
     */
    public function setMeta($meta = array())
    {
        if (! is_array($meta)) {
            debug('error', '$meta='.print_r($meta, true), __FILE__, __LINE__, __METHOD__);
            throw new Exception('$meta ist kein Array!');
        }

        foreach ($meta as $key => $value) {
            $this->meta[$key] = $value;
        }
    }

    /**
     * Set the Page title
     * @param string $new_title the new title of the page
     * @throws Exception if the param is not a string or is null
     */
    public function setTitle($new_title)
    {
        if (is_null($new_title)) {
            throw new Exception("$new_title must not be null!");
        }
        if (!is_string($new_title)) {
            throw new Exception("$new_title must be an string!");
        }

        $this->meta['title'] = $new_title;
    }

    /**
     * Redirects the User to a other page (must be part of Part-DB), using ajax.
     * Note, that the redirect happens when footer is printed.
     * @param $url string The URL to which should be redirected. Set to empty string, to disable redirect
     * @param $instant boolean True, if the page should be redirected with the call of redirect(). Not just when Footer
     * is printed. Note, if this option is activated, the code execution is stopped after this call.
     */
    public function redirect($url, $instant = false)
    {
        if (!is_string($url))
        {
            throw new \InvalidArgumentException(_('$url must be a valid a string'));
        }

        $this->redirect_url = $url;

        if ($instant) {
            $this->printHeader();
            $this->printFooter();
            exit();
        }
    }


    /**
     * Set all JavaScript filenames which must be included in the HTML header.
     *
     * @note    The JavaScript files must be located in "/javascript/".
     * @note    You only have to supply the filename, without path and without extension.
     *          Example: pass "popup" if you need the file "/javascript/popup.js".
     *
     * @param array  $filenames     @li all filenames (without path/extension) which you need
     *                              @li Example: array([0] => 'popup', [1] => 'validatenumber', ...)
     * @param string $body_onload   If you need some JavaScript functions in the "onLoad" of the body,
     *                              you can pass it here. If there is already a onLoad string set,
     *                              the new string will be attached to the end of the existing onLoad string!
     *
     * @throws Exception if there was an error
     */
    public function useJavascript($filenames = array(), $body_onload = '')
    {
        if (! is_array($filenames)) {
            debug('error', '$filenames='.print_r($filenames, true), __FILE__, __LINE__, __METHOD__);
            throw new Exception('$filenames ist kein Array!');
        }

        foreach ($filenames as $filename) {
            if (! in_array($filename, $this->javascript_files)) {
                $full_filename = BASE.'/javascript/'.$filename.'.js';
                if (! is_readable($full_filename)) {
                    throw new Exception('Die JavaScript-Datei "'.$full_filename.'" existiert nicht!');
                }

                $this->javascript_files[] = $filename;
            }
        }

        settype($body_onload, 'string');
        $this->body_onload .= $body_onload;
    }

    /**
     * Set a variable for the HTML template (site content)
     *
     * @param string    $key        the name of the variable
     * @param mixed     $value      the value of the variable
     * @param string    $type       @li the type of the variable
     *                              @li supported: 'boolean', 'bool', 'integer', 'int', 'float', 'string'
     *
     * @throws Exception if there was an error
     */
    public function setVariable($key, $value, $type = '')
    {
        settype($key, 'string');
        settype($type, 'string');

        if (strlen($key) == 0) {
            debug('error', '$key is not set!', __FILE__, __LINE__, __METHOD__);
            throw new Exception('$key ist leer!');
        }

        if (strlen($type) > 0) {
            if (! in_array($type, array('boolean', 'bool', 'integer', 'int', 'float', 'string'))) {
                debug('error', '$type='.print_r($type, true), __FILE__, __LINE__, __METHOD__);
                throw new Exception('$type hat einen ungültigen Inhalt!');
            }

            settype($value, $type);
        }

        $this->variables[$key] = $value;
    }

    /**
     * Set a loop for the HTML template (site content)
     *
     * @param string    $key        the name of the loop
     * @param array     $loop       the loop array
     *
     * @throws Exception if there was an error
     */
    public function setLoop($key, $loop = array())
    {
        settype($key, 'string');
        settype($loop, 'array');

        if (strlen($key) == 0) {
            debug('error', '$key is not set!', __FILE__, __LINE__, __METHOD__);
            throw new Exception('$key ist leer!');
        }

        $this->loops[$key] = $loop;
    }

    /********************************************************************************
     *
     *   Unsetters
     *
     *********************************************************************************/

    /**
     * Unset a variable
     *
     * @param string    $key        the name of the variable
     *
     * @throws Exception if there was an error
     */
    public function unsetVariable($key)
    {
        settype($key, 'string');

        if (strlen($key) == 0) {
            debug('error', '$key is not set!', __FILE__, __LINE__, __METHOD__);
            throw new Exception('$key ist leer!');
        }

        unset($this->variables[$key]);
    }

    /**
     * Unset a loop
     *
     * @param string    $key        the name of the loop
     *
     * @throws Exception if there was an error
     */
    public function unsetLoop($key)
    {
        settype($key, 'string');

        if (strlen($key) == 0) {
            debug('error', '$key is not set!', __FILE__, __LINE__, __METHOD__);
            throw new Exception('$key ist leer!');
        }

        unset($this->loops[$key]);
    }

    /********************************************************************************
     *
     *   Print Routines
     *
     *********************************************************************************/

    /**
     * Print the HTML header
     *
     * @param array     $messages               @li the messages which should be displayed at the top of the site
     *                                          @li Example: array('text' => 'this is a message', 'strong' => true, 'color' => 'red')
     * @param string    $reload_link            If this is a non-empty string, and there are messages to display,
     *                                          there will be printed a button at the bottom of the messages for
     *                                          reloading the page. Here you can pass the link which is used to reload.
     * @param string    $messages_div_title     If this is a non-empty string, and there are messages to display,
     *                                          the messages will be displayed in a box with this title.
     *
     * @param bool      $redirect               If this true, the page will redirect to startup.
     *
     * @throws Exception if there was an error
     */
    public function printHeader($messages = array(), $reload_link = '', $messages_div_title = '', $redirect = false)
    {
        if (PDBDebugBar::isActivated()) {
            PDBDebugBar::getInstance()->sendData();
        }

        global $config;

        if ((! is_array($this->meta)) || (count($this->meta) == 0) || (strlen($this->meta['theme']) == 0)) {
            debug('warning', 'Meta not set!', __FILE__, __LINE__, __METHOD__);
        }
        $smarty_head = BASE.'/templates/'.$this->meta['theme'].'/smarty_head.tpl';
        if (! is_readable($smarty_head)) {
            debug('error', 'File "'.$smarty_head.'" not found!', __FILE__, __LINE__, __METHOD__);
            throw new Exception('Template Header-Datei "'.$smarty_head.'" wurde nicht gefunden!');
        }

        $tmpl = new Smarty;

        if ($config['debug']['template_debugging_enable']) {
            $tmpl->debugging = true;
        }

        //Remove white space from Output
        $tmpl->loadFilter('output', 'trimwhitespace');

        $tmpl->escape_html = true;


        //Unix locales (de_DE) are other than the HTML lang (de), so edit them
        $lang = explode("_", $config['language'])[0];

        // header stuff
        $tmpl->assign('relative_path', BASE_RELATIVE.'/'); // constant from start_session.php
        $tmpl->assign('page_title', $this->meta['title']);
        $tmpl->assign('http_charset', $config['html']['http_charset']);
        $tmpl->assign('lang', $lang);
        $tmpl->assign('body_onload', $this->body_onload);
        $tmpl->assign('theme', $this->meta['theme']);
        $tmpl->assign('frameset', $this->meta['frameset']);
        $tmpl->assign('redirect', $redirect);
        $tmpl->assign('partdb_title', $config['partdb_title']);
        if (strlen($this->meta['custom_css']) > 0) {
            $tmpl->assign('custom_css', 'templates/custom_css/'.$this->meta['custom_css']);
        }

        if (isset($this->variables['ajax_request'])) {
            $tmpl->assign("ajax_request", $this->variables['ajax_request']);
        }


        //Only load X3D libraries if this is activated
        $tmpl->assign('foot3d_active', $config['foot3d']['active']);

        // JavaScript files
        $javascript_loop = array();
        foreach ($this->javascript_files as $filename) {
            $javascript_loop[] = array('filename' => $filename);
        }
        if (count($javascript_loop) > 0) {
            $tmpl->assign('javascript_files', $javascript_loop);
        }

        if (PDBDebugBar::isActivated()) {
            $renderer = PDBDebugBar::getInstance()->getRenderer();
            $tmpl->assign("debugbar_head", $renderer->renderHead());
        }

        // messages
        if ((is_array($messages) && (count($messages) > 0)) || ($config['debug']['request_debugging_enable'])) {
            if ($config['debug']['request_debugging_enable']) {
                if ((is_array($messages) && (count($messages) > 0))) {
                    $messages[] = array('text' => '');
                }
                $messages[] = array('text' => '$_REQUEST:', 'strong' => true, 'color' => 'darkblue');
                $messages[] = array('text' => print_r($_REQUEST, true), 'color' => 'darkblue');
            }

            $tmpl->assign('messages', $messages);
            $tmpl->assign('messages_div_title', $messages_div_title);
            $tmpl->assign('reload_link', $reload_link);
        }

        $tmpl->display($smarty_head);
    }

    /**
     * Print the HTML template (page content)
     *
     * @param string    $template           @li the template name (not the theme name!)
     *                                      @li Example: for the template "vlib_startup.tmpl"
     *                                          you have to pass "startup"
     * @param boolean   $use_scriptname     @li if true, the template file
     *                                          "/templates/<theme>/<scriptname>/vlib_<template>.tmpl"
     *                                          will be used, where <scriptname> is the filename (*.php)
     *                                          of the file which was calling this method.
     *                                      @li if false, the template file
     *                                          "/templates/<theme>/vlib_<template>.tmpl"
     *                                          will be used.
     *
     * @throws Exception if there was an error
     */
    public function printTemplate($template, $use_scriptname = true)
    {
        global $config;

        settype($template, 'string');
        settype($use_scriptname, 'boolean');

        if ($use_scriptname) {
            $smarty_template =    BASE.'/templates/'.$this->meta['theme'].'/'.
                basename($_SERVER['SCRIPT_NAME']).'/smarty_'.$template.'.tpl';
        } else {
            $smarty_template = BASE.'/templates/'.$this->meta['theme'].'/smarty_'.$template.'.tpl';
        }

        if (! is_readable($smarty_template)) {
            debug(
                'error',
                'Template-Datei "'.$smarty_template.'" konnte nicht gefunden werden!',
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Template-Datei "'.$smarty_template.'" konnte nicht gefunden werden!');
        }

        $tmpl = new Smarty();

        if ($config['debug']['template_debugging_enable']) {
            $tmpl->debugging = true;
        }

        $tmpl->assign('relative_path', BASE_RELATIVE.'/'); // constant from start_session.php

        foreach ($this->variables as $key => $value) {
            //debug('temp', $key.' => '.$value);
            $tmpl->assign($key, $value);
        }

        foreach ($this->loops as $key => $loop) {
            $tmpl->assign($key, $loop);
        }

        //Remove white space from Output
        $tmpl->loadFilter('output', 'trimwhitespace');

        //Prevents XSS
        $tmpl->escape_html = true;

        if($this->redirect_url == "") { //Dont print template, if the page should be redirected.
            $tmpl->display($smarty_template);
        }
    }

    /**
     * Print the HTML footer
     *
     * @param array     $messages               @li the messages which should be displayed at the bottom of the site
     *                                          @li Example: array('text' => 'this is a message', 'strong' => true, 'color' => 'red')
     * @param string    $messages_div_title     If this is a non-empty string, and there are messages to display,
     *                                          the messages will be displayed in a box with this title.
     *
     * @throws Exception if there was an error
     */
    public function printFooter($messages = array(), $messages_div_title = '')
    {
        global $config;

        $smarty_foot = BASE.'/templates/'.$this->meta['theme'].'/smarty_foot.tpl';

        if (! is_readable($smarty_foot)) {
            debug('error', 'File "'.$smarty_foot.'" not found!', __FILE__, __LINE__, __METHOD__);
            throw new Exception('Template Footer-Datei "'.$smarty_foot.'" wurde nicht gefunden!');
        }

        $tmpl = new Smarty();

        if ($config['debug']['template_debugging_enable']) {
            $tmpl->debugging = true;
        }

        if (isset($this->variables['ajax_request'])) {
            $tmpl->assign("ajax_request", $this->variables['ajax_request']);
        }

        $tmpl->assign('relative_path', BASE_RELATIVE.'/'); // constant from start_session.php
        $tmpl->assign('frameset', $this->meta['frameset']);

        // messages
        if ((is_array($messages) && (count($messages) > 0))) {
            $tmpl->assign('messages', $messages);
            $tmpl->assign('messages_div_title', $messages_div_title);
        }

        $tmpl->assign("tracking_code", $config['tracking_code']);
        $tmpl->assign("autorefresh", $this->meta['autorefresh']);

        if (PDBDebugBar::isActivated()) {
            $renderer = PDBDebugBar::getInstance()->getRenderer();
            $tmpl->assign("debugbar_body", $renderer->render(!isset($_REQUEST['ajax_request'])));
        }

        $tmpl->assign("redirect_url", $this->redirect_url);

        //Remove white space from Output
        $tmpl->loadFilter('output', 'trimwhitespace');

        //Prevents XSS
        $tmpl->escape_html = true;
        $tmpl->display($smarty_foot);
    }
}
