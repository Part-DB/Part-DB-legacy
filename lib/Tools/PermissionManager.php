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
     * @param $perm_holder IHasPermissions
     */
    public function __construct(&$perm_holder)
    {
        $this->perm_holder = $perm_holder;
        $this->fillPermissionsArray();
        $this->permissions = array();

        $this->fillPermissionsArray();
    }



    public function canDo($target, $operation)
    {

    }

    public function generatePermissionsLoop()
    {
        $loop = array();
        foreach ($this->permissions as $permission) {
            $loop[] = $permission->generateLoopRow();
        }

        return $loop;
    }


    protected function fillPermissionsArray()
    {
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::STORELOCATIONS, _("Lagerorte"));
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::FOOTRPINTS, _("Footprints"));
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::CATEGORIES, _("Kategorien"));
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::SUPPLIERS, _("Lieferanten"));
        $this->permissions[] = new StructuralPermission($this->perm_holder, static::MANUFACTURERS, _("Hersteller"));
    }
}