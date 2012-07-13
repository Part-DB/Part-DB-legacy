<?php
    
    // see: http://www.stichpunkt.de/beitrag/popup.html

    header("content-type: application/x-javascript");

    require( 'config.php');

    // set some defaults if not set in config.php
    $use_modal_dialog = isset( $use_modal_dialog) ? $use_modal_dialog : true;
    $dialog_width     = isset( $dialog_width )    ? $dialog_width     : 500;
    $dialog_height    = isset( $dialog_height)    ? $dialog_height    : 400;


    if ($use_modal_dialog)
    {
        print "
        function popUp(URL)
        {
            d  = new Date();
            id = d.getTime();
            eval(\"page\" + id + \" = window.showModalDialog(URL,'\"+id+\"','dialogWidth:".$dialog_width."px; dialogHeight:".$dialog_height."px; resizeable:on');\");
            location.reload(true);
            return false;
        }";
    }
    else
    {
        print "
        function popUp(URL)
        {
            d  = new Date();
            id = d.getTime();
            eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=1, scrollbars=1, location=1, statusbar=1, menubar=1, resizable=1, width=".$dialog_width.", height=".$dialog_height."');\");
            location.reload(true);
            return false;
        }";
    }
?>

