<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 11.08.2017
 * Time: 17:06
 */

class PartNameRegEx
{
    private static $pattern = '/^(\/.+\/\w*)(?:@([fn]+))?(?:\$(.+))*$/';

    private $regex = "";
    private $flags_str = "";
    private $capture_names = array();

    /**
     * PartNameRegEx constructor.
     * @param $partname_regex string The string which should be parsed
     * @throws Exception If there was an Error.
     */
    public function __construct($partname_regex)
    {
        if(!self::is_valid($partname_regex))
            throw new Exception("The PartNameRegex string (" . $partname_regex . ") is not valid!");

        $this->parse($partname_regex);
    }

    private function parse($str)
    {
        $matches = array();
        preg_match(PartNameRegEx::$pattern, $str, $matches);

        $this->regex = regex_allow_umlauts($matches[1]);
        $this->flags_str = $matches[2];

        $this->capture_names = explode("$", $matches[3]);
    }


    /**
     * Returns the Regular Expression part.
     * @return string The Reguala Expression.
     */
    public function get_regex()
    {
        return $this->regex;
    }

    public function get_flags()
    {
        return $this->flags_str;
    }

    /**
     * Checks if the Name filter is enforced, so it cant be ignored.
     * @return bool True, if the filter is enforced.
     */
    public function is_enforced()
    {
        return strcontains($this->flags_str, "f");
    }

    /**
     * Check if this RegEx does not apply a filter to the name.
     * @return bool True, if RegEx is not a filter.
     */
    public function is_nofilter()
    {
        return strcontains($this->flags_str, "n");
    }

    /**
     * Gets the names of the capture groups of this regex.
     * @return array
     */
    public function get_capturegroup_names()
    {
        return $this->capture_names;
    }

    /**
     * Gets the properties based on the name and the capture group names.
     * @param $name The name from which the properties should be parsed
     * @return array A array of PartProperty Elements.
     */
    public function get_properties($name)
    {
        preg_match($this->get_regex(), $name, $tmp);

        $properties = array();

        for ($n=0; $n<count($this->capture_names); $n++)
        {
            if(empty($tmp[$n + 1])) //Ignore empty values
                continue;
            $properties[] = new PartProperty("", $this->capture_names[$n], $tmp[$n + 1]);
        }

        return $properties;
    }

    /**
     * Checks if a name is valid.
     * @param $name string The name which should be checked.
     * @return bool True if the name is valid, or the nofilter flag is set.
     */
    public function check_name($name)
    {
        if($this->is_nofilter()) //When we dont filter, every name is ok.
            return true;

        if(preg_match($this->get_regex(), $name) == 1)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * Static functions
     */

    /**
     * Check if the string is valid.
     * @param $partname_regex string The string which should be checked.
     * @return bool True, if the string is valid.
     */
    public static function is_valid($partname_regex)
    {
        if(preg_match(PartNameRegEx::$pattern,$partname_regex) == 1)
            return true;
        else
            return false;

    }

    public static function get_pattern($for_html_pattern = false)
    {
        if($for_html_pattern)
        {
            $pattern = regex_strip_slashes(regex_allow_umlauts(PartNameRegEx::$pattern));
            return "($pattern)|(@@)";
        }
        else
        {
            return regex_allow_umlauts(PartNameRegEx::$pattern);
        }
    }

}