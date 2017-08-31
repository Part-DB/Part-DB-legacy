<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 11.08.2017
 * Time: 17:06
 */

namespace PartDB\PartProperty;

use Exception;

class PartNameRegEx
{
    /** @var string  */
    private static $pattern = '/^(\/.+\/)(?:@([fn]+))?(?:\$(.+))*$/';

    /** @var string  */
    private $regex = "";
    /** @var string  */
    private $flags_str = "";
    /** @var string[] */
    private $capture_names = array();

    /**
     * PartNameRegEx constructor.
     * @param $partname_regex string The string which should be parsed
     * @throws Exception If there was an Error.
     */
    public function __construct($partname_regex)
    {
        if (!empty($partname_regex)) {
            if (!self::isValid($partname_regex)) {
                throw new Exception("The PartNameRegex string (" . $partname_regex . ") is not valid!");
            }

            $this->parse($partname_regex);
        }
    }

    private function parse($str)
    {
        $matches = array();
        mb_ereg(self::getPattern(false, true), $str, $matches);

        $this->regex = $matches[1];
        $this->flags_str = $matches[2];

        $this->capture_names = explode("$", $matches[3]);
    }


    /**
     * Returns the Regular Expression part.
     * @param $is_mb bool True if should be prepared for the multibyte regex functions. (Strip slashes)
     * @return string The Reguala Expression.
     */
    public function getRegex($is_mb = false)
    {
        if ($is_mb) {
            return regexStripSlashes($this->regex);
        } else {
            return $this->regex;
        }
    }

    public function getFlags()
    {
        return $this->flags_str;
    }

    /**
     * Checks if the Name filter is enforced, so it cant be ignored.
     * @return bool True, if the filter is enforced.
     */
    public function isEnforced()
    {
        return strcontains($this->flags_str, "f");
    }

    /**
     * Check if this RegEx does not apply a filter to the name.
     * @return bool True, if RegEx is not a filter.
     */
    public function isNofilter()
    {
        return strcontains($this->flags_str, "n");
    }

    /**
     * Gets the names of the capture groups of this regex.
     * @return array
     */
    public function getCapturegroupNames()
    {
        return $this->capture_names;
    }

    /**
     * Gets the properties based on the name and the capture group names.
     * @param $name string The name from which the properties should be parsed
     * @return array A array of PartProperty Elements.
     */
    public function getProperties($name)
    {
        $tmp = array();

        if (_empty($this->getRegex())) {
            return $tmp;
        }

        mb_eregi($this->getRegex(true), $name, $tmp);

        $properties = array();

        for ($n=0; $n<count($this->capture_names); $n++) {
            if (empty($tmp[$n + 1])) { //Ignore empty values
                continue;
            }
            $properties[] = new PartProperty("", $this->capture_names[$n], $tmp[$n + 1]);
        }

        return $properties;
    }

    /**
     * Checks if a name is valid.
     * @param $name string The name which should be checked.
     * @return bool True if the name is valid, or the nofilter flag is set.
     */
    public function checkName($name)
    {
        if ($this->isNofilter() || _empty($this->getRegex())) { //When we dont filter, every name is ok.
            return true;
        }

        if (mb_eregi($this->getRegex(true), $name) !== false) {
            return true;
        } else {
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
    public static function isValid($partname_regex)
    {
        return mb_ereg_match(PartNameRegEx::getPattern(false, true), $partname_regex);
    }

    public static function getPattern($for_html_pattern = false, $for_mb = false)
    {
        if ($for_html_pattern) {
            $pattern = regexStripSlashes(regexAllowUmlauts(PartNameRegEx::$pattern));
            return "($pattern)|(@@)";
        } elseif ($for_mb) {
            return regexStripSlashes(PartNameRegEx::$pattern);
        } else {
            return regexAllowUmlauts(PartNameRegEx::$pattern);
        }
    }
}
