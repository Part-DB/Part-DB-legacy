<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 13.09.2017
 * Time: 12:51
 */

namespace PartDB\Tools;


use PartDB\Base\DBElement;
use PartDB\Interfaces\IHasPermissions;

class PermissionManager
{
    /** @var IHasPermissions  */
    protected $perm_holder;
    /** @var  BasePermission[] */
    protected $permissions;

    const STORELOCATIONS    = "storelocations";
    const FOOTRPINTS        = "footprints";
    const CATEGORIES        = "categories";
    const SUPPLIERS         = "suppliers";
    const MANUFACTURERS     = "manufacturers";

    /**
     * PermissionManager constructor.
     * @param $perm_holder IHasPermissions A object which has permissions properties and which should be used for read/write.
     *                  Use null, when you want to return default values.
     */
    public function __construct(&$perm_holder)
    {
        $this->perm_holder = $perm_holder;
        $this->fillPermissionsArray();
        $this->permissions = array();

        $this->fillPermissionsArray();
    }
    
    public function generatePermissionsLoop()
    {
        $loop = array();
        foreach ($this->permissions as $permission) {
            $loop[] = $permission->generateLoopRow();
        }

        return $loop;
    }

    public function parsePermissionsFromRequest($request_array)
    {
        foreach ($request_array as $request => $value) {
            //The request variable is a permission when it begins with perm/
            if (strpos($request, "perm/") !== false) {
                try {
                    //Split the $name string into the different parts.
                    $tmp = explode("/", $request);
                    $permission = $tmp[1];
                    $operation  = $tmp[2];

                    //Get permession object.
                    $perm = $this->getPermission($permission);
                    //Set Value of the operation.
                    $perm->setValue($operation, parseTristateCheckbox($value));

                } catch (\Exception $ex) {
                    //Ignore exceptions. Dont do anything.
                }
            }
        }
    }

    /**
     * Gets the value of the Permission.
     * @param $perm_name string The name of the permission.
     * @param $perm_op string The name of the operation.
     * @param bool $inheritance When this is true, than inherit values gets resolved.
     *      Set this to false, when you want to get only the value of the permission, and not to resolve inherit values.
     * @return int The value of the requested permission.
     */
    public function getPermissionValue($perm_name, $perm_op, $inheritance = true)
    {
        $perm = $this->getPermission($perm_name);
        $val = $perm->getValue($perm_op);
        if ($inheritance == false) { //When no inheritance is needed, simply return the value.
            return $val;
        } else {
            if ($val == BasePermission::INHERIT) {
                $parent = $this->perm_holder->getParentPermissionManager(); //Get the parent permission manager.
                if ($parent == null) { //When no parent exists, than return current value.
                    return $val;
                }
                //Repeat the request for the parent.
                return $parent->getPermissionValue($perm_name, $perm_op, true);
            }
            return $val;
        }
    }

    /**
     * Returns the permission object for the permission with given name.
     * @param $name string The name of the requested permission.
     * @return BasePermission The requeste
     */
    public function &getPermission($name)
    {
        foreach ($this->permissions as $perm) {
            if ($perm->getName() == $name) {
                return $perm;
            }
        }

        throw new \InvalidArgumentException(_("Keine Permission mit dem gegebenen Namen vorhanden!"));
    }


    protected function fillPermissionsArray()
    {
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::STORELOCATIONS, _("Lagerorte"));
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::FOOTRPINTS, _("Footprints"));
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::CATEGORIES, _("Kategorien"));
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::SUPPLIERS, _("Lieferanten"));
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::MANUFACTURERS, _("Hersteller"));
    }

    /*******************************************************
     * Static functions
     *******************************************************/

    public static function defaultPermissionsLoop()
    {
        //Create a temp object for pass by reference.
        $tmp = null;
        $manager = new static($tmp);
        return $manager->generatePermissionsLoop();

    }
}