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
 * @file lib.php
 * @brief Miscellaneous, global Functions
 * @author kami89
 */

use PartDB\Interfaces\IAPIModel;
use PartDB\Part;

/**
 * check if a given number is odd
 *
 * @param integer $number       A number
 *
 * @return boolean
 * * true if the number is odd
 * * false if the number is even
 */
function isOdd($number)
{
    return ($number & 1) ? true : false; // false = even, true = odd
}

/**
 * Get the Git branch name of the installed system
 *
 * @return  string|null       The current git branch name. Null, if this is no Git installation
 *
 * @throws Exception if there was an error
 */
function getGitBranchName()
{
    if (file_exists(BASE.'/.git/HEAD')) {
        $git = File(BASE.'/.git/HEAD');
        $head = explode("/", $git[0], 3);
        $branch = trim($head[2]);
        return $branch;
    }

    return null; // this is not a Git installation
}

/**
 * Get hash of the last git commit (on remote "origin"!)
 *
 * @note    If this method does not work, try to make a "git pull" first!
 *
 * @param integer $length       if this is smaller than 40, only the first $length characters will be returned
 *
 * @return string|null       The hash of the last commit, null If this is no Git installation
 *
 * @throws Exception if there was an error
 */
function getGitCommitHash($length = 40)
{
    $filename = BASE.'/.git/refs/remotes/origin/'.getGitBranchName();

    if (file_exists($filename)) {
        $head = File($filename);
        $hash = $head[0];
        return substr($hash, 0, $length);
    }

    return null; // this is not a Git installation
}


function treeviewNode($name, $href = null, $nodes = null, $icon = null)
{
    $ret = array('text' => $name);

    if (isset($href)) {
        $ret['href'] = $href;
    } else {
        $ret['selectable'] = false;
    }

    if (isset($nodes)) {
        $ret['nodes'] = $nodes;
    }

    if (isset($icon)) {
        $ret['icon'] = $icon;
    }

    return $ret;
}

/**
 * List all files (or all files with a specific string in the filename) in a directory
 *
 * @note This function is not case sensitive.
 *
 * @param string    $directory          Path to the directory (IMPORTANT: absolute UNIX path, with slash at the end! see to_unix_path())
 * @param boolean   $recursive          If true, the file search is recursive
 * @param string    $search_string      If this is a non-empty string, only files with
 *                                      that substring in the filename will be returned.
 *
 * @return string[]    all found filenames (incl. absolute UNIX paths, sorted alphabetically)
 *
 * @throws Exception if there was an error
 */
function findAllFiles($directory, $recursive = false, $search_string = '')
{
    $files = array();

    if ((! is_dir($directory)) || (mb_substr($directory, -1, 1) != '/') || (! isPathabsoluteAndUnix($directory, false))) {
        throw new Exception('"'.$directory.'" ist kein gültiges Verzeichnis!');
    }

    $dirfiles = scandir($directory);
    foreach ($dirfiles as $file) {
        if (($file != ".") && ($file != "..") && ($file != ".svn") && ($file != ".git") && ($file != ".gitignore") && ($file != ".htaccess")) {
            if (is_dir($directory.$file)) {
                if ($recursive) {
                    $files = array_merge($files, findAllFiles($directory.$file.'/', true, $search_string));
                }
            } elseif (($search_string == '') || (mb_substr_count(mb_strtolower($file), mb_strtolower($search_string)) > 0)) {
                $files[] = $directory.$file;
            }
        }
    }

    return $files;
}

/**
 * Find all subdirectories of a directory (not recursive)
 *
 * @param string    $directory          Path to the directory (IMPORTANT: absolute UNIX path, with slash at the end! see to_unix_path())
 * @param boolean   $recursive          if true, all subdirectories will be listed too
 *
 * @return string[] all found directories (without slashes at the end, incl. absolute UNIX paths, sorted alphabetically)
 *
 * @throws Exception if there was an error
 */
function findAllDirectories($directory, $recursive = false)
{
    $directories = array();

    if ((! is_dir($directory)) || (mb_substr($directory, -1, 1) != '/') || (! isPathabsoluteAndUnix($directory, false))) {
        throw new Exception('"'.$directory.'" ist kein gültiges Verzeichnis!');
    }

    $dirfiles = scandir($directory);
    foreach ($dirfiles as $file) {
        if (($file != ".") && ($file != "..") && ($file != ".svn") && ($file != ".git") && (is_dir($directory.$file))) {
            $directories[] = $directory.$file;
            if ($recursive) {
                $directories = array_merge($directories, findAllDirectories($directory.$file.'/', true));
            }
        }
    }

    return $directories;
}

/**
 * Send a file to the client (download file)
 *
 * @warning     This function must be called before there was any HTML output!
 *
 * @param string $filename      The full path to the filename
 * @param string $mimetype      * The mime type of the file
 * * if NULL, we will try to read the mimetype from the file
 */
function sendFile($filename, $mimetype = null)
{
    $mtime = ($mtime = filemtime($filename)) ? $mtime : time();

    if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") != false) {
        header("Content-Disposition: attachment; filename=".urlencode(basename($filename))."; modification-date=".date('r', $mtime).";");
    } else {
        header("Content-Disposition: attachment; filename=\"".basename($filename)."\"; modification-date=\"".date('r', $mtime)."\";");
    }

    if ($mimetype == null) {
        $mimetype = getMimetype($filename);
    } // lib.functions.php

    header("Content-Type: ".$mimetype);
    header("Content-Length:". filesize($filename));

    if (in_array('mod_xsendfile', apache_get_modules())) {
        header('X-Sendfile: '.$filename);
    } else {
        readfile($filename);
    }

    exit;
}

/**
 * The same as "send_file()", but with a string instead of a file on the disk (e.g. for XML and CSV)
 *
 * @warning     This function must be called before there was any HTML output!
 *
 * @param string $content       The content of the file which the user wants to download
 * @param string $filename      The name of the file which is displayed in the user's browser
 * @param string $mimetype      The mime type of the file
 */
function sendString($content, $filename, $mimetype)
{
    $mtime = time();

    if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") != false) {
        header("Content-Disposition: attachment; filename=".urlencode($filename)."; modification-date=".date('r', $mtime).";");
    } else {
        header("Content-Disposition: attachment; filename=\"".$filename."\"; modification-date=\"".date('r', $mtime)."\";");
    }

    header("Content-Type: ".$mimetype);
    header("Content-Length:". strlen($content));

    echo $content;
    exit;
}

/**
 * Upload a file (from "<input type="file">) to a directory on the server
 *
 * @param array         $file_array                 The file array, for example $_FILES['my_file']
 * @param string        $destination_directory      The directory where the file should be saved.
 *                                                  IMPORTANT: there must be a slash at the end!
 *                                                  Example: BASE.'/data/media/'
 * @param string|NULL   $destination_filename       The destination filename (without path).
 *                                                  NULL means same filename like the uploaded file.
 *
 * @return string   the (absolute) filename of the uploaded file (the destination, not the source)
 *
 * @throws Exception if the destination file exists already
 * @throws Exception if there was an error
 */
function uploadFile($file_array, $destination_directory, $destination_filename = null)
{
    if ((! isset($file_array['name'])) || (! isset($file_array['tmp_name'])) || (! isset($file_array['error']))) {
        throw new Exception(_('Ungültiges Array übergeben!'));
    }

    if ($destination_filename == null) {
        $destination_filename = $file_array['name'];
    }

    $destination = $destination_directory.$destination_filename;

    if ((! is_dir($destination_directory)) || (mb_substr($destination_directory, -1, 1) != '/') || (! isPathabsoluteAndUnix($destination_directory, false))) {
        throw new Exception('"'.$destination_directory.'" ist kein gültiges Verzeichnis!');
    }

    if (! is_writable($destination_directory)) {
        throw new Exception(_('Sie haben keine Schreibrechte im Verzeichnis "').$destination_directory.'"!');
    }

    if (file_exists($destination)) {
        // there is already a file with the same filename, check if it is exactly the same file
        $new_file_md5 = md5_file($file_array['tmp_name']);
        $existing_file_md5 = md5_file($destination);

        if (($new_file_md5 == $existing_file_md5) && ($new_file_md5 != false)) {
            return $destination;
        } // it's exactly the same file, we don't need to upload it again, re-use it!

        throw new Exception(_('Es existiert bereits eine Datei mit dem Dateinamen "').$destination.'"!');
    }

    switch ($file_array['error']) {
        case UPLOAD_ERR_OK:
            // all OK, upload was successfully
            break;
        case UPLOAD_ERR_INI_SIZE:
            throw new Exception('Die maximal mögliche Dateigrösse für Uploads wurde überschritten ("upload_max_filesize" in "php.ini")! '.
                '<a target="_blank" href="'.BASE_RELATIVE.'/documentation/dokuwiki/doku.php?id=anforderungen">Hilfe</a>');
        case UPLOAD_ERR_FORM_SIZE:
            throw new Exception('Die maximal mögliche Dateigrösse für Uploads wurde überschritten!');
        case UPLOAD_ERR_PARTIAL:
            throw new Exception('Die Datei wurde nur teilweise hochgeladen!');
        case UPLOAD_ERR_NO_FILE:
            throw new Exception('Es wurde keine Datei hochgeladen!');
        case UPLOAD_ERR_NO_TMP_DIR:
            throw new Exception('Es gibt keinen temporären Ordner für hochgeladene Dateien!');
        case UPLOAD_ERR_CANT_WRITE:
            throw new Exception('Das Speichern der Datei auf die Festplatte ist fehlgeschlagen!');
        case UPLOAD_ERR_EXTENSION:
            throw new Exception('Eine PHP Erweiterung hat den Upload der Datei gestoppt!');
        default:
            throw new Exception('Beim Hochladen der Datei trat ein unbekannter Fehler auf!');
    }

    if (! move_uploaded_file($file_array['tmp_name'], $destination)) {
        throw new Exception('Beim Hochladen der Datei trat ein unbekannter Fehler auf!');
    }

    return $destination;
}

/**
 * Set a new administrator password
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
function setAdminPassword($old_password, $new_password_1, $new_password_2, $save_config = true)
{
    global $config;
    $salt = 'h>]gW3$*j&o;O"s;@&G)';

    settype($old_password, 'string');
    settype($new_password_1, 'string');
    settype($new_password_2, 'string');
    $old_password = trim($old_password);
    $new_password_1 = trim($new_password_1);
    $new_password_2 = trim($new_password_2);

    if (! isAdminPassword($old_password)) {
        throw new Exception('Das eingegebene Administratorpasswort ist nicht korrekt!');
    }

    if (mb_strlen($new_password_1) < 4) {
        throw new Exception('Das neue Passwort muss mindestens 4 Zeichen lang sein!');
    }

    if ($new_password_1 !== $new_password_2) {
        throw new Exception('Die neuen Passwörter stimmen nicht überein!');
    }

    // all ok, save the new password
    $config['admin']['password'] = hash('sha256', $salt.$new_password_1);

    if ($save_config) {
        saveConfig();
    }
}

/**
 * Check if a string is the correct admin password
 *
 * @param $passwort string      The password (plain, not crypted) we want to check
 *                              (compare with the administrators password)
 *
 * @return boolean      * true if the password is correct
 * * false if the password is not correct
 */
function isAdminPassword($password)
{
    global $config;
    $salt = 'h>]gW3$*j&o;O"s;@&G)';

    settype($password, 'string');
    $password = trim($password);

    // If the admin password is not set yet, we will always return true.
    // This is needed for the first use of Part-DB.
    // In this case, the installer will be shown to set an admin password.
    if ((! $config['installation_complete']['admin_password']) && (! $config['admin']['password'])) {
        return true;
    }

    return (hash('sha256', $salt.$password) === $config['admin']['password']);
}

/**
 * Save the global array "$config" to the file "config.php"
 *
 * @throws Exception if there was an error (maybe not enought permissions)
 */
function saveConfig()
{
    if ((file_exists(BASE.'/data/config.php')) && (! is_writeable(BASE.'/data/config.php'))) {
        throw new Exception('Es sind nicht genügend Rechte vorhanden um die Datei "config.php" zu beschreiben!');
    }

    global $config;
    global $config_defaults;
    global $manual_config;

    // set config version to the latest one
    $config['system']['current_config_version'] = $config['system']['latest_config_version'];

    $content = "<?php\n\n";
    $content .= arrayToPhpLines($config_defaults, $config, '    $config', false);
    $content .= "\n    //How to declare manual configs:\n";
    $content .= '    //$manual_config[\'money_format\'][\'POSIX\']                = \'%!n €\';'."\n";
    $content .= '    //$manual_config[\'DOCUMENT_ROOT\']                        = \'/var/www\';'."\n";
    $content .= arrayToPhpLines($manual_config, $manual_config, '    $manual_config', false);
    $content .= "\n";

    if (! ($fp = fopen(BASE.'/data/config.php', 'wb'))) {
        throw new Exception('Die Datei "config.php" konnte nicht beschrieben werden. Überprüfen Sie, ob genügend Rechte vorhanden sind.');
    }

    if (! fwrite($fp, $content)) {
        throw new Exception('Die Datei "config.php" konnte nicht beschrieben werden. Überprüfen Sie, ob genügend Rechte vorhanden sind.');
    }

    if (! fclose($fp)) {
        throw new Exception('Es gab ein Fehler beim Abschliessen der Schreibvorgangs bei der Datei "config.php".');
    }
}

/**
 * @brief For save_config()
 */
function arrayToPhpLines(&$array_defaults, &$array, $path, $ignore_defaults)
{
    $lines = '';
    foreach ($array_defaults as $key => $value) {
        if (isset($array[$key])) {
            $full_path = $path.'['.var_export($key, true).']';
            if (is_array($value)) {
                $lines .= arrayToPhpLines($array_defaults[$key], $array[$key], $full_path, $ignore_defaults);
            } else {
                if (($array[$key] !== $array_defaults[$key]) || (! $ignore_defaults)) {
                    $space_count = max(60-mb_strlen($full_path), 0);
                    $spaces = str_repeat(' ', $space_count);
                    $lines .= $full_path.$spaces.' = '.var_export($array[$key], true).";\n";
                }
            }
        }
    }
    return $lines;
}

/**
 * Convert a float number to a formatted money string (with currency)
 *
 * @param float|NULL    $number     @li The price as a float number
 *                                  @li NULL if you mean "there is no price",
 *                                      then this function will return the string "-"
 * @param string        $language   @li language (locale) string, like "de_DE" or "de_DE.utf-8".
 *                                  @li an empty string means that we use the default language from $config
 *
 * @return string       The formatted money string
 */
function floatToMoneyString($number, $language = '')
{
    if ($number === null) {
        return '-';
    }

    // settype($number, 'float');

    global $config;

    if (strlen($language) == 0) {
        $language = $config['language'];
    }

    if ($language != $config['language']) {
        // change locale, because the $language is not the default language!
        if (! ownSetlocale(LC_MONETARY, $language)) {
            debug('error', 'Sprache "'.$language.'" kann nicht gesetzt werden!', __FILE__, __LINE__, __METHOD__);
        }
    }

    // get the money format from config(_defaults).php
    if (isset($config['money_format'][$language])) {
        $format = $config['money_format'][$language];
    } else {
        // not set in config, so generate it
        $locale = localeconv();
        // number of digits used in current language
        $local_digits = $locale['int_frac_digits'];
        // digits of the number
        $number_digits = ((int) $number != $number) ? (strlen($number) - strpos($number, $locale['decimal_point'])) - 1 : 0;

        // international or local format?
        $format_type = ($language == $config['language']) ? 'n' : 'i';

        if ($number_digits > $local_digits) {
            $n = $number_digits > 5 ? 5 : $number_digits;
            $format = "%." . $n . $format_type;
        } else {
            $format = '%' . $format_type;
        }
    }

    $result = trim(money_format($format, $number));

    if ($language != $config['language']) {
        ownSetlocale(LC_MONETARY, $config['language']);
    } // change locale back to default

    return $result;
}

/**
 * Download a file from the internet (with "curl")
 *
 * @param string $url   The internet URL to the file
 *
 * @return string       The downloaded file
 *
 * @throws Exception if there was an error (maybe "curl" is not installed on the server)
 */
function curlGetData($url)
{
    if (! extension_loaded('curl')) {
        throw new Exception('"curl" scheint auf ihrem System nicht installiert zu sein! '.
            "\nBitte installieren Sie das entsprechende Modul, ".
            'oder es werden gewisse Funktionen nicht zur Verfügung stehen.');
    }

    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);

    if ($data === false) {
        throw new Exception(_('Der Download mit "curl" lieferte kein Ergebnis!'));
    }

    return $data;
}

/**
 * Get proposed filenames for an invalid filename
 *
 * If the user moves a file (e.g. in the media/ directory), the files will be found no longer.
 * To re-assign "Attachement"-objects (see "Attachement.php") with the missing file,
 * this function is needed. You can pass the old filename, and you will get
 * proposed filenames. Maybe the original file can be found again this way.
 *
 * @param string $missing_filename      The filename of the missing file (absolute UNIX path from filesystem root [only slashes]!!)
 * @param array  $available_files       An array of absolute UNIX filenames with all available files.
 *                                      This function will search for proposed filenames in this array.
 *
 * @return string[]      * All proposed filenames as an array of strings (absolute UNIX filenames)
 * * Best matches are at the beginning of the array,
 *                          worst matches are at the end of the array
 */
function getProposedFilenames($missing_filename, $available_files)
{
    $filenames = array();
    $filenames_tmp = array();

    foreach ($available_files as $filename) {
        if (mb_substr_count(mb_strtolower($filename), mb_strtolower(basename($missing_filename))) > 0) {
            $filenames_tmp[] = $filename;
        }
    }

    // remove duplicates, sort $filenames
    $filenames_tmp = array_unique($filenames_tmp);
    sort($filenames_tmp);

    // move best matches to top
    foreach ($filenames_tmp as $key => $filename) {
        if (basename($filename) == basename($missing_filename)) {
            $filenames[] = $filename;
            unset($filenames_tmp[$key]);
        }
    }
    foreach ($filenames_tmp as $key => $filename) {
        if (pathinfo($filename, PATHINFO_FILENAME) == pathinfo($missing_filename, PATHINFO_FILENAME)) {
            $filenames[] = $filename;
            unset($filenames_tmp[$key]);
        }
    }
    foreach ($filenames_tmp as $key => $filename) {
        $filenames[] = $filename;
    }

    return $filenames;
}

/**
 * Build a simple template loop array with an array of values and a selected value
 *
 * @note    Have a look at system_config.php, there you can see how this function works.
 *
 * @param array $array              A simple array with keys and values
 * @param mixed $selected_value     The value of the selected item
 *
 * @return array        The template loop array
 */
function arrayToTemplateLoop($array, $selected_value = null)
{
    $loop = array();
    foreach ($array as $key => $value) {
        $loop[] = array('value' => $key, 'text' => $value, 'selected' => ($key == $selected_value));
    }
    return $loop;
}

/**
 * Convert a Windows file path (with backslashes) to an UNIX path (with slashes)
 *
 * @note    If you pass a UNIX path, this function will return that path without any changes.
 *
 * @param string $path      a Windows or UNIX path
 *
 * @return string           the UNIX path
 */
function toUnixPath($path)
{
    return str_replace('\\', '/', trim($path)); // replace all "\" with "/"
}

/**
 * Check if a path is absolute UNIX path (begins with filesystem root and has no backslashes)
 *
 * @param string $path                  a UNIX path
 * @param boolean $accept_protocols     if true, protocols like http:// or ftp:// are interpreted as valid, absolute UNIX paths
 *
 * @return boolean          * true if the path is (maybe) absolute (we cannot say it with 100% probability) and UNIX style
 * * false if the path is definitive not absolute or definitive not an UNIX path
 * * if $path is an empty string, this function will return "false"
 */
function isPathabsoluteAndUnix($path, $accept_protocols = true)
{
    if (mb_strpos($path, '\\') !== false) { // $path contains backslashes -> it's not a UNIX path
        return false;
    }

    //Dont check if DOCUMENT_ROOT or BASE_RELATIVE are empty, so we dont get a warning about missing delimiter
    if (defined(DOCUMENT_ROOT) && mb_strpos($path, DOCUMENT_ROOT) === 0) { // $path begins with DOCUMENT_ROOT
        return true;
    }

    if (defined(BASE_RELATIVE) && mb_strpos($path, BASE_RELATIVE) === 0) { // $path begins with BASE_RELATIVE
        return false;
    }

    if ((mb_strpos($path, '://') !== false) && ($accept_protocols)) { // there is a protocol in $path, like http://, ftp://, ...
        return true;
    }

    if (DIRECTORY_SEPARATOR == '/') {
        // for UNIX/Linux

        if (mb_strpos($path, '/') !== 0) { // $path does not begin with a slash
            return false;
        } else {
            return true;
        } // we are not sure; maybe $path is absolute, maybe not...
    } else {
        // for Windows

        if (mb_strpos($path, ':/') === 1) { // there is something like C:/ at the begin of $path
            return true;
        } else {
            return false;
        }
    }
}


/**
 * Replaces Placeholder strings like %id% or %name% with their corresponding Part properties.
 * Note: If the given Part does not have a property, it will be replaced with "".
 *
 * %id%         : Part id
 * %name%       : Name of the part
 * %desc%       : Description of the part
 * %comment%    : Comment to the part
 * %mininstock% : The minium in stock value
 * %instock%    : The current in stock value
 * %avgprice%   : The average price of this part
 * %cat%        : The name of the category the parts belongs to
 * %cat_full%   : The full path of the parts category
 *
 * @param string $string The string on which contains the placeholders
 * @param Part $part
 * @return string the
 */
function replacePlaceholderWithInfos($string, $part)
{
    //General infos
    $string = str_replace("%id%", $part->getID(), $string);                        //part id
    $string = str_replace("%name%", $part->getName(), $string);                    //Name of the part
    $string = str_replace("%desc%", $part->getDescription(), $string);             //description of the part
    $string = str_replace("%comment%", $part->getComment(), $string);              //comment of the part
    $string = str_replace("%mininstock%", $part->getMinInstock(), $string);        //minimum in stock
    $string = str_replace("%instock%", $part->getInstock(), $string);              //current in stock
    $string = str_replace("%avgprice%", $part->getAveragePrice(), $string);       //average price

    //Category infos
    $string = str_replace("%cat%", is_object($part->getCategory()) ? $part->getCategory()->getName() : "", $string);
    $string = str_replace("%cat_full%", is_object($part->getCategory()) ? $part->getCategory()->getFullPath() : "", $string);

    //Footprint info
    $string = str_replace("%foot%", is_object($part->getFootprint()) ? $part->getFootprint()->getName() : "", $string);
    $string = str_replace("%foot_full%", is_object($part->getFootprint()) ? $part->getFootprint()->getFullPath() : "", $string);

    //Manufacturer info
    $string = str_replace("%manufact%", is_object($part->getManufacturer()) ? $part->getManufacturer()->getName() : "", $string);

    //Order infos
    $all_orderdetails   = $part->getOrderdetails();
    $string = str_replace("%supplier%", (count($all_orderdetails) > 0) ? $all_orderdetails[0]->getSupplier()->getName() : "", $string);
    $string = str_replace("%order_nr%", (count($all_orderdetails) > 0) ? $all_orderdetails[0]->getSupplierPartNr() : "", $string);

    //Store location
    $storelocation      = $part->getStorelocation();
    $string = str_replace("%storeloc%", is_object($storelocation) ? $storelocation->getName() : '', $string);
    $string = str_replace("%storeloc_full%", is_object($storelocation) ? $storelocation->getFullPath() : '', $string);

    //Remove single '-' without other infos
    if (trim($string) == "-") {
        $string = "";
    }

    return $string;
}

/**
 * Split a search string with search modifiers like "incategory:Category1" or "inname:Name2" into a array with
 * the modifier keywords in named elemets.
 *
 * @param $search_str string             the search containing the search modifiers.
 *
 * @return array            * an array with the elements name, description, comment, footprint, category,
 *                          storelocation, suppliername, partnr and manufacturername. Element is "" when no modifier for
 *                          this element was given.
 * * if $search_str does not contain any search modifier, then every element of the array
 *                          will contain the original search string.
 */
function searchStringToArray($search_str)
{
    $arr = array();
    $arr['name'] = getKeywordAfterModifier($search_str, "inname:");

    $arr['description'] = getKeywordAfterModifier($search_str, "indescription:");
    $arr['description'] = getKeywordAfterModifier($search_str, "indesc:");

    $arr['comment'] = getKeywordAfterModifier($search_str, "incomment:");

    $arr['footprint'] = getKeywordAfterModifier($search_str, "infootprint:");
    $arr['footprint'] = getKeywordAfterModifier($search_str, "infoot:");

    $arr['category'] = getKeywordAfterModifier($search_str, "incategory:");
    $arr['category'] = getKeywordAfterModifier($search_str, "incat:");

    $arr['storelocation'] = getKeywordAfterModifier($search_str, "inlocation:");
    $arr['storelocation'] = getKeywordAfterModifier($search_str, "inloc:");

    $arr['suppliername'] = getKeywordAfterModifier($search_str, "insupplier:");

    $arr['partnr'] = getKeywordAfterModifier($search_str, "inpartnr:");

    $arr['manufacturername'] = getKeywordAfterModifier($search_str, "inmanufacturer:");

    //Check if all array entries are "", which means $search_str contains no modifier
    $no_modifier = true;
    foreach ($arr as $n) {
        if ($n !== "") {
            $no_modifier = false;
        }
    }

    if ($no_modifier === true) {    //When no modifier exists, fill every element with $search_str (emulate the old behaviour)
        foreach ($arr as &$n) {
            $n = $search_str;
        }
    }

    return $arr;
}

/***
 * Returns the keyword after a search modifier.(e.g. "inname:Test" with the modifier inname: would return "Test")
 * @param $search_str string The string which contains the modifiers and keywords.
 * @param $modifier  string The modifier which should be searched for
 * @return string Return the keyword after the modifier, if it was found. Else returns "".
 */
function getKeywordAfterModifier($search_str, $modifier)
{
    $pos = strpos($search_str, $modifier);
    if ($pos === false) {   //This modifier was not found in the search_str, so return "".
        return "";
    } else { //Modifier was found in the search string
        $start = $pos + strlen($modifier);
        if ($search_str[$start] == "\"" || $search_str[$start] == "\'") { //When a quote mark is detected, then treat the text up to the next quote as one literal
            $end = strpos($search_str, $search_str[$start], $start + 1);
            return substr($search_str, $start + 1, $end - $start - 1);
        } else { //Go only to the next space
            $end = strpos($search_str, " ", $start);
            if ($end === false) { //The modifier was the last part of the query, so we dont need an end.
                return substr($search_str, $start);
            } else {
                return substr($search_str, $start, $end - $start);
            }
        }
    }
}

/**
 * Allow the usage of umlauts in the given pattern
 * @param $pattern string
 * @return string
 */
function regexAllowUmlauts($pattern)
{
    return str_replace("\w", '[\wÄäÖöÜüß]', $pattern);
}

function regexStripSlashes($pattern, $mb = true)
{
    if (mb_substr($pattern, 0, 1) === "/" &&  substr($pattern, -1, 1) === "/") {
        return mb_substr($pattern, 1, -1);
    } else {
        return $pattern;
    }
}


/**
 * Generates a <input type="hidden"> Html string, with the given values.
 * @param $name  string The "name" attribute of the <input> element
 * @param $value string The "value" attribute of the <input> element
 * @return string The HTML string.
 */
function generateInputHidden($name, $value = "")
{
    return '<input type="hidden" name="' . $name . '" value="' . $value . '">';
}

function generateButton($name, $text, $theme = "btn-default", $val = "")
{
    return "<button type='submit' class='btn $theme' name='$name' value='$val'>$text</button>";
}

function generateButtonRed($name, $text, $theme = "btn-danger", $val = "")
{
    return generateButton($name, $text, $theme, $val);
}

/**
 * Checks if a string contains a specific substring
 * @param $haystack string The string which should be examined.
 * @param $needle string The string which should be searched.
 * @return bool True if $haystack contains $needle, else false.
 */
function strcontains($haystack, $needle)
{
    if (strpos($haystack, $needle) !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * Converts an array of objects implementing the APIModel interface to an array of API objects
 * @param $array array The array of the APIModel objects.
 * @return IAPIModel[] An array of API objects
 * @throws Exception
 */
function convertAPIModelArray($array, $verbose = false)
{
    if (is_null($array)) {
        return null;
    }

    $json = array();
    foreach ($array as $element) {
        if (! $element instanceof IAPIModel) {
            throw new Exception("The given array, contains objects that dont implement IAPIModel!");
        }
        $json[] = $element->getAPIArray($verbose);
    }
    return $json;
}

/**
 * Try to call get_APIModel_array of $object. If $object is null, null is returned!
 * @param IAPIModel $object The object, of which the API info should be get.
 * @return array An array describing the object.
 */
function tryToGetAPIModelArray($object, $verbose = false)
{
    if (is_null($object)) {
        return null;
    } else {
        return $object->getAPIArray($verbose);
    }
}

/**
 * Builds a TreeView for the Tools menu
 * @param $params
 * @return array
 */
function buildToolsTree($params)
{
    global $config;

    $disable_footprint = $config['footprints']['disable'];
    $disable_manufactur = $config['manufacturers']['disable'];
    $disable_devices = $config['devices']['disable'];
    $disable_help = $config['menu']['disable_help'];
    $disable_config = $config['menu']['disable_config'];
    $enable_debug_link = $config['menu']['enable_debug'];
    $disable_labels = $config['menu']['disable_labels'];
    $disable_calculator = $config['menu']['disable_calculator'];
    $disable_iclogos = $config['menu']['disable_iclogos'];
    $disable_tools_footprints = $config['menu']['disable_footprints'];
    $developer_mode = $config['developer_mode'];
    $db_backup_name = $config['db']['backup']['name'];
    $db_backup_url = $config['db']['backup']['url'];


    //Tools nodes
    $tools_nodes = array();
    $tools_nodes[] = treeviewNode(_("Import"), BASE_RELATIVE . "/tools_import.php");
    if (!$disable_labels) {
        $tools_nodes[] = treeviewNode(_("Labels"), BASE_RELATIVE . "/tools_labels.php");
    }
    if (!$disable_calculator) {
        $tools_nodes[] = treeviewNode(_("Widerstandsrechner"), BASE_RELATIVE . "/tools_calculator.php");
    }
    if (!$disable_footprint) {
        $tools_nodes[] = treeviewNode(_("Footprints"), BASE_RELATIVE . "/tools_footprints.php");
    }
    if (!$disable_iclogos) {
        $tools_nodes[] = treeviewNode(_("IC-Logos"), BASE_RELATIVE . "/tools_iclogos.php");
    }

    $system_nodes = array();
    $system_nodes[] = treeviewNode(_("Konfiguration"), BASE_RELATIVE . "/system_config.php");
    $system_nodes[] = treeviewNode(_("Datenbank"), BASE_RELATIVE . "/system_database.php");


    //Show nodes
    $show_nodes = array();
    $show_nodes[] = treeviewNode(_("Zu bestellende Teile"), BASE_RELATIVE . "/show_order_parts.php");
    $show_nodes[] = treeviewNode(_("Teile ohne Preis"), BASE_RELATIVE . "/show_noprice_parts.php");
    $show_nodes[] = treeviewNode(_("Obsolente Bauteile"), BASE_RELATIVE . "/show_obsolete_parts.php");
    $show_nodes[] = treeviewNode(_("Statistik"), BASE_RELATIVE . "/statistics.php");
    $show_nodes[] = treeviewNode(_("Alle Teile"), BASE_RELATIVE . "/show_all_parts.php");

    //Edit nodes
    $edit_nodes = array();
    if (!$disable_devices) {
        $edit_nodes[] = treeviewNode(_("Baugruppen"), BASE_RELATIVE . "/edit_devices.php");
    }
    $edit_nodes[] = treeviewNode(_("Lagerorte"), BASE_RELATIVE . "/edit_storelocations.php");
    $edit_nodes[] = treeviewNode(_("Footprints"), BASE_RELATIVE . "/edit_footprints.php");
    $edit_nodes[] = treeviewNode(_("Kategorien"), BASE_RELATIVE . "/edit_categories.php");
    $edit_nodes[] = treeviewNode(_("Lieferanten"), BASE_RELATIVE . "/edit_suppliers.php");
    if (!$disable_manufactur) {
        $edit_nodes[] = treeviewNode(_("Hersteller"), BASE_RELATIVE . "/edit_manufacturers.php");
    }
    $edit_nodes[] = treeviewNode(_("Dateitypen"), BASE_RELATIVE . "/edit_attachement_types.php");

    //Developer nodes
    $dev_nodes = array();
    $dev_nodes[] = treeviewNode(_("Werkzeuge"), BASE_RELATIVE . "/development/developer_tools.php");
    $dev_nodes[] = treeviewNode(_("Debugging"), BASE_RELATIVE . "/system_debug.php");
    $dev_nodes[] = treeviewNode(_("Sandkasten"), BASE_RELATIVE . "/development/sandbox.php");
    $dev_nodes[] = treeviewNode(_("Quellcode-Doku"), BASE_RELATIVE . "/development/phpdoc/html/index.html");

    //Add nodes to root
    $tree = array();
    $tree[] = treeviewNode(_("Tools"), null, $tools_nodes);
    $tree[] = treeviewNode(_("Bearbeiten"), null, $edit_nodes);
    $tree[] = treeviewNode(_("Zeige"), null, $show_nodes);
    if (!$disable_config) {
        $tree[] = treeviewNode(_("System"), null, $system_nodes);
    }
    if ($developer_mode) {
        $tree[] = treeviewNode(_("Entwickler-Werkzeuge"), null, $dev_nodes);
    }
    $tree[] = treeviewNode(_("Hilfe"), "https://github.com/jbtronics/Part-DB/wiki", null);

    return $tree;
}

/**
 * Short for "set if empty"
 * Checks if $test is null, then set it to $default_val, else return the normal
 * @param mixed $test The value which should be checked.
 * @param mixed $default_val The value, to which the value should be set defaultly
 * @return mixed The result
 */
function sie($test, $default_val = "")
{
    if (isset($test)) {
        return $test;
    } else {
        return $default_val;
    }
}

/**
 * Gets the name of the class of the given Object without the namespace.
 * @param $object mixed  The object, whose clasname should be get.
 * @return string The class name of $object.
 */
function getClassShort($object)
{
    $reflect = new \ReflectionClass($object);
    return $reflect->getShortName();
}

/**
 * Checks if $var is empty. This function capsules the empty function, so we can use it for expressions.
 * @param $var mixed The variable which should be checked.
 * @return boolean
 */
function _empty($var)
{
    return empty($var);
}

/**
 * Check if the connection to the server is using HTTPS.
 * @return bool True if the connection is using HTTPS, false if not.
 */
function isUsingHTTPS() {
    return
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443;
}