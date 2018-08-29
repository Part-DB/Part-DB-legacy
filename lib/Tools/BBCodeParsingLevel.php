<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 29.08.2018
 * Time: 16:30
 */

namespace PartDB\Tools;

/**
 * The constants in this class determines how BBCode should be returned.
 * @package PartDB\Tools
 */
class BBCodeParsingLevel
{
    //The false and true definitions, remains backwards compatibility.

    /**
     * Returns the raw value, like it is saved in the DB. Example: "[b]Test[/b]"
     */
    const RAW = false;
    /**
     * Returns a version of the BBCode parsed to HTML code. Example: "<b>Test</b>"
     */
    const PARSE = true;
    /**
     * Returns a version without any BBCode or HTML tags (pure text). Example: "Test"
     */
    const STRIP = 2;
}