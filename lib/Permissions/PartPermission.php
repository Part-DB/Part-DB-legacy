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
    const SEARCH    = "search";
    const ALL_PARTS = "all_parts";
    const ORDER_PARTS = "order_parts";
    const NO_PRICE_PARTS = "no_price_parts";
    const OBSOLETE_PARTS = "obsolete_parts";

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
        $operations[] = static::buildOperationArray(10, static::SEARCH, _("Suchen"));
        $operations[] = static::buildOperationArray(12, static::ALL_PARTS, _("Alle Teile auflisten"));
        $operations[] = static::buildOperationArray(14, static::ORDER_PARTS, _("Zu bestellende Teile auflisten"));
        $operations[] = static::buildOperationArray(16, static::NO_PRICE_PARTS, _("Teile ohne Preis auflisten"));
        $operations[] = static::buildOperationArray(18, static::OBSOLETE_PARTS, _("Obsolente Teile auflisten"));

        return $operations;
    }

    protected function modifyValueBeforeSetting($operation, $new_value, $data)
    {
        //Set read permission, too, when you get edit permissions.
        if (($operation == static::EDIT
                || $operation == static::DELETE
                || $operation == static::MOVE
                || $operation == static::CREATE
                || $operation == static::SEARCH
                || $operation == static::ALL_PARTS)
            && $new_value == static::ALLOW) {
            return parent::writeBitPair($data, static::opToBitN(static::READ), static::ALLOW);
        }

        return $data;
    }
}