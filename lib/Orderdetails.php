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

/**
 * @file Orderdetails.php
 * @brief class Orderdetails

 * @class Orderdetails
 * All elements of this class are stored in the database table "orderdetails".
 *
 * One Orderdetails-object includes these things:
 *  - 1 supplier (this is always required, you cannot have orderdetails without a supplier!)
 *  - 0..1 supplier-part-nr. (empty string means "no part-nr")
 *  - 0..* Pricedetails
 *
 * A Part can have more than one Orderdetails-object, which can have more than one Pricedetails-objects.
 *
 * @author kami89
 */
class Orderdetails extends Base\DBElement implements Interfaces\IAPIModel
{
    /********************************************************************************
     *
     *   Calculated Attributes
     *
     *   Calculated attributes will be NULL until they are requested for first time (to save CPU time)!
     *   After changing an element attribute, all calculated data will be NULLed again.
     *   So: the calculated data will be cached.
     *
     *********************************************************************************/

    /** @var Part the part of this orderdetails */
    private $part           = null;
    /** @var Supplier the supplier of this orderdetails */
    private $supplier       = null;
    /** @var array all pricedetails of this orderdetails, as a one-dimensional array of Pricedetails objects */
    private $pricedetails   = null;

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * @brief Constructor
     *
     * @param Database  &$database      reference to the Database-object
     * @param User      &$current_user  reference to the current user which is logged in
     * @param Log       &$log           reference to the Log-object
     * @param integer   $id             ID of the orderdetails we want to get
     *
     * @throws Exception    if there is no such orderdetails record in the database
     * @throws Exception    if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $id, $data = null)
    {
        parent::__construct($database, $current_user, $log, 'orderdetails', $id, false, $data);
    }

    /**
     * @copydoc DBElement::reset_attributes()
     */
    public function resetAttributes($all = false)
    {
        $this->part             = null;
        $this->supplier         = null;
        $this->pricedetails     = null;

        parent::resetAttributes($all);
    }

    /********************************************************************************
     *
     *   Basic Methods
     *
     *********************************************************************************/

    /**
     * Delete this orderdetails incl. all their pricedetails
     *
     * @throws Exception if there was an error
     */
    public function delete()
    {
        try {
            $transaction_id = $this->database->beginTransaction(); // start transaction

            // Delete all Pricedetails
            $all_pricedetails = array_reverse($this->getPricedetails()); // the last one must be deleted first!
            $this->resetAttributes(); // set $this->pricedetails to NULL
            foreach ($all_pricedetails as $pricedetails) {
                /** @var Pricedetails $pricedetails */
                $pricedetails->delete();
            }

            // Check if this Orderdetails is the Part's selected Orderdetails for ordering and delete this reference if neccessary
            $order_orderdetails = $this->getPart()->getOrderOrderdetails();
            if (is_object($order_orderdetails) && ($order_orderdetails->getID() == $this->getID())) {
                $this->getPart()->setOrderOrderdetailsID(null);
            } else {
                $this->getPart()->setAttributes(array());
            } // save part attributes to update its "last_modified"

            // now we can delete this orderdetails
            parent::delete();

            $this->database->commit($transaction_id); // commit transaction
        } catch (Exception $e) {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->resetAttributes();

            throw new Exception("Die Einkaufsinformationen konnten nicht gelöscht werden!\nGrund: " . $e->getMessage());
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the part
     *
     * @return Part     the part of this orderdetails
     *
     * @throws Exception if there was an error
     */
    public function getPart()
    {
        if (! is_object($this->part)) {
            $this->part = new Part(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['part_id']
            );
        }

        return $this->part;
    }

    /**
     * Get the supplier
     *
     * @return Supplier     the supplier of this orderdetails
     *
     * @throws Exception if there was an error
     */
    public function getSupplier()
    {
        if (! is_object($this->supplier)) {
            $this->supplier = new Supplier(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['id_supplier']
            );
        }

        return $this->supplier;
    }

    /**
     * Get the supplier part-nr.
     *
     * @return string       the part-nr.
     */
    public function getSupplierPartNr()
    {
        return $this->db_data['supplierpartnr'];
    }

    /**
     * Get if this orderdetails is obsolete
     *
     * "Orderdetails is obsolete" means that the part with that supplier-part-nr
     * is no longer available from the supplier of that orderdetails.
     *
     * @return boolean      @li true if this part is obsolete at that supplier
     *                      @li false if this part isn't obsolete at that supplier
     */
    public function getObsolete()
    {
        return $this->db_data['obsolete'];
    }

    /**
     * Get the link to the website of the article on the suppliers website
     *
     * @return string           the link to the article
     */
    public function getSupplierProductUrl()
    {
        if (strlen($this->db_data['supplier_product_url']) > 0) {
            return $this->db_data['supplier_product_url'];
        } else {
            return $this->getSupplier()->getAutoProductUrl($this->db_data['supplierpartnr']);
        } // maybe an automatic url is available...
    }

    /**
     * Get all pricedetails
     *
     * @return Pricedetails[]    all pricedetails as a one-dimensional array of Pricedetails objects,
     *                  sorted by minimum discount quantity
     *
     * @throws Exception if there was an error
     */
    public function getPricedetails()
    {
        if (! is_array($this->pricedetails)) {
            $this->pricedetails = array();

            $query = 'SELECT * FROM pricedetails '.
                'WHERE orderdetails_id=? '.
                'ORDER BY min_discount_quantity ASC';

            $query_data = $this->database->query($query, array($this->getID()));

            foreach ($query_data as $row) {
                $this->pricedetails[] = new Pricedetails($this->database, $this->current_user, $this->log, $row['id'], $row);
            }
        }

        return $this->pricedetails;
    }

    /**
     * Get the price for a specific quantity
     *
     * @param boolean $as_money_string      @li if true, this method returns a money string incl. currency
     *                                      @li if false, this method returns the price as float
     * @param integer       $quantity       this is the quantity to choose the correct pricedetails
     * @param integer|NULL  $multiplier     @li This is the multiplier which will be applied to every single price
     *                                      @li If you pass NULL, the number from $quantity will be used
     *
     * @return float|null|string    float: the price as a float number (if "$as_money_string == false")
     * * null: if there are no prices and "$as_money_string == false"
     * * string:   the price as a string incl. currency (if "$as_money_string == true")
     *
     * @throws Exception if there are no pricedetails for the choosed quantity
     *          (for example, there are only one pricedetails with the minimum discount quantity '10',
     *          but the choosed quantity is '5' --> the price for 5 parts is not defined!)
     * @throws Exception if there was an error
     *
     * @see floatToMoneyString()
     */
    public function getPrice($as_money_string = false, $quantity = 1, $multiplier = null)
    {
        if (($quantity == 0) && ($multiplier === null)) {
            if ($as_money_string) {
                return floatToMoneyString(0);
            } else {
                return 0;
            }
        }

        $all_pricedetails = $this->getPricedetails();

        if (count($all_pricedetails) == 0) {
            if ($as_money_string) {
                return floatToMoneyString(null);
            } else {
                return null;
            }
        }

        foreach ($all_pricedetails as $pricedetails) {
            // choose the correct pricedetails for the choosed quantity ($quantity)
            if ($quantity < $pricedetails->getMinDiscountQuantity()) {
                break;
            }

            $correct_pricedetails = $pricedetails;
        }

        if ((! isset($correct_pricedetails)) || (! is_object($correct_pricedetails))) {
            throw new Exception('Es sind keine Preisinformationen für die angegebene Bestellmenge vorhanden!');
        }

        if ($multiplier === null) {
            $multiplier = $quantity;
        }

        return $correct_pricedetails->getPrice($as_money_string, $multiplier);
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     * Set the supplier ID
     *
     * @param integer $new_supplier_id       the ID of the new supplier
     *
     * @throws Exception if the new supplier ID is not valid
     * @throws Exception if there was an error
     */
    public function setSupplierId($new_supplier_id)
    {
        $this->setAttributes(array('id_supplier' => $new_supplier_id));
    }

    /**
     * Set the supplier part-nr.
     *
     * @param string $new_supplierpartnr       the new supplier-part-nr
     *
     * @throws Exception if there was an error
     */
    public function setSupplierpartnr($new_supplierpartnr)
    {
        $this->setAttributes(array('supplierpartnr' => $new_supplierpartnr));
    }

    /**
     * Set if the part is obsolete at the supplier of that orderdetails
     *
     * @param boolean $new_obsolete       true means that this part is obsolete
     *
     * @throws Exception if there was an error
     */
    public function setObsolete($new_obsolete)
    {
        $this->setAttributes(array('obsolete' => $new_obsolete));
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * @copydoc DBElement::check_values_validity()
     */
    public static function checkValuesValidity(&$database, &$current_user, &$log, &$values, $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        // set the datetype of the boolean attributes
        settype($values['obsolete'], 'boolean');

        // check "part_id"
        try {
            $part = new Part($database, $current_user, $log, $values['part_id']);
            $part->setAttributes(array()); // save part attributes to update its "last_modified"
        } catch (Exception $e) {
            debug(
                'error',
                'Ungültige "part_id": "'.$values['part_id'].'"'.
                "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Das gewählte Bauteil existiert nicht!');
        }

        // check "id_supplier"
        try {
            if ($values['id_supplier'] < 1) {
                throw new Exception('id_supplier < 1');
            }

            $supplier = new Supplier($database, $current_user, $log, $values['id_supplier']);
        } catch (Exception $e) {
            debug(
                'error',
                'Ungültige "id_supplier": "'.$values['id_supplier'].'"'.
                "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception('Der gewählte Lieferant existiert nicht!');
        }
    }

    /**
     * @Create a new orderdetails record
     *
     * @param Database  &$database          reference to the database onject
     * @param User      &$current_user      reference to the current user which is logged in
     * @param Log       &$log               reference to the Log-object
     * @param integer   $part_id            the ID of the part with that the orderdetails is associated
     * @param integer   $supplier_id        the ID of the supplier (see Orderdetails::set_supplier_id())
     * @param string    $supplierpartnr     the supplier-part-nr (see Orderdetails::set_supplierpartnr())
     * @param boolean   $obsolete           the obsolete attribute of the new orderdetails (see Orderdetails::set_obsolete())
     *
     * @return Orderdetails     the new orderdetails object
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
        $part_id,
        $supplier_id,
        $supplierpartnr = '',
        $obsolete = false
    ) {
        return parent::addByArray(
            $database,
            $current_user,
            $log,
            'orderdetails',
            array(  'part_id'                   => $part_id,
                'id_supplier'               => $supplier_id,
                'supplierpartnr'            => $supplierpartnr,
                'obsolete'                  => $obsolete)
        );
    }

    /**
     * Returns a Array representing the current object.
     * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
     * @return array A array representing the current object.
     */
    public function getAPIArray($verbose = false)
    {
        $json =  array( "id" => $this->getID(),
            "supplierpartnr" => $this->getSupplierPartNr()
        );

        if ($verbose == true) {
            $ver = array("supplier" => $this->getSupplier()->getAPIArray(),
                "obsolete" => $this->getObsolete() == true,
                "supplier_product_url" => $this->getSupplierProductUrl(),
                "pricedetails" => convertAPIModelArray($this->getPricedetails(), true));
            return array_merge($json, $ver);
        }
        return $json;
    }
}
