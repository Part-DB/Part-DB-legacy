<?php
/*
    ---------------------------------------------------------------------------------------

    phpBookWorm: MIME-Helper für GeShi


    Copyright: CC-BY-SA 3.0, 2013
    Author: Udo Neist (webmaster@singollo.de, GPG-Fingerprint 4A8F B229 2AE9 9634 04D1 E2F0 21F2 E27D FE97 D87F)

    Part: mime2geshi.php

    Info: Setzt MIME-Typ zu GeShi um

    ---------------------------------------------------------------------------------------

    06.04.2013: Erste Version (Udo Neist)
*/

$mime2geshi = array(
    'text/css'                  =>  'css',
    'text/diff'                 =>  'diff',
    'text/html'                 =>  'xml',
    'text/xml'                  =>  'xml',
    'text/ini'                  =>  'ini',
    'text/javascript'           =>  'javascript', // depricated!
    'application/javascript'    =>  'javascript',
    'application/x-perl'        =>  'pl',
    'text/x-php'                =>  'php',
    'text/x-ruby'               =>  'rails',
    'text/x-shellscript'        =>  'bash',
    'text/x-sql'                =>  'sql',
    'text/plain'                =>  'txt'
);

?>
