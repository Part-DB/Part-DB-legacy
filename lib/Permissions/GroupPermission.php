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

use PartDB\User;

class GroupPermission extends StructuralPermission
{
    const EDIT_PERMISSIONS = "edit_permissions";

    protected static $operation_cache = null;

    /**
     * Returns an array of all available operations for this Permission.
     * @return array All availabel operations.
     */
    public static function listOperations() : array
    {
        if (!isset(static::$operation_cache)) {
            /**
             * Dont change these definitions, because it would break compatibility with older database.
             * However you can add other definitions, the return value can get high as 30, as the DB uses a 32bit integer.
             */
            $operations = array();
            $operations[static::READ] = static::buildOperationArray(0, static::READ, _("Anzeigen"));
            $operations[static::EDIT] = static::buildOperationArray(2, static::EDIT, _("Bearbeiten"));
            $operations[static::CREATE] = static::buildOperationArray(4, static::CREATE, _("Anlegen"));
            $operations[static::MOVE] = static::buildOperationArray(6, static::MOVE, _("Verschieben"));
            $operations[static::DELETE] = static::buildOperationArray(8, static::DELETE, _("Löschen"));
            $operations[static::EDIT_PERMISSIONS] = static::buildOperationArray(10, static::EDIT_PERMISSIONS, _("Berechtigungen ändern"));

            static::$operation_cache = $operations;
        }
        return static::$operation_cache;
    }

    protected function modifyValueBeforeSetting(string $operation, int $new_value, int $data) : int
    {
        //Set read permission, too, when you get edit permissions.
        if (($operation == static::EDIT
                || $operation == static::DELETE
                || $operation == static::MOVE
                || $operation == static::CREATE
            || $operation == static::EDIT_PERMISSIONS)
            && $new_value == static::ALLOW) {
            return parent::writeBitPair($data, static::opToBitN(static::READ), static::ALLOW);
        }

        return $data;
    }

    public function generateLoopRow(bool $read_only = false, bool $inherit = false) : array
    {
        if (!$read_only) {
            //User cannot change its own User permission, so make his permission read-only.
            if ($this->perm_holder instanceof User) {
                $perm_holder = $this->perm_holder;
                /** @var $perm_holder User */
                if ($perm_holder->isLoggedInUser()) {
                    $read_only = true;
                }
            }
        }
        return parent::generateLoopRow($read_only, $inherit);
    }
}
