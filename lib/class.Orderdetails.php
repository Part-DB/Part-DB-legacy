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
        2012-09-30  kami89          - added "price_related_quantity" and "min_discount_quantity"
        2012-09-30  kami89          - moved some methods to the new class "Pricedetails"
        2013-01-29  kami89          - added support for transactions in "delete()"
                                    - moved attrubute "obsolete" from Parts to Orderdetails
        2014-05-12  kami89          - added function "get_supplier_product_url()"
*/

    /**
     * @file class.Orderdetails.php
     * @brief class Orderdetails

     * @class Orderdetails
     * @brief All elements of this class are stored in the database table "orderdetails".
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
    class Orderdetails extends DBElement
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

        /** @brief (Part) the part of this orderdetails */
        private $part           = NULL;
        /** @brief (Supplier) the supplier of this orderdetails */
        private $supplier       = NULL;
        /** @brief (array) all pricedetails of this orderdetails, as a one-dimensional array of Pricedetails objects */
        private $pricedetails   = NULL;

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
        public function __construct(&$database, &$current_user, &$log, $id)
        {
            parent::__construct($database, $current_user, $log, 'orderdetails', $id);
        }

        /**
         * @copydoc DBElement::reset_attributes()
         */
        public function reset_attributes($all = false)
        {
            $this->part             = NULL;
            $this->supplier         = NULL;
            $this->pricedetails     = NULL;

            parent::reset_attributes($all);
        }

        /********************************************************************************
        *
        *   Basic Methods
        *
        *********************************************************************************/

        /**
         * @brief Delete this orderdetails incl. all their pricedetails
         *
         * @throws Exception if there was an error
         */
        public function delete()
        {
            try
            {
                $transaction_id = $this->database->begin_transaction(); // start transaction

                // Delete all Pricedetails
                $all_pricedetails = array_reverse($this->get_pricedetails()); // the last one must be deleted first!
                $this->reset_attributes(); // set $this->pricedetails to NULL
                foreach ($all_pricedetails as $pricedetails)
                    $pricedetails->delete();

                // Check if this Orderdetails is the Part's selected Orderdetails for ordering and delete this reference if neccessary
                $order_orderdetails = $this->get_part()->get_order_orderdetails();
                if (is_object($order_orderdetails) && ($order_orderdetails->get_id() == $this->get_id()))
                    $this->get_part()->set_order_orderdetails_id(NULL);
                else
                    $this->get_part()->set_attributes(array()); // save part attributes to update its "last_modified"

                // now we can delete this orderdetails
                parent::delete();

                $this->database->commit($transaction_id); // commit transaction
            }
            catch (Exception $e)
            {
                $this->database->rollback(); // rollback transaction

                // restore the settings from BEFORE the transaction
                $this->reset_attributes();

                throw new Exception("Die Einkaufsinformationen konnten nicht gelöscht werden!\nGrund: ".$e->getMessage());
            }
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get the part
         *
         * @retval Part     the part of this orderdetails
         *
         * @throws Exception if there was an error
         */
        public function get_part()
        {
            if ( ! is_object($this->part))
            {
                $this->part = new Part($this->database, $this->current_user,
                                                $this->log, $this->db_data['part_id']);
            }

            return $this->part;
        }

        /**
         * @brief Get the supplier
         *
         * @retval Supplier     the supplier of this orderdetails
         *
         * @throws Exception if there was an error
         */
        public function get_supplier()
        {
            if ( ! is_object($this->supplier))
            {
                $this->supplier = new Supplier($this->database, $this->current_user,
                                                $this->log, $this->db_data['id_supplier']);
            }

            return $this->supplier;
        }

        /**
         * @brief Get the supplier part-nr.
         *
         * @retval string       the part-nr.
         */
        public function get_supplierpartnr()
        {
            return $this->db_data['supplierpartnr'];
        }

        /**
         * @brief Get if this orderdetails is obsolete
         *
         * "Orderdetails is obsolete" means that the part with that supplier-part-nr
         * is no longer available from the supplier of that orderdetails.
         *
         * @retval boolean      @li true if this part is obsolete at that supplier
         *                      @li false if this part isn't obsolete at that supplier
         */
        public function get_obsolete()
        {
            return $this->db_data['obsolete'];
        }

        /**
         * @brief Get the link to the website of the article on the suppliers website
         *
         * @retval string           the link to the article
         */
        public function get_supplier_product_url()
        {
            if (strlen($this->db_data['supplier_product_url']) > 0)
                return $this->db_data['supplier_product_url'];  // a manual url is available
            else
                return $this->get_supplier()->get_auto_product_url($this->db_data['supplierpartnr']); // maybe an automatic url is available...
        }

        /**
         * @brief Get all pricedetails
         *
         * @retval array    all pricedetails as a one-dimensional array of Pricedetails objects,
         *                  sorted by minimum discount quantity
         *
         * @throws Exception if there was an error
         */
        public function get_pricedetails()
        {
            if ( ! is_array($this->pricedetails))
            {
                $this->pricedetails = array();

                $query = 'SELECT id FROM pricedetails '.
                            'WHERE orderdetails_id=? '.
                            'ORDER BY min_discount_quantity ASC';

                $query_data = $this->database->query($query, array($this->get_id()));

                foreach ($query_data as $row)
                    $this->pricedetails[] = new Pricedetails($this->database, $this->current_user, $this->log, $row['id']);
            }

            return $this->pricedetails;
        }

       /**
         * @brief Get the price for a specific quantity
         *
         * @param boolean $as_money_string      @li if true, this method returns a money string incl. currency
         *                                      @li if false, this method returns the price as float
         * @param integer       $quantity       this is the quantity to choose the correct pricedetails
         * @param integer|NULL  $multiplier     @li This is the multiplier which will be applied to every single price
         *                                      @li If you pass NULL, the number from $quantity will be used
         *
         * @retval float    the price as a float number (if "$as_money_string == false")
         * @retval NULL     if there are no prices and "$as_money_string == false"
         * @retval string   the price as a string incl. currency (if "$as_money_string == true")
         *
         * @throws Exception if there are no pricedetails for the choosed quantity
         *          (for example, there are only one pricedetails with the minimum discount quantity '10',
         *          but the choosed quantity is '5' --> the price for 5 parts is not defined!)
         * @throws Exception if there was an error
         *
         * @see float_to_money_string()
         */
        public function get_price($as_money_string = false, $quantity = 1, $multiplier = NULL)
        {
            if (($quantity == 0) && ($multiplier === NULL))
            {
                if ($as_money_string)
                    return float_to_money_string(0);
                else
                    return 0;
            }

            $all_pricedetails = $this->get_pricedetails();

            if (count($all_pricedetails) == 0)
            {
                if ($as_money_string)
                    return float_to_money_string(NULL);
                else
                    return NULL;
            }

            foreach ($all_pricedetails as $pricedetails)
            {
                // choose the correct pricedetails for the choosed quantity ($quantity)
                if ($quantity < $pricedetails->get_min_discount_quantity())
                    break;

                $correct_pricedetails = $pricedetails;
            }

            if (( ! isset($correct_pricedetails)) || ( ! is_object($correct_pricedetails)))
                throw new Exception('Es sind keine Preisinformationen für die angegebene Bestellmenge vorhanden!');

            if ($multiplier === NULL)
                $multiplier = $quantity;

            return $correct_pricedetails->get_price($as_money_string, $multiplier);
        }

        /********************************************************************************
        *
        *   Setters
        *
        *********************************************************************************/

        /**
         * @brief Set the supplier ID
         *
         * @param integer $new_supplier_id       the ID of the new supplier
         *
         * @throws Exception if the new supplier ID is not valid
         * @throws Exception if there was an error
         */
        public function set_supplier_id($new_supplier_id)
        {
            $this->set_attributes(array('id_supplier' => $new_supplier_id));
        }

        /**
         * @brief Set the supplier part-nr.
         *
         * @param string $new_supplierpartnr       the new supplier-part-nr
         *
         * @throws Exception if there was an error
         */
        public function set_supplierpartnr($new_supplierpartnr)
        {
            $this->set_attributes(array('supplierpartnr' => $new_supplierpartnr));
        }

        /**
         * @brief Set if the part is obsolete at the supplier of that orderdetails
         *
         * @param boolean $new_obsolete       true means that this part is obsolete
         *
         * @throws Exception if there was an error
         */
        public function set_obsolete($new_obsolete)
        {
            $this->set_attributes(array('obsolete' => $new_obsolete));
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

            // set the datetype of the boolean attributes
            settype($values['obsolete'], 'boolean');

            // check "part_id"
            try
            {
                $part = new Part($database, $current_user, $log, $values['part_id']);
                $part->set_attributes(array()); // save part attributes to update its "last_modified"
            }
            catch (Exception $e)
            {
                debug('error', 'Ungültige "part_id": "'.$values['part_id'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                         __FILE__, __LINE__, __METHOD__);
                throw new Exception('Das gewählte Bauteil existiert nicht!');
            }

            // check "id_supplier"
            try
            {
                if ($values['id_supplier'] < 1)
                    throw new Exception('id_supplier < 1');

                $supplier = new Supplier($database, $current_user, $log, $values['id_supplier']);
            }
            catch (Exception $e)
            {
                debug('error', 'Ungültige "id_supplier": "'.$values['id_supplier'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                         __FILE__, __LINE__, __METHOD__);
                throw new Exception('Der gewählte Lieferant existiert nicht!');
            }
        }

        /**
         * @brief Create a new orderdetails record
         *
         * @param Database  &$database          reference to the database onject
         * @param User      &$current_user      reference to the current user which is logged in
         * @param Log       &$log               reference to the Log-object
         * @param integer   $part_id            the ID of the part with that the orderdetails is associated
         * @param integer   $supplier_id        the ID of the supplier (see Orderdetails::set_supplier_id())
         * @param string    $supplierpartnr     the supplier-part-nr (see Orderdetails::set_supplierpartnr())
         * @param boolean   $obsolete           the obsolete attribute of the new orderdetails (see Orderdetails::set_obsolete())
         *
         * @retval Orderdetails     the new orderdetails object
         *
         * @throws Exception    if (this combination of) values is not valid
         * @throws Exception    if there was an error
         *
         * @see DBElement::add()
         */
        public static function add(&$database, &$current_user, &$log, $part_id, $supplier_id,
                                    $supplierpartnr = '', $obsolete = false)
        {
            return parent::add($database, $current_user, $log, 'orderdetails',
                                array(  'part_id'                   => $part_id,
                                        'id_supplier'               => $supplier_id,
                                        'supplierpartnr'            => $supplierpartnr,
                                        'obsolete'                  => $obsolete));
        }

    }

?>
