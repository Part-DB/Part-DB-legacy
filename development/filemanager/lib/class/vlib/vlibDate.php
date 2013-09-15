<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002 Active Fish Group                                 |
// +----------------------------------------------------------------------+
// | Authors: Kelvin Jones <kelvin@kelvinjones.co.uk>                     |
// +----------------------------------------------------------------------+
//
// $Id$

// check to avoid multiple including of class
if (!defined('vlibDateClassLoaded')) {
    define('vlibDateClassLoaded', 1);

    include_once(dirname(__FILE__).'/vlibDate/error.php');
    include_once (dirname(__FILE__).'/vlibIni.php');

    /**
     * vlibDate is a class for manipulating dates (not times).
     * There are 2 main uses for this class:
     *
     * 1) to handle dates outside of the normal date range of 32-bit systems.
     *    this can be handy for handling Dates of Birth for example.
     *
     * 2) to easily handle dates in different languages.
     *    vlibDate has several supported languages and the possibility to
     *    handle as many as submitted, so if you would like a language
     *    added, see the CONTRIBUTE section of the documentation.
     *
     * For more information see the vlibDate.html in the 'docs' directory.
     *
     * @since 03/04/2002
     * @author Kelvin Jones <kelvin@kelvinjones.co.uk>
     * @package vLIB
     * @access public
     * @see vlibDate.html
     */

    class vlibDate {

    /*-----------------------------------------------------------------------------\
    |                                 ATTENTION                                    |
    |  Do not touch the following variables. vlibDate will not work otherwise.     |
    \-----------------------------------------------------------------------------*/

        /** contains the list of language specific weekdays */
        var $days = array();

        /** if abbreviations are not a substring of the weekday i.e. in Portuguese*/
        var $daysabbr = array();

        /** contains the list of language specific months */
        var $months = array();

        /** contains the list of suffixes */
        var $suffixes = array();

        /** a list of possible languages. */
        var $accepted_langs = array('en','fr','de','es','pt','nl','it','no','sv','da','fi','ro','ar','ru');


    /*-----------------------------------------------------------------------------\
    |                           public functions                                   |
    \-----------------------------------------------------------------------------*/



        /** FUNCTION: formatDate
         *
         * Formats the date like strftime(), but can handle dates from year 0001 - 9999
         * and can display dates in different languages.
         *
         * formatting options:
         *
         * %a        abbreviated weekday name according to the current language setting (Mon, Tue..)
         * %A        full weekday name according to the current language setting (Sunday, Monday, Tuesday...)
         * %b        abbreviated month name according to the current  (Jan, Feb, Mar)
         * %B        full month name according to the current  (January, February, March)
         * %d        day of the month as a decimal number  (range 01 to 31)
         * %e        day of the month as a decimal number  (range 0 to 31)
         * %E        number of days since unspecified epoch (integer)
         *            (%E is useful for storing a date in a Db/Session ..etc as an integer value.
         *             Then use daysToDate() to convert back to a date.)
         * %j        day of the year as a decimal number  (range 001 to 366)
         * %m        month as decimal number (range 01 to 12)
         * %n        newline character
         * %s        ordinal suffix for day of month
         * %t        tab character
         * %U        week number of current year as a decimal number, starting with the first Sunday
         *           as the first day of the first week.
         * %w        weekday as decimal (0 = Sunday)
         * %y        year as a decimal number without a century  (range 00 to 99)
         * %Y        year as a decimal number including the century (range 0000 to 9999)
         * %%        literal '%'
         *
         * @param string $timestamp vlibDate timestamp
         * @param string $format for returned string
         * @return string date in $format
         * @access public
         */

        function formatDate ($timestamp, $format) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year))  $year  = $this->_dateNow("%Y");
            if (empty($month)) $month = $this->_dateNow("%m");
            if (empty($day))   $day   = $this->_dateNow("%d");

            $output = "";
            for ($strpos = 0; $strpos < strlen($format); $strpos++)
            {
                $char = substr($format,$strpos,1);
                if ($char == "%")
                {
                    $nextchar = substr($format,$strpos + 1,1);
                    switch ($nextchar)
                    {
                        case "a":
                            $output .= $this->getWeekdayAbbrname($timestamp);
                            break;
                        case "A":
                            $output .= $this->getWeekdayFullname($timestamp);
                            break;
                        case "b":
                            $output .= $this->getMonthAbbrname($timestamp);
                            break;
                        case "B":
                            $output .= $this->getMonthFullname($timestamp);
                            break;
                        case "d":
                            $output .= sprintf("%02d",$day);
                            break;
                        case "e":
                            $output .= sprintf("%01d",$day);
                            break;
                        case "E":
                            $output .= $this->dateToDays($timestamp);
                            break;
                        case "j":
                            $output .= $this->julianDate($timestamp);
                            break;
                        case "m":
                            $output .= sprintf("%02d",$month);
                            break;
                        case "n":
                            $output .= "\n";
                            break;
                        case "s":
                            $output .= $this->getSuffix($timestamp);
                            break;
                        case "t":
                            $output .= "\t";
                            break;
                        case "U":
                            $output .= $this->weekOfYear($timestamp);
                            break;
                        case "w":
                            $output .= $this->dayOfWeek($timestamp);
                            break;
                        case "W":
                            $output .= $this->weekOfYear($timestamp, true);
                            break;
                        case "y":
                            $output .= substr($year,2,2);
                            break;
                        case "Y":
                            $output .= $year;
                            break;
                        case "%":
                            $output .= "%";
                            break;
                        default:
                            $output .= $char.$nextchar;
                    }
                    $strpos++;
                }
                else {
                    $output .= $char;
                }
            }
            return $output;
        }

        /** FUNCTION: getMonthFullname
         *
         * Returns the full month name for the vlibDate $timestamp.
         *
         * @param string $timestamp vlibDate timestamp
         * @return string full month name
         * @access public
         */
        function getMonthFullname ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));
            if (empty($month)) $month = $this->_dateNow("%m");

            return $this->months[($month - 1)];
        }

        /** FUNCTION: getMonthAbbrname
         *
         * Returns the abbreviated month name for the vlibDate $timestamp.
         *
         * @param string $timestamp vlibDate timestamp
         * @param int length of abbreviation, default=3
         * @return string abbreviated month name
         * @access public
         */
        function getMonthAbbrname ($timestamp, $length=3) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));
            if (empty($month)) $month = $this->_dateNow("%m");

            return substr($this->months[($month - 1)],0,$length);
        }

        /** FUNCTION: getWeekdayFullname
         *
         * Returns the full weekday name for the vlibDate $timestamp.
         *
         * @param string $timestamp vlibDate timestamp
         * @return string full weekday name
         * @access public
         */
        function getWeekdayFullname ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year))  $year  = $this->_dateNow("%Y");
            if (empty($month)) $month = $this->_dateNow("%m");
            if (empty($day))   $day   = $this->_dateNow("%d");

            $weekday = $this->dayOfWeek("$year-$month-$day");

            return $this->days[$weekday];
        }

        /** FUNCTION: getWeekdayAbbrname
         *
         * Returns the abbreviated weekday name for the vlibDate $timestamp.
         *
         * @param string $timestamp vlibDate timestamp
         * @param int length of abbreviation, default=3
         * @return string abbreviated weekday name
         * @access public
         */
        function getWeekdayAbbrname ($timestamp, $length=3) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year))  $year  = $this->_dateNow("%Y");
            if (empty($month)) $month = $this->_dateNow("%m");
            if (empty($day))   $day   = $this->_dateNow("%d");

            $weekday = $this->dayOfWeek("$year-$month-$day");
            if (!empty($this->daysabbr)) {
                return ($this->daysabbr[$weekday]);
            }
            else {
                return substr($this->days[$weekday],0,$length);
            }
        }

        /** FUNCTION: now
         *
         * returns a vlibDate timestamp for the current day.
         *
         * @return string vlibDate timestamp
         * @access public
         */
        function now () {
            return $this->_dateNow('%Y-%m-%d');
        }

        /** FUNCTION: getSuffix
         *
         * returns the suffix for the day of the month, i.e. 03 = rd (in english)
         *
         * @param string $timestamp vlibDate timestamp
         * @param string suffix
         * @access public
         */
        function getSuffix ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));
            if (empty($day)) $day = $this->_dateNow("%e");

            return $this->suffixes[($day - 1)];
        }

        /** FUNCTION: mkTimestamp
         *
         * given the day, month and year of a date will return a vlibDate timestamp.
         *
         * @param string $year in YYYY format
         * @param string $month in MM format
         * @param string $day in DD format
         * @return string vlibDate timestamp
         * @access public
         */
        function mkTimestamp ($year="", $month="", $day="") {
            if(empty($year))  $year  = $this->_dateNow("%Y");
            if(empty($month)) $month = $this->_dateNow("%m");
            if(empty($day))   $day   = $this->_dateNow("%d");

            return "$year-$month-$day";
        }

        /** FUNCTION: fromUnixTime
         *
         * returns a vlibDate timestamp from a given unix timestamp.
         *
         * @param int $unixtime
         * @return string vlibDate timestamp
         * @access public
         */
        function fromUnixTime ($unixtime) {
            return strftime('%Y-%m-%d', $unixtime);
        }

        /** FUNCTION: setLang
         *
         * sets the current language to the language specified by $lang.
         * For a list of supported languages and there language codes, see
         * the vlibDate.html file in the 'docs' directory.
         *
         * @param string $lang language code of language.
         * @access public
         */
        function setLang ($lang='en') {
            if (in_array($lang, $this->accepted_langs)) {
                require (dirname(__FILE__).'/vlibDate/langrefs_'.$lang.'.php');
                $this->days = $days;
                $this->daysabbr = $daysabbr;
                $this->months = $months;
                $this->suffixes = $suffixes;
            }
            else { // raise error
                vlibDateError::raiseError('VD_ERROR_INVALID_LANG', FATAL);
            }
        }

        /** FUNCTION: addInterval
         *
         * this function adds a specified interval to the specified timestamp.
         * allowed intervals are DAY[S], WEEK[S], MONTH[S], YEAR[S]
         * and CENTURY [CENTURIES].
         * example of usage:
         *      $date = new vlibDate ('en');
         *      $now = $date->now();
         *      $nextweek = $date->addInterval($now, '1 WEEK');
         *      $nextweeknextyear = $date->addInterval($now, '1 WEEK 1 YEAR');
         *
         * you can use any combination of intervals.
         *
         * @param string $timestamp vlibDate timestamp
         * @param string $interval desired interval
         * @return string vlibDate timestamp
         * @access public
         */
        function addInterval ($timestamp, $interval) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            // these vars hold the temporary values as we calculate
            $this->_day = $day;
            $this->_month = $month;
            $this->_year = $year;
            $this->_interval = 0;
            $this->_days = $this->dateToDays ($timestamp);
            preg_replace("/([\d]+)\s*([\w]+)/xSe"
                                ,"\$this->_calcAddInterval('\\1', '\\2');"
                                ,$interval);

            if ($this->_interval < 1) return $timestamp;

            $days = $this->_days + $this->_interval;
            return $this->daysToDate ($days);
        }

        /** FUNCTION: subInterval
         *
         * this function subtracts a specified interval from the specified timestamp.
         * allowed intervals are DAY[S], WEEK[S], MONTH[S], YEAR[S]
         * and CENTURY [CENTURIES].
         * example of usage:
         *      $date = new vlibDate ('en');
         *      $now = $date->now();
         *      $lastweek = $date->subInterval($now, '1 WEEK');
         *      $lastweeklastyear = $date->subInterval($now, '1 WEEK 1 YEAR');
         *
         * you can use any combination of intervals.
         *
         * @param string $timestamp vlibDate timestamp
         * @param string $interval desired interval
         * @return string vlibDate timestamp
         * @access public
         */
        function subInterval ($timestamp, $interval) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            // these vars hold the temporary values as we calculate
            $this->_day = $day;
            $this->_month = $month;
            $this->_year = $year;
            $this->_interval = 0;
            $this->_days = $this->dateToDays ($timestamp);
            preg_replace("/([\d]+)\s*([\w]+)/xSe"
                                ,"\$this->_calcSubInterval('\\1', '\\2');"
                                ,$interval);

            if ($this->_interval < 1) return $timestamp;

            $days = $this->_days - $this->_interval;
            return $this->daysToDate ($days);
        }

        /** FUNCTION: nextDay
         *
         * Returns a vlibDate timestamp for the day after $timestamp.
         * If format is specified, then that format is returned instead
         * of the timestamp.
         *
         * @param string $timestamp vlibDate timestamp
         * @param string $format for returned date
         * @return string if  date in given format or vlibDate timestamp
         * @access public
         */
        function nextDay ($timestamp, $format="%Y-%m-%d") {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year))  $year  = $this->_dateNow("%Y");
            if (empty($month)) $month = $this->_dateNow("%m");
            if (empty($day))   $day   = $this->_dateNow("%d");

            $days = $this->dateToDays("$year-$month-$day");
            return ($this->daysToDate($days + 1,$format));
        }

        /** FUNCTION: prevDay
         *
         * Returns a vlibDate timestamp for the day before $timestamp.
         * If format is specified, the that format is returned instead
         * of the timestamp.
         *
         * @param string $timestamp vlibDate timestamp
         * @param string $format for returned date
         * @return string if  date in given format or vlibDate timestamp
         * @access public
         */
        function prevDay ($timestamp,$format="%Y-%m-%d") {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year))  $year  = $this->_dateNow("%Y");
            if (empty($month)) $month = $this->_dateNow("%m");
            if (empty($day))   $day   = $this->_dateNow("%d");

            $days = $this->dateToDays("$year-$month-$day");
            return ($this->daysToDate($days - 1,$format));
        }

        /** FUNCTION: isFutureDate
         *
         * Returns true if the timestamp is in the future.
         *
         * @param string $timestamp vlibDate timestamp
         * @return boolean true/false
         * @access public
         */
        function isFutureDate ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            $this_year = $this->_dateNow("%Y");
            $this_month = $this->_dateNow("%m");
            $this_day = $this->_dateNow("%d");

            if ($year > $this_year) {
                return true;
            }
            elseif ($year == $this_year) {
                if ($month > $this_month) {
                    return true;
                }
                elseif ($month == $this_month) {
                    if ($day > $this_day) {
                        return true;
                    }
                }
            }
            return false;
        }

        /** FUNCTION: isPastDate
         *
         * Returns true if the timestamp is in the past.
         *
         * @param string $timestamp vlibDate timestamp
         * @return boolean true/false
         * @access public
         */
        function isPastDate ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            $this_year = $this->_dateNow("%Y");
            $this_month = $this->_dateNow("%m");
            $this_day = $this->_dateNow("%d");

            if ($year < $this_year) {
                return true;
            }
            elseif ($year == $this_year) {
                if ($month < $this_month) {
                    return true;
                }
                elseif ($month == $this_month) {
                    if ($day < $this_day) {
                        return true;
                    }
                }
            }
            return false;
        }

        /** FUNCTION: isDate
         *
         * Returns true if the timestamp is valid, false otherwise.
         *
         * @param string $timestamp vlibDate timestamp
         * @return boolean true/false
         * @access public
         */
        function isDate ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year) || empty($month) || empty($day)) return false;

            if ($year < 0 || $year > 9999) {
                return false;
            }
            if ($month < 1 || $month > 12) {
                return false;
            }
            if ($day < 1 || $day > $this->daysInMonth($month,$year)) {
                return false;
            }
            return true;
        }

        /** FUNCTION: isLeapYear
         *
         * Returns true if $year is a leap year.
         *
         * @param string $year in format YYYY or a vlibDate timestamp
         * @return boolean true/false
         * @access public
         */
        function isLeapYear ($year="") {
            if (strlen($year) > 4) { // timestamp has bee parsed
                list ($day, $month, $year) = array_values($this->_breakTimestamp($year));
            }
            elseif (strlen($year) == 2) {
                $year  = $this->getLongYear($year);
            }
            elseif (empty($year)) {
                $year = $this->_dateNow("%Y");
            }
            if (preg_match("/\D/",$year)) return false;
            return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0);
        }

        /** FUNCTION: dayOfWeek
         *
         * Returns day of week for the timestamp: 0=Sunday ... 6=Saturday
         *
         * @param string $timestamp vlibDate timestamp
         * @return int $weekday_number
         * @access public
         */
        function dayOfWeek ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year)) $year = $this->_dateNow("%Y");
            if (empty($month)) $month = $this->_dateNow("%m");
            if (empty($day)) $day = $this->_dateNow("%d");

            if ($month > 2) {
                $month -= 2;
            }
            else {
                $month += 10;
                $year--;
            }

            $day = (floor((13 * $month - 1) / 5) +
                    $day + ($year % 100) +
                    floor(($year % 100) / 4) +
                    floor(($year / 100) / 4) - 2 *
                    floor($year / 100) + 77);

            $weekday_number = (($day - 7 * floor($day / 7)));
            return $weekday_number;
        }

        /** FUNCTION: weekOfYear
         *
         * Returns week of the year.
         * If $start_monday is true, then the first week of the Year starts with the first
         * Monday of the year, otherwise it's the first Sunday.
         *
         * @param string $timestamp vlibDate timestamp
         * @param bool $start_monday
         * @return integer $week_number
         * @access public
         */
        function weekOfYear ($timestamp, $start_monday=false) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year))  $year  = $this->_dateNow("%Y");
            if (empty($month)) $month = $this->_dateNow("%m");
            if (empty($day))   $day   = $this->_dateNow("%d");

            $week_year = $year - 1501;
            $capt_days = ($start_monday) ? 29873 : 29872;
            $week_day = $week_year * 365 + floor($week_year / 4) - $capt_days + 1
                    - floor($week_year / 100) + floor(($week_year - 300) / 400);

            $week_number = floor(($this->julianDate("$year-$month-$day") + floor(($week_day + 4) % 7)) / 7);
            return $week_number;
        }

        /** FUNCTION: julianDate
         *
         * Returns number of days since (and including) 1st January of the year
         * in the given timestamp.
         *
         * @param string $timestamp vlibDate timestamp
         * @return int $julian
         * @access public
         */
        function julianDate ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year))  $year  = $this->_dateNow("%Y");
            if (empty($month)) $month = $this->_dateNow("%m");
            if (empty($day))   $day   = $this->_dateNow("%d");

            $days = array(0,31,59,90,120,151,181,212,243,273,304,334);
            $julian = ($days[$month - 1] + $day);
            if ($month > 2 && $this->isLeapYear($year)) $julian++;
            return ($julian);
        }

        /** FUNCTION: quarterOfYear
         *
         * Returns quarter of the year for given timestamp.
         *
         * @param string $timestamp vlibDate timestamp
         * @return int $year_quarter
         * @access public
         */
        function quarterOfYear ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            if (empty($year))  $year  = $this->_dateNow("%Y");
            if (empty($month)) $month = $this->_dateNow("%m");
            if (empty($day))   $day   = $this->_dateNow("%d");

            $year_quarter = (intval(($month - 1) / 3 + 1));
            return $year_quarter;
        }

        /** FUNCTION: getLongYear
         *
         * Returns the year in YYYY format for the given year in YY format.
         * 0-49 is considered 21st century, 50-99 is 20th century.
         *
         * @param string $year in YY format
         * @return string year in YYYY format
         * @access public
         */
        function getLongYear ($year) {
            if (strlen($year) == 1) return ("200$year");
            if ($year > 50) {
                return ("19$year");
            }
            else {
                return ("20$year");
            }
        }

        /** FUNCTION: diffInDays
         *
         * Returns number of days between two given dates.
         *
         * @param string $timestamp1 vlibDate timestamp
         * @param string $timestamp2 vlibDate timestamp
         * @return int absolute number of days between dates, or false on error
         * @access public
         */
        function diffInDays ($timestamp1,$timestamp2) {
            if (!$this->isDate($timestamp1)) return false;
            if (!$this->isDate($timestamp2)) return false;

            return (abs(($this->dateToDays($timestamp1)) - ($this->dateToDays($timestamp2))));
        }

        /** FUNCTION: daysInMonth
         *
         * Find the number of days in the $month.
         *
         * @param mixed int month in MM format or a string vlibDate timestamp
         * @param string year in YYYY format
         * @return int number of days
         * @access public
         */
        function daysInMonth ($month="",$year="") {
            if (strlen($month) > 2) { // timestamp has bee parsed
                list ($day, $month, $year) = array_values($this->_breakTimestamp($month));
            }
            else {
                if (empty($year))  $year  = $this->_dateNow("%Y");
                if (empty($month)) $month = $this->_dateNow("%m");
            }

            $months = array (31,28,31,30,31,30,31,31,30,31,30,31);
            $days = $months[($month-1)];
            return (($month == 2 && $this->isLeapYear($year)) ? ($days+1) : $days);
        }

        /** FUNCTION: dateToDays
         *
         * Converts a vlibDate timestamp to a number of days since a long
         * distant epoch.
         * You can use this to store dates or to pass dates from 1
         * URL to another.
         *
         * @param string $timestamp vlibDate timestamp
         * @return int number of days
         * @access public
         */
        function dateToDays ($timestamp) {
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            $century = substr($year,0,2);
            $year = substr($year,2,2);

            if ($month > 2) {
                $month -= 3;
            }
            else {
                $month += 9;
                if ($year) {
                    $year--;
                }
                else {
                    $year = 99;
                    $century --;
                }
            }

            return ( floor((  146097 * $century)    /  4 ) +
                    floor(( 1461 * $year)        /  4 ) +
                    floor(( 153 * $month +  2) /  5 ) +
                        $day +  1721119);

        }

        /** FUNCTION: encodeDate
         *
         * alias for dateToDays().
         *
         */
        function encodeDate ($timestamp) {
            return $this->dateToDays($timestamp);
        }

        /** FUNCTION: daysToDate
         *
         * Converts a number of days since a long distant epoch
         * to a vlibDate timestamp or to a format specified by $format.
         *
         * @param int $days days since long distant epoch
         * @return string vlibDate timestamp or other $format
         * @access public
         */
        function daysToDate ($days,$format="%Y-%m-%d")
        {
            $days -= 1721119;
            $century = floor(( 4 * $days -  1) /  146097);
            $days = floor(4 * $days - 1 - 146097 * $century);
            $day = floor($days /  4);
            $year = floor(( 4 * $day +  3) /  1461);
            $day = floor(4 * $day +  3 -  1461 * $year);
            $day = floor(($day +  4) /  4);
            $month = floor(( 5 * $day -  3) /  153);
            $day = floor(5 * $day -  3 -  153 * $month);
            $day = floor(($day +  5) /  5);

            if ($month < 10) {
                $month +=3;
            }
            else {
                $month -=9;
                if ($year++ == 99) {
                    $year = 0;
                    $century++;
                }
            }

            $century = sprintf("%02d",$century);
            $year = sprintf("%02d",$year);
            $month = sprintf("%02d",$month);
            $day = sprintf("%02d",$day);

            return ($this->formatDate("$century$year-$month-$day",$format));
        }

        /** FUNCTION: decodeDate
         *
         * alias for daysToDate().
         *
         */
        function decodeDate ($days, $format="%Y-%m-%d") {
            return $this->daysToDate($days, $format);
        }

        /** FUNCTION: getYear
         *
         * Returns the current year in YYYY format
         *
         * @return string year in YYYY format
         * @access public
         */
        function getYear () {
            return $this->_dateNow("%Y");
        }

        /** FUNCTION: getMonth
         *
         * Returns the current month in MM format
         *
         * @return string month in MM format
         * @access public
         */
        function getMonth () {
            return $this->_dateNow("%m");
        }

        /** FUNCTION: getDay
         *
         * Returns the current day in DD format
         *
         * @return string day in DD format
         * @access public
         */
        function getDay () {
            return $this->_dateNow("%d");
        }


    /*-----------------------------------------------------------------------------\
    |                           private functions                                  |
    \-----------------------------------------------------------------------------*/


        /** FUNCTION: _calcSubInterval
         *
         * does the calculations for $this->subInterval();
         *
         * @param int $number number of $interval's
         * @param string $interval type of interval, i.e. DAY, MONTH ...etc.
         * @access private
         */
        function _calcSubInterval ($number, $interval) {
            $interval = strtoupper($interval);
            if (substr($interval, -1, 1)=='S') $interval = substr($interval, 0, -1);

            /* retreive the timestamp with the interval added so we can keep track
               of the current year and month of calculation */
            $timestamp = $this->daysToDate (($this->_days - $this->_interval));
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            switch ($interval) {
                case 'DAY':
                    if ($number > 0) {
                        $this->_interval += $number;
                    }
                break;

                case 'WEEK':
                    if ($number > 0) {
                        $this->_interval += ($number*7);
                    }
                break;

                case 'MONTH':
                    if ($number > 0) {
                        for ($i=0; $i < $number; $i++) {
                            // first we goto prev month
                            if ($month != 1) {
                                $month--;
                            }
                            else {
                                $month = 12;
                                $year--;
                            }

                            $sub = $this->daysInMonth($month, $year);
                            $this->_interval += $sub;
                        }
                    }
                break;

                case 'CENTURIE':
                    $interval = 'CENTURY';

                case 'CENTURY':
                    $interval = 'YEAR';
                    $number *= 100;

                case 'YEAR':
                    if ($number > 0) {
                        for ($i=0; $i < $number; $i++) {
                            if (
                                ($month < 3 && $this->isLeapYear(($year-1))) or
                                ($month > 2 && $this->isLeapYear($year))
                                ) {
                                $this->_interval += 366;
                            }
                            else {
                                $this->_interval += 365;
                            }
                            $year--; // goto previous year
                        }
                    }
                break;
            }
            return;
        }

        /** FUNCTION: _calcAddInterval
         *
         * does the calculations for $this->addInterval();
         *
         * @param int $number number of $interval's
         * @param string $interval type of interval, i.e. DAY, MONTH ...etc.
         * @access private
         */
        function _calcAddInterval ($number, $interval) {
            $interval = strtoupper($interval);
            if (substr($interval, -1, 1)=='S') $interval = substr($interval, 0, -1);

            /* retreive the timestamp with the interval added so we can keep track
               of the current year and month of calculation */
            $timestamp = $this->daysToDate (($this->_days + $this->_interval));
            list ($day, $month, $year) = array_values($this->_breakTimestamp($timestamp));

            switch ($interval) {
                case 'DAY':
                    if ($number > 0) {
                        $this->_interval += $number;
                    }
                break;

                case 'WEEK':
                    if ($number > 0) {
                        $this->_interval += ($number*7);
                    }
                break;

                case 'MONTH':
                    if ($number > 0) {
                        for ($i=0; $i < $number; $i++) {
                            $add = $this->daysInMonth($month, $year);
                            $this->_interval += $add;
                            if ($month < 12) {
                                $month++;
                            }
                            else {
                                $month = 1;
                                $year++;
                            }
                        }
                    }
                break;

                case 'CENTURIE':
                    $interval = 'CENTURY';

                case 'CENTURY':
                    $interval = 'YEAR';
                    $number *= 100;

                case 'YEAR':
                    if ($number > 0) {
                        for ($i=0; $i < $number; $i++) {
                            if (
                                ($month < 3 && $this->isLeapYear($year)) or
                                ($month > 2 && $this->isLeapYear(($year+1)))
                                ) {
                                $this->_interval += 366;
                            }
                            else {
                                $this->_interval += 365;
                            }
                            $year++;
                        }
                    }
                break;
            }
            return;
        }

        /** FUNCTION: vlibDate [constructor]
         *
         * This function includes the languages specfic variables for displaying
         * weekdays and months for example.
         *
         * @param string $lang language, see documentation for a list of langs.
         * @access public
         */
        function vlibDate ($lang = null) {
            $config = vlibIni::vlibDate();
            $default_lang = $config['DEFAULT_LANG'];
            $this->setLang((($lang != null) ? $lang : $default_lang));
        }

        /**
         * Returns the current local date. NOTE: This function
         * retrieves the local date using strftime(), which may
         * or may not be 32-bit safe on your system.
         *
         * @param string the strftime() format to return the date
         * @access private
         * @return string the current date in specified format
         */

        function _dateNow($format="%Y%m%d") {
            return(strftime($format,time()));
        }

        /**
         * Returns an array with day, month, year key values for a timstamp.
         *
         * @param string $timestamp
         * @access private
         * @return array associative array with date values.
         */
        function _breakTimestamp ($timestamp) {
            $timestamp = preg_replace("/[\D]/", '', $timestamp); // remove all non digits

            if (strlen($timestamp) == 8) {
                $year = substr($timestamp, 0, 4);
                $month= substr($timestamp, 4, 2);
                $day  = substr($timestamp, 6, 2);
                return array('day'=>$day, 'month'=>$month, 'year'=>$year);
            }
            else {
                vlibDateError::raiseError('VD_ERROR_INVALID_TIMESTAMP', FATAL);
            }
        }

    } // end class vlibDate
} // << end if(!defined())..
?>