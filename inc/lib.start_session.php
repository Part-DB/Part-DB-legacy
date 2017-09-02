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
 * @file lib.start_session.php
 * @brief in this file are some functions which are needed in start_session.php
 * @author kami89
 */

/**
 * Print out nice formatted messages in Part-DB design without using templates
 *
 * This is needed for uncaught exceptions or other error messages in start_session.php
 *
 * @param string        $page_title     the page title
 * @param string|NULL   $div_title      a DIV title, or NULL for a message without a title
 * @param string        $messages       the HTML-coded messages
 */
function printMessagesWithoutTemplate($page_title, $div_title, $messages)
{
    print '<!DOCTYPE html><html lang="en"><head>';
    print '<title>'.htmlspecialchars($page_title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</title>';
    print '<meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link href="css/bootstrap.min.css" rel="stylesheet"></head>';
    print '<body><main><div class="container-fluid" id="content"><br><div class="panel panel-danger">';
    if ($div_title) {
        print '<div class="panel-heading">'.$div_title.'</div>';
    }
    print '<div class="panel-body">';
    print $messages;
    {print '</div>';}
    print '</div></main></body></html>';
}

/**
 * This is an improved version of the setlocale() from PHP
 *
 * This function will first try to set an UTF-8 locale.
 * This function is used in start_session.php, install.php, lib.functions.php and system_config.php
 *
 * @param integer $category     locale category, see PHP documentation of setlocale()
 * @param string  $locale       locale string, see PHP documentation of setlocale()
 *
 * @return boolean  true if success, false if fail
 *
 * @todo    the Workaround for Windows is not really pretty -> make it better!
 */
function ownSetlocale($category, $locale)
{
    $charsets = array('utf8', 'UTF8', 'utf-8', 'UTF-8');
    $base_locales = array($locale);

    // workaround for Windows/XAMPP:
    switch ($locale) {
        case 'de_AT':
            $base_locales[] = 'german-austrian';
            $base_locales[] = 'dea';
            break;
        case 'de_CH':
            $base_locales[] = 'german-swiss';
            $base_locales[] = 'swiss';
            $base_locales[] = 'des';
            break;
        case 'de_DE':
            $base_locales[] = 'german';
            $base_locales[] = 'deu';
            break;
        case 'de_LU':
            $base_locales[] = 'german';
            $base_locales[] = 'deu';
            break;
        case 'en_GB':
            $base_locales[] = 'english-uk';
            $base_locales[] = 'uk';
            $base_locales[] = 'eng';
            break;
        case 'en_US':
            $base_locales[] = 'english-us';
            $base_locales[] = 'english-usa';
            $base_locales[] = 'english-american';
            $base_locales[] = 'american-english';
            $base_locales[] = 'american english';
            $base_locales[] = 'american';
            $base_locales[] = 'usa';
            $base_locales[] = 'us';
            $base_locales[] = 'enu';
            break;
        case 'POSIX':
            $base_locales[] = 'C';
            break;
        default:
            break;
    }

    $locales = array();
    foreach ($base_locales as $base_locale) {
        foreach ($charsets as $charset) {
            $locales[] = $base_locale.'.'.$charset;
        }

        $locales[] = $base_locale;
    }


    //Set locale in env, will work on windows servers too.
    putenv('LANG='.$locale);
    putenv('LC_ALL='.$locale);

    $retval = setlocale($category, $locales);
    $debug =  setlocale($category, "0");

    return (($retval !== false) || ($locale == 'POSIX'));
}

/**
 * Check if the server complies all minimum requirements of Part-DB
 *
 * @warning    All requirements must be defined in the array "$config['requirements']" in "config_defaults.php"!
 *
 * @return string[]    For every requirement which is not complied there's a message
 *                  (This array is empty if the server complies all requirements)
 *
 * @throws Exception if there was an error
 */
function checkRequirements()
{
    global $config;
    $messages = array();

    foreach ($config['requirements'] as $key => $value) {
        switch ($key) {
            case 'php_version':
                if (version_compare(PHP_VERSION, $value) < 0) {
                    $messages[] =   'Für Part-DB wird mindestens PHP '.$value.' vorausgesetzt! '.
                        'Die derzeit installierte Version ist PHP '.PHP_VERSION.'.';
                }
                break;

            case 'pdo':
                if (! class_exists('PDO', false)) {
                    $messages[] =   _('PDO (PHP Data Objects) wird benötigt, ist aber nicht installiert!');
                }
                break;

            default:
                throw new Exception(_('Unbekannte Mindestanforderung: ').$key);
        }
    }

    return $messages;
}

/**
 * @brief Check file permissions
 *
 * @return string[]  * an array with a string for each error message
 * * an empty array means everything is fine
 */
function checkFilePermissions()
{
    $messages = array();

    // directories must have a slash at the end!
    // e: must exist
    // r: must be readable
    // w: must be writable
    // x: executable bit must be set (important for directories with write access! will be ignored on Windows)
    $permissions = array(   // Part-DB/data
        '/data/'                                => 'erwx',
        '/data/config.php'                      => 'rw',    // don't need to exist (for first startup)
        '/data/backup/'                         => 'erwx',
        '/data/log/'                            => 'erwx',
        '/data/media/'                          => 'erwx');
    // DokuWiki/data
    //'/documentation/dokuwiki/data/'         => 'erwx',
    //'/documentation/dokuwiki/data/cache/'   => 'erwx',
    //'/documentation/dokuwiki/data/meta/'    => 'erwx',
    //'/documentation/dokuwiki/data/pages/'   => 'erwx',
    //'/documentation/dokuwiki/data/tmp/'     => 'erwx');

    foreach ($permissions as $filename => $needed_perms) {
        $whole_filename = BASE.$filename;

        if (! file_exists($whole_filename)) {
            if (strpos($needed_perms, 'e') !== false) {
                $messages[] =   'Das Verzeichnis bzw. die Datei "'.$filename.'" existiert nicht oder kann nicht gelesen werden!';
            }

            continue; // file does not exist - go to next file
        }

        // is_executable() may does not work correctly, see comment at "http://www.php.net/manual/de/function.is-executable.php#44454"
        $is_executable = (is_executable($whole_filename) || @file_exists($whole_filename.'.'));

        if (((strpos($needed_perms, 'r') !== false) && (! is_readable($whole_filename)))
            || ((strpos($needed_perms, 'w') !== false) && (! is_writable($whole_filename)))
            || ((strpos($needed_perms, 'x') !== false) && (DIRECTORY_SEPARATOR == '/') && (! $is_executable))) { // check for execution bit only for UNIX/Linux
            $messages[] =   'Das Verzeichnis bzw. die Datei "'.$filename.'" hat nicht die richtigen Dateirechte! '.
                'Benötigt werden "'.str_replace('e', '', $needed_perms).'". Bitte manuell korrigieren.';
        }
    }

    return $messages;
}

/**
 * Check if the vendor/ folder exists, with the dependencies we need.
 * If not, a message is returned, where it is explained how to fix the dependencies problem.
 *  @return array * an array with a string for each error message
 *  * an empty array means everything is fine
 */
function checkComposerFolder()
{
    $messages = array();

    $check_filenames = array("/vendor/autoload.php");

    foreach ($check_filenames as $filename) {
        $whole_filename = BASE.$filename;
        if (!file_exists($whole_filename)) {
            $messages[] = "Die Datei " . $filename . " ist benötigt und wurde nicht gefunden!";
        }
    }

    return $messages;
}

/**
 * Check if the config.php is valid
 *
 * Maybe some people are trying to create their config.php with a copy of config_defaults.php.
 * This is not good! In this case we will print out an error message!
 *
 * @return string|bool      * true if the config.php is valid
 * * an error message if the config.php is not valid
 */
function checkIfConfigIsValid()
{
    global $config_defaults;

    if (isset($config_defaults['system']) && isset($config_defaults['system']['version'])) {
        // it seems that the user has copied the config_defaults.php to the config.php, this is not good!
        return  'Es scheint, als hätten Sie die Datei "config_defaults.php" als Vorlage für Ihre "config.php" verwendet.<br>'.
            'Das ist aber nicht so vorgesehen und darf nicht so gemacht werden, da dies Probleme verursachen wird!<br><br>'.
            'Löschen Sie Ihre "config.php" und öffnen Sie Part-DB im Webbrowser.<br>'.
            'Es wird dann ein Installationsassistent gestartet, der automatisch eine korrekte "config.php" anlegen wird.';
    }

    return true;
}

/**
 * Handles an Exception
 * @param $e \Exception
 */
function exception_handler($e)
{
    printMessagesWithoutTemplate(
        'Part-DB: Schwerwiegender Fehler!',
        null,
        '<span style="color: red; "><strong>Es ist ein schwerwiegender Fehler aufgetreten:' .
        '<br><br>'.nl2br($e->getMessage()).'</strong><br><br>'.
        '(Exception wurde geworfen in '.$e->getFile().', Zeile '.$e->getLine(). ')</span>'
    );
    exit;
}
