<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 18.09.2017
 * Time: 16:59
 */

namespace PartDB\Permissions;

/**
 * Use this class to group multiple permissions into one table.
 * @package PartDB\Permissions
 */
class PermissionGroup
{
    /** @var string */
    protected $title = "";
    /*** @var string */
    protected $description = "";
    /** @var  BasePermission[] */
    protected $permissions;

    /**
     * Creates a new PermissionGroup object.
     * @param string $title The title of the new Permission group.
     * @param BasePermission[] $permissions All permissions of the Permissiongroup.
     * @param string $description A string describing the new permissiongroup.
     */
    public function __construct($title, &$permissions, $description = "")
    {
        $this->title = $title;
        $this->permissions = $permissions;
        $this->description = $description;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function &getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Generates a template loop for smarty_permissions.tpl (the permissions table).
     * @param $read_only boolean When true, all checkboxes are disabled (greyed out)
     * @param $inherit boolean If true, inherit values, are resolved.
     * @return array The loop for the permissions table.
     */
    public function generatePermissionsLoop($read_only = false, $inherit = false)
    {
        $perms = array();
        foreach ($this->permissions as $permission) {
            $perms[] = $permission->generateLoopRow($read_only, $inherit);
        }

        return array("permissions" => $perms,
            "title" => $this->title,
            "description" => $this->description
            );
    }
}