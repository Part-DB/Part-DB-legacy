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
// $Id: common.php,v 1.3 2002/12/11 00:14:26 releasedj Exp $

// check to avoid multiple including of class
if (!defined('vlibCommonClassLoaded')) {
    define('vlibCommonClassLoaded', 1);

    /**
     * vlibCommon is a class which contains generic funtions which can be used
     * in more than 1 class.
     *
     * @since 22/04/2002
     * @author Kelvin Jones <kelvin@kelvinjones.co.uk>
     * @package vLIB
     * @access private
     */

    class vlibCommon {

        /** FUNCTION: getMimeType
         * This function return a mime-type for the name of the file given.
         * This is based purely on the file name and is derived from the
         * array contained in vlibCommon/mime_types.php.
         *
         * @param string $filename name of file i.e. results.xls
         * @return string mime-type
         * @access private
         */
        function getMimeType ($filename) {
            if (empty($filename)) return false;

            // lets get the mime-type
            require(dirname(__FILE__).'/mime_types.php');

            $extarr = explode('.', $filename);
            $ext = array_pop($extarr);

            if (!empty($VLIB_MIMETYPES[$ext])) {
                return $VLIB_MIMETYPES[$ext];
            }
            else {
                return $VLIB_MIMETYPES['default'];
            }
        }


    } // << end class vlibCommon

} // << end if(!defined())..
?>