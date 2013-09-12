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
        2013-09-11  kami89          - created
*/

    /**
     * @file class.SystemUpdate.php
     * @brief class SystemUpdate
     *
     * @class SystemUpdate
     * @brief Class SystemUpdate
     *
     * A SystemUpdate object represents an extracted system update in the directory "updates/"
     *
     * @author kami89
     */
    class SystemUpdate
    {
        /********************************************************************************
        *
        *   Normal Attributes
        *
        *********************************************************************************/

        /** (String) the full filename to the "update.xml" file */
        private $xml_filename       = NULL;
        /** (String) the full path to the directory where the "update.xml" is located, without slash at the end */
        private $filepath           = NULL;
        /** (SimpleXML) the SimpleXML object of the "update.xml" file */
        private $simplexml_object   = NULL;
        /** (SystemVersion) the system version, from which the update will start */
        private $from_version       = NULL;
        /** (SystemVersion) the system version, which the system will have after running this update */
        private $to_version         = NULL;
        /** (string) the release date of this update */
        private $release_date       = NULL;
        /** (Array) all changelog entries */
        private $changelog          = array();

        /********************************************************************************
        *
        *   Static Attributes ("cached" Attributes)
        *
        *********************************************************************************/

        /** (Array) all updates which are available in the updates directory */
        private static $all_available_local_updates = NULL;

        /********************************************************************************
        *
        *   Constructor / Destructor
        *
        *********************************************************************************/

        /**
         * @brief Constructor
         *
         * @param string $xml_filename      the full path to the "update.xml" file
         *
         * @throws Exception if the parameter was not valid
         */
        public function __construct($xml_filename)
        {
            if ( ! is_readable($xml_filename))
            {
                debug('error', 'Update-Datei nicht gefunden: "'.$xml_filename.'"', __FILE__, __LINE__, __METHOD__);
                throw new Exception('Das Update "'.$xml_filename.'" wurde nicht gefunden!');
            }

            $this->xml_filename = $xml_filename;
            $this->filepath     = pathinfo($xml_filename, PATHINFO_DIRNAME);

            $this->simplexml_object = simplexml_load_file($xml_filename);

            // read header
            $this->from_version = new SystemVersion((string)$this->simplexml_object->header->from_version);
            $this->to_version   = new SystemVersion((string)$this->simplexml_object->header->to_version);
            $this->release_date = (string)$this->simplexml_object->header->release_date;

            // read changelog
            foreach ($this->simplexml_object->changelog_items->changelog as $changelog)
                $this->changelog[] = (string)$changelog;
        }

        /********************************************************************************
        *
        *   Basic Methods
        *
        *********************************************************************************/

        /**
         * @brief Install this update
         *
         * @warning     Do not call this method directly!! Use always the method System::update() instead!
         *
         * @param Array $log_loop       The reference to an template loop for the log (@see System::update())
         *
         * @throws Exception if there was an error
         */
        public function install(&$log_loop)
        {
            global $config;

            // make some backups?

            // check files.zip checksum?

            // overwrite files
            $zip_filename = $this->filepath.'/files.zip';
            $log_loop[] = array('message' => '- Entpacke neue Dateien: '.$zip_filename);
            $zip = new ZipArchive;
            if ( ! $zip->open($zip_filename))
                throw new Exception('Die ZIP-Datei "'.$zip_filename.'" konnte nicht geöffnet werden!');
            $zip->extractTo(BASE.'/');
            $zip->close();
            $log_loop[] = array('message' => '- Neue Dateien erfolgreich entpackt');
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get the required version of this update
         *
         * @retval SystemVersion    The required version of this update
         */
        public function get_from_version()
        {
            return $this->from_version;
        }

        /**
         * @brief Get the target version of this update
         *
         * @retval SystemVersion    The target version of this update
         */
        public function get_to_version()
        {
            return $this->to_version;
        }

        /**
         * @brief Get the release date of this update
         *
         * @retval String   The release date of this update
         */
        public function get_release_date()
        {
            return $this->release_date;
        }

        /**
         * @brief Get the changelog of this update
         *
         * @retval Array    An array of strings with changelog messages
         */
        public function get_changelog()
        {
            return $this->changelog;
        }

        /********************************************************************************
        *
        *   Static Methods
        *
        *********************************************************************************/

        /*
         * @brief Download all available update archives for a specific version to the folder "updates/"
         *
         * @param string $for_version       @li the version string of the system version, which updates we need
         *                                  @li NULL means we use the current version of the installed system
         *
         * @throws Exception if there was an error
         */
        public static function download_update_archives($for_version = NULL, $download_list = true)
        {
            global $config;

            if ($config['disable_internet_access'])
                return;

            if ($for_version == NULL)
                $for_version = SystemVersion::get_installed_version()->as_string();

            $ini_file = BASE.'/updates/update_list.ini';

            if ($download_list) // download the list of all available updates
            {
                @unlink($ini_file);
                file_put_contents($ini_file, curl_get_data($config['update']['download_base_url'].'update_list.ini'));
            }

            $ini_array = parse_ini_file($ini_file, true);

            $available_updates = isset($ini_array['from_'.$for_version]) ? $ini_array['from_'.$for_version] : array();

            foreach ($available_updates as $to_version => $filename)
            {
                $archive_source = $config['update']['download_base_url'].'archives/'.$filename;
                $archive_checksum = $ini_array['sha1sums'][$filename];
                $archive_target = BASE.'/updates/'.$filename;

                // ONLY FOR DEVELOPMENT: download ALWAYS, even if the checksum is correct
                //if (( ! file_exists($archive_target)) || (sha1_file($archive_target) != $archive_checksum))
                {
                    if (file_exists($archive_target))   // the checksum is not correct, we need to download the file again
                        unlink($archive_target);

                    // download the archive
                    $archive = curl_get_data($archive_source);

                    if ($archive == NULL)
                        throw new Exception('Das Update-Paket "'.$archive_source.'" konnte nicht heruntergeladen werden!');

                    file_put_contents($archive_target, $archive);

                    // check SHA-1 checksum (DISABLED FOR DEVELOPMENT)
                    //if (sha1_file($archive_target) != $archive_checksum)
                    //    throw new Exception('Die Prüfsumme der Datei "'.basename($archive_target).'" ist fehlerhaft!');
                }

                SystemUpdate::download_update_archives(str_replace('to_', '', $to_version), false);
            }
        }

        /*
         * @brief Extract all available update archives in the folder "updates/"
         *
         * @throws Exception if there was an error
         */
        public static function extract_local_update_archives()
        {
            $zip_files = find_all_files(BASE.'/updates/', false, '.zip');

            foreach ($zip_files as $filename)
            {
                if (strpos(basename($filename), 'update_from_') === 0)
                {
                    SystemUpdate::unzip($filename);
                }
            }
        }

        /**
         * @brief Unzip an update archive (*.zip) to the updates directory
         *
         * @param string $zip_filename      the full filename to the zip archive
         *
         * @throws Exception if there was an error
         */
        public static function unzip($zip_filename)
        {
            if ( ! class_exists('ZipArchive'))
                throw new Exception('"ZipArchive" scheint auf Ihrem Server nicht installiert zu sein!');

            // directory, where to extract the files
            $target_directory = str_replace('.zip', '', $zip_filename);

            // if the target directory exists already, remove it (maybe it is outdated...)
            if (is_dir($target_directory))
                rmdir_recursive($target_directory);

            // extract the archive
            $zip = new ZipArchive;
            if ( ! $zip->open($zip_filename))
                throw new Exception('Die ZIP-Datei "'.$zip_filename.'" konnte nicht geöffnet werden!');

            $zip->extractTo(BASE.'/updates/');
            $zip->close();
        }

        /**
         * @brief Get all updates which are available in the updates directory
         *
         * @note this mehtod will NOT return old updates (for versions before the current version)
         *
         * @param string $zip_filename      the full filename to the zip archive
         *
         * @throws Exception if there was an error
         */
        public static function get_all_available_local_updates()
        {
            if (SystemUpdate::$all_available_local_updates == NULL)
            {
                SystemUpdate::$all_available_local_updates = array();
                $current_version = SystemVersion::get_installed_version();

                $xml_files = find_all_files(BASE.'/updates/', true, 'update.xml');

                foreach ($xml_files as $filename)
                {
                    $update = new SystemUpdate($filename);
                    if ( ! $current_version->is_newer_than($update->get_from_version()))
                        SystemUpdate::$all_available_local_updates[] = $update;
                }
            }

            return SystemUpdate::$all_available_local_updates;
        }

        /*
         * @brief Get the best (recommended) update order from version A up to version B
         *
         * Sometimes, there are multiple ways to update the installed system to the latest version.
         *
         * Example:             Get the best way to update from version 0.3.5 up to version 0.6.1
         * Possible ways are:   0.3.5 -> 0.3.6 -> 0.4.0 -> 0.4.1 -> 0.5.0 -> 0.5.1 -> 0.5.2 -> 0.6.0 -> 0.6.1
         *                      0.3.5 -> 0.3.6 -------------------> 0.5.0 -> 0.5.1 -> 0.5.2 -> 0.6.0 -> 0.6.1
         *                      0.3.5 -> 0.3.6 -> 0.4.0 -> 0.4.1 ----------------------------> 0.6.0 -> 0.6.1
         *                      0.3.5 -> 0.3.6 ----------------------------------------------> 0.6.0 -> 0.6.1
         *
         * This method will determine the best way from a specific version to a specific version.
         * The best way is defined as the way with the least count of updates.
         *
         * @param SystemVersion $from_version      The start version
         * @param SystemVersion $to_version        The target version
         *
         * @retval Array    @li An array of SystemUpdate objects in the recommended order for updating
         *                  @li The first array element is always an update for the version "$from_version"
         *                  @li The last array element is always an update up to the version "$to_version"
         *                  @li If there is no way to update from "$from_version" to "$to_version", an empty array will be returned
         *
         * @throws Exception if there was an error
         */
        public static function get_best_update_order(&$from_version, &$to_version)
        {
            $all_available_updates = SystemUpdate::get_all_available_local_updates();
            $update_variants = array();

            foreach ($all_available_updates as $update)
            {
                if ($update->get_from_version()->is_equal_to($from_version)) // catch only updates for version "$from_version"
                {
                    if ($update->get_to_version()->is_equal_to($to_version)) // is this an update from "$from_version" to "$to_version"?
                        $update_variants[] = array($update);
                    else
                    {
                        $next_updates = SystemUpdate::get_best_update_order($update->get_to_version(), $to_version);

                        // did this way reaches the end version $to_version?
                        if ((count($next_updates) > 0) && ($next_updates[count($next_updates)-1]->get_to_version()->is_equal_to($to_version)))
                            $update_variants[] = array_merge(array($update), $next_updates);
                    }
                }
            }

            if (count($update_variants) == 0)
                return array(); // there is no way from $from_update to $to_update

            // search the variant with the least count of updates
            $least_count = count($update_variants[0]);
            $least_count_index = 0;
            foreach ($update_variants as $key => $variant)
            {
                if (count($variant) < $least_count)
                {
                    $least_count = count($variant);
                    $least_count_index = $key;
                }
            }

            return $update_variants[$least_count_index];
        }

    }

?>
