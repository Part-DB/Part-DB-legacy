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
    const UNKNONW_INSTOCK_PARTS = "unknown_instock_parts";
    const CHANGE_FAVORITE = "change_favorite";
    const SHOW_FAVORITE_PARTS = "show_favorite_parts";
    const SHOW_LAST_EDIT_PARTS = "show_last_edit_parts";
    const SHOW_USERS = "show_users";
    const SHOW_HISTORY = "show_history";

    static protected $operation_cache = null;

    /**
     * Returns an array of all available operations for this Permission.
     * @return array All availabel operations.
     */
    public static function listOperations() : array
    {
        if(!isset(static::$operation_cache)) {
            /**
             * Dont change these definitions, because it would break compatibility with older database.
             * However you can add other definitions, the return value can get high as 62, as the DB uses a 32bit integer.
             */
            $operations = array();
            $operations[static::READ] = static::buildOperationArray(0, static::READ, _("Anzeigen"));
            $operations[static::EDIT] = static::buildOperationArray(2, static::EDIT, _("Bearbeiten"));
            $operations[static::CREATE] = static::buildOperationArray(4, static::CREATE, _("Anlegen"));
            $operations[static::MOVE] = static::buildOperationArray(6, static::MOVE, _("Verschieben"));
            $operations[static::DELETE] = static::buildOperationArray(8, static::DELETE, _("Löschen"));
            $operations[static::SEARCH] = static::buildOperationArray(10, static::SEARCH, _("Suchen"));
            $operations[static::ALL_PARTS] = static::buildOperationArray(12, static::ALL_PARTS, _("Alle Teile auflisten"));
            $operations[static::ORDER_PARTS] = static::buildOperationArray(14, static::ORDER_PARTS, _("Zu bestellende Teile auflisten"));
            $operations[static::NO_PRICE_PARTS] = static::buildOperationArray(16, static::NO_PRICE_PARTS, _("Teile ohne Preis auflisten"));
            $operations[static::OBSOLETE_PARTS] = static::buildOperationArray(18, static::OBSOLETE_PARTS, _("Obsolete Teile auflisten"));
            $operations[static::UNKNONW_INSTOCK_PARTS] = static::buildOperationArray(20, static::UNKNONW_INSTOCK_PARTS, _("Teile mit unbekanntem Lagerbestand auflisten"));
            $operations[static::CHANGE_FAVORITE] = static::buildOperationArray(22, static::CHANGE_FAVORITE, _("Favoritenstatus ändern"));
            $operations[static::SHOW_FAVORITE_PARTS] = static::buildOperationArray(24, static::SHOW_FAVORITE_PARTS, _("Favorisierte Bauteile auflisten"));
            $operations[static::SHOW_LAST_EDIT_PARTS] = static::buildOperationArray(26, static::SHOW_LAST_EDIT_PARTS, _("Zuletzt bearbeitete/hinzugefügte Bauteile auflisten"));
            $operations[static::SHOW_USERS] = static::buildOperationArray(28, static::SHOW_USERS, _("Letzten bearbeitenden Nutzer anzeigen"));
            $operations[static::SHOW_HISTORY] = static::buildOperationArray(30, static::SHOW_HISTORY, _("Historie anzeigen"));

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
                || $operation == static::SEARCH
                || $operation == static::ALL_PARTS)
            && $new_value == static::ALLOW) {
            return parent::writeBitPair($data, static::opToBitN(static::READ), static::ALLOW);
        }

        return $data;
    }
}
