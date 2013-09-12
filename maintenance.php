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

    $Id$

    Changelog (sorted by date):
        [DATE]      [NICKNAME]          [CHANGES]
        2013-09-11  kami89              - created
*/

    /*
     * IMPORTANT:
     *
     * We do NOT use templates for the maintenance site, because the templatesystem may is not ready at the moment!
     */

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Part-DB - Wartungsmodus</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
            <style type="text/css">
                html, body  {   text-align: center;
                                font: 20px Helvetica, sans-serif;
                                color: #333;
                                height: 70%;}
                h2          {   font-size: 35px; }
            </style>
    </head>
    <body>
        <table height="100%" align="center" valign="middle">
            <tr>
                <td>
                    <img src="img/partdb/maintenance.png" width="200">
                </td>
                <td>
                    <h2>Part-DB ist zur Zeit nicht verfügbar</h2>

                    <p>Es werden gerade Wartungsarbeiten an Part-DB vorgenommen, <br>
                    daher ist die Seite zur Zeit nicht verfügbar.</p>
                    <p>Versuchen Sie es bitte später nochmal.</p>
                    <p><a href="index.php">Seite aktualisieren</a></p>
                </td>
            </tr>
        </table>
    </body>
</html>
