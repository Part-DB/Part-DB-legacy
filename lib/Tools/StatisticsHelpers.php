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

/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 31.03.2018
 * Time: 12:36
 */

namespace PartDB\Tools;

use PartDB\Database;
use PartDB\Log;
use PartDB\User;

class StatisticsHelpers
{
    protected $database;
    protected $current_user;
    protected $log;

    const COLOR_BLUE = "rgba(66, 139, 202, 0.4)";
    const COLOR_RED = "rgba(217,83,79, 0.4)";
    const COLOR_LIGHT_BLUE = "rgba(91,192,222, 0.4)";
    const COLOR_GREEN = "rgba(92,184,92, 0.4)";
    const COLOR_ORANGE = "rgba(240, 173, 78, 0.4)";

    /**
     * Creates an StatisticsHelper object, using the following objects for Database Access.
     * @param $database Database
     * @param $current_user User
     * @param $log Log
     */
    public function __construct(Database $database, User $current_user, Log $log)
    {
        $this->database = $database;
        $this->current_user = $current_user;
        $this->log = $log;
    }

    /**
     * Returns an array, with data about the most used categories.
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getMostUsedCategories(int $limit = 25) : array
    {
        if (!is_int($limit)) {
            throw new \InvalidArgumentException(_('$limit muss eine Integerzahl sein!)'));
        }

        $query = "SELECT categories.name AS name, COUNT(parts.id_category) AS count FROM categories, parts"
         ." WHERE categories.id = parts.id_category GROUP BY parts.id_category ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public function getMostUsedLocations(int $limit = 25) : array
    {
        if (!is_int($limit)) {
            throw new \InvalidArgumentException(_('$limit muss eine Integerzahl sein!)'));
        }

        $query = "SELECT storelocations.name AS name, COUNT(parts.id_storelocation) AS count FROM storelocations, parts"
            ." WHERE storelocations.id = parts.id_storelocation GROUP BY parts.id_storelocation ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public function getMostUsedFootprints(int $limit = 25) : array
    {
        if (!is_int($limit)) {
            throw new \InvalidArgumentException(_('$limit muss eine Integerzahl sein!)'));
        }

        $query = "SELECT footprints.name AS name, COUNT(parts.id_footprint) AS count FROM footprints, parts"
            ." WHERE footprints.id = parts.id_footprint GROUP BY parts.id_footprint ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public function getMostUsedManufacturers(int $limit = 25) : array
    {
        if (!is_int($limit)) {
            throw new \InvalidArgumentException(_('$limit muss eine Integerzahl sein!)'));
        }

        $query = "SELECT manufacturers.name AS name, COUNT(parts.id_manufacturer) AS count FROM manufacturers, parts"
            ." WHERE manufacturers.id = parts.id_manufacturer GROUP BY parts.id_manufacturer ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public function getPartsWithMostInstock(int $limit = 25) : array
    {
        $query = "SELECT parts.name AS name, parts.instock AS count FROM parts ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public static function arrayToChartJSData(array $array, string $label, string $bg_color = self::COLOR_BLUE) : string
    {
        //Split array in name and count section
        $names = array();
        $counts = array();
        foreach ($array as $element) {
            $names[] = $element['name'];
            $counts[] = $element['count'];
        }

        $data = array();
        $data['labels'] = $names;

        $dataset = array("label" => $label,
                       "data" => $counts,
                        "backgroundColor" => $bg_color);

        $data['datasets'] = array($dataset);

        return json_encode($data);
    }
}
