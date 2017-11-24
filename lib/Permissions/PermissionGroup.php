<?php
/*
    Part-DB Version 0.4+ "nextgen"
    Copyright (C) 2017 Jan Böhmer
    https://github.com/jbtronics

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
