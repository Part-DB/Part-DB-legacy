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
use PartDB\Interfaces\ISearchable;

/**
 * @file Manufacturer.php
 * @brief class Manufacturer
 *
 * @class Manufacturer
 *  All elements of this class are stored in the database table "manufacturers".
 * @author kami89
 */
class Manufacturer extends Base\Company implements ISearchable
{
    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     *  Constructor
     *
     * @note  It's allowed to create an object with the ID 0 (for the root element).
     *
     * @param Database  &$database      reference to the Database-object
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log           reference to the Log-object
     * @param integer   $id             ID of the manufacturer we want to get
     *
     * @throws Exception    if there is no such manufacturer in the database
     * @throws Exception    if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, 'manufacturers', $id, $data);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * @brief Get all parts from this manufacturer
     *
     * @param boolean $recursive                if true, the parts of all sub-manufacturers will be listed too
     * @param boolean $hide_obsolete_and_zero   if true, obsolete parts with "instock == 0" will not be returned
     *
     * @return Part[]        all parts as a one-dimensional array of Manufacturer objects,
     *                      sorted by their names
     *
     * @throws Exception if there was an error
     *
     * @see PartsContainingDBElement::getParts()
     */
    public function getParts($recursive = false, $hide_obsolete_and_zero = false)
    {
        return parent::getTableParts('id_manufacturer', $recursive, $hide_obsolete_and_zero);
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * Get count of manufacturers
     *
     * @param Database &$database   reference to the Database-object
     *
     * @return integer              count of manufacturers
     *
     * @throws Exception            if there was an error
     */
    public static function getCount(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        return $database->getCountOfRecords('manufacturers');
    }

    /**
     * @brief Create a new manufacturer
     *
     * @param Database  &$database          reference to the database onject
     * @param User      &$current_user      reference to the current user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param string    $name               the name of the new manufacturer (see Manufacturer::set_name())
     * @param integer   $parent_id          the parent ID of the new manufacturer (see Manufacturer::set_parent_id())
     * @param string    $address            the address of the new manufacturer (see Manufacturer::set_address())
     * @param string    $phone_number       the phone number of the new manufacturer (see Manufacturer::set_phone_number())
     * @param string    $fax_number         the fax number of the new manufacturer (see Manufacturer::set_fax_number())
     * @param string    $email_address      the e-mail address of the new manufacturer (see Manufacturer::set_email_address())
     * @param string    $website            the website of the new manufacturer (see Manufacturer::set_website())
     * @param string    $auto_product_url   the automatic link to the product website (see Company::set_auto_product_url())
     *
     * @return Manufacturer     the new manufacturer
     *
     * @throws Exception    if (this combination of) values is not valid
     * @throws Exception    if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(
        &$database,
        &$current_user,
        &$log,
        $name,
        $parent_id,
        $address = '',
        $phone_number = '',
        $fax_number = '',
        $email_address = '',
        $website = '',
        $auto_product_url = ''
    ) {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            'manufacturers',
            array(  'name'              => $name,
                'parent_id'         => $parent_id,
                'address'           => $address,
                'phone_number'      => $phone_number,
                'fax_number'        => $fax_number,
                'email_address'     => $email_address,
                'website'           => $website,
                'auto_product_url'  => $auto_product_url)
        );
    }

    /**
     * @copydoc NamedDBElement::search()
     */
    public static function search(&$database, &$current_user, &$log, $keyword, $exact_match = false)
    {
        return parent::searchTable($database, $current_user, $log, 'manufacturers', $keyword, $exact_match);
    }
}
