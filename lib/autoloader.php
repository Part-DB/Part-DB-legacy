<?php

/********************************************************************************
*
*   autoload function for classes
*
*********************************************************************************/
// __autoload should not be used,  because only one handler can be used! Smarty doesnt work if this is overwritten
function load_internalclasses($classname)
{
    $filename = "";
    if(strpos(strtolower($classname), 'smarty') == false ) {
        $filename = BASE . '/lib/class.' . $classname . '.php';
    }

    //Only load file if it really exists
    if(is_file($filename))
    {
    include_once($filename);
    }
}

spl_autoload_register("load_internalclasses");