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

namespace PartDB\PartProperty;

use PartDB\Interfaces\IAPIModel;

class PartProperty implements IAPIModel
{
    private $name;
    private $value;
    private $raw_string;

    /**
     * Creates a new PartProperty object with the given parameters.
     * @param $property_string string The original (sub)string, where this property get extracted from. (eg. "Name: 1234")
     * @param $property_name string The extracted name of the Property, without delimiter (e.g. "Name")
     * @param $property_value string The extracted value of the Property (e.g. "1234")
     */
    public function __construct(string $property_string, string $property_name, string $property_value)
    {
        $property_string = trim($property_string);
        $property_name = trim($property_name);
        $property_value = trim($property_value);

        $this->name = $property_name;
        $this->value = $property_value;
        $this->raw_string = $property_string;
    }

    /**
     * Returns the value part of this Property
     * @return string The value part of this property
     */
    public function getValue() : string
    {
        return $this->value;
    }


    /**
     * Returns the name part of this Property. Either with a trailing colon or without.
     * @param bool $with_colon If true, a colon get added at the end of the name.
     * @return string The name part of this property.
     */
    public function getName($with_colon = true)
    {
        if ($with_colon === true) {
            return $this->name . ":";
        } else {
            return $this->name;
        }
    }

    /**
     * Returns a array with the contents of this Property. Either with named keys(default) or with 0 and 1.
     * @param bool $named If true the array has named keys.
     * @return array An array with the contents of this Property. 'name'/0 contains the name part. 'value'/1 the value part.
     */
    public function getArray($named = true)
    {
        if ($named == true) {
            return array(
                'name' => $this->getName(),
                'value' => $this->getValue()
            );
        } else {
            return array($this->getName(), $this->getValue());
        }
    }

    /****************************************************************
     * Parse functions
     ****************************************************************/


    /**
     * Parses the in $description given string, extracts different part properties and retuns an array
     *          PartProperty objects.
     * @param $description string The description string which should be parsed
     * @return PartProperty[] An array containing PartProperty objects. Empty if no properties could be parsed.
     */
    public static function parseDescription($description)
    {
        $pattern = '/([^\,\;\n]+)\s?[\=\:]\s?(\w+(?:[\.\,]\w+)?(?:[^\,\;\n]|\,\w)*)/iu';

        //$pattern = regex_allow_umlauts($pattern);

        preg_match_all($pattern, $description, $results);

        $raw_strings = $results[0];
        $names  = $results[1];
        $values = $results[2];

        $arr = array();
        for ($n = 0, $nMax = count($names); $n< $nMax; $n++) {
            if (!empty(trim($names[$n])) && empty(trim($values[$n]))) {
                $arr[] = new PartProperty($raw_strings[$n], $names[$n], $values[$n]);
            }
        }

        return $arr;
    }

    /**
     * Returns a Array representing the current object.
     * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
     * @return array A array representing the current object.
     */
    public function getAPIArray(bool $verbose = false) : array
    {
        return $this->getArray(true);
    }
}
