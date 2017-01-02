<?php

//set_include_path(get_include_path() . PATH_SEPARATOR . '../../lib/lib.php');
define( 'BASE', "." );
include_once "lib/autoloader.php";
//Load BBCode Parser
include_once('lib/bbcode/Autoloader.php');


//Fake function global debug funtion
function debug($string,$msg)
{
    printf("\n===== Debug Output: =====\n");
    printf($msg."\n");
    printf("===== End Debug: ======\n");
}