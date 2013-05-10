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

if (!defined('FATAL')) 		define('FATAL', E_USER_ERROR);
if (!defined('WARNING')) 	define('WARNING', E_USER_WARNING);
if (!defined('NOTICE')) 	define('NOTICE', E_USER_NOTICE);

/**
 * Class is used by vlibDate.
 * It handles all of the error reporting for vlibDate.
 *
 * @author Kelvin Jones <kelvin@kelvinjones.co.uk>
 * @since 26/04/2002
 * @package vLIB
 * @access private
 */

class vlibDateError {

/*-----------------------------------------------------------------------------\
|     DO NOT TOUCH ANYTHING IN THIS CLASS IT MAY NOT WORK OTHERWISE            |
\-----------------------------------------------------------------------------*/

    function raiseError ($code, $level = null, $extra=null) {
        if (!($level & error_reporting())) return; // binary AND checks for reporting level

        $error_codes = array(
                        'VD_ERROR_INVALID_ERROR_CODE'   => 'vlibDate error: Invalid error raised.',
                        'VD_ERROR_INVALID_LANG'         => 'vlibDate error: Invalid language code used.',
                        'VD_ERROR_INVALID_TIMESTAMP'    => 'vlibDate error: Invalid timstamp used.'
                            );

        $error_levels = array(
                        'VD_ERROR_INVALID_ERROR_CODE'   => FATAL,
                        'VD_ERROR_INVALID_LANG'         => FATAL,
                        'VD_ERROR_INVALID_TIMESTAMP'    => FATAL
                            );

        if ($level === null) $level = $error_levels[$code];

        if ($msg = $error_codes[$code]) {
            trigger_error($msg, $level);
        } else {
            trigger_error($error_codes['VD_ERROR_INVALID_ERROR_CODE'], $error_levels['VD_ERROR_INVALID_ERROR_CODE']);
        }
        return;
    }
}
?>