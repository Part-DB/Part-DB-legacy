<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 20.09.2017
 * Time: 21:36
 */

namespace PartDB\Permissions;


class SelfPermission extends BasePermission
{

    const EDIT_USERNAME  = "edit_username";
    const EDIT_INFOS     = "edit_infos";
    const SHOW_PERMISSIONS = "show_perms";

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
        $operations[] = static::buildOperationArray(0, static::EDIT_INFOS, _("Informationen ändern"));
        $operations[] = static::buildOperationArray(2, static::EDIT_USERNAME, _("Benutzername ändern"));
        $operations[] = static::buildOperationArray(4, static::SHOW_PERMISSIONS, _("Berechtigungen auflisten"));

        return $operations;
    }

    protected function modifyValueBeforeSetting($operation, $new_value, $data)
    {

        return $data;
    }

}