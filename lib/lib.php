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

    $Id$

    Changelog (sorted by date):
        [DATE]      [NICKNAME]      [CHANGES]
        2012-08-28  weinbauer73     - check if function strfmon() exists
        2012-09-03  kami89          - removed all functions which are already implemented in classes
        2012-09-30  weinbauer73     - replaced settype($svn, 'integer') with settype($revision, 'integer') in function get_svn_revision()
        2012-10-21  kami89          - added doxygen comments
                                    - added Exceptions
                                    - renamed "download_file()" to "send_file()" ("download_file()" is ambiguous)
        2012-10-26  kami89          - Function save_config(): Now, only configuration parameters which are different
                                      from default configuration parameters will be stored in "config.php".
        2013-01-22  kami89          - added array_to_template_loop()
        2013-02-14  kami89          - implemented function "get_proposed_filenames()"
        2013-05-04  kami89          - added function "upload_file()"
        2013-05-07  kami89          - added function "check_requirements()"
        2013-05-18  kami89          - added functions "set_admin_password()" and "is_admin_password()"
*/

    /**
     * @file lib.php
     * @brief Miscellaneous, global Functions
     * @author kami89
     */


    /**
     * @brief check if a given number is odd
     *
     * @param integer $number       A number
     *
     * @retval boolean      @li true if the number is odd
     *                      @li false if the number is even
     */
    function is_odd($number)
    {
        return ($number & 1) ? true : false; // false = even, true = odd
    }

    /**
     * @brief Get the SVN Revision number of the installed system
     *
     * @retval integer      The SVN Revision number
     * @retval NULL         If this is no SVN installation
     *
     * @throws Exception if the revision number could not be read
     */
    function get_svn_revision()
    {
        // New SVN format
        if (file_exists(BASE.'/.svn/wc.db'))
        {
            try
            {
                $pdo = new PDO('sqlite:'.BASE.'/.svn/wc.db');
                if ( ! ($result = $pdo->query('SELECT MAX(revision) AS rev FROM nodes')))
                    throw new Exception('$result ist NULL');
                if (( ! is_array($data = $result->fetch())) || ( ! isset($data['rev'])))
                    throw new Exception('$data ist kein gültiges Array');
                return intval($data['rev']);
            }
            catch (Exception $e)
            {
                throw new Exception('SVN-Revision konnte nicht aus "/.svn/wc.db" gelesen werden! '.$e->getMessage());
            }
        }

        // Old SVN format
        if (file_exists(BASE.'/.svn/entries'))
        {
            $svn = File(BASE.'/.svn/entries');
            if (is_array($svn) && isset($svn[3]))
                return intval($svn[3]);
            else
                throw new Exception('Die SVN Revision konnte nicht aus "'.$filename.'" gelesen werden.');
        }

        return NULL; // this is not a SVN installation
    }

    /**
     * @brief List all files (or all files with a specific string in the filename) in a directory
     *
     * @note This function is not case sensitive.
     *
     * @param string    $directory          Path to the directory (with slash at the end!)
     * @param boolean   $recursive          If true, the file search is recursive
     * @param string    $search_string      If this is a non-empty string, only files with
     *                                      that substring in the filename will be returned.
     *
     * @retval array    all found filenames (incl. paths, sorted alphabetically)
     *
     * @throws Exception if there was an error
     */
    function find_all_files($directory, $recursive = false, $search_string = '')
    {
        $files = array();

        if ( ! is_dir($directory))
            throw new Exception('"'.$directory.'" ist kein Verzeichnis!');

        $dirfiles = scandir($directory);
        foreach ($dirfiles as $file)
        {
            if (($file != ".") && ($file != "..") && ($file != ".svn"))
            {
                if (is_dir($directory.$file))
                {
                    if ($recursive)
                        $files = array_merge($files, find_all_files($directory.$file.'/', true, $search_string));
                }
                elseif (($search_string == '') || (substr_count(strtolower($file), strtolower($search_string)) > 0))
                {
                    $files[] = $directory.$file;
                }
            }
        }

        return $files;
    }

    /**
     * @brief Find all subdirectories of a directory (not recursive)
     *
     * @param string    $directory          Path to the directory (with slash at the end!)
     * @param boolean   $recursive          if true, all subdirectories will be listed too
     *
     * @retval array    all found directories (without slashes at the end!)
     *
     * @throws Exception if there was an error
     */
    function find_all_directories($directory, $recursive = false)
    {
        $directories = array();

        if ( ! is_dir($directory))
            throw new Exception('"'.$directory.'" ist kein Verzeichnis!');

        $dirfiles = scandir($directory);
        foreach ($dirfiles as $file)
        {
            if (($file != ".") && ($file != "..") && ($file != ".svn") && (is_dir($directory.$file)))
            {
                $directories[] = $directory.$file;
                if ($recursive)
                    $directories = array_merge($directories, find_all_directories($directory.$file.'/', true));
            }
        }

        return $directories;
    }

    /**
     * @brief Send a file to the client (download file)
     *
     * @warning     This function must be called before there was any HTML output!
     *
     * @param string $filename      The full path to the filename
     * @param string $mimetype      @li The mime type of the file
     *                              @li if NULL, we will try to read the mimetype from the file
     */
    function send_file($filename, $mimetype = NULL)
    {
        $mtime = ($mtime = filemtime($filename)) ? $mtime : gmtime();

        if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") != false)
            header("Content-Disposition: attachment; filename=".urlencode(basename($filename))."; modification-date=".date('r', $mtime).";");
        else
            header("Content-Disposition: attachment; filename=\"".basename($filename)."\"; modification-date=\"".date('r', $mtime)."\";");

        if ($mimetype == NULL)
            $mimetype = get_mimetype($filename); // lib.functions.php

        header("Content-Type: ".$mimetype);
        header("Content-Length:". filesize($filename));

        if (in_array('mod_xsendfile', apache_get_modules()))
            header('X-Sendfile: '.$filename);
        else
            readfile($filename);

        exit;
    }

    /**
     * @brief The same as "send_file()", but with a string instead of a file on the disk (e.g. for XML and CSV)
     *
     * @warning     This function must be called before there was any HTML output!
     *
     * @param string $content       The content of the file which the user wants to download
     * @param string $filename      The name of the file which is displayed in the user's browser
     * @param string $mimetype      The mime type of the file
     */
    function send_string($content, $filename, $mimetype)
    {
        $mtime = gmmktime();

        if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") != false)
            header("Content-Disposition: attachment; filename=".urlencode($filename)."; modification-date=".date('r', $mtime).";");
        else
            header("Content-Disposition: attachment; filename=\"".$filename."\"; modification-date=\"".date('r', $mtime)."\";");

        header("Content-Type: ".$mimetype);
        header("Content-Length:". strlen($content));

        echo $content;
        exit;
    }

    /**
     * @brief Upload a file (from "<input type="file">) to a directory on the server
     *
     * @param array         $file_array                 The file array, for example $_FILES['my_file']
     * @param string        $destination_directory      The directory where the file should be saved.
     *                                                  IMPORTANT: there must be a slash at the end!
     *                                                  Example: BASE.'/media/'
     * @param string|NULL   $destination_filename       The destination filename (without path).
     *                                                  NULL means same filename like the uploaded file.
     *
     * @retval string   the (absolute) filename of the uploaded file (the destination, not the source)
     *
     * @throws Exception if the destination file exists already
     * @throws Exception if there was an error
     */
    function upload_file($file_array, $destination_directory, $destination_filename = NULL)
    {
        if ( ! isset($file_array['name']))
            throw new Exception('Ungültiges Array übergeben!');

        if ($destination_filename == NULL)
            $destination_filename = $file_array['name'];

        $destination = $destination_directory.$destination_filename;

        if (file_exists($destination))
            throw new Exception('Die Datei "'.$destination.'" existiert bereits!');

        if ( ! move_uploaded_file($file_array['tmp_name'], $destination))
            throw new Exception('Es gab ein Fehler beim Hochladen der Datei!');

        return $destination;
    }

    /**
     * @brief Set a new administrator password
     *
     * @note    The password will be trimmed, salted, crypted with sha256 and stored in $config.
     *          Optionally, $config can be written in config.php.
     *
     * @param string    $old_password       The current administrator password (plain, not crypted)
     * @param string    $new_password_1     The new administrator password (plain, not crypted) (first time)
     * @param string    $new_password_2     The new administrator password (plain, not crypted) (second time)
     * @param boolean   $save_config        If true, the config.php file will be overwritten.
     *                                      If false, the new password will be stored in $config,
     *                                      but you must manually save the $config with save_config()!
     *
     * @throws Exception    if the old password is not correct
     * @throws Exception    if the new password is not allowed (maybe empty)
     * @throws Exception    if the new passworts are different
     * @throws Exception    if $config could not be saved in config.php
     */
    function set_admin_password($old_password, $new_password_1, $new_password_2, $save_config = true)
    {
        global $config;
        $salt = 'h>]gW3$*j&o;O"s;@&G)';

        settype($old_password, 'string');
        settype($new_password_1, 'string');
        settype($new_password_2, 'string');
        $old_password = trim($old_password);
        $new_password_1 = trim($new_password_1);
        $new_password_2 = trim($new_password_2);

        if ( ! is_admin_password($old_password))
            throw new Exception('Das eingegebene Administratorpasswort ist nicht korrekt!');

        if (strlen($new_password_1) < 4)
            throw new Exception('Das neue Passwort muss mindestens 4 Zeichen lange sein!');

        if ($new_password_1 !== $new_password_2)
            throw new Exception('Die neuen Passwörter stimmen nicht überein!');

        // all ok, save the new password
        $config['admin']['password'] = hash('sha256', $salt.$new_password_1);

        if ($save_config)
            save_config();
    }

    /**
     * @brief Check if a string is the correct admin password
     *
     * @param string $passwort      The password (plain, not crypted) we want to check
     *                              (compare with the administrators password)
     *
     * @retval boolean      true if the password is correct
     *                      false if the password is not correct
     */
    function is_admin_password($password)
    {
        global $config;
        $salt = 'h>]gW3$*j&o;O"s;@&G)';

        settype($password, 'string');
        $password = trim($password);

        // If the admin password is not set yet, we will always return true.
        // This is needed for the first use of Part-DB.
        // In this case, the installer will be shown to set an admin password.
        if (( ! $config['installation_complete']['admin_password']) && ( ! $config['admin']['password']))
            return true;

        return (hash('sha256', $salt.$password) === $config['admin']['password']);
    }

    /**
     * @brief Save the global array "$config" to the file "config.php"
     *
     * @throws Exception if there was an error (maybe not enought permissions)
     */
    function save_config()
    {
        if ((file_exists(BASE.'/data/config.php')) && (! is_writeable(BASE.'/data/config.php')))
            throw new Exception('Es sind nicht genügend Rechte vorhanden um die Datei "config.php" zu beschreiben!');

        global $config;
        global $config_defaults;
        global $manual_config;

        // set config version to the latest one
        $config['system']['current_config_version'] = $config['system']['latest_config_version'];

        $content = "<?php\n\n";
        $content .= array_to_php_lines($config_defaults, $config, '    $config', false);
        $content .= "\n    //How to declare manual configs:\n";
        $content .= '    //$manual_config[\'money_format\'][\'de_DE\']                = \'%!n Euro\';'."\n";
        $content .= array_to_php_lines($manual_config, $manual_config, '    $manual_config', false);
        $content .= "\n?>";

        if ( ! ($fp = fopen(BASE.'/data/config.php', 'wb')))
            throw new Exception('Die Datei "config.php" konnte nicht beschrieben werden. Überprüfen Sie, ob genügend Rechte vorhanden sind.');

        if ( ! fwrite($fp, $content))
            throw new Exception('Die Datei "config.php" konnte nicht beschrieben werden. Überprüfen Sie, ob genügend Rechte vorhanden sind.');

        if ( ! fclose($fp))
            throw new Exception('Es gab ein Fehler beim Abschliessen der Schreibvorgangs bei der Datei "config.php".');
    }

    /**
     * @brief For save_config()
     */
    function array_to_php_lines(&$array_defaults, &$array, $path, $ignore_defaults)
    {
        $lines = '';
        foreach ($array_defaults as $key => $value)
        {
            if (isset($array[$key]))
            {
                $full_path = $path.'['.var_export($key, true).']';
                if (is_array($value))
                {
                    $lines .= array_to_php_lines($array_defaults[$key], $array[$key], $full_path, $ignore_defaults);
                }
                else
                {
                    if (($array[$key] !== $array_defaults[$key]) || ( ! $ignore_defaults))
                    {
                        $space_count = max(60-strlen($full_path), 0);
                        $spaces = str_repeat(' ', $space_count);
                        $lines .= $full_path.$spaces.' = '.var_export($array[$key], true).";\n";
                    }
                }
            }
        }
        return $lines;
    }

    /**
     * @brief Convert a float number to a formatted money string (with currency)
     *
     * @param float|NULL    $number     @li The price as a float number
     *                                  @li NULL if you mean "there is no price",
     *                                      then this function will return the string "-"
     * @param string        $language   @li language (locale) string, like "de_DE" or "de_DE.utf-8".
     *                                  @li an empty string means that we use the default language from $config
     *
     * @retval string       The formatted money string
     */
    function float_to_money_string($number, $language = '')
    {
        if ($number === NULL)
            return '-';

        settype($number, 'float');

        global $config;

        if (strlen($language) == 0)
            $language = $config['language'];

        // catch only the characters before the point (if there is one),
        // like "de_DE" if the language is "de_DE.uft-8"
        if (strpos($language, '.') > 0)
            $main_language = substr($language, 0, strpos($language, '.'));
        else
            $main_language = $language;

        // get the money format from config(_defaults).php
        $format = $config['money_format'][$main_language];

        if ($language != $config['language'])
        {
            // change locale, because the $language is not the default language!
            if ( ! setlocale(LC_MONETARY, $language))
                debug('error', 'Sprache "'.$language.'" kann nicht gesetzt werden!', __FILE__, __LINE__, __METHOD__);
        }

        $result = trim(money_format($format, $number));

        if ($language != $config['language'])
            setlocale(LC_MONETARY, $config['language']); // change locale back to default

        return $result;
    }

    /**
     * @brief Download a file from the internet (with "curl")
     *
     * @param string $url   The internet URL to the file
     *
     * @retval string       The downloaded file
     *
     * @throws Exception if there was an error (maybe "curl" is not installed on the server)
     */
    function curl_get_data($url)
    {
        if ( ! function_exists('curl_init'))
            throw new Exception('"curl" scheint auf ihrem System nicht installiert zu sein! '.
                                "\nBitte installieren Sie das entsprechende Modul, ".
                                'oder es werden gewisse Funktionen nicht zur Verfügung stehen.');

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        curl_close($ch);

        if ($data === false)
            throw new Exception('Der Download mit "curl" lieferte kein Ergebnis!');

        return $data;
    }

    /**
     * @brief Get proposed filenames for an invalid filename
     *
     * If the user moves a file (e.g. in the media/ directory), the files will be found no longer.
     * To re-assign "File"-objects (see "class.File.php") with the missing file,
     * this function is needed. You can pass the old filename, and you will get
     * proposed filenames. Maybe the original file can be found again this way.
     *
     * @param string $missing_filename      The filename of the missing file
     * @param string $search_path           The path where we will search for similar files (with slash at the end!)
     *
     * @retval array        @li All proposed filenames as an array of strings
     *                      @li Best matches are at the beginning of the array,
     *                          worst matches are at the end of the array
     */
    function get_proposed_filenames($missing_filename, $search_path)
    {
        $original_path = pathinfo($missing_filename, PATHINFO_DIRNAME);
        $basename = basename($missing_filename);

        if (is_dir($original_path))
            $filenames = find_all_files($original_path, false, $basename);
        else
            $filenames = array();
        $filenames_2 = array_diff(find_all_files($search_path, true, $basename), $filenames);
        $filenames = array_merge($filenames, $filenames_2);

        return $filenames;
    }

    /**
     * @brief Build a simple template loop array with an array of values and a selected value
     *
     * @note    Have a look at system_config.php, there you can see how this function works.
     *
     * @param array $array              A simple array with keys and values
     * @param mixed $selected_value     The value of the selected item
     *
     * @retval array        The template loop array
     */
    function array_to_template_loop($array, $selected_value = NULL)
    {
        $loop = array();
        foreach ($array as $key => $value)
            $loop[] = array('value' => $key, 'text' => $value, 'selected' => ($key == $selected_value));
        return $loop;
    }

    /**
     * @brief Check if the server complies all minimum requirements of Part-DB
     *
     * @warning    All requirements must be defined in the array "$config['requirements']" in "config_defaults.php"!
     *
     * @retval array    For every requirement which is not complied there's an message
     *                  (This array is empty if the server complies all requirements)
     *
     * @throws Exception if there was an error
     */
    function check_requirements()
    {
        global $config;
        $messages = array();

        foreach ($config['requirements'] as $key => $value)
        {
            switch ($key)
            {
                case 'php_version':
                    if (version_compare(PHP_VERSION, $value) < 0)
                    {
                        $messages[] =   'Für Part-DB wird mindestens PHP '.$value.' vorausgesetzt! '.
                                        'Die derzeit installierte Version ist PHP '.PHP_VERSION.'.';
                    }
                    break;

                case 'pdo':
                    if ( ! class_exists('PDO', false))
                    {
                        $messages[] =   'PDO (PHP Data Objects) wird benötigt, ist aber nicht installiert!';
                    }
                    break;

                default:
                    throw new Exception('Unbekannte Mindestanforderung: '.$key);
            }
        }

        return $messages;
    }

?>
