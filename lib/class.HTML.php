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

    Changelog (sorted by date):
        [DATE]      [NICKNAME]      [CHANGES]
        2012-??-??  weinbauer73     - created
        2012-08-29  weinbauer73     - adding head_switch_ds_path-switch
        2012-09-28  kami89          - added Exceptions
                                    - added doxygen comments
*/

    /**
     * @file class.HTML.php
     * @brief class HTML
     *
     * @class HTML
     * @brief Class HTML
     *
     * This class is used for generating HTML output with the template system "vlib".
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

        /** (array) Meta variables for the HTML header */
        private $meta               = array();
        /** (array) The filenames (without extension) of all JavaScript files which will be included
         *  in the header. The files must be stored in "/javascript/". */
        private $javascript_files   = array();
        /** (string) OnLoad string for the HTML body */
        private $body_onload        = '';

        /** (array) variables for the HTML template */
        private $variables          = array();
        /** (array) loops for the HTML template */
        private $loops              = array();

        /********************************************************************************
        *
        *   Constructor / Destructor
        *
        *********************************************************************************/

        /**
         * @brief Constructor
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
        public function __construct($theme, $custom_css_file = '', $page_title = '')
        {
            // specify the variable type
            settype($this->meta,                'array');
            settype($this->javascript_files,    'array');
            settype($this->body_onload,         'string');
            settype($this->variables,           'array');
            settype($this->loops,               'array');

            // specify the variable type of array $this->meta
            settype($this->meta['theme'],               'string');
            settype($this->meta['title'],               'string');
            settype($this->meta['custom_css'],          'string');
            settype($this->meta['frameset'],            'boolean');

            // check passed parameters
            if (($theme != 'standard') && ( ! is_readable(BASE.'/templates/'.$theme."/partdb.css" )))
            {
                debug('warning', 'Template "'.$theme.'" coult not be found! '.
                                        'Use "standard" template instead...', __FILE__, __LINE__, __METHOD__);
                $theme = 'standard';
            }
            if (( ! is_string($custom_css_file))
                || (($custom_css_file != '') && ( ! is_readable(BASE.'/templates/custom_css/'.$custom_css_file))))
            {
                debug('warning', 'Custom CSS file "'.$custom_css_file.'" coult not be found! '.
                                        'Use standard instead...', __FILE__, __LINE__, __METHOD__);
                $custom_css_file = '';
            }
            if ( ! is_string($page_title))
            {
                debug('warning', 'Page title "'.$page_title.'" is no string!', __FILE__, __LINE__, __METHOD__);
                $page_title = '';
            }

            $this->meta['theme']        = $theme;
            $this->meta['custom_css']   = $custom_css_file;
            $this->meta['title']        = $page_title;
            $this->meta['frameset']     = false;
        }

        /********************************************************************************
        *
        *   Setters
        *
        *********************************************************************************/

        /**
         * @brief Set some meta variables for the HTML header
         *
         * @param array $meta       @li all variables which you want to set
         *                          @li Example: array('title' => 'foo')
         *
         * @throws Exception if the parameter is no array
         */
        public function set_meta($meta = array())
        {
            if ( ! is_array($meta))
            {
                debug('error', '$meta='.print_r($meta, true), __FILE__, __LINE__, __METHOD__);
                throw new Exception('$meta ist kein Array!');
            }

            foreach ($meta as $key => $value)
            {
                $this->meta[$key] = $value;
            }
        }

        /**
         * @brief Set all JavaScript filenames which must be included in the HTML header.
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
        public function use_javascript($filenames = array(), $body_onload = '')
        {
            if ( ! is_array($filenames))
            {
                debug('error', '$filenames='.print_r($filenames, true), __FILE__, __LINE__, __METHOD__);
                throw new Exception('$filenames ist kein Array!');
            }

            foreach ($filenames as $filename)
            {
                if ( ! in_array($filename, $this->javascript_files))
                {
                    $full_filename = BASE.'/javascript/'.$filename.'.js';
                    if ( ! is_readable($full_filename))
                        throw new Exception('Die JavaScript-Datei "'.$full_filename.'" existiert nicht!');

                    $this->javascript_files[] = $filename;
                }
            }

            settype($body_onload, 'string');
            $this->body_onload .= $body_onload;
        }

        /**
         * @brief Set a variable for the HTML template (site content)
         *
         * @param string    $key        the name of the variable
         * @param mixed     $value      the value of the variable
         * @param string    $type       @li the type of the variable
         *                              @li supported: 'boolean', 'bool', 'integer', 'int', 'float', 'string'
         *
         * @throws Exception if there was an error
         */
        public function set_variable($key, $value, $type = '')
        {
            settype($key, 'string');
            settype($type, 'string');

            if (strlen($key) == 0)
            {
                debug('error', '$key is not set!', __FILE__, __LINE__, __METHOD__);
                throw new Exception('$key ist leer!');
            }

            if (strlen($type) > 0)
            {
                if ( ! in_array($type, array('boolean', 'bool', 'integer', 'int', 'float', 'string')))
                {
                    debug('error', '$type='.print_r($type, true), __FILE__, __LINE__, __METHOD__);
                    throw new Exception('$type hat einen ungültigen Inhalt!');
                }

                settype($value, $type);
            }

            $this->variables[$key] = $value;
        }

        /**
         * @brief Set a loop for the HTML template (site content)
         *
         * @param string    $key        the name of the loop
         * @param array     $loop       the loop array
         *
         * @throws Exception if there was an error
         */
        public function set_loop($key, $loop = array())
        {
            settype($key, 'string');
            settype($loop, 'array');

            if (strlen($key) == 0)
            {
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
         * @brief Unset a variable
         *
         * @param string    $key        the name of the variable
         *
         * @throws Exception if there was an error
         */
        public function unset_variable($key)
        {
            settype($key, 'string');

            if (strlen($key) == 0)
            {
                debug('error', '$key is not set!', __FILE__, __LINE__, __METHOD__);
                throw new Exception('$key ist leer!');
            }

            unset($this->variables[$key]);
        }

        /**
         * @brief Unset a loop
         *
         * @param string    $key        the name of the loop
         *
         * @throws Exception if there was an error
         */
        public function unset_loop($key)
        {
            settype($key, 'string');

            if (strlen($key) == 0)
            {
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
         * @brief Print the HTML header
         *
         * @param array     $messages               @li the messages which should be displayed at the top of the site
         *                                          @li Example: array('text' => 'this is a message', 'strong' => true, 'color' => 'red')
         * @param string    $reload_link            If this is a non-empty string, and there are messages to display,
         *                                          there will be printed a button at the bottom of the messages for
         *                                          reloading the page. Here you can pass the link which is used to reload.
         * @param string    $messages_div_title     If this is a non-empty string, and there are messages to display,
         *                                          the messages will be displayed in a box with this title.
         *
         * @throws Exception if there was an error
         */
        public function print_header($messages = array(), $reload_link = '', $messages_div_title = '')
        {
            global $config;

            if (( ! is_array($this->meta)) || (count($this->meta) == 0) || (strlen($this->meta['theme']) == 0))
            {
                debug('warning', 'Meta not set!', __FILE__, __LINE__, __METHOD__);
            }

            $vlib_head = BASE.'/templates/'.$this->meta['theme'].'/vlib_head.tmpl';

            if ( ! is_readable($vlib_head))
            {
                debug('error', 'File "'.$vlib_head.'" not found!', __FILE__, __LINE__, __METHOD__);
                throw new Exception('Template Header-Datei "'.$vlib_head.'" wurde nicht gefunden!');
            }

            if ($config['debug']['template_debugging_enable'])
            {
                include_once(BASE.'/lib/vlib/vlibTemplate/debug.php');
                $tmpl = new vlibTemplateDebug($vlib_head);
            }
            else
                $tmpl = new vlibTemplate($vlib_head);

            // header stuff
            $tmpl->setVar('relative_path',              BASE_RELATIVE.'/'); // constant from start_session.php
            $tmpl->setVar('page_title',                 $this->meta['title']);
            $tmpl->setVar('http_charset',               $config['html']['http_charset']);
            $tmpl->setVar('body_onload',                $this->body_onload);
            $tmpl->setVar('theme',                      $this->meta['theme']);
            $tmpl->setVar('frameset',                   $this->meta['frameset']);
            if (strlen($this->meta['custom_css']) > 0)
                $tmpl->setVar('custom_css', 'templates/custom_css/'.$this->meta['custom_css']);

            // JavaScript files
            $javascript_loop = array();
            foreach ($this->javascript_files as $filename)
            {
                $javascript_loop[] = array('filename' => $filename);
            }
            if (count($javascript_loop) > 0)
                $tmpl->setLoop('javascript_files', $javascript_loop);

            // messages
            if ((is_array($messages) && (count($messages) > 0)) || ($config['debug']['request_debugging_enable']))
            {
                if ($config['debug']['request_debugging_enable'])
                {
                    if ((is_array($messages) && (count($messages) > 0)))
                        $messages[] = array('text' => '');
                    $messages[] = array('text' => '$_REQUEST:', 'strong' => true, 'color' => 'darkblue');
                    $messages[] = array('text' => print_r($_REQUEST, true), 'color' => 'darkblue');
                }

                $tmpl->setLoop('messages',              $messages);
                $tmpl->setVar('messages_div_title',     $messages_div_title);
                $tmpl->setVar('reload_link',            $reload_link);
            }

            $tmpl->pparse();
        }

         /**
         * @brief Print the HTML template (page content)
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
        public function print_template($template, $use_scriptname = true)
        {
            global $config;

            settype($template, 'string');
            settype($use_scriptname, 'boolean');

            if ($use_scriptname)
            {
                $vlib_template =    BASE.'/templates/'.$this->meta['theme'].'/'.
                                    basename($_SERVER['SCRIPT_NAME']).'/vlib_'.$template.'.tmpl';
            }
            else
            {
                $vlib_template = BASE.'/templates/'.$this->meta['theme'].'/vlib_'.$template.'.tmpl';
            }

            if ( ! is_readable($vlib_template))
            {
                debug('error', 'Template-Datei "'.$vlib_template.'" konnte nicht gefunden werden!',
                                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Template-Datei "'.$vlib_template.'" konnte nicht gefunden werden!');
            }

            if ($config['debug']['template_debugging_enable'])
            {
                include_once(BASE.'/lib/vlib/vlibTemplate/debug.php');
                $tmpl = new vlibTemplateDebug($vlib_template);
            }
            else
                $tmpl = new vlibTemplate($vlib_template);

            $tmpl->setVar('relative_path', BASE_RELATIVE.'/'); // constant from start_session.php

            foreach ($this->variables as $key => $value)
            {
                //debug('temp', $key.' => '.$value);
                $tmpl->setVar($key, $value);
            }

            foreach ($this->loops as $key => $loop)
            {
                $tmpl->setLoop($key, $loop);
            }

            $tmpl->pparse();
        }

        /**
         * @brief Print the HTML footer
         *
         * @param array     $messages               @li the messages which should be displayed at the bottom of the site
         *                                          @li Example: array('text' => 'this is a message', 'strong' => true, 'color' => 'red')
         * @param string    $messages_div_title     If this is a non-empty string, and there are messages to display,
         *                                          the messages will be displayed in a box with this title.
         *
         * @throws Exception if there was an error
         */
        public function print_footer($messages = array(), $messages_div_title = '')
        {
            global $config;

            $vlib_foot = BASE.'/templates/'.$this->meta['theme'].'/vlib_foot.tmpl';

            if ( ! is_readable($vlib_foot))
            {
                debug('error', 'File "'.$vlib_foot.'" not found!', __FILE__, __LINE__, __METHOD__);
                throw new Exception('Template Footer-Datei "'.$vlib_foot.'" wurde nicht gefunden!');
            }

            if ($config['debug']['template_debugging_enable'])
            {
                include_once(BASE.'/lib/vlib/vlibTemplate/debug.php');
                $tmpl = new vlibTemplateDebug($vlib_foot);
            }
            else
                $tmpl = new vlibTemplate($vlib_foot);

            $tmpl->setVar('relative_path',  BASE_RELATIVE.'/'); // constant from start_session.php
            $tmpl->setVar('frameset',       $this->meta['frameset']);

            // messages
            if ((is_array($messages) && (count($messages) > 0)))
            {
                $tmpl->setLoop('messages',              $messages);
                $tmpl->setVar('messages_div_title',     $messages_div_title);
            }

            $tmpl->pparse();
        }
    }

?>
