<?php

namespace PartDB;


use PartDB\Base\DBElement;
use PartDB\Exceptions\ElementNotExistingException;
use Symfony\Component\VarDumper\Cloner\Data;

class ElementCache
{

    const USERID_CURRENT_USER = -1;

    const ELEMENT_PART = 1;
    const ELEMENT_ATTACHEMENT = 2;
    const ELEMENT_ATTACHEMENT_TYPE = 3;
    const ELEMENT_CATEGORY = 4;
    const ELEMENT_DEVICEPART = 5;
    const ELEMENT_FOOTPRINT = 6;
    const ELEMENT_MANUFACTURER = 7;
    const ELEMENT_ORDERDETAILS = 8;
    const ELEMENT_PRICEDETAILS = 9;
    const ELEMENT_STORELOCATION = 10;
    const ELEMENT_SUPPLIER = 11;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Log
     */
    protected $log;

    /**
     * @var array This is used to store the already known elements. Elements are saved in the form $cache[type][id]
     */
    protected $cache;

    /**
     * Creates a new ElementCache with the given objects.
     *
     * @param $user_id int By default the ElementCache uses the logged in user of the current session.
     *  If you want to use another one, you
     *
     * @throws \Exception If an exception happened
     */
    public function __construct(int $user_id = self::USERID_CURRENT_USER)
    {
        $this->database = new Database();
        $this->log = new Log($this->database);

        if ($user_id == self::USERID_CURRENT_USER) {
            $this->user = User::getLoggedInUser($this->database, $this->log);
        } else {
            $this->user = User::getInstance($this->database, $this->user, $this->log, $user_id);
        }


        //Init cache with empty array
        $this->cache = array();
    }


    /**
     * Checks if the given element is already known and saved in the cache. If not, nothing else happens.
     * @param int $element_type The element type of the element you want to check.
     * @param int $id The ID of the element you want to check.
     * @return bool True if the element is already cached, false if not.
     */
    public function isCached(int $element_type, int $id) : bool
    {
        if ($element_type <= 0) {
            throw new \InvalidArgumentException(_("Es gibt kein Elementtyp mit der gegebenen ID!!"));
        }

        if ($id <= 0) {
            throw new ElementNotExistingException(_("Es gibt kein Element mit der gewünschten ID!"));
        }

        return isset($this[$element_type][$id]);
    }

    /**
     * Clears the whole cache.
     */
    public function clearCache()
    {
        $this->cache = array();
    }

    /**
     * Removes a single element from the cache.
     * @param int $element_type The element type of the element you want to remove.
     * @param int $id The ID of the element you want to remove from cache.
     */
    public function clearElement(int $element_type, int $id)
    {
        if ($element_type <= 0) {
            throw new \InvalidArgumentException(_("Es gibt kein Elementtyp mit der gegebenen ID!!"));
        }

        if ($id <= 0) {
            throw new ElementNotExistingException(_("Es gibt kein Element mit der gewünschten ID!"));
        }

        unset($this->cache[$element_type][$id]);
    }

    /**
     * Returns the given Element. If it was not cached yet, it will be created from DB, and then cached.
     * Otherwise a reference to the cache is returned.
     * @param int $element_type The element type of the element you want to get (see ELEMENT_* consts)
     * @param int $id The ID of the element you want to get
     * @return DBElement The Element you requested.
     */
    public function &getElement(int $element_type, int $id) : DBElement
    {
        if ($element_type <= 0) {
            throw new \InvalidArgumentException(_("Es gibt kein Elementtyp mit der gegebenen ID!!"));
        }

        if ($id <= 0) {
            throw new ElementNotExistingException(_("Es gibt kein Element mit der gewünschten ID!"));
        }

        if (isset($this[$element_type][$id])) {
            //We already know this element so, only return the reference to it!
            return $this[$element_type][$id];
        } else {
            //We have to create the object
            /** @var DBElement $class */
            $class = self::IDtoClassName($element_type);
            $element = new $class($this->database, $this->user, $this->log);
            $this[$element_type][$id] = $element;
            return $this[$element_type][$id];
        }
    }

    /**
     * Converts the element type id to the class name of the type.
     * @param int $element_type the elment type, for which you want to get the class name.
     * @return string The class name of the elementtype you requested.
     */
    protected static function IDtoClassName(int $element_type) : string
    {
        switch ($element_type) {
            case self::ELEMENT_ATTACHEMENT:
                return Attachement::class;
            case self::ELEMENT_ATTACHEMENT_TYPE:
                return AttachementType::class;
            case self::ELEMENT_CATEGORY:
                return Category::class;
            case self::ELEMENT_DEVICEPART:
                return DevicePart::class;
            case self::ELEMENT_FOOTPRINT:
                return DevicePart::class;
            case self::ELEMENT_MANUFACTURER:
                return Manufacturer::class;
            case self::ELEMENT_ORDERDETAILS:
                return Orderdetails::class;
            case self::ELEMENT_PRICEDETAILS:
                return Pricedetails::class;
            case self::ELEMENT_STORELOCATION:
                return Storelocation::class;
            case self::ELEMENT_SUPPLIER:
                return Supplier::class;

            default:
                throw new \InvalidArgumentException(_("Es gibt kein Elementtyp mit der gegebenen ID!"));
        }
    }

    public function &getDatabase() : Database
    {
        return $this->database;
    }

    public function &getUser() : User
    {
        return $this->user;
    }

    public function &getLog() : Log
    {
        return $this->log;
    }
}