<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 13.01.2018
 * Time: 15:13
 */

namespace PartDB\Label;


/**
 * A class describing a Label for a storelocation (for example a 1D Label or an QR-Code)
 *
 * @version 1.0
 * @author janhb
 */
class StorelocationLabel extends BaseLabel
{
    /**
     * Returns all presets for lines
     */
    public static function getLinePresets()
    {
        $presets = array();

        //Preset A: format like label generator from F. Thiessen
        //Link: http://fthiessen.de/part-db-etiketten-drucken/
        $lines = array();
        $lines[] = "<h3>%NAME% - %CAT%</h3>";
        $lines[] = "%STORELOC%";
        $lines[] = "%FOOT%";
        $lines[] = "%ORDER_NR% - %SUPPLIER%";
        $presets[] = array("name" => "Preset A", "lines" => $lines);

        //Preset B: Like A, full storelocation path
        $lines = array();
        $lines[] = "<h3>%NAME% - %CAT%</h3>";
        $lines[] = "%STORELOC_FULL%";
        $lines[] = "%FOOT%";
        $lines[] = "%ORDER_NR% - %SUPPLIER%";
        $presets[] = array("name" => "Preset B", "lines" => $lines);

        //Presets C: Show description in second line, Order infos may be cutted...
        $lines = array();
        $lines[] = "<h3>%NAME% - %CAT%</h3>";
        $lines[] = "%DESC%";
        $lines[] = "%STORELOC%";
        $lines[] = "%FOOT%";
        $lines[] = "%ORDER_NR% - %SUPPLIER%";
        $presets[] = array("name" => "Preset C", "lines" => $lines);

        //Presets C: With labels
        $lines = array();
        $lines[] = "BAUTEIL : %NAME% - %CAT%";
        $lines[] = "LAGER   : %STORELOC%";
        $lines[] = "GEHÄUSE : %FOOT%";
        $lines[] = "BEST-NR : %ORDER_NR% - %SUPPLIER%";
        $presets[] = array("name" => "Preset D", "lines" => $lines);

        return $presets;
    }

    /**
     * Returns all label sizes, that are supported by this class.
     * @return string[] A array containing all sizes that are supported by this class.
     */
    public static function getSupportedSizes()
    {
        return array(static::SIZE_50X30, static::SIZE_62X30);
    }

    /**
     * Returns all label types, that are supported by this class.
     * @return int[] A array containing all sizes that are supported by this class.
     */
    public static function getSupportedTypes()
    {
        return array(static::TYPE_BARCODE, static::TYPE_TEXT);
    }
}