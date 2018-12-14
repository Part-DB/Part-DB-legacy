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
use PartDB\Permissions\PartPermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\StructuralPermission;
use PartDB\Permissions\ToolsPermission;
use PartDB\Tools\JSONStorage;
use PartDB\User;

use geertw\IpAnonymizer\IpAnonymizer;

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
        throw new Exception(sprintf(_('"%s" ist kein gültiges Verzeichnis!'), $directory));
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
        throw new Exception(sprintf(_('"%s" ist kein gültiges Verzeichnis!'), $directory));
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

    //Dont allow to upload a PHP file.
    if(strpos($file_array['name'], ".php") != false
        || strpos($destination_filename, ".php") != false)
    {
        throw new \Exception(_("Es ist nicht erlaubt PHP Dateien hochzuladen!"));
    }

    if ($destination_filename == null) {
        $destination_filename = $file_array['name'];
    }

    $destination = $destination_directory.$destination_filename;

    if ((mb_substr($destination_directory, -1, 1) != '/') || (! isPathabsoluteAndUnix($destination_directory, false))) {
        throw new Exception(sprintf(_('"%s" ist kein gültiges Verzeichnis!'), $destination_directory));
    }

    try {
        createPath($destination_directory);
    } catch (Exception $ex) {
        throw new Exception(_("Das Verzeichniss konnte nicht angelegt werden!"));
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
            throw new Exception(_('Die maximal mögliche Dateigrösse für Uploads wurde überschritten ("upload_max_filesize" in "php.ini")! ').
                '<a target="_blank" href="'._("https://github.com/Part-DB/Part-DB/wiki/Anforderungen").'>'._("Hilfe").'</a>');
        case UPLOAD_ERR_FORM_SIZE:
            throw new Exception(_('Die maximal mögliche Dateigrösse für Uploads wurde überschritten!'));
        case UPLOAD_ERR_PARTIAL:
            throw new Exception(_('Die Datei wurde nur teilweise hochgeladen!'));
        case UPLOAD_ERR_NO_FILE:
            throw new Exception(_('Es wurde keine Datei hochgeladen!'));
        case UPLOAD_ERR_NO_TMP_DIR:
            throw new Exception(_('Es gibt keinen temporären Ordner für hochgeladene Dateien!'));
        case UPLOAD_ERR_CANT_WRITE:
            throw new Exception(_('Das Speichern der Datei auf die Festplatte ist fehlgeschlagen!'));
        case UPLOAD_ERR_EXTENSION:
            throw new Exception(_('Eine PHP Erweiterung hat den Upload der Datei gestoppt!'));
        default:
            throw new Exception(_('Beim Hochladen der Datei trat ein unbekannter Fehler auf!'));
    }

    if (! move_uploaded_file($file_array['tmp_name'], $destination)) {
        throw new Exception(_('Beim Hochladen der Datei trat ein unbekannter Fehler auf!'));
    }

    return $destination;
}

/**
 * Set a password for the "admin" password that will be written into, the database, when DB will be created.
 * This function should be only used in install.php !!
 *
 * @note    The password will be trimmed, salted, crypted with sha256 and stored in $config.
 *          Optionally, $config can be written in config.php.
 *
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
function setTempAdminPassword($new_password_1, $new_password_2, $save_config = true)
{
    global $config;

    settype($old_password, 'string');
    settype($new_password_1, 'string');
    settype($new_password_2, 'string');
    $new_password_1 = trim($new_password_1);
    $new_password_2 = trim($new_password_2);

    if (mb_strlen($new_password_1) < 6) {
        throw new Exception(_('Das neue Passwort muss mindestens 6 Zeichen lang sein!'));
    }

    if ($new_password_1 !== $new_password_2) {
        throw new Exception(_('Die neuen Passwörter stimmen nicht überein!'));
    }

    // all ok, save the new password
    $config['admin']['tmp_password'] = password_hash($new_password_1, PASSWORD_DEFAULT);

    if ($save_config) {
        saveConfig();
    }
}

/**
 * Save the global array "$config" to the file "config.php"
 *
 * @throws Exception if there was an error (maybe not enought permissions)
 */
function saveConfig()
{
    if ((file_exists(BASE.'/data/config.php')) && (! is_writeable(BASE.'/data/config.php'))) {
        throw new Exception(_('Es sind nicht genügend Rechte vorhanden um die Datei "config.php" zu beschreiben!'));
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
        throw new Exception(_('Die Datei "config.php" konnte nicht beschrieben werden. Überprüfen Sie, ob genügend Rechte vorhanden sind.'));
    }

    if (! fwrite($fp, $content)) {
        throw new Exception(_('Die Datei "config.php" konnte nicht beschrieben werden. Überprüfen Sie, ob genügend Rechte vorhanden sind.'));
    }

    if (! fclose($fp)) {
        throw new Exception(_('Es gab ein Fehler beim Abschliessen der Schreibvorgangs bei der Datei "config.php".'));
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
 * Returns the Currency symbol for the configured locale.
 * @return string The currency symbol.
 */
function getCurrencySymbol()
{
    global $config;
    $language = $config['language'];

    //User can override the currency symbol in config, we need to respect that...
    if(isset($config['money_format'][$language])) {
        return $config['money_format'][$language];
    }

  return localeconv()['currency_symbol'];
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
        throw new Exception(_('"curl" scheint auf ihrem System nicht installiert zu sein! '.
            "\nBitte installieren Sie das entsprechende Modul, ".
            'oder es werden gewisse Funktionen nicht zur Verfügung stehen.'));
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
 * Download a file from web to the server.
 * @param $url string The URL of the resource which should be downloaded.
 * @param $path string The path, where the file should be placed. (Must be absolute, unix style and end with a slash)
 * @param string $filename string Defaultly the filename of the new file gets determined from the url.
 *          However you can override the filename with this param.
 * @param $download_override boolean Set this to true, if you want to download a file, even when $config['allow_server_downloads'] is false.
 * @throws Exception Throws an exception if an error happened, or file could not be downloaded.
 * @return string|boolean The path of the created file, when the file was successful downloaded. False, when an error happened.
 */
function downloadFile($url, $path, $filename = "", $download_override = false)
{
    global $config;
    if ($config['allow_server_downloads'] == false && $download_override == false) {
        throw new Exception(_("Das Herunterladen von Dateien über den Server ist deaktiviert!"));
    }

    if (!isPathabsoluteAndUnix($path)) {
        throw new Exception(_('$path ist kein gültiger und absoluter Pfad!'));
    }
    if (!isURL($url)) {
        throw new Exception(_('$url ist keine gültige URL'));
    }
    if ($filename == "") {
        $parts = parse_url($url);
        $filename = basename($parts['path']);
    }

    //Dont allow to upload a PHP file.
    if(strpos($filename, ".php") != false) {
        throw new \Exception(_("Es ist nicht erlaubt PHP Dateien herunterzuladen!"));
    }

    set_time_limit(30);

    createPath($path);

    $ret = file_put_contents($path . $filename, fopen($url, 'r'));
    if ($ret !== false) { //If download was successful
        return $path . $filename;
    }
    return false;
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

function generateButton($name, $text, $theme = "btn-secondary", $val = "")
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
 * @param bool $verbose Show all available informations about the IAPIModel.
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
 * @param bool $verbose Show all available informations about the IAPIModel, when set to true.
 *          Otherwise only most important informations are shown.
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
 * @throws Exception
 */
function buildToolsTree($params)
{
    global $config;

    //Build objects
    $current_user       = User::getLoggedInUser();

    $disable_footprint = $config['footprints']['disable'];
    $disable_manufactur = $config['manufacturers']['disable'];
    $disable_suppliers  = $config['suppliers']['disable'];
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
    $footprint_3d_active = $config['foot3d']['active'];


    //Tools nodes
    $tools_nodes = array();
    if ($current_user->canDo(PermissionManager::TOOLS, ToolsPermission::IMPORT)) {
        $tools_nodes[] = treeviewNode(_("Import"), BASE_RELATIVE . "/tools_import.php");
    }
    if (!$disable_labels && $current_user->canDo(PermissionManager::TOOLS, ToolsPermission::LABELS)) {
        $tools_nodes[] = treeviewNode(_("SMD Labels"), BASE_RELATIVE . "/tools_labels.php");
    }
    if (!$disable_calculator && $current_user->canDo(PermissionManager::TOOLS, ToolsPermission::CALCULATOR)) {
        $tools_nodes[] = treeviewNode(_("Widerstandsrechner"), BASE_RELATIVE . "/tools_calculator.php");
    }
    if (!$disable_tools_footprints && $current_user->canDo(PermissionManager::TOOLS, ToolsPermission::FOOTPRINTS)) {
        $tools_nodes[] = treeviewNode(_("Footprints"), BASE_RELATIVE . "/tools_footprints.php");
    }
    if ($footprint_3d_active && $current_user->canDo(PermissionManager::TOOLS, ToolsPermission::FOOTPRINTS)) {
        $tools_nodes[] = treeviewNode(_("3D Footprints"), BASE_RELATIVE . "/tools_3d_footprints.php");
    }
    if (!$disable_labels && $current_user->canDo(PermissionManager::LABELS, \PartDB\Permissions\LabelPermission::CREATE_LABELS)) {
        $tools_nodes[] = treeviewNode(_("Labelgenerator"), BASE_RELATIVE . "/show_part_label.php");
    }
    if (!$disable_iclogos && $current_user->canDo(PermissionManager::TOOLS, ToolsPermission::IC_LOGOS)) {
        $tools_nodes[] = treeviewNode(_("IC-Logos"), BASE_RELATIVE . "/tools_iclogos.php");
    }

    $system_nodes = array();
    if ($current_user->canDo(PermissionManager::USERS, \PartDB\Permissions\UserPermission::READ)) {
        $system_nodes[] = treeviewNode(_("Benutzer"), BASE_RELATIVE . "/edit_users.php");
    }
    if ($current_user->canDo(PermissionManager::GROUPS, \PartDB\Permissions\GroupPermission::READ)) {
        $system_nodes[] = treeviewNode(_("Gruppen"), BASE_RELATIVE . "/edit_groups.php");
    }
    if ($current_user->canDo(PermissionManager::CONFIG, \PartDB\Permissions\ConfigPermission::READ_CONFIG)
        || $current_user->canDo(PermissionManager::CONFIG, \PartDB\Permissions\ConfigPermission::SERVER_INFO)) {
        $system_nodes[] = treeviewNode(_("Konfiguration"), BASE_RELATIVE . "/system_config.php");
    }
    if ($current_user->canDo(PermissionManager::DATABASE, \PartDB\Permissions\DatabasePermission::SEE_STATUS)
        || $current_user->canDo(PermissionManager::DATABASE, \PartDB\Permissions\DatabasePermission::READ_DB_SETTINGS)) {
        $system_nodes[] = treeviewNode(_("Datenbank"), BASE_RELATIVE . "/system_database.php");
    }
    if ($current_user->canDo(PermissionManager::SYSTEM, \PartDB\Permissions\SystemPermission::SHOW_LOGS)
            || $current_user->canDo(PermissionManager::SELF, \PartDB\Permissions\SelfPermission::SHOW_LOGS)) {
        $system_nodes[] = treeviewNode(_("Eventlog"), BASE_RELATIVE . "/system_log.php");
    }



    //Show nodes
    $show_nodes = array();
    if ($current_user->canDo(PermissionManager::PARTS, PartPermission::ORDER_PARTS)) {
        $show_nodes[] = treeviewNode(_("Zu bestellende Teile"), BASE_RELATIVE . "/show_order_parts.php");
    }
    if ($current_user->canDo(PermissionManager::PARTS, PartPermission::NO_PRICE_PARTS)) {
        $show_nodes[] = treeviewNode(_("Teile ohne Preis"), BASE_RELATIVE . "/show_noprice_parts.php");
    }
    if ($current_user->canDo(PermissionManager::PARTS, PartPermission::OBSOLETE_PARTS)) {
        $show_nodes[] = treeviewNode(_("Obsolete Bauteile"), BASE_RELATIVE . "/show_obsolete_parts.php");
    }
    if ($current_user->canDo(PermissionManager::TOOLS, ToolsPermission::STATISTICS)) {
        $show_nodes[] = treeviewNode(_("Statistik"), BASE_RELATIVE . "/statistics.php");
    }
    if ($current_user->canDo(PermissionManager::PARTS, PartPermission::ALL_PARTS)) {
        $show_nodes[] = treeviewNode(_("Alle Teile"), BASE_RELATIVE . "/show_all_parts.php");
    }
    if ($current_user->canDo(PermissionManager::PARTS, PartPermission::UNKNONW_INSTOCK_PARTS)) {
        $show_nodes[] = treeviewNode(_("Teile mit unbekanntem Lagerbestand"), BASE_RELATIVE . "/show_unknown_instock_parts.php");
    }
    if ($current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_FAVORITE_PARTS)) {
        $show_nodes[] = treeviewNode(_('Favorisierte Bauteile'), BASE_RELATIVE . "/show_favorite_parts.php");
    }
    if ($current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_LAST_EDIT_PARTS)) {
        $show_nodes[] = treeviewNode(_('Zuletzt bearbeitete Bauteile'), BASE_RELATIVE . "/show_last_modified_parts.php");
    }
    if ($current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_LAST_EDIT_PARTS)) {
        $show_nodes[] = treeviewNode(_('Zuletzt hinzugefügte Bauteile'), BASE_RELATIVE . "/show_last_modified_parts.php?mode=last_created");
    }

    //Edit nodes
    $edit_nodes = array();
    if (!$disable_devices && $current_user->canDo(PermissionManager::DEVICES, StructuralPermission::READ)) {
        $edit_nodes[] = treeviewNode(_("Baugruppen"), BASE_RELATIVE . "/edit_devices.php");
    }
    if ($current_user->canDo(PermissionManager::STORELOCATIONS, StructuralPermission::READ)) {
        $edit_nodes[] = treeviewNode(_("Lagerorte"), BASE_RELATIVE . "/edit_storelocations.php");
    }
    if (!$disable_footprint && $current_user->canDo(PermissionManager::FOOTRPINTS, StructuralPermission::READ)) {
        $edit_nodes[] = treeviewNode(_("Footprints"), BASE_RELATIVE . "/edit_footprints.php");
    }
    if ($current_user->canDo(PermissionManager::CATEGORIES, StructuralPermission::READ)) {
        $edit_nodes[] = treeviewNode(_("Kategorien"), BASE_RELATIVE . "/edit_categories.php");
    }
    if (!$disable_suppliers && $current_user->canDo(PermissionManager::SUPPLIERS, StructuralPermission::READ)) {
        $edit_nodes[] = treeviewNode(_("Lieferanten"), BASE_RELATIVE . "/edit_suppliers.php");
    }
    if (!$disable_manufactur && $current_user->canDo(PermissionManager::MANUFACTURERS, StructuralPermission::READ)) {
        $edit_nodes[] = treeviewNode(_("Hersteller"), BASE_RELATIVE . "/edit_manufacturers.php");
    }
    if ($current_user->canDo(PermissionManager::ATTACHEMENT_TYPES, StructuralPermission::READ)) {
        $edit_nodes[] = treeviewNode(_("Dateitypen"), BASE_RELATIVE . "/edit_attachement_types.php");
    }
    if($current_user->canDo(PermissionManager::PARTS, PartPermission::CREATE)) {
        $edit_nodes[] = treeviewNode(_("Bauteil anlegen"), BASE_RELATIVE . "/edit_part_info.php");
    }

    //Developer nodes
    $dev_nodes = array();
    $dev_nodes[] = treeviewNode(_("Werkzeuge"), BASE_RELATIVE . "/development/developer_tools.php");
    $dev_nodes[] = treeviewNode(_("Debugging"), BASE_RELATIVE . "/system_debug.php");
    $dev_nodes[] = treeviewNode(_("Sandkasten"), BASE_RELATIVE . "/development/sandbox.php");
    $dev_nodes[] = treeviewNode(_("Quellcode-Doku"), BASE_RELATIVE . "/development/phpdoc/html/index.html");

    //Add nodes to root
    $tree = array();
    if (!empty($tools_nodes)) {
        $tree[] = treeviewNode(_("Tools"), null, $tools_nodes);
    }
    if (!empty($edit_nodes)) {
        $tree[] = treeviewNode(_("Bearbeiten"), null, $edit_nodes);
    }
    if (!empty($show_nodes)) {
        $tree[] = treeviewNode(_("Zeige"), null, $show_nodes);
    }
    if (!$disable_config && !empty($system_nodes)) {
        $tree[] = treeviewNode(_("System"), null, $system_nodes);
    }
    if ($developer_mode && $current_user->canDo(PermissionManager::SYSTEM, \PartDB\Permissions\SystemPermission::USE_DEBUG)) {
        $tree[] = treeviewNode(_("Entwickler-Werkzeuge"), null, $dev_nodes);
    }
    if (!$disable_help) {
        $tree[] = treeviewNode(_("Hilfe"), "https://github.com/jbtronics/Part-DB/wiki", null);
    }


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
 * @throws ReflectionException
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
function isUsingHTTPS()
{
    return
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443;
}

/**
 * Generates a path, based on category structure of a part.
 * @param $base_dir string The base path for the file path structure (with trailing slash)
 * @param $element \PartDB\Base\StructuralDBElement
 * @return string The generated path
 * @throws Exception
 */
function generateAttachementPath($base_dir, $element)
{
    //Split full path into different categories
    $categories = explode("@@", $element->getFullPath("@@"));
    //Sanatize each category path
    foreach ($categories as &$category) {
        $category = filter_filename($category, true);
    }

    return $base_dir . "" . implode("/", $categories). "/";
}

/**
 * Removes characters, that are not allowed in filenames, from the filenames.
 * @param $filename string The filename which should be parsed.
 * @param bool $beautify boolean When true, the filename gets beautified, so test---file.pdf, becomes test-file.pdf
 * @return mixed|string
 */
function filter_filename($filename, $beautify = true)
{
    // sanitize filename
    $filename = preg_replace(
        '~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~x',
        '-',
        $filename
    );
    // avoids ".", ".." or ".hiddenFiles"
    $filename = ltrim($filename, '.-');
    // optional beautification
    if ($beautify) {
        $filename = beautify_filename($filename);
    }
    // maximise filename length to 255 bytes http://serverfault.com/a/9548/44086
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
    return $filename;
}

/**
 * Makes a filename more beatiful. For example: file___name.zip becomes file-name.zip
 * @param $filename
 * @return mixed|string
 */
function beautify_filename($filename)
{
    //Spaces becomes _
    $filename = preg_replace(array('/ +/'), "_", $filename);
    $filename = preg_replace(array('/_+/'), "_", $filename);
    // reduce consecutive characters
    $filename = preg_replace(array(
        // "file---name.zip" becomes "file-name.zip"
        '/-+/'
    ), '-', $filename);
    $filename = preg_replace(array(
        // "file--.--.-.--name.zip" becomes "file.name.zip"
        '/-*\.-*/',
        // "file...name..zip" becomes "file.name.zip"
        '/\.{2,}/'
    ), '.', $filename);
    // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
    //$filename = mb_strtolower($filename, mb_detect_encoding($filename));
    // ".file-name.-" becomes "file-name"
    $filename = trim($filename, '.-');
    return $filename;
}

/**
 * Recursively creates a long directory path, if it not exists.
 * @param $path string The path of the deepest folder, that should be created.
 * @return boolean Returns true, if the folder hierachy was created successful.
 */
function createPath($path)
{
    if (is_dir($path)) {
        return true;
    }
    $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
    $return = createPath($prev_path);
    return ($return && is_writable($prev_path)) ? mkdir($path) : false;
}

/**
 * Check if a string is a URL and is valid.
 * @param $string string The string which should be checked.
 * @param bool $path_required If true, the string must contain a path to be valid. (e.g. foo.bar would be invalid, foo.bar/test.php would be valid).
 * @param $only_http bool Set this to true, if only HTTPS or HTTP schemata should be allowed.
 *  *Caution: When this is set to false, a attacker could use the file:// schema, to get internal server files, like /etc/passwd.*
 * @return bool True if the string is a valid URL. False, if the string is not an URL or invalid.
 */
function isURL($string, $path_required = true, $only_http = true)
{
    if ($only_http) {   //Check if scheme is HTTPS or HTTP
        $scheme = parse_url($string, PHP_URL_SCHEME);
        if ($scheme !== "http" && $scheme !== "https") {
            return false;   //All other schemes are not valid.
        }
    }
    if ($path_required) {
        return filter_var($string, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
    } else {
        return filter_var($string, FILTER_VALIDATE_URL);
    }
}

/**
 * Returns a Fontawesome icon for the filepath based on the file extension.
 * @param $path string The path (including filename) for which the Icon should be generated.
 * @param $with_html bool When true a whole HTML tag is generated (e.g. <i class="fa fa-file" aria-hidden="true"></i>).
 *      When false, only the special fa-class is returned. (e.g. fa-file)
 * @param $size string The size of the icon as an FA size class (e.g. fa-lg)
 * @return string The resulted HTML code or the fa-class.
 */
function extToFAIcon($path, $with_html = true, $size = "fa-lg")
{
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    $fa_class = "";
    switch ($ext) {
        case "pdf":
            $fa_class = "fa-file-pdf";
            break;
        case "txt":
        case "csv":
        case "md":
        case "rtf":
            $fa_class = "fa-file-alt";
            break;
        case "jpg":
        case "jpeg":
        case "gif":
        case "png":
        case "svg":
        case "tif":
        case "tiff":
            $fa_class = "fa-file-image";
            break;
        case "zip":
        case "rar":
        case "bz2":
        case "tar":
        case "7z":
            $fa_class = "fa-file-archive";
            break;
        case "mp3":
        case "wav":
        case "aac":
        case "m4a":
        case "wma":
            $fa_class = "fa-file-audio";
            break;
        case "mp4":
        case "mkv":
        case "wmv":
            $fa_class = "fa-file-video";
            break;
        case "ppt":
        case "pptx":
        case "odp":
            $fa_class = "fa-file-powerpoint";
            break;
        case "doc":
        case "docx":
        case "odt":
            $fa_class = "fa-file-word";
            break;
        case "xls":
        case "xlsx":
        case "ods":
            $fa_class = "fa-file-excel";
            break;
        case "php":
        case "xml":
        case "html":
        case "js":
        case "ts":
        case "htm":
            $fa_class = "fa-file-code";
            break;

        default: //Use generic file icon
            $fa_class = "fa-file";
            break;
    }

    if ($with_html == false) {
        return $fa_class;
    }

    $fa_class = $fa_class . " " . $size;

    //Build HTML
    return '<i class="far ' . $fa_class . '" aria-hidden="true"></i>';
}

/**
 * Parses the value of a Tristate Checkbox input.
 * @param $tristate_data string The Request data of the Tristate input.
 * @return int 0, if checkbox was indetermined, 1 if checkbox was checked, 2 if checkbox, was not checked.
 */
function parseTristateCheckbox($tristate_data)
{
    switch ($tristate_data) {
        case "true":
            return 1;
        case "false":
            return 2;
        case "indeterminate":
            return 0;
    }

    throw new InvalidArgumentException(_("Der gegebene Wert konnte keinem Tristatewert zugeordnet werden!"));
}

/**
 * Format the current timestamp regarding to the locale settings.
 * @param $timestamp int The timestamp which should be formatted.
 * @return string The formatted string.
 */
function formatTimestamp($timestamp)
{
    global $config;
    $language = $config['language'];
    $timezone = $config['timezone'];

    //Try to get the settings specific to the user.
    try {
        $current_user = User::getLoggedInUser();
        $language = $current_user->getLanguage();
        $timezone = $current_user->getTimezone();
    } catch (Exception $ex) {
        //Dont do anything
    }


    //Check if user has intl extension installed.
    if (class_exists("\IntlDateFormatter")) {
        $formatter = $formatter = new \IntlDateFormatter(
            $language,
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::MEDIUM,
            $timezone
        );

        return $formatter->format($timestamp);
    } else {
        //Failsafe, return as non localized string.
        return date('Y-m-d H:i:s', $timestamp);
    }
}

function generatePagination($page_link, $selected_page, $limit, $max_entries, $get_params = null)
{
    $links = array();

    $get_string = "";
    $prefix = "";
    //We only need the &, if the page_link does not end with ? (this is e.g. on show_all_parts.php the case)
    if(substr($page_link, -1) != "?") {
        $prefix = "&";
    }
    if(!empty($get_params)) {
        $get_string = $prefix . http_build_query($get_params);
    }

    //Back to first page
    $links[] = array("label" => '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
        "href" => $page_link . $prefix . "page=1&limit=$limit" . $get_string,
        "disabled" => $selected_page == 1,
        "hint" => _("Springe zur ersten Seite"));

    $max_page = ceil($max_entries / $limit);
    $max_page = $max_page > 0 ? $max_page : 1;

    $min_number = ($selected_page - 1) < 1 ? 1 : $selected_page -1;
    $max_number = ($selected_page + 2) > $max_page ? $max_page : $selected_page + 2;

    if ($selected_page == 0) {
        $min_number = 1;
        $max_number = 1;
    }

    for ($n=$min_number; $n <= $max_number; $n++) {
        $links[] = array("label" => $n,
            "href" => $page_link . $prefix. "page=" . ($n). "&limit=$limit" . $get_string,
            "active" => $n == $selected_page);
    }

    //Jump to last page.
    $links[] = array("label" => '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
        "href" => $page_link . $prefix . "page=$max_page&limit=$limit" . $get_string,
        "disabled" => $selected_page == $max_page,
        "hint" => _("Springe zur letzten Seite"));

    //Show all results
    $links[] = array("label" => '<i class="fa fa-bars" aria-hidden="true"></i>',
        "href" => $page_link . $prefix . "page=0" . $get_string,
        "active" => $selected_page == 0,
        "hint" => _("Alle anzeigen"));

    $upper_results = ($selected_page * $limit + 1) <= $max_entries && $selected_page > 0 ? $selected_page * $limit : $max_entries;
    if($upper_results == 0) {
        $lower_results = 0;
    } else {
        $lower_results = $selected_page > 0 ? ($selected_page - 1) * $limit + 1 : 1;
    }

    return array("lower_result" =>  $lower_results,
        "upper_result" => $upper_results,
        "max_entries" => $max_entries,
        "entries" => $links);
}

function parsePartsSelection(&$database, &$current_user, &$log, $selection, $action, $target)
{
    $ids = explode(",", $selection);
    foreach ($ids as $id) {
        $part = new Part($database, $current_user, $log, $id);
        if ($action=="delete_confirmed") {
            $part->delete();
        } elseif ($action=="move") {
            if ($target == "") {
                throw new Exception(_("Bitte wählen sie ein Ziel zum Verschieben aus."));
            }
            $type = substr($target, 0, 1);
            $target_id = intval(substr($target, 1));
            //Check if target ID is valid.
            if ($target_id < 1) {
                throw new Exception(_("Ungültige ID"));
            }
            switch ($type) {
                case "c": //Category
                    $part->setCategoryID($target_id);
                    break;
                case "f": //Footptint
                    $part->setFootprintID($target_id);
                    break;
                case "m": //Manufacturer
                    $part->setManufacturerID($target_id);
                    break;
                case "s": //Storelocation
                    $part->setStorelocationID($target_id);
                    break;
            }
        } elseif ($action=="favor") {
            $part->setFavorite(true);
        } elseif ($action=="defavor") {
            $part->setFavorite(false);
        } elseif ($action == "") {
            throw new Exception(_("Bitte wählen sie eine Aktion aus."));
        } else {
            throw new Exception(_("Unbekannte Aktion"));
        }
    }
}

function build_custom_css_loop($selected = null, $include_default_theme = false)
{
    global $config;
    if ($selected == null) {
        $selected = $config['html']['custom_css'];
    }

    $loop = array();
    if ($include_default_theme) {
        $loop[] = array("value" => "@@", "text" => _("Standardmäßiges Theme"), "selected" => ($selected == "@@"));
    }
    $files = findAllFiles(BASE.'/templates/custom_css/', true, '.css');

    foreach ($files as $file) {
        $name = str_ireplace(BASE.'/templates/custom_css/', '', $file);
        $loop[] = array('value' => $name, 'text' => $name, 'selected' => ($name == $selected));
    }

    return $loop;
}

/**
 * Generates a list of available profiles for the given generator.
 * @param $generator string The generator to which the profile belongs to.
 * @param $include_default bool If this is set to true, the default profile is included in the returned array.
 * @return string[] An string array with the names of all profiles
 */
function buildLabelProfilesDropdown($generator, $include_default = false)
{
    $json_storage = new JSONStorage(BASE_DATA . "/label_profiles.json");

    $data =  $json_storage->getKeyList($generator . "@");

    foreach ($data as $key => &$item) {
        $item = str_replace($generator . "@", "", $item);
        if (!$include_default && $item == "default") {
            unset($data[$key]);
        }
    }

    return $data;
}

/**
 * Return the IP Address the current user is accessing the DB.
 * IP Addresses gets anonymized based on the ip_anonymize_mask settings.
 * @param bool|string $mask_override_ipv4 Overrides the anonymization mask for IPv4 addresses.
 *          Use false to use values from config.php. Set to "" to disable anonymization completly.
 * @param bool|string $mask_override_ipv6 Overrides the anonymization mask for IPv6 addresses.
 * @return string The anonymized IP Address
 */
function getConnectionIPAddress($mask_override_ipv4 = false, $mask_override_ipv6 = false)
{
    global $config;

    $raw_ip = $_SERVER['REMOTE_ADDR'];

    //Determine mask for IPv4
    if ($mask_override_ipv4 === false) {
       $mask_ipv4 = $config['logging_system']['ip_anonymize_mask_ipv4'];
    } else {
        $mask_ipv4 = $mask_override_ipv4;
    }

    //Determine mask for IPv6
    if ($mask_override_ipv6 === false) {
        $mask_ipv6 = $config['logging_system']['ip_anonymize_mask_ipv6'];
    } else {
        $mask_ipv6 = $mask_override_ipv6;
    }

    if($mask_ipv4 === "") {
        //Return IP address without any anonymization
        return $raw_ip;
    }

    $ipAnonymizer = new IpAnonymizer();
    //Set masks
    $ipAnonymizer->ipv4NetMask = $mask_ipv4;
    $ipAnonymizer->ipv6NetMask = $mask_ipv6;

    return $ipAnonymizer->anonymize($raw_ip);
}