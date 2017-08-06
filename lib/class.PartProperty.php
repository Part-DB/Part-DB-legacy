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

    public function __construct($property_string, $property_name = "", $property_value = "")
    {
        if($property_name !== "" && $property_value !== "")
        {
            $this->name = $property_name;
            $this->value = $property_value;
            $this->raw_string = $property_string;
        }
        else
        {
            $this->raw_string = $property_string;
            $this->name = $this->parse_name($property_string);
        }
    }

    public function get_value()
    {
        return $this->value;
    }

    public function get_name()
    {
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

    private function parse_name($str)
    {
        $results = array();
        //$pattern = '/[a-zäöüß]+\s?[\=\:]/i';
        $pattern = '/[[:alnum:]]+[[:space:]]?[\=\:]/i';
        if(preg_match($str,$pattern, $results) == 1)
        {
            //if(count($results > 1))
             //   throw new Exception("Can not parse the PropertyName of" . $str . " ! Multiple pattern match found!");



        }
        else
        {
            throw new Exception("Can not parse the PropertyName of" . $str . " ! No pattern match found!");
        }
    }

    private function remove_delimiters($str)
    {
        $delimiter_list = array("=", ",");

        str_replace($delimiter_list, "", $str);
    }


    public static function parse_description($description)
    {
        $pattern = '/(\w+)\s?[\=\:]\s?(\w+([\.\,]\w+)?)/i';

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