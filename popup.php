<?php
    header("content-type: application/x-javascript");

    require( 'config.php');

    if ($use_modal_dialog)
    {
        print "
        function popUp(URL)
        {
            d = new Date();
            id = d.getTime();
            eval(\"page\" + id + \" = window.showModalDialog(URL,'\"+id+\"','dialogWidth:".$dialog_width."px; dialogHeight:".$dialog_height."px; resizeable:on');\");
            location.reload(true);
        }";
    }
    else
    {
        print "
        function popUp(URL)
        {
            d = new Date();
            id = d.getTime();
            eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=1, scrollbars=1, location=1, statusbar=1, menubar=1, resizable=1, width=".$dialog_width.", height=".$dialog_height."');\");
        }";
    }
?>

