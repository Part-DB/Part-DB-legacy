<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

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

