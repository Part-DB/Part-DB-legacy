<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 06.08.2017
 * Time: 17:30
 */

class PartProperty
{

    private $name;
    private $value;
    private $raw_string;

    public function __construct($property_string, $property_name, $property_value)
    {
        $property_string = trim($property_string);
        $property_name = trim($property_name);
        $property_value = trim($property_value);

        $this->name = $property_name;
        $this->value = $property_value;
        $this->raw_string = $property_string;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function get_name($with_colon = true)
    {
        if($with_colon === true)
            return $this->name . ":";
        else
            return $this->name;
    }

    public function get_array($named = true)
    {
        if($named == true)
        {
            return array(
                'name' => $this->get_name(),
                'value' => $this->get_value()
            );
        }
        else
        {
            return array($this->get_name(), $this->get_value());
        }
    }

    /****************************************************************
     * Parse functions
     ****************************************************************/


    public static function parse_description($description)
    {
        $pattern = '/([^\,\;]+)\s?[\=\:]\s?(\w+(?:[\.\,]\w+)?(?:[^\,\;]|\,\w)*)/i';

        $pattern = regex_allow_umlauts($pattern);

        preg_match_all($pattern, $description, $results);

        $raw_strings = $results[0];
        $names  = $results[1];
        $values = $results[2];

        $arr = array();
        for ($n = 0; $n<count($names); $n++)
        {
            $arr[] = new PartProperty($raw_strings[$n], $names[$n], $values[$n]);
        }

        return $arr;
    }
}