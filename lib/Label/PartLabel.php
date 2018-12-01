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

namespace PartDB\Label;

/** @noinspection PhpIncludeInspection */

use PartDB\Base\NamedDBElement;
use PartDB\Part;
use TCPDF;

/**
 * A class describing a Part Label (for example a 1D Label or an QR-Code)
 *
 * @version 1.0
 * @author janhb
 */
class PartLabel extends BaseLabel
{
    /********************************************************************************
     *
     *   Basic Methods
     *
     *********************************************************************************/

    /**
     * Returns all presets for lines
     */
    public static function getLinePresets() : array
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
    public static function getSupportedSizes() : array
    {
        return array(static::SIZE_50X30, static::SIZE_62X30);
    }

    /**
     * Returns all label types, that are supported by this class.
     * @return int[] A array containing all sizes that are supported by this class.
     */
    public static function getSupportedTypes() : array
    {
        return array(static::TYPE_BARCODE, static::TYPE_TEXT);
    }
}
