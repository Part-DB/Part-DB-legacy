<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 01.01.2019
 * Time: 18:05
 */

namespace PartDB\FilterSystem;


use PartDB\Base\DBElement;
use PartDB\Base\StructuralDBElement;
use PartDB\Category;
use PartDB\Database;
use PartDB\Exceptions\DatabaseException;
use PartDB\Exceptions\ElementNotExistingException;
use PartDB\Footprint;
use PartDB\Log;
use PartDB\Manufacturer;
use PartDB\Supplier;
use PartDB\User;

class PartFilter extends AbstractFilter
{
    //Objects for internal use
    protected $database;
    protected $current_user;
    protected $log;

    //Part ID filters
    protected $part_ids = array();

    //Part name filter
    protected $name = "";
    protected $name_regex = false;

    //Category ID filtering
    protected $category_ids = array();

    //Footprint ID filtering
    protected $footprint_ids = array();

    //Manufacturer ID filtering
    protected $manufacturer_ids = array();

    //Supplier ID filtering
    protected $supplier_ids = array();

    //Storelocation ID filtering
    protected $storelocation_ids = array();

    //Instock filtering
    const INSTOCK_NAN = -5;
    protected $instock_min = 0;
    protected $instock_max = self::INSTOCK_NAN;

    public function __construct(Database &$database, User &$current_user, Log &$log)
    {
        $this->database = $database;
        $this->current_user = $current_user;
        $this->log = $log;
    }

    /******************************************************
     * Setters
     *****************************************************/

    /**
     * The filtered parts, must have one of the passed IDs as part ID.
     * Pass empty array, if the part ID is not important.
     * @param array $ids
     */
    public function setPartIDs(array $ids = array())
    {
        $this->part_ids = $ids;
    }

    /**
     * Filter for parts which has the given name.
     * @param string $pattern The pattern, the name of the part has to match.
     * Set to empty string, if you want no name filtering.
     * @param bool $regex If true the above $pattern will be treated as regular expression.
     * If false you can only use % (multiple chars), or _ (single char) as wildcard operator.
     */
    public function setName(string $pattern = "", bool $regex = false)
    {
        $this->name = $pattern;
        $this->name_regex = $regex;
    }

    /**
     * Filter for parts that have one of the given categories. (array elements are treated OR connected).
     * @param array $ids The IDs of the categories, the parts should have. Empty array if this does not matter.
     * @param bool $recursive Set to true, if sub categories of the given categories (and there subcategories)
     * should be included too. Set to false, to only include the passed categories.
     * @throws DatabaseException
     */
    public function setCategoryIDs(array $ids = array(), $recursive = true)
    {
        // If recursive is activated, we need to add the subelements first.
        if ($recursive) {
            $this->category_ids = $this->addSubelements(Category::class, $ids);
        } else { //Othwise we can simply use $ids
            $this->category_ids = $ids;
        }
    }

    /**
     * Filter for parts that have one of the given manufacturer. (array elements are treated OR connected).
     * @param array $ids The IDs of the categories, the parts should have. Empty array if this does not matter.
     * If one of the IDs is null, then all parts without an manufactuerer is found.
     * @param bool $recursive Set to true, if sub categories of the given categories (and there subcategories)
     * should be included too. Set to false, to only include the passed categories.
     * @throws DatabaseException
     */
    public function setManufacturerIDs(array $ids = array(), $recursive = true)
    {
        // If recursive is activated, we need to add the subelements first.
        if ($recursive) {
            $this->manufacturer_ids = $this->addSubelements(Manufacturer::class, $ids);
        } else { //Othwise we can simply use $ids
            $this->manufacturer_ids = $ids;
        }
    }

    /**
     * Filter for parts that have one of the given manufacturer. (array elements are treated OR connected).
     * @param array $ids The IDs of the categories, the parts should have. Empty array if this does not matter.
     * If one of the IDs is null, then all parts without an manufactuerer is found.
     * @param bool $recursive Set to true, if sub categories of the given categories (and there subcategories)
     * should be included too. Set to false, to only include the passed categories.
     * @throws DatabaseException
     */
    public function setFootprintIDs(array $ids = array(), $recursive = true)
    {
        // If recursive is activated, we need to add the subelements first.
        if ($recursive) {
            $this->manufacturer_ids = $this->addSubelements(Footprint::class, $ids);
        } else { //Othwise we can simply use $ids
            $this->manufacturer_ids = $ids;
        }
    }

    /**
     * Filter for parts that have one of the given manufacturer. (array elements are treated OR connected).
     * @param array $ids The IDs of the categories, the parts should have. Empty array if this does not matter.
     * If one of the IDs is null, then all parts without an manufactuerer is found.
     * @param bool $recursive Set to true, if sub categories of the given categories (and there subcategories)
     * should be included too. Set to false, to only include the passed categories.
     * @throws DatabaseException
     */
    public function setSupplierIDs(array $ids = array(), $recursive = true)
    {
        // If recursive is activated, we need to add the subelements first.
        if ($recursive) {
            $this->supplier_ids = $this->addSubelements(Supplier::class, $ids);
        } else { //Othwise we can simply use $ids
            $this->supplier_ids = $ids;
        }
    }

    /**
     * Show only parts, whose instock value is in the given limit (both values are included in the limit).
     * Set both values to the same value, if you want to show only parts with exact this count.
     * @param int $instock_min The lower limit that parts
     * @param int $instock_max
     */
    public function setInstock($instock_min = 0, $instock_max = self::INSTOCK_NAN)
    {
        //Check if the limits are valid
        if ($instock_min < 0) {
            throw new \InvalidArgumentException(_('$instock_min muss größer gleich als 0 sein!'));
        }
        if ($instock_max < 0 && !$instock_max == self::INSTOCK_NAN) {
            throw new \InvalidArgumentException(_('$instock_max muss größer gleich als 0 sein!'));
        }
        if ($instock_max < $instock_min) {
            throw new \InvalidArgumentException(_('$instock_min ist größer als $instock_max. Dies ist nicht erlaubt!'));
        }

        $this->instock_min = $instock_min;
        $this->instock_max = $instock_max;
    }

    public function toQuery() : array
    {
        $query = 'SELECT parts.* FROM parts WHERE ';
        $data = array();

        //Part ID filtering
        if (!empty($this->part_ids)) {
            $query .= 'parts.id IN (' . str_repeat('?, ', count($this->part_ids)) . ') AND';
            $data = array_merge($data, $this->part_ids);
        }

        //Category ID filtering
        if (!empty($this->category_ids)) {
            $query .= 'parts.id_category IN (' . str_repeat('?, ', count($this->category_ids)) . ') AND';
            $data = array_merge($data, $this->category_ids);
        }

        //Manufacturer ID filtering
        if (!empty($this->manufacturer_ids)) {
            $query .= 'parts.id_manufacturer IN (' . str_repeat('?, ', count($this->manufacturer_ids)) . ') AND';
            $data = array_merge($data, $this->manufacturer_ids);
        }

        //Footprint ID filtering
        if (!empty($this->footprint_ids)) {
            $query .= 'parts.id_footprint IN (' . str_repeat('?, ', count($this->footprint_ids)) . ') AND';
            $data = array_merge($data, $this->footprint_ids);
        }


    }

    /***********************************************************************************************************
     * Helper functions
     **********************************************************************************************************/

    /**
     * This function looks up the subelements for the passed Elements, returns an array, with all (the passed ids, and
     * the looked up ones) IDs. This function is usefull, for the setCategoryIDs() and similar function, which has
     * and recusive option.
     * @param $class_name string The class names of the elements for which the subelements should be determined.
     * @param array $element_ids The IDs of the elements, for which the subelements IDs should be determined.
     * @return array[] An array containing the element ids and their subelements.
     * @throws DatabaseException
     * @throws ElementNotExistingException
     */
    protected function addSubelements($class_name, array $element_ids) : array
    {
        /** @var StructuralDBElement $class_name */

        $tmp = $element_ids;
        foreach ($element_ids as $id) {
            $element = $class_name::getInstance(
                $this->database,
                $this->current_user,
                $this->log,
                (int) $id
            );
            $subelements = $element->getSubelements(true);
            //Add the IDs of the subelements to $tmp
            foreach ($subelements as $subelement) {
                $tmp[] =  $subelement->getID();
            }
        }

        return $tmp;
    }
}