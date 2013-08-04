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
        2012-08-??  kami89          - created
        2012-09-28  kami89          - added doxygen comments
*/

    /**
     * @file class.SystemVersion.php
     * @brief class SystemVersion
     *
     * @class SystemVersion
     * @brief Class SystemVersion
     *
     * A SystemVersion object represents a system version with the following attributes:
     *  - major version
     *  - minor version
     *  - update version
     *  - (release candidate number)
     *  - (SVN revision number)
     *  - type ('stable', 'unstable' or 'svn', depends on release candidate number and SVN revision number)
     *
     * A version string has this structure:
     *      "major.minor.update.[RC].[r]" --> "#.#.#.[RC#].[r#]" (brackets means "optional", # stands for numbers)
     *
     * @note    We will always use the format "#.#.#.[RC#].[r#]" for handling with version numbers!
     *          Also the filenames of update packages and their version descriptions uses that format!
     *          Only for displaying the version number in a HTML output, we use the format "#.#.# [RC#] [r#]" (spaces instead of dots).
     *
     * @par Examples:
     *  - "0.2.3":          stable version 0.2.3
     *  - "0.2.3.RC4":      unstable version 0.2.3, release candidate 4
     *  - "0.2.3.r456":     svn revision 456, system version 0.2.3
     *  - "0.2.3.RC4.r456": svn revision 456, system version 0.2.3 RC4
     *
     * @author kami89
     *
     * @todo SVN versions are not supported yet
     */
    class SystemVersion
    {
        /********************************************************************************
        *
        *   Normal Attributes
        *
        *********************************************************************************/

        /** (integer) */
        private $major_version      = NULL;
        /** (integer) */
        private $minor_version      = NULL;
        /** (integer) */
        private $update_version     = NULL;
        /** (integer) Release Candidate number, zero means "stable version" */
        private $release_candidate  = NULL;
        /** (integer) the svn revision number, zero means "no SVN version" */
        private $svn_revision       = NULL;
        /** (string) the version type ('stable', 'unstable' or 'svn') */
        private $type               = NULL;

        /********************************************************************************
        *
        *   Static Attributes ("cached" Attributes)
        *
        *********************************************************************************/

        /** (SystemVersion) the latest stable version which is available */
        private static $latest_stable_version      = NULL;
        /** (SystemVersion) the latest unstable version which is available */
        private static $latest_unstable_version    = NULL;
        /** (SystemVersion) the latest svn version which is available */
        private static $latest_svn_version         = NULL;

        /********************************************************************************
        *
        *   Constructor / Destructor
        *
        *********************************************************************************/

        /**
         * @brief Constructor
         *
         * @param string $version_string        @li here we have to supply the version string
         *                                      @li Format: "#.#.#.[RC#].[r#]" (brackets means "optional", # stands for numbers)
         *                                      @li Examples see in the description of this class SystemVersion.
         *
         * @throws Exception if the parameter was not valid
         */
        public function __construct($version_string)
        {
            $version = str_replace(' ', '.', trim(strtolower($version_string)));

            // if $version is a stable version with a svn revision number, we add "RC0" to the string
            if ((strpos($version, 'r') > 0) && (strpos($version, 'rc') === false))
                $version = str_replace('r', 'rc0.r', $version);

            // if $version has no "RC", we will add it
            if (strpos($version, 'rc') === false)
                $version .= '.rc0';

            $version = str_replace('rc', '', $version);
            $version = str_replace('r', '', $version);
            $array = explode('.', $version);

            if ((count($array) < 3) || (count($array) > 5)
                || ((count($array) == 5) && (( ! is_int($array[4])) && ( ! ctype_digit($array[4]))))
                || (( ! is_int($array[0])) && ( ! ctype_digit($array[0])))
                || (( ! is_int($array[1])) && ( ! ctype_digit($array[1])))
                || (( ! is_int($array[2])) && ( ! ctype_digit($array[2]))))
            {
                debug('error', 'Fehlerhafte Version: "'.$version.'"', __FILE__, __LINE__, __METHOD__);
                throw new Exception('Es gab ein Fehler bei der Auswertung des Version-Strings!');
            }

            $this->major_version = $array[0];
            $this->minor_version = $array[1];
            $this->update_version = $array[2];
            $this->release_candidate = $array[3];

            if ($this->release_candidate == 0)
                $this->type = 'stable';
            else
                $this->type = 'unstable';

            if (count($array) == 5)
            {
                $this->svn_revision = $array[4];
                $this->type = 'svn';
            }
            else
                $this->svn_revision = 0;
        }

        /********************************************************************************
        *
        *   Basic Methods
        *
        *********************************************************************************/

        /**
         * @brief Generate a string of the version
         *
         * @param boolean $internal_format  If true, the internal format (with points instead of spaces) will be used.
         *                                  All other parameters will be ignored if this is true.
         * @param boolean $hide_rc          if true, the release candidate number will never be printed
         * @param boolean $hide_rev         if true, the svn revision number will never be printed
         * @param boolean $show_type        if true, the type (stable, unstable or svn) will be printed in brackets
         *
         * @retval string       the version string, like "0.2.3.RC2" (internal format), "0.2.3", "0.2.3 RC5", "0.2.3 (stable)", and so on...
         *
         * @note    The release candidate number won't be printed if it is zero (even if "$hide_rc == false")!
         * @note    The SVN revision number won't be printed if this is not a svn version (even if "$hide_rev == false")!
         */
        public function as_string($internal_format = true, $hide_rc = false, $hide_rev = false, $show_type = false)
        {
            $string = $this->major_version.'.'.$this->minor_version.'.'.$this->update_version;

            if ($internal_format)
            {
                if ($this->release_candidate > 0)
                    $string .= '.RC'.$this->release_candidate;

                if ($this->svn_revision > 0)
                    $string .= '.r'.$this->svn_revision;

                return $string;
            }
            else
            {
                if (($this->release_candidate > 0) && ( ! $hide_rc))
                    $string .= ' RC'.$this->release_candidate;

                if (($this->svn_revision > 0) && ( ! $hide_rev))
                    $string .= ' r'.$this->svn_revision;

                if ($show_type)
                    $string .= ' ('.$this->type.')';

                return $string;
            }
        }

        /**
         * @brief Check if this Version is newer than another Version
         *
         * With this function we can compare two objects.
         *
         * @param Version $version_2    the Version which we want to compare with this Version
         *
         * @retval boolean  @li true if this Version is newer than $version_2
         *                  @li otherwise false (equal or older)
         */
        public function is_newer_than($version_2)
        {
            if ($this->major_version != $version_2->major_version)
                return ($this->major_version > $version_2->major_version);

            if ($this->minor_version != $version_2->minor_version)
                return ($this->minor_version > $version_2->minor_version);

            if ($this->update_version != $version_2->update_version)
                return ($this->update_version > $version_2->update_version);

            // both versions have the same major, minor and update version!

            if (($this->release_candidate == 0) && ($version_2->release_candidate > 0))
                return true; // this is stable, $version_2 is only a release candidate

            if (($this->release_candidate > 0) && ($version_2->release_candidate == 0))
                return false; // $version_2 is stable, this version is only a release candidate

            if ($this->release_candidate > $version_2->release_candidate)
                return true; // this version is the newer release candidate than $version_2

            if ($this->release_candidate < $version_2->release_candidate)
                return false; // this version is the older release candidate than $version_2

            // both versions have the same major, minor, update and release candidate number!

            if ($this->svn_revision > $version_2->svn_revision)
                return true; // the svn revision of this version is higher than the revision of $version_2

            return false; // this svn revision is equal to or lower than $version_2
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get the version type of this version ('stable', 'unstable' or 'svn')
         *
         * @retval string       'stable', 'unstable' or 'svn'
         */
        public function get_version_type()
        {
            return $this->type;
        }

        /********************************************************************************
        *
        *   Static Methods
        *
        *********************************************************************************/

        /**
         * @brief Get the installed system version
         *
         * @retval Version      the installed system version
         *
         * @throws Exception if there was an error
         */
        public static function get_installed_version()
        {
            global $config;

            /* TODO
            $file_svn_revision = get_svn_revision();
            settype($file_svn_revision, 'integer');
            if ($file_svn_revision > $svn_revision)
                $version = new Version($log, $file_svn_revision);
            else
                $version = new Version($log, $svn_revision, $version_string);
            */

            $version = new SystemVersion($config['system']['version']);

            return $version;
        }

        /**
         * @brief Get the latest system version which is available (in the internet or in the directory "/updates/")
         *
         * @param string $type      'stable', 'unstable' or 'svn'
         *
         * @retval Version          the latest available system version
         *
         * @throws Exception if there was an error
         *
         * @todo    Search also in the local direcotry "/updates/" for updates.
         *          This is needed for manual updates (maybe the server has no internet access, or no "curl").
         */
        public static function get_latest_version($type)
        {
            if ((($type == 'stable') && ( ! is_object(SystemVersion::$latest_stable_version)))
                || (($type == 'unstable') && ( ! is_object(SystemVersion::$latest_unstable_version))))
            {
                $ini = curl_get_data('http://kami89.myparts.info/updates/latest.ini');
                $ini_array = parse_ini_string($ini, true);

                SystemVersion::$latest_stable_version    = new SystemVersion($ini_array['stable']['version']);
                SystemVersion::$latest_unstable_version  = new SystemVersion($ini_array['unstable']['version']);
            }

            if (($type == 'svn') && ( ! is_object(SystemVersion::$latest_svn_version)))
            {
                if ( ! function_exists('svn_log'))
                    throw new Exception('Die Funktion "svn_log()" existiert nicht auf ihrem System, daher kann die '.
                                        'neuste verfügbare SVN Revision nicht aus dem Internet gelesen werden!');

                // first, we will read the revision number of the latest svn revision (e.g. 567)
                $svn = svn_log('https://part-db.googlecode.com/svn/branches/uneist_kami89/', SVN_REVISION_HEAD);
                $svn_revision = $svn[0]['rev'];
                settype($svn_revision, 'integer');
                if ($svn_revision < 1)
                    throw new Exception('Die Revisionsnummer konnte nicht aus dem Internet gelesen werden!');

                // then we will read the system version of the latest svn revision (e.g. "0.2.3 RC2")
                $latest_config = curl_get_data('https://part-db.googlecode.com/svn/branches/uneist_kami89/config_defaults.php');
                // TODO: read the value from $config['system']['version']

                SystemVersion::$latest_svn_version = new SystemVersion('0.2.2 RC3 r123'); // just a dummy...
            }

            switch ($type)
            {
                case 'stable':
                    return SystemVersion::$latest_stable_version;
                case 'unstable':
                    return SystemVersion::$latest_unstable_version;
                case 'svn':
                    return SystemVersion::$latest_svn_version;
                default:
                    debug('error', '$type='.print_r($type, true), __FILE__, __LINE__, __METHOD__);
                    throw new Exception('$type hat einen ungültigen Inhalt!');
            }
        }
    }

?>
