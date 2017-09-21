<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 21.09.2017
 * Time: 12:33
 */

namespace PartDB\Permissions;


class DatabasePermission extends BasePermission
{
    const SEE_STATUS    = "see_status";
    const UPDATE_DB     = "update_db";
    const READ_DB_SETTINGS = "read_db_settings";
    const WRITE_DB_SETTINGS = "write_db_settings";

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
        $operations[] = static::buildOperationArray(0, static::SEE_STATUS, _("Status anzeigen"));
        $operations[] = static::buildOperationArray(2, static::UPDATE_DB, _("Datenbank aktualisieren"));
        $operations[] = static::buildOperationArray(4, static::READ_DB_SETTINGS, _("Datenbankeinstellungen anzeigen"));
        $operations[] = static::buildOperationArray(2, static::WRITE_DB_SETTINGS, _("Datenbankeinstellungen ändern"));
        return $operations;
    }

    protected function modifyValueBeforeSetting($operation, $new_value, $data)
    {
        //Set read permission, too, when you get edit permissions.
        if ($operation == static::UPDATE_DB && $new_value == static::ALLOW) {
            return parent::writeBitPair($data, static::opToBitN(static::SEE_STATUS), static::ALLOW);
        }

        //Set read permission, too, when you get edit permissions.
        if ($operation == static::WRITE_DB_SETTINGS && $new_value == static::ALLOW) {
            return parent::writeBitPair($data, static::opToBitN(static::READ_DB_SETTINGS), static::ALLOW);
        }

        return $data;
    }
}