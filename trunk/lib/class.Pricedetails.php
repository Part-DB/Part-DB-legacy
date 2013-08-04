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
        2012-09-30  kami89          - created
*/

    /**
     * @file class.Pricedetails.php
     * @brief class Pricedetails

     * @class Pricedetails
     * @brief All elements of this class are stored in the database table "pricedetails".
     *
     * One Pricedetails-object includes these things:
     *  - Price
     *  - Price related quantity (see Pricedetails::set_price_related_quantity())
     *  - Minimum discount quantity (see Pricedetails::set_min_discount_quantity())
     *
     * An Orderdetails object can have more than one Pricedetails objects.
     *
     * @author kami89
     */
    class Pricedetails extends DBElement
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

        /** @brief (object) the orderdetails which includes this pricedetails */
        private $orderdetails   = NULL;

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
         * @param integer   $id             ID of the pricedetails we want to get
         *
         * @throws Exception    if there is no such pricedetails record in the database
         * @throws Exception    if there was an error
         */
        public function __construct(&$database, &$current_user, &$log, $id)
        {
            parent::__construct($database, $current_user, $log, 'pricedetails', $id);
        }

        /**
         * @copydoc DBElement::reset_attributes()
         */
        public function reset_attributes($all = false)
        {
            $this->orderdetails = NULL;

            parent::reset_attributes($all);
        }

        /********************************************************************************
        *
        *   Basic Methods
        *
        *********************************************************************************/

        /**
         * @brief Delete this pricedetails record
         *
         * @warning     The pricedetails with "min_discount_quantity == 1" cannot be deleted
         *              if there are already other pricedetails! If you want to delete all
         *              pricedetails, you have to delete all other pricedetails first!
         *
         * @throws Exception if it is not allowed to delete this pricedetails
         * @throws Exception if there was an error
         */
        public function delete()
        {
            // Check if it is allowed to delete this pricedetails
            if ($this->get_min_discount_quantity() == 1)
            {
                $orderdetails = $this->get_orderdetails();
                $all_pricedetails = $orderdetails->get_pricedetails();

                if (count($all_pricedetails) > 1)
                    throw new Exception('Die ausgewählte Preisinformation kann erst gelöscht werden '.
                                        'wenn es sonst keine weiteren Preisinformationen gibt!');
            }

            // save orderdetails attributes to update its "last_modified" and "last_modified" of the part
            $this->get_orderdetails()->set_attributes(array());

            // now we can delete this orderdetails
            parent::delete();
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get the orderdetails of this pricedetails
         *
         * @retval Orderdetails     the orderdetails object
         *
         * @throws Exception if there was an error
         */
        public function get_orderdetails()
        {
            if ( ! is_object($this->orderdetails))
            {
                $this->orderdetails = new Orderdetails($this->database, $this->current_user,
                                                $this->log, $this->db_data['orderdetails_id']);
            }

            return $this->orderdetails;
        }

        /**
         * @brief Get the price
         *
         * @param boolean $as_money_string      @li if true, this method returns a money string incl. currency
         *                                      @li if false, this method returns the price as float
         * @param integer $multiplier           The returned price (float or string) will be multiplied
         *                                      with this multiplier.
         *
         * @note    You will get the price for $multiplier parts. If you want the price which is stored
         *          in the database, you have to pass the "price_related_quantity" count as $multiplier.
         *
         * @retval float    the price as a float number (if "$as_money_string == false")
         * @retval string   the price as a string incl. currency (if "$as_money_string == true")
         *
         * @see float_to_money_string()
         */
        public function get_price($as_money_string = false, $multiplier = 1)
        {
            $price = ($this->db_data['price'] * $multiplier) / $this->db_data['price_related_quantity'];

            if ($as_money_string)
                return float_to_money_string($price);
            else
                return $price;
        }

        /**
         * @brief Get the price related quantity
         *
         * This is the quantity, for which the price is valid.
         *
         * @retval integer       the price related quantity
         *
         * @see Pricedetails::set_price_related_quantity()
         */
        public function get_price_related_quantity()
        {
            return $this->db_data['price_related_quantity'];
        }

        /**
         * @brief Get the minimum discount quantity
         *
         * "Minimum discount quantity" means the minimum order quantity for which the price
         * of this orderdetails is valid.
         *
         * @retval integer       the minimum discount quantity
         *
         * @see Pricedetails::set_min_discount_quantity()
         */
        public function get_min_discount_quantity()
        {
            return $this->db_data['min_discount_quantity'];
        }

        /********************************************************************************
        *
        *   Setters
        *
        *********************************************************************************/

        /**
         * @brief Set the price
         *
         * @param float $new_price       the new price as a float number
         *
         * @warning     @li This is the price for "price_related_quantity" parts!!
         *              @li Example: if "price_related_quantity" is '10',
         *                  you have to set here the price for 10 parts!
         *
         * @throws Exception if the new price is not valid
         * @throws Exception if there was an error
         */
        public function set_price($new_price)
        {
            $this->set_attributes(array('price' => $new_price));
        }

        /**
         * @brief Set the price related quantity
         *
         * This is the quantity, for which the price is valid.
         *
         * @par Example:
         * If 100pcs costs 20$, you have to set the price to 20$ and the price related
         * quantity to 100. The single price (20$/100 = 0.2$) will be calculated automatically.
         *
         * @param integer $new_price_related_quantity       the price related quantity
         */
        public function set_price_related_quantity($new_price_related_quantity)
        {
            $this->set_attributes(array('price_related_quantity' => $new_price_related_quantity));
        }

        /**
         * @brief Set the minimum discount quantity
         *
         * "Minimum discount quantity" means the minimum order quantity for which the price
         * of this orderdetails is valid. This way, you're able to use different prices
         * for different order quantities (quantity discount!).
         *
         * @par Example:
         *      - 1-9pcs costs 10$: set price to 10$/pcs and minimum discount quantity to 1
         *      - 10-99pcs costs 9$: set price to 9$/pcs and minimum discount quantity to 10
         *      - 100pcs or more costs 8$: set price/pcs to 8$ and minimum discount quantity to 100
         *
         * (Each of this examples would be an own Pricedetails-object.
         * So the orderdetails would have three Pricedetails for one supplier.)
         *
         * @param integer $new_min_discount_quantity       the minimum discount quantity
         */
        public function set_min_discount_quantity($new_min_discount_quantity)
        {
            $this->set_attributes(array('min_discount_quantity' => $new_min_discount_quantity));
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

            // set the type of the boolean attributes
            settype($values['manual_input'], 'boolean');

            // check "orderdetails_id"
            try
            {
                $orderdetails = new Orderdetails($database, $current_user, $log, $values['orderdetails_id']);

                // save orderdetails attributes to update its "last_modified" and "last_modified" of the part
                $orderdetails->set_attributes(array());
            }
            catch (Exception $e)
            {
                debug('error', 'Ungültige "orderdetails_id": "'.$values['orderdetails_id'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                         __FILE__, __LINE__, __METHOD__);
                throw new Exception('Die gewählten Einkaufsinformationen existieren nicht!');
            }

            // check "price"
            if (( ! is_numeric($values['price'])) || ($values['price'] < 0))
            {
                debug('error', 'Ungültiger Preis: "'.$values['price'].'"',
                        __FILE__, __LINE__, __METHOD__);

                throw new Exception('Der neue Preis ist ungültig!');
            }

            // check "price_related_quantity"
            if ((( ! is_int($values['price_related_quantity'])) && ( ! ctype_digit($values['price_related_quantity'])))
                || ($values['price_related_quantity'] < 1))
            {
                debug('error', '"price_related_quantity" = "'.$values['price_related_quantity'].'"',
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Die Preisbezogene Menge ist ungültig!');
            }

            // check "min_discount_quantity"
            if ((( ! is_int($values['min_discount_quantity'])) && ( ! ctype_digit($values['min_discount_quantity'])))
                || ($values['min_discount_quantity'] < 1))
            {
                debug('error', '"min_discount_quantity" = "'.$values['min_discount_quantity'].'"',
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Die Mengenrabatt-Menge ist ungültig!');
            }

            // search for pricedetails with the same "min_discount_quantity"
            $same_min_discount_quantity_count = 0;
            $all_pricedetails = $orderdetails->get_pricedetails();
            foreach ($all_pricedetails as $pricedetails)
            {
                if ($pricedetails->get_min_discount_quantity() == $values['min_discount_quantity'])
                    $same_min_discount_quantity_count++;
            }

            if ($is_new)
            {
                // first pricedetails, but "min_discount_quantity" != 1 ?
                if ((count($all_pricedetails) == 0) && ($values['min_discount_quantity'] != 1))
                    throw new Exception('Die Mengenrabatt-Menge muss bei der ersten Preisangabe "1" sein!');

                // is there already a pricedetails with the same "min_discount_quantity" ?
                if ($same_min_discount_quantity_count > 0)
                    throw new Exception('Es existiert bereits eine Preisangabe für die selbe Mengenrabatt-Menge!');
            }
            elseif ($values['min_discount_quantity'] != $element->get_min_discount_quantity())
            {
                // does the user try to change the "min_discount_quantity", but it is "1" ?
                if ($element->get_min_discount_quantity() == 1)
                    throw new Exception('Die Mengenrabatt-Menge beim Preis für ein Bauteil kann nicht verändert werden!');

                // change the "min_discount_quantity" to a already existing value?
                if ($same_min_discount_quantity_count > 0)
                    throw new Exception('Es existiert bereits eine Preisangabe mit der selben Mengenrabatt-Menge!');
            }
        }

        /**
         * @brief Create a new orderdetails record
         *
         * @param Database  &$database                  reference to the database onject
         * @param User      &$current_user              reference to the current user which is logged in
         * @param Log       &$log                       reference to the Log-object
         * @param integer   $orderdetails_id            the ID of the orderdetails with that the pricedetails is associated
         * @param float     $price                      the price of the part (see Pricedetails::set_price())
         * @param integer   $price_related_quantity     the price related quantity (see Pricedetails::set_price_related_quantity())
         * @param integer   $min_discount_quantity      the minimum discount quantity (see Pricedetails::set_min_discount_quantity())
         *
         * @note   The database column "last_update" will be filled automatically
         *         in Pricedetails::check_values_validity().
         *
         * @warning     The attribute "min_discount_quantity" must be "1" if there are no other
         *              pricedetails in the selected orderdetails yet!
         *
         * @retval Pricedetails     the new Pricedetails object
         *
         * @throws Exception    if (this combination of) values is not valid
         * @throws Exception    if there was an error
         *
         * @see DBElement::add()
         */
        public static function add(&$database, &$current_user, &$log, $orderdetails_id, $price,
                                    $price_related_quantity = 1, $min_discount_quantity = 1)
        {
            return parent::add($database, $current_user, $log, 'pricedetails',
                                array(  'orderdetails_id'           => $orderdetails_id,
                                        'manual_input'              => true,
                                        'price'                     => $price,
                                        'price_related_quantity'    => $price_related_quantity,
                                        'min_discount_quantity'     => $min_discount_quantity));
        }

    }

?>
