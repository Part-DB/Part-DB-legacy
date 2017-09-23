<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 13.09.2017
 * Time: 13:08
 */

namespace PartDB\Interfaces;


use PartDB\Permissions\PermissionManager;

interface IHasPermissions
{
    /**
     * Gets the integer value of a permission of the current object.
     * @param $permsission_name string The name of the permission that should be get. (Without "perms_")
     * @return int The int value of the requested permission.
     */
    public function getPermissionRaw($permsission_name);

    /**
     * Sets the integer value of a permission of the current object.
     * @param $permsission_name string The name of the permission that should be get. (Without "perms_")
     * @param $value int The value the permission should be set to.
     */
    public function setPermissionRaw($permission_name, $value);

    /**
     * Returns the PermissionManager of the (permission) parent of the current object.
     * @return PermissionManager|null The PermissionManager of the parent, or null if the current object has no parent.
     */
    public function &getParentPermissionManager();
}