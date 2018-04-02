<?php
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
    public function __construct($database, $current_user, $log)
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
    public function getMostUsedCategories($limit = 25)
    {
        if(!is_int($limit)) {
            throw new \InvalidArgumentException(_('$limit muss eine Integerzahl sein!)'));
        }

        $query = "SELECT categories.name AS name, COUNT(parts.id_category) AS count FROM categories, parts"
         ." WHERE categories.id = parts.id_category GROUP BY parts.id_category ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public function getMostUsedLocations($limit = 25)
    {
        if(!is_int($limit)) {
            throw new \InvalidArgumentException(_('$limit muss eine Integerzahl sein!)'));
        }

        $query = "SELECT storelocations.name AS name, COUNT(parts.id_storelocation) AS count FROM storelocations, parts"
            ." WHERE storelocations.id = parts.id_storelocation GROUP BY parts.id_storelocation ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public function getMostUsedFootprints($limit = 25)
    {
        if(!is_int($limit)) {
            throw new \InvalidArgumentException(_('$limit muss eine Integerzahl sein!)'));
        }

        $query = "SELECT footprints.name AS name, COUNT(parts.id_footprint) AS count FROM footprints, parts"
            ." WHERE footprints.id = parts.id_footprint GROUP BY parts.id_footprint ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public function getMostUsedManufacturers($limit = 25)
    {
        if(!is_int($limit)) {
            throw new \InvalidArgumentException(_('$limit muss eine Integerzahl sein!)'));
        }

        $query = "SELECT manufacturers.name AS name, COUNT(parts.id_manufacturer) AS count FROM manufacturers, parts"
            ." WHERE manufacturers.id = parts.id_manufacturer GROUP BY parts.id_manufacturer ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public function getPartsWithMostInstock($limit = 25)
    {
        $query = "SELECT parts.name AS name, parts.instock AS count FROM parts ORDER BY count DESC LIMIT $limit";
        $values = array();

        return $this->database->query($query, $values);
    }

    public static function arrayToChartJSData($array, $label, $bg_color = self::COLOR_BLUE)
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