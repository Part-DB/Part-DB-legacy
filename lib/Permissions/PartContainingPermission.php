<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 21.09.2017
 * Time: 11:52
 */

namespace PartDB\Permissions;


class PartContainingPermission extends StructuralPermission
{
    const LIST_PARTS  = "list_parts";

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
        $operations[] = static::buildOperationArray(10, static::LIST_PARTS, _("Teile Auflisten"));

        return $operations;
    }

    protected function modifyValueBeforeSetting($operation, $new_value, $data)
    {
        //Set read permission, too, when you get edit permissions.
        if (($operation == static::EDIT
                || $operation == static::DELETE
                || $operation == static::MOVE
                || $operation == static::CREATE
                || $operation == static::LIST_PARTS)
            && $new_value == static::ALLOW) {
            return parent::writeBitPair($data, static::opToBitN(static::READ), static::ALLOW);
        }

        return $data;
    }


}