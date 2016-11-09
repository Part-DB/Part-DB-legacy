<?php

/**
 * A Class describing different Modes for API access.
 *
 * This class contains various functions to work with the  different API Modes.
 * The main definitions of the modes are public consts.
 *
 * @version 1.0
 * @author jbtronics
 */
class APIMode
{
    const INVALID           =   0;
    const TREE_TOOLS        =   1;
    const TREE_CATEGORY     =   2;
    const GET_PART_INFO     =   3;
    const SEARCH_PARTS      =   4;
    const TREE_DEVICES      =   5;


    public function __construct()
    {
        //Do nothing
    }

    /**
     * Converts a string with the mode to the mode as integer (alias the public consts)
     * @param string $s The string containing the mode.
     * @return integer
     */
    public static function paramToMode($s)
    {
        if(strpos($s, "tree_tools") !== false)
        {
            return APIMode::TREE_TOOLS;
        }
        else if(strpos($s, "tree_category") !== false)
        {
            return APIMode::TREE_CATEGORY;
        }
        else if(strpos($s, "tree_devices") !== false)
        {
            return APIMode::TREE_DEVICES;
        }
        else
        {
            return APIMode::INVALID;
        }
    }

}