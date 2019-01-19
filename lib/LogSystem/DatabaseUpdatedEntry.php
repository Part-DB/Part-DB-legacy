<?php

/**
 *
 * Part-DB Version 0.4+ "nextgen"
 * Copyright (C) 2016 - 2018 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 *
 */

namespace PartDB\LogSystem;

use PartDB\Base\NamedDBElement;
use PartDB\Database;
use PartDB\Log;
use PartDB\User;

class DatabaseUpdatedEntry extends BaseEntry
{
    /** @var int */
    protected $old_version;
    protected $new_version;

    protected $successful;

    public function __construct(Database $database, User $current_user, Log $log, $id, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $db_data);

        //Fill our extra values.

        $extra_array = $this->deserializeExtra();

        $this->old_version = $extra_array['o'];
        $this->new_version = $extra_array['n'];
        $this->successful = $extra_array['s'];
    }

    /**
     * Returns the old database version (the one before the update)
     * @return int The old version.
     */
    public function getOldVersion() : int
    {
        return $this->old_version;
    }

    /**
     * Returns the new database version (the one after the update)
     * @return int The new instock value.
     */
    public function getNewVersion() : int
    {
        return $this->new_version;
    }

    /**
     * Checks if the database update associated with this entry was successful.
     * @return bool True if the
     */
    public function isSuccessful() : bool
    {
        return $this->successful;
    }

    public function getSuccessString() : string
    {
        if ($this->isSuccessful()) {
            return _('Erfolgreich');
        } else {
            return _('Fehlgeschlagen');
        }
    }

    public function getExtra(bool $html = false) : string
    {
        return $this->getSuccessString() . _('; Alte Version: ') . $this->getOldVersion() . _('; Neue Version: ') . $this->getNewVersion();
    }

    /**
     * Returns the a text representation of the target
     * @return string The text describing the target
     */
    public function getTargetText() : string
    {
        return _('Datenbank');
    }

    /**
     * Return a link to the target. Returns empty string if no link is available.
     * @return string the link to the target.
     */
    public function getTargetLink() : string
    {
        return '';
    }



    /**
     * Adds a new log entry to the database.
     * @param $database Database The database which should be used for requests.
     * @param $current_user User The database which should be used for requests.
     * @param $log Log The database which should be used for requests.
     * @param $part NamedDBElement The ip adress the user loggs in from
     *
     * @return static|BaseEntry The new created Entry.
     *
     * @throws \Exception
     */
    public static function add(Database $database, User $current_user, Log $log, int $old_version, int $new_version, bool $successful = true)
    {
        $extra_array = array();
        $extra_array['o'] = $old_version; //Old version
        $extra_array['n'] = $new_version; //New version
        $extra_array['s'] = $successful;

        $level = Log::LEVEL_WARNING;

        return static::addEntry(
            $database,
            $current_user,
            $log,
            Log::TYPE_DATABASEUPDATE,
            $level,
            $current_user->getID(),
            0,
            0,
            $extra_array
        );
    }
}
