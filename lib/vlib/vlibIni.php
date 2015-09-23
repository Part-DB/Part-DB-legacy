<?php

// +------------------------------------------------------------------------+
// | PHP version 5.x, tested with 5.1.4, 5.1.6, 5.2.6                       |
// +------------------------------------------------------------------------+
// | Copyright (c) 2002-2008 Kelvin Jones, Claus van Beek, Stefan Deussen   |
// +------------------------------------------------------------------------+
// | Authors: Kelvin Jones, Claus van Beek, Stefan Deussen                  |
// +------------------------------------------------------------------------+

/*
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; This file contains configuration parametres for use  ;
; with the vLIB library.                               ;
;                                                      ;
; vLIB uses this file so that for future releases, you ;
; will not have to delve through all the php script    ;
; again to set your specific variable/properties .etc  ;
;                                                      ;
; ---------------------------------------------------- ;
; ATTENTION: Do NOT remove any variable given in the   ;
; configurations below as they will probably still be  ;
; needed by vLIB. If you do not need a variable simply ;
; let it be.                                           ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
*/

if (!defined('vlibIniClassLoaded'))
{
    define('vlibIniClassLoaded', 1);

    /**
     * vlibIni is a class used to store configuration parameters
     * for the vLIB library.
     *
     * @since 2002-07-21
     * @package vLIB
     * @access private
     */

    class vlibIni
    {

        /** config vars for vlibTemplate */
        public static function vlibTemplate()
        {
            return array(
                'TEMPLATE_DIR' => BASE.'/templates', // Default directory for your template files (full path) leave the '/' or '\' off the end of the directory.

                'MAX_INCLUDES' => 10, // Drill depth for tmpl_include's

                'GLOBAL_VARS' => 1, // if set to 1, any variables not found in a loop will search for a global var as well

                'GLOBAL_CONTEXT_VARS' => 1, // if set to 1, vlibTemplate will add global vars (__SELF__, __REQUEST_URI__, __PARSE_TIME__) reflecting the environment.

                'LOOP_CONTEXT_VARS' => 1, // if set to 1, vlibTemplate will add loop specific vars (see dokumentation) on each row of the loop.

                'SET_LOOP_VAR' => 1, // Sets a global variable for each top level loops

                'DEFAULT_ESCAPE' => 'html', // 1 of the following: html, url, sq, dq, none

                'STRICT' => 0, // Dies when encountering an incorrect tmpl_* style tags i.e. tmpl_vae

                'CASELESS' => 0, // Removes case sensitivity on all variables

                'UNKNOWNS' => 'ignore', // How to handle unknown variables.
                    // One of the following: ignore, remove, leave, print, comment

                'TIME_PARSE' => '0', // Will enable you to time how long vlibTemplate takes to parse your template. You then use the function: getParseTime().

                'ENABLE_PHPINCLUDE' => '1', // Will allow template to include a php file using <TMPL_PHPINCLUDE>

                'ENABLE_SHORTTAGS'  => '0', // Will allow you to use short tags in your script i.e.: <VAR name="my_var">, <LOOP name="my_loop">...</LOOP>


                /**
                 * the following are only used by the vlibTemplateCache class.
                **/

                'CACHE_DIRECTORY' => '/tmp',
                    // Directory where the cached filesystem
                    // will be set up (full path, and must be writable)
                    // '/' or '\' off the end of the directory.

                'CACHE_LIFETIME' => 0,// [temporarly deactivated by kami89] 604800, // Duration until file is re-cached in seconds (604800 = 1 week)

                'CACHE_EXTENSION' => 'vtc', // extention to be used by the cached file i.e. index.php will become index.vtc (vlibTemplate Compiled)

                'DEBUG_WITHOUT_JAVASCRIPT' => 0 // if set to 1, the external debug window won't be displayed and the debug output is placed below every template output.
            );
        } // << end method vlibTemplate


        /** config vars for vlibDate */
        public static function vlibDate()
        {
            return array(
                'DEFAULT_LANG' => 'de' // default language for the date displays
            );
        } // << end method vlibDate


        /** config vars for vlibSearchToSQL */
        public static function vlibSearchToSQL()
        {
            return array(
                'MIN_WORD_LENGTH'       =>   3, // minimum length of word

                'ALLOW_WILDCARDS'       =>   0, // whether to allow % and _ as wildcards in SQL LIKE '' clause

                'ENCLOSE_FIELDS_WITH'   =>   '', // i.e., enclose with ` will give you `search_field` LIKE ... Leave Empty for nothing

                'DEFAULT_SEPERATOR'     =>   'OR', // default clause seperator, can have 'AND' or 'OR'

                // list of words that are not used in search
                'STOP_WORDS'            =>   'a all an and are as at be but by can for from had have he her his in is it may not of on or that the there this to was where which will with you your'
            );
        } // << end method vlibSearchToSQL

    } // << end class vlibIni
}

?>
