<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 12.09.2017
 * Time: 16:32
 */

namespace PartDB\Permissions;


use PartDB\Exceptions\NotImplementedException;
use PartDB\Interfaces\IHasPermissions;
use Psr\Log\InvalidArgumentException;

abstract class BasePermission
{
    const INHERIT   = 0b00;
    const ALLOW     = 0b01;
    const DISALLOW  = 0b10;

    /** @var  IHasPermissions */
    protected $perm_holder;
    /** @var string  */
    protected $perm_name;
    /** @var string  */
    protected $description;

    /**
     * BasePermission constructor.
     * @param $perm_holder IHasPermissions The object which has permissions, that can be written and read.
     * @param $perm_name string The name of the permission (without perms_)
     * @param string $description string A trivial name for this permission.
     */
    public function __construct(&$perm_holder, $perm_name, $description = "")
    {
        $this->perm_holder = $perm_holder;
        $this->perm_name = $perm_name;
        //When no description is set, than use perm_name as description
        if ($description == "") {
            $description = $perm_name;
        }
        $this->description = $description;
    }

    /**
     * Get the permission value for the given Operation
     * @param $operation string The operation.
     * @return int The permission value for the operation.
     */
    public function getValue($operation)
    {
        $n = static::opToBitN($operation);
        if ($this->perm_holder == null) {
            return BasePermission::INHERIT; //Defaultly disallow.
        }
        return static::readBitPair($this->perm_holder->getPermissionRaw($this->perm_name), $n);
    }

    /**
     * Sets the permission value for the given operation.
     * @param $operation string The operation for which the value should be set.
     * @param $new_value int The new value of the operation bit pair.
     */
    public function setValue($operation, $new_value)
    {
        $n = static::opToBitN($operation);
        $old_value = $this->perm_holder->getPermissionRaw($this->perm_name);
        $value = static::writeBitPair($old_value, $n, $new_value);
        $value = static::modifyValueBeforeSetting($operation, $new_value, $value);
        if ($value !== $old_value) { //Only write to DB, if a value was changed.
            $this->perm_holder->setPermissionRaw($this->perm_name, $value);
        }
    }

    /**
     * Modify the permissions data, before it gets written, to Database. This is called while setValue() is executed.
     * @param $operation string The original $operation string from setValue().
     * @param $new_value string The original $new_value string from setValue().
     * @param $data int The raw data, that can be modifed.
     * @return int The modified data.
     */
    protected function modifyValueBeforeSetting($operation, $new_value, $data)
    {
        //Do nothing on default
        return $data;
    }

    /**
     * Returns the current data of the Permission.
     * @return int The data of the permission.
     */
    public function toData()
    {
        return $this->perm_holder->getPermissionRaw($this->perm_name);
    }

    /**
     * Returns the name of the permsission. (without perms_)
     * @return string The permission string.
     */
    public function getName()
    {
        return $this->perm_name;
    }

    /**
     * Returns the description of the permsission.
     * @return string The permission string.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Generates the a template loop, for this permission.
     * @param $read_only boolean When true, all checkboxes are disabled (greyed out)
     * @param $inherit boolean If true, inherit values, are resolved.
     * @return array The template loop
     */
    public function generateLoopRow($read_only = false, $inherit = false)
    {
        $all_ops = static::listOperations();

        $ops = array();

        foreach ($all_ops as $op) {
            $val = $this->getValue($op["name"]);

            if ($inherit && $val == static::INHERIT) {
                //Try to inherit permission
                if ($this->perm_holder->getParentPermissionManager() == null) {
                    $val = static::DISALLOW; //Default to disallow
                } else {
                    $this->perm_holder->getParentPermissionManager()->getPermissionValue($this->getName(), $op['name'], true);
                }

            }

            $ops[] = array("name" => $op["name"],
                "description" => $op["description"],
                "value" => $val);
        }

        return array("name" => $this->getName(),
            "description" => $this->getDescription(),
            "readonly"    => $read_only,
            "ops"   => $ops);
    }

    /*******************************************************
     * Static Functions
     ******************************************************/

    /**
     * Gets the bit number for every operation (see Constants in Permission class).
     * @param $op string The operation for which the bit number should be calculated.
     * @return int The bitnumber for the operation.
     * @throws \InvalidArgumentException If no operation with the given name exists.
     */
    protected static function opToBitN($op)
    {
        $op = mb_strtolower($op);

        $operations = static::listOperations();

        foreach ($operations as $operation) {
            if ($operation["name"] == $op) {
                return $operation["n"];
            }
        }

        throw new \InvalidArgumentException(_('$op ist keine gültige Operation!'));
    }

    /**
     * Gets a trivial name for the operation.
     * @param $op string The operation (name) for which the operation should be determined.
     * @return static The trivial name for the operation.
     * @throws \InvalidArgumentException If no operation with the given name exists.
     */
    public static function opToDescription($op)
    {
        $op = mb_strtolower($op);

        $operations = static::listOperations();

        foreach ($operations as $operation) {
            if ($operation["name"] == $op) {
                return $operation["description"];
            }
        }

        throw new \InvalidArgumentException(_('$op ist keine gültige Operation!'));
    }

    /**
     * Returns an array of all available operations for this Permission.
     * @return array All availabel operations.
     * @throws NotImplementedException When this function is not implemented in child classes, this exception is thrown.
     */
    public static function listOperations()
    {
        throw new NotImplementedException(_("listOperations() ist in nicht implementiert"));
    }

    /**
     * Reads a bit pair from $data.
     * @param $data int The data from where the bits should be extracted from.
     * @param $n int The number of the lower bit (of the pair) that should be read. Starting from zero.
     */
    final protected static function readBitPair($data, $n)
    {
        if (!is_int($data) || !is_int($n)) {
            throw new \InvalidArgumentException(_("Die Parameter müssen alles gültige Integervariablen sein!"));
        }
        if ($n > 31) {
            throw new \InvalidArgumentException(_('$n muss kleiner als 32 sein, da nur eine 32bit Variable verwendet wird.'));
        }

        $mask = 0b11 << $n; //Create a mask for the data
        return ($data & $mask) >> $n; //Apply mask and shift back
    }

    /**
     * Writes a bit pair in the given $data and returns it.
     * @param $data int The data which should be modified.
     * @param $n int The number of the lower bit of the pair which should be written.
     * @param $new int The new value of the pair.
     * @return int The new data with the modified pair.
     */
    final protected static function writeBitPair($data, $n, $new)
    {
        if (!is_int($data) || !is_int($n) || !is_int($new)) {
            throw new \InvalidArgumentException(_("Die Parameter müssen alles gültige Integervariablen sein!"));
        }
        if ($n > 31) {
            throw new \InvalidArgumentException(_('$n muss kleiner als 32 sein, da nur eine 32bit Variable verwendet wird.'));
        }
        if ($new > 3) {
            throw new \InvalidArgumentException(_('$new kann nicht größer als 3 sein, da ein Bitpaar beschrieben wird.'));
        }

        $mask = 0b11 << $n; //Mask all bits that should be writen
        $newval = $new << $n; //The new value.
        $data = ($data & ~$mask) | ($newval & $mask);
        return $data;
    }

    /**
     *
     * @param $n
     * @param $name
     * @return array
     */
    protected static function buildOperationArray($n, $name, $description)
    {
        return array("n" => $n, "name" => $name, "description" => $description);
    }

}