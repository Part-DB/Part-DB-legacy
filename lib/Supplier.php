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
 * @file Supplier.php
 * @brief class Supplier
 *
 * @class Supplier
 * All elements of this class are stored in the database table "suppliers".
 * @author kami89
 */
class Supplier extends Base\Company implements ISearchable
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
     * @param integer   $id             ID of the supplier we want to get
     *
     * @throws Exception    if there is no such supplier in the database
     * @throws Exception    if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $id, $data = null)
    {
        parent::__construct($database, $current_user, $log,'suppliers', $id, $data);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     *  Get all parts from this element
     *
     * @param boolean $recursive                if true, the parts of all sub-suppliers will be listed too
     * @param boolean $hide_obsolete_and_zero   if true, obsolete parts with "instock == 0" will not be returned
     *
     * @return array        all parts in a one-dimensional array of Part objects
     *
     * @throws Exception    if there was an error
     */
    public function getParts($recursive = false, $hide_obsolete_and_zero = false)
    {
        if (! is_array($this->parts)) {
            $this->parts = array();

            $query =    'SELECT part_id FROM orderdetails '.
                'LEFT JOIN parts ON parts.id=orderdetails.part_id '.
                'WHERE id_supplier=? '.
                'GROUP BY part_id ORDER BY parts.name';

            $query_data = $this->database->query($query, array($this->getID()));

            foreach ($query_data as $row) {
                $this->parts[] = new Part($this->database, $this->current_user, $this->log, $row['part_id']);
            }
        }

        $parts = $this->parts;

        if ($hide_obsolete_and_zero) {
            // remove obsolete parts from array
            $parts = array_values(array_filter($parts, function ($part) {
                /** @var Part $part */
                return ((! $part->getObsolete()) || ($part->getInstock() > 0));
            }));
        }

        if ($recursive) {
            $sub_suppliers = $this->getSubelements(true);

            foreach ($sub_suppliers as $sub_supplier) {
                $parts = array_merge($parts, $sub_supplier->getParts(false, $hide_obsolete_and_zero));
            }
        }

        return $parts;
    }

    /**
     *  Get all parts from this element
     *
     * @param boolean $recursive        if true, the parts of all sub-suppliers will be listed too
     *
     * @return array        all parts in a one-dimensional array of Part objects
     *
     * @throws Exception    if there was an error
     */
    public function getCountOfPartsToOrder()
    {
        $query =    'SELECT COUNT(*) as count FROM parts '.
            'LEFT JOIN device_parts ON device_parts.id_part = parts.id '.
            'LEFT JOIN devices ON devices.id = device_parts.id_device '.
            'LEFT JOIN orderdetails ON orderdetails.id = parts.order_orderdetails_id '.
            'WHERE ((parts.instock < parts.mininstock) OR (parts.manual_order != false) '.
            'OR ((devices.order_quantity > 0) '.
            'AND ((devices.order_only_missing_parts = false) '.
            'OR (parts.instock - device_parts.quantity * devices.order_quantity < parts.mininstock)))) '.
            'AND (parts.order_orderdetails_id IS NOT NULL) '.
            'AND (orderdetails.id_supplier = ?)';

        $query_data = $this->database->query($query, array($this->getID()));

        return $query_data[0]['count'];
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     *  Get count of suppliers
     *
     * @param Database &$database   reference to the Database-object
     *
     * @return integer              count of suppliers
     *
     * @throws Exception            if there was an error
     */
    public static function getCount(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        return $database->getCountOfRecords('suppliers');
    }

    /**
     *  Get all suppliers which have parts to order
     *
     * @note    This method will only return suppliers, which have parts to order and
     *          which have an supplier selected for the order. Parts, which should be
     *          ordered, but have "order_supplier_id == 0" will be ignored!
     *
     * @param Database  &$database          reference to the database object
     * @param User      &$current_user      reference to the user which is logged in
     * @param Log       &$log               reference to the Log-object
     *
     * @return array    all suppliers as a one-dimensional array of Supplier objects,
     *                  sorted by their count of parts to order (desc.)
     *
     * @throws Exception if there was an error
     *
     * @todo Check if the SQL query works correctly! It's a quite complicated query...
     */
    public static function getOrderSuppliers(&$database, &$current_user, &$log)
    {
        if (!$database instanceof Database) {
            throw new Exception('$database ist kein Database-Objekt!');
        }

        $suppliers = array();

        $query =    'SELECT orderdetails.id_supplier, COUNT(*) as count FROM parts '.
            'LEFT JOIN device_parts ON device_parts.id_part = parts.id '.
            'LEFT JOIN devices ON devices.id = device_parts.id_device '.
            'LEFT JOIN orderdetails ON orderdetails.id = parts.order_orderdetails_id '.
            'WHERE ((parts.instock < parts.mininstock) OR (parts.manual_order != false) '.
            'OR ((devices.order_quantity > 0) '.
            'AND ((devices.order_only_missing_parts = false) '.
            'OR (parts.instock - device_parts.quantity * devices.order_quantity < parts.mininstock)))) '.
            'AND parts.order_orderdetails_id IS NOT NULL '.
            'GROUP BY orderdetails.id_supplier '.
            'ORDER BY count DESC';

        $query_data = $database->query($query);

        foreach ($query_data as $row) {
            $suppliers[] = new Supplier($database, $current_user, $log, $row['id_supplier']);
        }

        return $suppliers;
    }

    /**
     *  Create a new supplier
     *
     * @param Database  &$database          reference to the database onject
     * @param User      &$current_user      reference to the current user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param string    $name               the name of the new supplier (see Supplier::set_name())
     * @param integer   $parent_id          the parent ID of the new supplier (see Supplier::set_parent_id())
     * @param string    $address            the address of the new supplier (see Supplier::set_address())
     * @param string    $phone_number       the phone number of the new supplier (see Supplier::set_phone_number())
     * @param string    $fax_number         the fax number of the new supplier (see Supplier::set_fax_number())
     * @param string    $email_address      the e-mail address of the new supplier (see Supplier::set_email_address())
     * @param string    $website            the website of the new supplier (see Supplier::set_website())
     * @param string    $auto_product_url   the automatic link to the product website (see Company::set_auto_product_url())
     *
     * @return Supplier     the new supplier
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
            'suppliers',
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
        return parent::searchTable($database, $current_user, $log, 'suppliers', $keyword, $exact_match);
    }
}
