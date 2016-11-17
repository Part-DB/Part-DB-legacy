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

    Changelog (sorted by date):
        [DATE]      [NICKNAME]          [CHANGES]
        2012-09-04  kami89              - created
        2013-03-09  kami89              - added param "no_reload" to popUp()
*/

    function popUp(URL, modal, dialog_width, dialog_height, no_reload)
    {
        d  = new Date();
        id = d.getTime();

        if (modal)
            eval("page" + id + " = window.showModalDialog(URL,'"+id+"','dialogWidth:"+dialog_width+"px; dialogHeight:"+dialog_height+"px; resizeable:on');");
        else
            eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=1, scrollbars=1, location=1, statusbar=1, menubar=1, resizable=1, width="+dialog_width+", height="+dialog_height+"');");

        if ((modal) && ( ! no_reload))
            location.reload(true);

        return false;
    }
