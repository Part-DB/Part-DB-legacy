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
        2012-09-27  kami89          - added doxygen comments
*/

    /**
     * @file class.Footprint.php
     * @brief class Footprint
     *
     * @class Footprint
     * @brief All elements of this class are stored in the database table "footprints".
     * @author kami89
     */
    class Footprint extends PartsContainingDBElement
    {
        /********************************************************************************
        *
        *   Constructor / Destructor / reset_attributes()
        *
        *********************************************************************************/

        /**
         * @brief Constructor
         *
         * @note  It's allowed to create an object with the ID 0 (for the root element).
         *
         * @param Database  &$database      reference to the Database-object
         * @param User      &$current_user  reference to the current user which is logged in
         * @param Log       &$log           reference to the Log-object
         * @param integer   $id             ID of the footprint we want to get
         *
         * @throws Exception if there is no such footprint
         * @throws Exception if there was an error
         */
        public function __construct(&$database, &$current_user, &$log, $id)
        {
            parent::__construct($database, $current_user, $log, 'footprints', $id);

            if ($id == 0)
            {
                // this is the root node
                $this->db_data['filename'] = '';
                return;
            }
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get the filename
         *
         * @param boolean $hide_document_root   @li if true, and the filename begins with the document root
         *                                          (constant DOCUMENT_ROOT), this part of the path will be removed.
         *                                          This is useful if you use the returned path directly for HTML output.
         *                                      @li if false, the whole path from filesystem root will be returned.
         * @param boolean $hide_base_relative   If "$hide_document_root == true" and "$hide_base_relative == true",
         *                                      this method will return the path from the base path of the installation
         *                                      directory of Part-DB (without the relative path from document root!).
         *
         * @note    if "$hide_document_root == true" and the file is outside the document root,
         *          this method will return the whole path, like "$hide_document_root == false".
         *
         * @retval string   @li if "$hide_document_root == true": filename, related to the document root.
         *                      Example: "/part-db/img/foo.png" if the filename is "/var/www/part-db/img/foo.png"
         *                  @li if "$hide_document_root == false": the whole filename,
         *                      like "/var/www/part-db/img/foo.png"
         */
        public function get_filename($hide_document_root = true, $hide_base_relative = false)
        {
            if ($hide_document_root)
            {
                if ($hide_base_relative)
                    return str_replace('%BASE%/', '', $this->db_data['filename']);
                else
                    return str_replace('%BASE%/', BASE_RELATIVE, $this->db_data['filename']);
            }
            else
                return str_replace('%BASE%', BASE, $this->db_data['filename']);
        }

        /**
         * @brief Get all parts which have this footprint
         *
         * @param boolean $recursive    if true, the parts of all sub-footprints will be listed too
         *
         * @retval array        all parts as a one-dimensional array of Part objects
         *
         * @throws Exception    if there was an error
         *
         * @see PartsContainingDBElement::get_parts()
         */
        public function get_parts($recursive = false)
        {
            return parent::get_parts('id_footprint', $recursive);
        }

        /**
         * @brief Check if the filename of this footprint is valid (picture exists)
         *
         * This method is used to get all footprints with broken filename
         * (Footprint::get_broken_filename_footprints()).
         *
         * @note An empty filename is a valid filename.
         *
         * @retval boolean      @li true if file exists or filename is empty
         *                      @li false if there is no file with this filename
         */
        public function is_filename_valid()
        {
            if (strlen($this->get_filename(false)) == 0)
                return true;

            return file_exists($this->get_filename(false));
        }

        /********************************************************************************
        *
        *   Setters
        *
        *********************************************************************************/

        /**
         * @brief Change the filename of this footprint
         *
         * @note    The filename won't be checked if it is valid.
         *          It's not really a Problem if there is no such file...
         *          (For this purpose we have the method Footprint::get_broken_filename_footprints())
         *
         * @param string $new_filename      the new filename (absolute path from filesystem root!!)
         *
         * @warning     It's really important that you pass the whole path from filesystem root!
         *              If the file is located in the base directory of Part-DB, the base path
         *              will be automatically replaced with a placeholder before write it in the database.
         *              This way, the filenames are still correct if the installation directory
         *              of Part-DB is moved.
         *
         * @note        The path-replacing will be done in Footprint::check_values_validity(), not here.
         *
         * @throws Exception if there was an error
         */
        public function set_filename($new_filename)
        {
            $this->set_attributes(array('filename' => $new_filename));
        }

        /********************************************************************************
        *
        *   Static Methods
        *
        *********************************************************************************/

        /**
         * @copydoc DBElement::check_values_validity()
         */
        public static function check_values_validity(&$database, &$current_user, &$log, &$values, $is_new, &$element = NULL)
        {
            // first, we let all parent classes to check the values
            parent::check_values_validity($database, $current_user, $log, $values, $is_new, $element);

            // we will replace the base path from the filename with a placeholder (see Footprint::set_filename())
            if (is_string($values['filename']) && (strlen($values['filename']) > 0))
            {
                // first, we convert the filename in the absolute filepath from filesystem root
                // (but you shouldn't use relative paths anyway because it could give problems...)
                if (strpos($values['filename'], BASE.DIRECTORY_SEPARATOR) === false)
                {
                    $filename_absolute = realpath(str_replace(BASE_RELATIVE, '', $values['filename']));
                    if ($filename_absolute != false)
                        $values['filename'] = $filename_absolute;
                    else
                        debug('warning', 'realpath('.$filename_absolute.') == FALSE! [Footprint ID '.$values['id'].']', __FILE__, __LINE__, __METHOD__);
                }
            }
            // and then we replace the path of the Part-DB installation directory with a placeholder
            $values['filename'] = str_replace(BASE, '%BASE%', trim($values['filename']));
        }

        /**
         * @brief Get count of footprints
         *
         * @param Database &$database   reference to the Database-object
         *
         * @retval integer              count of footprints
         *
         * @throws Exception            if there was an error
         */
        public static function get_count(&$database)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            return $database->get_count_of_records('footprints');
        }

        /**
         * @brief Get all footprints with invalid filenames (file does not exist)
         *
         * @param Database  &$database      reference to the database onject
         * @param User      &$current_user  reference to the current user which is logged in
         * @param Log       &$log           reference to the Log-object
         *
         * @retval array    all footprints with broken filename as a one-dimensional
         *                  array of Footprint objects, sorted by their names
         *
         * @throws Exception if there was an error
         */
        public static function get_broken_filename_footprints(&$database, &$current_user, &$log)
        {
            $broken_filename_footprints = array();
            $root_footprint = new Footprint($database, $current_user, $log, 0);
            $all_footprints = $root_footprint->get_subelements(true);

            foreach ($all_footprints as $footprint)
            {
                if ( ! $footprint->is_filename_valid())
                    $broken_filename_footprints[] = $footprint;
            }

            return $broken_filename_footprints;
        }

        /**
         * @brief Create a new footprint
         *
         * @param Database  &$database      reference to the database onject
         * @param User      &$current_user  reference to the current user which is logged in
         * @param Log       &$log           reference to the Log-object
         * @param string    $name           the name of the new footprint (see Footprint::set_name())
         * @param integer   $parent_id      the parent ID of the new footprint (see Footprint::set_parent_id())
         * @param boolean   $filename       the filename of the new footprint (see Footprint::set_filename())
         *
         * @warning         You should use the absolute path from filesystem root for $filename!!
         *                  More details: Footprint::set_filename()
         *
         * @retval Footprint    the new footprint
         *
         * @throws Exception if (this combination of) values is not valid
         * @throws Exception if there was an error
         *
         * @see DBElement::add()
         */
        public static function add(&$database, &$current_user, &$log, $name, $parent_id, $filename = '')
        {
            return parent::add($database, $current_user, $log, 'footprints',
                                array(  'name'      => $name,
                                        'parent_id' => $parent_id,
                                        'filename'  => $filename));
        }

        /**
         * @copydoc NamedDBElement::search()
         */
        public static function search(&$database, &$current_user, &$log, $keyword, $exact_match = false)
        {
            return parent::search($database, $current_user, $log, 'footprints', $keyword, $exact_match);
        }

    }

?>
