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
use PartDB\Tools\SystemVersion;

/**
 * @file System.php
 * @brief class System
 *
 * @class System
 * @brief Class System
 *
 * This class is used for managing system versions and system updates.
 *
 * @author kami89
 *
 * @todo system updates are not implemented yet
 */
class System
{
    /********************************************************************************
     *
     *   Attributes
     *
     *********************************************************************************/

    /** @var Database the Database object for the database access of the logs */
    private $database                   = null;
    /** @var Log the Log object for logging */
    private $log                        = null;

    /** @var SystemVersion the installed version */
    private $installed_version          = null;

    /********************************************************************************
     *
     *   Constructor / Destructor
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @param Database  &$database      reference to the Database object
     * @param Log       &$log           reference to the Log object
     *
     * @throws Exception if there was an error
     */
    public function __construct(&$database, &$log)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        if (!$log instanceof Log) {
            throw new Exception('$log ist kein Log-Objekt!');
        }

        $this->database = $database;
        $this->log = $log;

        // get the installed version
        $this->installed_version = SystemVersion::getInstalledVersion();

        debug(
            'hint',
            'System initialisiert: Version '.$this->getInstalledVersion()->asString(false, false, false, true),
            __FILE__,
            __LINE__,
            __METHOD__
        );
    }

    /********************************************************************************
     *
     *   System Versions
     *
     *********************************************************************************/

    /**
     *  Get the installed version of the system
     *
     * @return SystemVersion      the installed version
     *
     * @see SystemVersion::getInstalledVersion()
     */
    public function getInstalledVersion()
    {
        return $this->installed_version;
    }

    /**
     *  Get the latest version which is available
     *
     * @param string $type          the version type ('stable' or 'unstable')
     *
     * @return SystemVersion        the latest available version
     *
     * @throws Exception if there was an error
     *
     * @see SystemVersion::getLatestVersion()
     */
    public function getLatestVersion($type)
    {
        return SystemVersion::getLatestVersion($type);
    }

    /********************************************************************************
     *
     *   System Updates
     *
     *********************************************************************************/

    /*
     * Check if a system update is available
     *
     * Return:
     *      true:   if update is required
     *      false:  if we have the latest version
     */
    /*public function is_update_available()
    {

    }*/

    /*
     *  Download and extract all available update archives to the folder "updates/"
     *
     * @throws Exception if there was an error
     *
     * @todo ...not finished...
     */
    public function downloadAndExtractUpdateArchives()
    {
        /*if ( ! class_exists('ZipArchive'))
            throw new Exception('"ZipArchive" scheint nicht installiert zu sein!');

        $current = $this->get_installed_version();

        // we will also download (not install!) unstable versions,
        // even if the user only wants stable versions
        $latest = SystemVersion::get_latest_version('unstable');

        while ($latest->is_newer_than($current) || $latest->as_string() == $current->as_string())
        {
            // get the list of all update-pack download-links
            $downloadlinks = curl_get_data('http://kami89.myparts.info/updates/downloadlinks.ini');
            $ini_array = parse_ini_string($downloadlinks, true);

            if ( ! isset($ini_array['update_for_'.$current->as_string()]))
                break; // no update available

            $archive_source = $ini_array['update_for_'.$current->as_string()]['download_link'];
            $archive_checksum = $ini_array['update_for_'.$current->as_string()]['checksum'];
            $archive_target = BASE.'/updates/'.basename($archive_source);

            if (file_exists($archive_target)) // TODO: only delete it, if the checksum is not the same
                unlink($archive_target);

            // download the archive
            $archive = curl_get_data($archive_source);
            file_put_contents($archive_target, $archive);

            // TODO: check if the md5 sum is correct!

            $directory = BASE.'/updates/update_for_'.$current->as_string();
            if (is_dir($directory))
                rmdir_recursive($directory);

            // extract the archive
            $zip = new ZipArchive;
            if ($zip->open($archive_target) === TRUE)
            {
                $zip->extractTo(BASE.'/updates/');
                $zip->close();
            }

            unlink($archive_target);

            // check what version we have downloaded
            $dom = new DOMDocument('1.0', 'utf-8');
            $success = $dom->load($directory.'/info.xml', LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NOBLANKS);

            if ( ! $success)
                throw new Exception('Das DOMDocument-Objekt konnte nicht erstellt werden!');

            $xpath = new DomXPath($dom);
            $version_nodes = $xpath->query('/update_info/header/version');
            if ($version_nodes->length != 1)
                throw new Exception('Die Version wurde nicht gefunden in der XML-Datei!');

            $next_version = $version_nodes->item(0)->nodeValue;

            if ($current->as_string() == $next_version)
                throw new Exception('Unendliche Schleife in Updates entdeckt!');

            $current = new SystemVersion($next_version);
        }*/
    }

    /*
     *  Update the system to the newest (or another) version
     *
     * @param string $to_version    @li The version string of the version we want to update
     *                              @li Or pass an empty string to update to the latest version
     *                                  (depends on the update type [stable, unstable] in the config.php)
     *                              @li @see SystemVersion::_construct()
     *
     * @throws Exception if there was an error
     *
     * @todo ...not finished...
     */
    public function update($to_version = '')
    {
        /*global $config;

        $current = $this->get_installed_version();

        if (strlen($to_version) > 0)
            $latest = new SystemVersion($to_version);
        else
            $latest = $this->get_latest_version($config['update']['type']);

        while ($latest->is_newer_than($current))
        {
            $last_version_string = $current->as_string();
            $this->update_to_next_version();
            $current = $this->get_installed_version();

            if ($current->as_string() == $last_version_string)
                throw new Exception('Das Update schlug fehl (unbekannte Ursache)!');
        }*/
    }

    /*
     *  Update the system to the next highter version
     *
     * @throws Exception if there was an error
     *
     * @todo ...not finished...
     */
    public function updateToNextVersion()
    {
        /*$current_version = $this->get_installed_version();
        $update_folder = BASE.'/updates/update_for_'.$current_version->as_string().'/';

        // check validity of update
        $dom = new DOMDocument('1.0', 'utf-8');
        $success = $dom->load($update_folder.'info.xml', LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NOBLANKS);

        if ( ! $success)
            throw new Exception('Das DOMDocument-Objekt konnte nicht erstellt werden!');

        $xpath = new DomXPath($dom);
        $version_nodes = $xpath->query('/update_info/header/version');
        $required_version_nodes = $xpath->query('/update_info/header/required_version');
        if (($version_nodes->length != 1) || ($required_version_nodes->length != 1))
            throw new Exception('Die (vorausgesetzte) Version wurde nicht gefunden in der XML-Datei!');

        if ($current_version->as_string() != $required_version_nodes->item(0)->nodeValue)
            throw new Exception('Das Update ist nicht für die installierte Version bestimmt!');

        $next_version = new SystemVersion($version_nodes->item(0)->nodeValue);*/

        // TODO: make backup of database!!

        // make backup of files
        /*$files_to_backup = array();
        foreach ($instructions as $instruction)
        {
            $type = $instruction['type'];

            switch ($type)
            {
                case 'file':
                    switch ($instruction['action'])
                    {
                        case 'copy':
                            if (isset($instruction['source']))
                                $files_to_backup[] = $instruction['source'];
                            break;

                        case 'rename':
                            $files_to_backup[] = $instruction;
                            break;

                        case 'delete':
                            $files_to_backup[] = $instruction;
                            break;
                    }
                    break;

                default:
                    // TODO
            }
        }*/

        //$error = false;

        /*foreach ($files_to_backup as $old_filename)
            copy($old_filename, $old_filename.'.backup');*/

        // execute the update instructions
        /*foreach ($instructions as $instruction)
        {
            $type = $instruction['type'];

            switch ($type)
            {
                case 'file':
                    switch ($instruction['action'])
                    {
                        case 'copy':
                            if (isset($instruction['source']))
                                $source = $instruction['source'];
                            else
                                $source = $update_folder.'files/'.$instruction;
                            $target = $instruction;

                            if (isset($instruction['md5']))
                            {
                                if (md5_file($source) != $instruction['md5'])
                                    return false;
                            }

                            copy($source, $target);

                            break;

                        case 'rename':
                            break;

                        case 'delete':
                            break;

                        default:
                            break;
                    }
                    break;

                case 'sql':
                    // TODO
                    break;

                case 'custom':
                    // TODO
                    break;

                default:
                    // TODO
            }
        }*/

        /*if ($error)
        {
            // restore files
            //foreach ($files_to_backup as $old_filename)
            //    copy($old_filename.'.backup', $old_filename);

            // TODO: restore database backup

            throw new Exception('Es gab ein Fehler beim Update!');
        }
        else
        {
            $this->installed_version = SystemVersion::get_installed_version();

            // cleanup backup files
            //foreach($files_to_backup as $old_filename)
            //    unlink($old_filename.'.backup');
        }*/
    }
}
