<?php declare(strict_types=1);
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
use PartDB\Exceptions\TemplateNotFoundException;
use PartDB\Exceptions\TemplateSystemException;
use PartDB\Permissions\PartContainingPermission;
use PartDB\Permissions\PartPermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\StructuralPermission;
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
    /** @var array variables (and which was called loops before => arrays) for the HTML template */
    private $variables          = array();
    /** @var string  */
    private $redirect_url       = '';

    /**
     * @var Smarty The shared Smarty object.
     */
    private $tmpl;

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
     * @param int       $autorefresh        If you want that the page refresh it self, set this value to the desired interval (in seconds).
     *                                      Set to 0 to disable autorefreshing.
     *
     * @throws Exception if there was an error
     */
    public function __construct(string $theme, string $custom_css_file = '', string $page_title = '', int $autorefresh = 0)
    {
        $this->javascript_files = (array)$this->javascript_files;

        // specify the variable type of array $this->meta
        $this->meta['theme'] = (string)$this->meta['theme'];
        $this->meta['title'] = (string)$this->meta['title'];
        $this->meta['custom_css'] = (string)$this->meta['custom_css'];
        $this->meta['autorefresh'] = (int)$this->meta['autorefresh'];

        // check passed parameters
        if (($theme !== 'nextgen') && (! is_readable(BASE.'/templates/'.$theme. '/info.json'))) {
            debug('warning', 'Template "'.$theme.'" could not be found! '.
                'Use "nextgen" template instead...', __FILE__, __LINE__, __METHOD__);
            $theme = 'nextgen';
        }
        if ((! \is_string($custom_css_file))
            || (($custom_css_file != '') && (! is_readable(BASE.'/templates/custom_css/'.$custom_css_file)))) {
            debug('warning', 'Custom CSS file "'.$custom_css_file.'" could not be found! '.
                'Use standard instead...', __FILE__, __LINE__, __METHOD__);
            $custom_css_file = '';
        }
        if (! \is_string($page_title)) {
            debug('warning', 'Page title "'.$page_title.'" is no string!', __FILE__, __LINE__, __METHOD__);
            $page_title = '';
        }

        $this->meta['theme']        = $theme;
        $this->meta['custom_css']   = $custom_css_file;
        $this->meta['title']        = $page_title;
        $this->meta['autorefresh'] = $autorefresh;

        global $config;

        //Init Smarty Objects
        $this->tmpl = new Smarty();

        if ($config['debug']['template_debugging_enable']) {
            $this->tmpl->debugging = true;
        }

        //Remove white space from Output
        $this->tmpl->loadFilter('output', 'trimwhitespace');
        //Prevent XSS attacks
        $this->tmpl->escape_html = true;
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
    public function setMeta(array $meta = array())
    {
        if (! \is_array($meta)) {
            debug('error', '$meta='.print_r($meta, true), __FILE__, __LINE__, __METHOD__);
            throw new TemplateSystemException(_('$meta ist kein Array!'));
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
    public function setTitle(string $new_title)
    {
        $this->meta['title'] = $new_title;
    }

    /**
     * Redirects the User to a other page (must be part of Part-DB), using ajax.
     * Note, that the redirect happens when footer is printed.
     * @param $url string The URL to which should be redirected. Set to empty string, to disable redirect
     * @param $instant boolean True, if the page should be redirected with the call of redirect(). Not just when Footer
     * is printed. Note, if this option is activated, the code execution is stopped after this call.
     * @throws Exception
     */
    public function redirect(string $url, bool $instant = false)
    {
        if (!\is_string($url)) {
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
    public function useJavascript(array $filenames = array(), string $body_onload = '')
    {
        foreach ($filenames as $filename) {
            if (! \in_array($filename, $this->javascript_files)) {
                $full_filename = BASE.'/javascript/'.$filename.'.js';
                if (! is_readable($full_filename)) {
                    throw new TemplateSystemException(sprintf(_('Die JavaScript-Datei "%s" existiert nicht!'), $full_filename));
                }

                $this->javascript_files[] = $filename;
            }
        }
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
    public function setVariable(string $key, $value, string $type = '')
    {
        if (empty($key)) {
            debug('error', '$key is not set!', __FILE__, __LINE__, __METHOD__);
            throw new TemplateSystemException(_('$key ist leer!'));
        }

        if (!empty($type)) {
            if (! \in_array($type, array('boolean', 'bool', 'integer', 'int', 'float', 'double', 'string', 'array', 'object'))) {
                debug('error', '$type='.print_r($type, true), __FILE__, __LINE__, __METHOD__);
                throw new TemplateSystemException(_('$type hat einen ungültigen Inhalt!'));
            }

            settype($value, $type);
        }

        $this->variables[$key] = $value;
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
    public function unsetVariable(string $key)
    {
        if (empty($key)) {
            debug('error', '$key is not set!', __FILE__, __LINE__, __METHOD__);
            throw new TemplateSystemException(_('$key ist leer!'));
        }

        unset($this->variables[$key]);
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
    public function printHeader(array $messages = array(), string $reload_link = '', string $messages_div_title = '', bool $redirect = false)
    {
        if (PDBDebugBar::isActivated()) {
            PDBDebugBar::getInstance()->sendData();
        }

        global $config;

        if ((! \is_array($this->meta)) || (count($this->meta) == 0) || empty($this->meta['theme'])) {
            debug('warning', 'Meta not set!', __FILE__, __LINE__, __METHOD__);
        }
        $smarty_head = BASE.'/templates/'.$this->meta['theme'].'/smarty_head.tpl';
        if (! is_readable($smarty_head)) {
            debug('error', 'File "'.$smarty_head.'" not found!', __FILE__, __LINE__, __METHOD__);
            throw new TemplateNotFoundException(sprintf(_('Template Header-Datei "%s" wurde nicht gefunden!'), $smarty_head));
        }

        $tmpl = $this->tmpl;
        //Clear all assigned variables
        $tmpl->clearAllAssign();


        //Unix locales (de_DE) are other than the HTML lang (de), so edit them
        $lang = explode('_', $config['language'])[0];

        // header stuff
        $tmpl->assign('relative_path', BASE_RELATIVE.'/'); // constant from start_session.php
        $tmpl->assign('page_title', $this->meta['title']);
        $tmpl->assign('http_charset', $config['html']['http_charset']);
        $tmpl->assign('lang', $lang);
        $tmpl->assign('body_onload', $this->body_onload);
        $tmpl->assign('theme', $this->meta['theme']);
        $tmpl->assign('redirect', $redirect);
        $tmpl->assign('partdb_title', $config['partdb_title']);

        //Informations about User
        $tmpl->assign('loggedin', User::isLoggedIn());
        try {
            $user = User::getLoggedInUser();
            $tmpl->assign('username', $user->getName());
            $tmpl->assign('firstname', $user->getFirstName());
            $tmpl->assign('lastname', $user->getLastName());
            $tmpl->assign('can_search', $user->canDo(PermissionManager::PARTS, PartPermission::SEARCH));
            $tmpl->assign('can_category', $user->canDo(PermissionManager::CATEGORIES, StructuralPermission::READ)
                && $user->canDo(PermissionManager::CATEGORIES, PartContainingPermission::LIST_PARTS));
            $tmpl->assign('can_device', $user->canDo(PermissionManager::DEVICES, StructuralPermission::READ));
        } catch (Exception $exception) {
            //TODO
        }



        if (\strlen($this->meta['custom_css']) > 0) {
            $tmpl->assign('custom_css', 'templates/custom_css/'.$this->meta['custom_css']);
        }

        if (isset($this->variables['ajax_request'])) {
            $tmpl->assign('ajax_request', $this->variables['ajax_request']);
        }

        $tmpl->assign('devices_disabled', $config['devices']['disable']);
        $tmpl->assign('footprints_disabled', $config['footprints']['disable']);
        $tmpl->assign('manufacturers_disabled', $config['manufacturers']['disable']);
        $tmpl->assign('suppliers_disabled', $config['suppliers']['disable']);

        $tmpl->assign('livesearch_active', $config['search']['livesearch']);

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
            $tmpl->assign('debugbar_head', $renderer->renderHead());
        }

        $tmpl->assign('debugging_activated', $config['debug']['enable']);

        // messages
        if ((\is_array($messages) && (count($messages) > 0)) || $config['debug']['request_debugging_enable']) {
            if ($config['debug']['request_debugging_enable']) {
                if (\is_array($messages) && (count($messages) > 0)) {
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
    public function printTemplate(string $template, bool $use_scriptname = true)
    {
        global $config;

        $template = (string)$template;
        $use_scriptname = (bool)$use_scriptname;

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
            throw new TemplateNotFoundException(sprintf(_('Template-Datei "%s" konnte nicht gefunden werden!'), $smarty_template));
        }

        $tmpl = $this->tmpl;
        $tmpl->clearAllAssign();


        $tmpl->assign('relative_path', BASE_RELATIVE.'/'); // constant from start_session.php

        $tmpl->assign('debugging_activated', $config['debug']['enable']);

        foreach ($this->variables as $key => $value) {
            //debug('temp', $key.' => '.$value);
            $tmpl->assign($key, $value);
        }

        if ($this->redirect_url == '') { //Dont print template, if the page should be redirected.
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
    public function printFooter(array $messages = array(), string $messages_div_title = '')
    {
        global $config;

        $smarty_foot = BASE.'/templates/'.$this->meta['theme'].'/smarty_foot.tpl';

        if (! is_readable($smarty_foot)) {
            debug('error', 'File "'.$smarty_foot.'" not found!', __FILE__, __LINE__, __METHOD__);
            throw new TemplateNotFoundException(sprintf(_('Template Footer-Datei "%s" wurde nicht gefunden!'), $smarty_foot));
        }

        $tmpl = $this->tmpl;
        $tmpl->clearAllAssign();

        if (isset($this->variables['ajax_request'])) {
            $tmpl->assign('ajax_request', $this->variables['ajax_request']);
        }

        $tmpl->assign('relative_path', BASE_RELATIVE.'/'); // constant from start_session.php

        // messages
        if (\is_array($messages) && (count($messages) > 0)) {
            $tmpl->assign('messages', $messages);
            $tmpl->assign('messages_div_title', $messages_div_title);
        }

        $tmpl->assign('tracking_code', $config['tracking_code']);
        $tmpl->assign('auto_sort', $config['table']['autosort']);
        $tmpl->assign('autorefresh', $this->meta['autorefresh']);

        if (PDBDebugBar::isActivated()) {
            $renderer = PDBDebugBar::getInstance()->getRenderer();
            $tmpl->assign('debugbar_body', $renderer->render(!isset($_REQUEST['ajax_request'])));
        }

        $tmpl->assign('redirect_url', $this->redirect_url);

        $tmpl->assign('cookie_consent_active', $config['cookie_consent']['enable']);
        if ($config['cookie_consent']['enable']) {
            $tmpl->assign('cookie_consent_config', $config['cookie_consent']);
        }

        $tmpl->assign('debugging_activated', $config['debug']['enable']);

        $tmpl->display($smarty_foot);
    }
}
