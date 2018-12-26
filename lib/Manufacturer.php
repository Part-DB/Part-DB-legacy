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
use PartDB\Permissions\PermissionManager;

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
    const TABLE_NAME = "manufacturers";

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
    public function __construct(Database &$database, User &$current_user, Log &$log, int $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $data);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

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
        return parent::getPartsForRowName('id_manufacturer', $recursive, $hide_obsolete_and_zero, $limit, $page);
    }
    /**
     * Return the number of all parts in this PartsContainingDBElement
     * @param boolean $recursive                if true, the parts of all subcategories will be listed too
     * @return int The number of parts of this PartContainingDBElement
     */
    public function getPartsCount(bool $recursive = false) : int
    {
        return parent::getPartsCountForRowName($recursive, 'id_manufacturer');
    }


    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

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
     * @return Base\Company|Manufacturer
     *
     * @throws Exception    if (this combination of) values is not valid
     * @throws Exception    if there was an error
     *
     * @see DBElement::add()
     */
    public static function add(
        Database &$database,
        User &$current_user,
        Log &$log,
        string $name,
        int $parent_id,
        string $address = '',
        string $phone_number = '',
        string $fax_number = '',
        string $email_address = '',
        string $website = '',
        string $auto_product_url = '',
        string $comment = ""
    ) : Manufacturer {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            array(  'name'              => $name,
                'parent_id'         => $parent_id,
                'address'           => $address,
                'phone_number'      => $phone_number,
                'fax_number'        => $fax_number,
                'email_address'     => $email_address,
                'website'           => $website,
                'auto_product_url'  => $auto_product_url,
                "comment"           => $comment)
        );
    }


    /**
     * Returns the ID as an string, defined by the element class.
     * This should have a form like P000014, for a part with ID 14.
     * @return string The ID as a string;
     */
    public function getIDString(): string
    {
        return "M" . sprintf("%06d", $this->getID());
    }

    /**
     * @copydoc NamedDBElement::search()
     * @throws Exception
     */
    public static function search(Database &$database, User &$current_user, Log &$log, string $keyword, bool $exact_match = false) : array
    {
        return parent::searchTable($database, $current_user, $log, 'manufacturers', $keyword, $exact_match);
    }

    /**
     * Gets the permission name for control access to this StructuralDBElement
     * @return string The name of the permission for this StructuralDBElement.
     */
    protected static function getPermissionName() : string
    {
        return PermissionManager::MANUFACTURERS;
    }
}
