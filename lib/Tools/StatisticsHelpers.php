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

    public function getMostUsedCategories($limit = 25)
    {
        $query = "SELECT categories.name AS name, COUNT(parts.id_category) AS count FROM categories, parts"
         ." WHERE categories.id = parts.id_category GROUP BY parts.id_category ORDER BY count DESC LIMIT 25";
        $values = array();

        return $this->database->query($query, $values);
    }

    public static function arrayToChartJSData($array, $label)
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
                        "backgroundColor" => "rgba(66, 139, 202, 0.4)");

        $data['datasets'] = array($dataset);

        return json_encode($data);
    }

}