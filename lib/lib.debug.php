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

    /**
     * @file lib.debug.php
     * @brief Functions for the Debug Log
     * @author kami89
     */

    define('DEBUG_LOG_FILENAME', BASE.'/data/log/debug_log.xml');

    /********************************************************************************
    *
    *   Basic Functions
    *
    *********************************************************************************/

    /**
     * @brief Add an Element to the Debug-Log
     *
     * @par Example:
     * @code debug('error', 'This is your Message...', __FILE__, __LINE__, __METHOD__); @endcode
     *
     * @param string    $type       @li The type of the log message, e.g. "error", "warning", "hint", ...
     *                              @l1 You can use every string you want, but this types will be highlighted:
     *                              @li "error", "warning", "temp"
     *                              @li "temp" means "temporary", it will be printed bold, so it's easy to
     *                                  find your message in a large list of messages. Use this only temporary!
     * @param string    $text       The message of your choice
     * @param string    $file       The file where the error/whatever is located (use the PHP constant "__FILE__")
     * @param string    $line       The line where the error/whatever is located (use the PHP constant "__LINE__")
     * @param string    $method     The method/function where the error/whatever is located (use the PHP constant "__METHOD__")
     * @param boolean   $silent     @li If true, this function will never throw an exception!
     *                              @li This function should never disturb the normal operation,
     *                                  so you should always use the silent mode!
     *
     * @note Use always "__METHOD__", not "__FUNCTION__" ("__METHOD__" works in classes too, "__FUNCTION__" not!)
     *
     * @throws Exception if there was an error and "$silent == false"
     */
    function debug($type, $text, $file = '', $line = '', $method = '', $silent = true)
    {
        global $config;

        if ( ! $config['debug']['enable'])
            return;


        if($config['debug']['debugbar'])
        {
            $level = Psr\Log\LogLevel::WARNING;
            $debugbar = PDBDebugBar::getInstance()->getDebugBar();
            $type = strtolower($type);
            switch ($type)
            {
                case "error":
                    $level = Psr\Log\LogLevel::ERROR;
                    break;
                case "warning":
                    $level = Psr\Log\LogLevel::WARNING;
                    break;
                case "hint":
                    $level = Psr\Log\LogLevel::NOTICE;
                    break;
                case "temp":
                    $level = Psr\Log\LogLevel::DEBUG;
                    break;
                case "success":
                    $level = Psr\Log\LogLevel::INFO;
            }

            $debugbar['messages']->log($level, $text);
        }

        if ( ! file_exists(DEBUG_LOG_FILENAME))
            return;

        if ( ! is_readable(DEBUG_LOG_FILENAME))
        {
            if ($silent)
                return;
            else
                throw new Exception('Die Debug-Log Datei ist nicht lesbar!');
        }

        if ( ! is_writable(DEBUG_LOG_FILENAME))
        {
            if ($silent)
                return;
            else
                throw new Exception('Die Debug-Log Datei ist nicht beschreibbar!');
        }

        $file = str_replace(BASE, '', $file); // we don't need the whole filename, the relative path is enought

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $success = $dom->load(DEBUG_LOG_FILENAME/*, LIBXML_NOERROR | LIBXML_NOWARNING*/);

        if ( ! $success)
        {
            if ($silent)
                return;
            else
                throw new Exception('Das DOMDocument-Objekt konnte nicht erstellt werden!');
        }

        $root_nodes = $dom->getElementsByTagName('debug_log');
        if (count($root_nodes) == 0)
            $root_node = $dom->createElement('debug_log');
        else
            $root_node = $root_nodes->item(0);

        // add new line to XML file
        $new_node = $dom->createElement('log', $text);
        $new_node->setAttribute('datetime', date('Y-m-d_H:i:s'));
        $new_node->setAttribute('type', strtoupper($type));
        $new_node->setAttribute('file', $file);
        $new_node->setAttribute('line', $line);
        $new_node->setAttribute('function', $method);
        $root_node->appendChild($new_node);

        if ( ! $dom->save(DEBUG_LOG_FILENAME, LIBXML_NOEMPTYTAG))
        {
            if ($silent)
                return;
            else
                throw new Exception('Die Debug-Log XML-Datei konnte nicht gespeichert werden!');
        }
    }

    /**
     * @brief Create a new (empty) Debug Log File
     *
     * @throws Exception if there was an error (maybe no permissions)
     */
    function create_debug_log_file()
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $root_node = $dom->createElement('debug_log');
        $dom->appendChild($root_node);

        if ( ! $dom->save(DEBUG_LOG_FILENAME, LIBXML_NOEMPTYTAG))
            throw new Exception('Die Debug-Log XML-Datei konnte nicht gespeichert werden!');
    }

    /**
     * @brief Delete the Debug Log File
     *
     * @throws Exception if there was an error (maybe no permissions)
     */
    function delete_debug_log_file()
    {
        if ( ! file_exists(DEBUG_LOG_FILENAME))
            return;

        if ( ! unlink(DEBUG_LOG_FILENAME))
            throw new Exception('Die Debug-Log Datei konnte nicht gelöscht werden!');
    }

    /********************************************************************************
    *
    *   Getters
    *
    *********************************************************************************/

    /**
     * @brief Get log elements
     *
     * @param array|NULL $types     @li here you can supply an array of all log types (strings) you want to get
     *                              @li NULL if you want to get ALL log elements
     *
     * @retval array     log elements (array-like)
     *
     * @throws Exception if there was an error (maybe no file or no read permissions)
     */
    function get_debug_log_elements($types = NULL)
    {
        if ( ! is_readable(DEBUG_LOG_FILENAME))
            //throw new Exception('Es existiert kein Debug-Log!');
            create_debug_log_file();

        $dom = new DOMDocument('1.0', 'utf-8');
        $success = $dom->load(DEBUG_LOG_FILENAME/*, LIBXML_NOERROR | LIBXML_NOWARNING*/);

        if ( ! $success)
            throw new Exception('Das DOMDocument-Objekt konnte nicht erstellt werden!');

        $elements = $dom->getElementsByTagName('log');
        $log_array = array();
        foreach ($elements as $element)
        {
            $values = array();
            $values['message'] = $element->nodeValue;
            $values['datetime'] = $element->getAttribute('datetime');
            $values['type'] = $element->getAttribute('type');
            $values['file'] = $element->getAttribute('file');
            $values['line'] = $element->getAttribute('line');
            $values['function'] = $element->getAttribute('function');
            $log_array[] = $values;
        }

        if ( ! is_array($types))
        {
            return $log_array;
        }
        else
        {
            $types = array_map('strtoupper', $types);
            $logs = array();
            foreach ($log_array as $log)
            {
                if (in_array($log['type'], $types))
                    $logs[] = $log;
            }
            return $logs;
        }
    }

    /**
     * @brief Get all different log types which exists in the log
     *
     * @retval array    log types (array of strings)
     *
     * @throws Exception if there was an error (maybe no file or no read permissions)
     */
    function get_all_debug_types()
    {
        $types = array();
        $logs = get_debug_log_elements();

        foreach ($logs as $log)
        {
            if (! in_array((string)$log['type'], $types))
                $types[] = $log['type'];
        }

        sort($types);

        return $types;
    }

    /********************************************************************************
    *
    *   Setters
    *
    *********************************************************************************/

    /**
     * @brief Set the enable attribute
     * ¨
     * @param boolean       $new_enable         The new enable state
     * @param string|NULL   $admin_password     @li The admin password for enabling/disabling the debug log (from "config.php").
     *                                          @li see $config['debug']['password'] in config_defaults.php
     *                                          @li For disabling the debug log,
     *                                              the passwort is not required (pass NULL)!
     *
     * @throws Exception if there was an error (maybe wrong password)
     */
    function set_debug_enable($new_enable, $admin_password = NULL)
    {
        global $config;

        if ($new_enable == $config['debug']['enable'])
            return; // there is nothing to do...

        if ($new_enable == false)
        {
            $config['debug']['enable'] = false;
            $config['debug']['template_debugging_enable'] = false;
            $config['debug']['request_debugging_enable'] = false;
            try
            {
                save_config();
            }
            catch (Exception $e)
            {
                $config['debug']['enable'] = true;
                throw $e;
            }

            return;
        }

        // to activate the debug log, we have to check the admin password.
        // or, for online demos, it's allowed to activate debugging for everyone.
        if (( ! is_admin_password($admin_password)) && ( ! $config['is_online_demo']))
            throw new Exception('Das Passwort ist nicht korrekt!');

        // create new debug log file if it does not exist already
        if ( ! is_readable(DEBUG_LOG_FILENAME))
            create_debug_log_file();

        $config['debug']['enable'] = true;
        try
        {
            save_config();
        }
        catch (Exception $e)
        {
            $config['debug']['enable'] = false;
            throw $e;
        }

        return;
    }