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
use PartDB\Permissions\PermissionManager;

/**
 * @file Footprint.php
 * @brief class Footprint
 *
 * @class Footprint
 * All elements of this class are stored in the database table "footprints".
 * @author kami89
 */
class Footprint extends Base\PartsContainingDBElement implements Interfaces\IAPIModel, Interfaces\ISearchable
{
    const TABLE_NAME = 'footprints';

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /** This creates a new Element object, representing an entry from the Database.
     *
     * @param Database $database reference to the Database-object
     * @param User $current_user reference to the current user which is logged in
     * @param Log $log reference to the Log-object
     * @param integer $id ID of the element we want to get
     * @param array $db_data If you have already data from the database,
     * then use give it with this param, the part, wont make a database request.
     *
     * @throws \PartDB\Exceptions\TableNotExistingException If the table is not existing in the DataBase
     * @throws \PartDB\Exceptions\DatabaseException If an error happening during Database AccessDeniedException
     * @throws \PartDB\Exceptions\ElementNotExistingException If no such element exists in DB.
     */
    protected function __construct(Database &$database, User &$current_user, Log &$log, int $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $data);
    }

    public function getVirtualData(int $virtual_id): array
    {
        $tmp = parent::getVirtualData($virtual_id);
        if ($virtual_id == parent::ID_ROOT_ELEMENT) {
            $tmp['filename'] = '';
        }

        return $tmp;
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the filename of the picture (absolute path from filesystem root)
     *
     * @param bool $absolute If set to true, then the absolute filename (from system root) is returned.
     * If set to false, then the path relative to Part-DB folder is returned.
     * @return string   @li the absolute path to the picture (from filesystem root), as a UNIX path (with slashes)
     * @li an empty string if there is no picture
     */
    public function getFilename(bool $absolute = true) : string
    {
        if ($absolute == true) {
            return str_replace('%BASE%', BASE, $this->db_data['filename']);
        } else {
            return $this->db_data['filename'];
        }
    }

    /**
     *   Get the filename of the 3d model (absolute path from filesystem root)
     *
     * @param bool $absolute If set to true, then the absolute filename (from system root) is returned.
     * If set to false, then the path relative to Part-DB folder is returned.
     *
     * @return string   @li the absolute path to the model (from filesystem root), as a UNIX path (with slashes)
     *                  @li an empty string if there is no model
     */
    public function get3dFilename(bool $absolute = true) : string
    {
        if ($absolute == true) {
            return str_replace('%BASE%', BASE, $this->db_data['filename_3d']);
        } else {
            return $this->db_data['filename_3d'];
        }
    }

    /**
     *  Get all parts from this element
     *
     * @param boolean $recursive                if true, the parts of all subcategories will be listed too
     * @param boolean $hide_obsolete_and_zero   if true, obsolete parts with "instock == 0" will not be returned
     * @param int       $limit                      Limit the number of results, to this value.
     *                                              If set to 0, then the results are not limited.
     * @param int       $page                       Show the results of the page with given number.
     *                                              Use in combination with $limit.
     *
     * @return array        all parts as a one-dimensional array of Part-objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public function getParts(bool $recursive = false, bool $hide_obsolete_and_zero = false, int $limit = 50, int $page = 1) : array
    {
        return $this->getPartsForRowName('id_footprint', $recursive, $hide_obsolete_and_zero, $limit, $page);
    }
    /**
     * Return the number of all parts in this PartsContainingDBElement
     * @param boolean $recursive                if true, the parts of all subcategories will be listed too
     * @return int The number of parts of this PartContainingDBElement
     */
    public function getPartsCount(bool $recursive = false) : int
    {
        return parent::getPartsCountForRowName('id_footprint', $recursive);
    }

    /**
     *  Check if the filename of this footprint is valid (picture exists)
     *
     * This method is used to get all footprints with broken filename
     * (Footprint::get_broken_filename_footprints()).
     *
     * @note An empty filename is a valid filename.
     *
     * @return boolean      @li true if file exists or filename is empty
     *                      @li false if there is no file with this filename
     */
    public function isFilenameValid() : bool
    {
        if (empty($this->getFilename())) {
            return true;
        }

        return file_exists($this->getFilename());
    }

    /**
     *  Check if the filename of this 3d footprint is valid (model exists and have )
     *
     * This method is used to get all footprints with broken 3d filename
     * (Footprint::get_broken_3d_filename_footprints()).
     *
     * @note An empty filename is a valid filename.
     *
     * @return boolean      @li true if file exists or filename is empty
     *                      @li false if there is no file with this filename
     */
    public function is3dFilenameValid() : bool
    {
        if (empty($this->get3dFilename())) {
            return true;
        }

        //Check if file is X3D-Model (these has .x3d extension)
        if (strpos($this->get3dFilename(), '.x3d') == false) {
            return false;
        }

        return file_exists($this->get3dFilename());
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     *  Change the filename of this footprint
     *
     * @note    The filename won't be checked if it is valid.
     *          It's not really a Problem if there is no such file...
     *          (For this purpose we have the method Footprint::get_broken_filename_footprints())
     *
     * @param string $new_filename      @li the new filename (absolute path from filesystem root, as a UNIX path [only slashes!] !! )
     *                                  @li see also lib.functions.php::to_unix_path()
     *
     * @warning     It's really important that you pass the whole (UNIX) path from filesystem root!
     *              If the file is located in the base directory of Part-DB, the base path
     *              will be automatically replaced with a placeholder before write it in the database.
     *              This way, the filenames are still correct if the installation directory
     *              of Part-DB is moved.
     *
     * @note        The path-replacing will be done in Footprint::check_values_validity(), not here.
     *
     * @throws Exception if there was an error
     */
    public function setFilename(string $new_filename)
    {
        $this->setAttributes(array('filename' => $new_filename));
    }

    /**
     *  Change the 3d model filename of this footprint
     * @throws Exception if there was an error
     */
    public function set3dFilename(string $new_filename)
    {
        $this->setAttributes(array('filename_3d' => $new_filename));
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * @copydoc DBElement::check_values_validity()
     * @throws Exception
     */
    public static function checkValuesValidity(Database &$database, User &$current_user, Log &$log, array &$values, bool $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        //For image footprints

        // trim $values['filename']
        $values['filename'] = trim($values['filename']);

        // check if "filename" is a valid (absolute and UNIX) filepath
        //if ((strlen($values['filename']) > 0) && ( ! is_path_absolute_and_unix($values['filename'])))
        //throw new Exception('Der Dateipfad "'.$values['filename'].'" ist kein gültiger absoluter UNIX Dateipfad!');

        // we replace the path of the Part-DB installation directory (Constant "BASE") with a placeholder ("%BASE%")
        $values['filename'] = str_replace(BASE, '%BASE%', $values['filename']);


        //For 3d models

        // trim $values['filename']
        if (isset($values['filename_3d'])) {
            $values['filename_3d'] = trim($values['filename_3d']);

            // check if "filename" is a valid (absolute and UNIX) filepath
            //if ((strlen($values['filename_3d']) > 0) && ( ! is_path_absolute_and_unix($values['filename_3d'])))
            //throw new Exception('Der Dateipfad "'.$values['filename_3d'].'" ist kein gültiger absoluter UNIX Dateipfad!');

            // we replace the path of the Part-DB installation directory (Constant "BASE") with a placeholder ("%BASE%")
            $values['filename_3d'] = str_replace(BASE, '%BASE%', $values['filename_3d']);
        }
    }

    /**
     *  Get all footprints with invalid filenames (file does not exist)
     *
     * @param Database  &$database      reference to the database onject
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log           reference to the Log-object
     *
     * @return Footprint[]    all footprints with broken filename as a one-dimensional
     *                  array of Footprint objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getBrokenFilenameFootprints(Database &$database, User &$current_user, Log &$log) : array
    {
        $broken_filename_footprints = array();
        $root_footprint = Footprint::getInstance($database, $current_user, $log, 0);
        $all_footprints = $root_footprint->getSubelements(true);

        foreach ($all_footprints as $footprint) {
            if (! $footprint->isFilenameValid()) {
                $broken_filename_footprints[] = $footprint;
            }
        }

        return $broken_filename_footprints;
    }


    /**
     *  Get all footprints with invalid filenames (file does not exist or file is not a X3D model)
     *
     * @param Database  &$database      reference to the database onject
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log           reference to the Log-object
     *
     * @return Footprint[]    all footprints with broken filename as a one-dimensional
     *                  array of Footprint objects, sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function getBroken3dFilenameFootprints(Database &$database, User &$current_user, Log &$log) : array
    {
        $broken_filename_footprints = array();
        $root_footprint = Footprint::getInstance($database, $current_user, $log, 0);
        $all_footprints = $root_footprint->getSubelements(true);

        foreach ($all_footprints as $footprint) {
            if (! $footprint->is3dFilenameValid()) {
                $broken_filename_footprints[] = $footprint;
            }
        }

        return $broken_filename_footprints;
    }

    /**
     *  Create a new footprint
     *
     * @param Database  &$database      reference to the database onject
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log           reference to the Log-object
     * @param string    $name           the name of the new footprint (see Footprint::set_name())
     * @param integer   $parent_id      the parent ID of the new footprint (see Footprint::set_parent_id())
     * @param boolean   $filename       the filename of the new footprint (IMPORTANT: see Footprint::set_filename())
     *
     * @warning         You have to use the absolute path from filesystem root for $filename, as a UNIX path (only slashes)!!
     *                  More details: Footprint::set_filename()
     *
     * @return Base\PartsContainingDBElement|Footprint
     *
     * @throws Exception if (this combination of) values is not valid
     * @throws Exception if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(Database &$database, User &$current_user, Log &$log, string $name, int $parent_id, string $filename = '', string $filename_3d = '', string $comment = '')
    {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            array(  'name'      => $name,
                'parent_id' => $parent_id,
                'filename'  => $filename,
                'filename_3d' => $filename_3d,
                'comment' => $comment)
        );
    }

    /**
     * Returns a Array representing the current object.
     * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
     * @return array A array representing the current object.
     * @throws Exception
     */
    public function getAPIArray(bool $verbose = false) : array
    {
        $json =  array( 'id' => $this->getID(),
            'name' => $this->getName(),
            'fullpath' => $this->getFullPath('/'),
            'parentid' => $this->getParentID(),
            'level' => $this->getLevel()
        );

        if ($verbose == true) {
            $ver = array('filename' => $this->getFilename(false),
                'filename_valid' => $this->isFilenameValid(),
                'filename3d' => $this->get3dFilename(),
                'filename3d_valid' => $this->is3dFilenameValid());
            return array_merge($json, $ver);
        }
        return $json;
    }

    /**
     * Gets the permission name for control access to this StructuralDBElement
     * @return string The name of the permission for this StructuralDBElement.
     */
    protected static function getPermissionName() : string
    {
        return PermissionManager::FOOTRPINTS;
    }

    /**
     * Returns the ID as an string, defined by the element class.
     * This should have a form like P000014, for a part with ID 14.
     * @return string The ID as a string;
     */
    public function getIDString(): string
    {
        return 'F' . sprintf('%06d', $this->getID());
    }
}
