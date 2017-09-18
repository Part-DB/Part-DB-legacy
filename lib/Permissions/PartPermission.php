<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 18.09.2017
 * Time: 21:04
 */

namespace PartDB\Permissions;


class PartPermission extends BasePermission
{
    const CREATE = "create";
    const READ  = "read";
    const EDIT  = "edit";
    const MOVE  = "move";
    const DELETE = "delete";
    const FOOTPRINT = "footprint";

    /**
     * Returns an array of all available operations for this Permission.
     * @return array All availabel operations.
     */
    public static function listOperations()
    {
        /**
         * Dont change these definitions, because it would break compatibility with older database.
         * However you can add other definitions, the return value can get high as 30, as the DB uses a 32bit integer.
         */
        $operations = array();
        $operations[] = static::buildOperationArray(0, static::READ, _("Anzeigen"));
        $operations[] = static::buildOperationArray(2, static::EDIT, _("Bearbeiten"));
        $operations[] = static::buildOperationArray(4, static::CREATE, _("Anlegen"));
        $operations[] = static::buildOperationArray(6, static::MOVE, _("Verschieben"));
        $operations[] = static::buildOperationArray(8, static::DELETE, _("Löschen"));

        return $operations;
    }
}