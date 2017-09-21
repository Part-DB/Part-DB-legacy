<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 21.09.2017
 * Time: 13:14
 */

namespace PartDB\Permissions;


class ConfigPermission extends BasePermission
{
    const READ_CONFIG        = "read_config";
    const EDIT_CONFIG       = "edit_config";
    const CHANGE_ADMIN_PW   = "change_admin_pw";
    const SERVER_INFO       = "server_info";

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
        $operations[] = static::buildOperationArray(0, static::READ_CONFIG, _("Konfiguration anzeigen"));
        $operations[] = static::buildOperationArray(2, static::EDIT_CONFIG, _("Konfiguration bearbeiten"));
        $operations[] = static::buildOperationArray(4, static::CHANGE_ADMIN_PW, _("Administratorpassword ändern"));
        $operations[] = static::buildOperationArray(6, static::SERVER_INFO, _("Serverinformationen anzeigen"));
        return $operations;
    }

    protected function modifyValueBeforeSetting($operation, $new_value, $data)
    {

        //Set read permission, too, when you get edit permissions.
        if ($operation == static::EDIT_CONFIG && $new_value == static::ALLOW) {
            return parent::writeBitPair($data, static::opToBitN(static::READ_CONFIG), static::ALLOW);
        }


        return $data;
    }
}