<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 13.09.2017
 * Time: 12:55
 */

namespace PartDB\Tools;

/**
 * Use permissions objects of this class, if you want to control permissions on StructuralDBElements like Category or Footprint
 * @package PartDB\Tools
 */
class StructuralPermission extends BasePermission
{
    const CREATE = "create";
    const READ  = "read";
    const EDIT  = "edit";
    const MOVE  = "move";
    const DELETE = "delete";

    /**
     * Gets the bit number for every operation (see Constants in Permission class).
     * @param $op string The operation for which the bit number should be calculated.
     * @return int The bitnumber for the operation.
     */
    protected static function opToBitN($op)
    {
        $op = mb_strtolower($op);
        /**
         * Dont change these definitions, because it would break compatibility with older database.
         * However you can add other definitions, the return value can get high as 30, as the DB uses a 32bit integer.
         */
        switch ($op) {
            case static::READ:
                return 0;
            case static::EDIT:
                return 2;
            case static::CREATE:
                return 4;
            case static::MOVE:
                return 6;
            case static::DELETE:
                return 8;
        }

        throw new \InvalidArgumentException(_('$op ist keine gültige Operation!'));
    }

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