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
// $Id: error.php,v 1.4 2004/01/07 22:00:20 releasedj Exp $

if (!defined('FATAL')) 		define('FATAL', E_USER_ERROR);
if (!defined('WARNING')) 	define('WARNING', E_USER_WARNING);
if (!defined('NOTICE')) 	define('NOTICE', E_USER_NOTICE);

/**
 * Class is used by vlibMimeMail.
 * It handles all of the error reporting for vlibMimeMail.
 *
 * @author Kelvin Jones <kelvin@kelvinjones.co.uk>
 * @since 22/04/2002
 * @package vLIB
 * @access private
 */

class vlibMimeMailError {

/*-----------------------------------------------------------------------------\
|     DO NOT TOUCH ANYTHING IN THIS CLASS IT MAY NOT WORK OTHERWISE            |
\-----------------------------------------------------------------------------*/

    function raiseError ($code, $level = null, $extra=null) {
        if (!($level & error_reporting())) return; // binary AND checks for reporting level

        $error_codes = array(
                        'VM_ERROR_INVALID_ERROR_CODE'   => 'vlibMimeMail error: Invalid error raised.',
                        'VM_ERROR_NOFILE'               => 'vlibMimeMail error: Attachment ('.$extra.') file not found.',
                        'VM_ERROR_BADEMAIL'             => 'vlibMimeMail error: Email address ('.$extra.') not valid.',
                        'VM_ERROR_NOBODY'               => 'vlibMimeMail error: Tried to send a message with no body.',
                        'VM_ERROR_CANNOT_SEND'          => 'vlibMimeMail error: Tried to send a message without declaring a body or a recipient.'
                            );

        $error_levels = array(
                        'VM_ERROR_INVALID_ERROR_CODE'   => FATAL,
                        'VM_ERROR_NOFILE'               => FATAL,
                        'VM_ERROR_BADEMAIL'             => FATAL,
                        'VM_ERROR_NOBODY'               => FATAL,
                        'VM_ERROR_CANNOT_SEND'          => FATAL
                            );

        if ($level === null) $level = $error_levels[$code];

        if ($msg = $error_codes[$code]) {
            trigger_error($msg, $level);
        } else {
            trigger_error($error_codes['VM_ERROR_INVALID_ERROR_CODE'], $error_levels['VM_ERROR_INVALID_ERROR_CODE']);
        }
        return;
    }
}
?>