<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 03.01.2018
 * Time: 17:36
 */

namespace PartDB\Permissions;

class LabelPermission extends BasePermission
{
    const CREATE_LABELS = "create_labels";
    const EDIT_OPTIONS = "edit_options";
    const DELETE_PROFILES = "delete_profiles";
    const EDIT_PROFILES = "edit_profiles";

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
        $operations[] = static::buildOperationArray(0, static::CREATE_LABELS, _("Labels erzeugen"));
        $operations[] = static::buildOperationArray(2, static::EDIT_OPTIONS, _("Labels anpassen"));
        $operations[] = static::buildOperationArray(4, static::DELETE_PROFILES, _("Profile löschen"));
        $operations[] = static::buildOperationArray(6, static::EDIT_PROFILES, _("Profile anlegen/bearbeiten"));

        return $operations;
    }

    protected function modifyValueBeforeSetting($operation, $new_value, $data)
    {
        //Set read permission, too, when you get edit permissions.
        if (($operation == static::EDIT_OPTIONS
                || $operation == static::EDIT_PROFILES
                || $operation == static::DELETE_PROFILES
                || $operation == static::EDIT_OPTIONS)
            && $new_value == static::ALLOW) {
            return parent::writeBitPair($data, static::opToBitN(static::CREATE_LABELS), static::ALLOW);
        }

        return $data;
    }
}
