<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

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

*/
    include('lib.php');
    partdb_init();
    
    $action = 'default';
    if ( isset( $_REQUEST["add"]))    { $action = 'add';}
    if ( isset( $_REQUEST["delete"])) { $action = 'delete';}
    if ( isset( $_REQUEST["rename"])) { $action = 'rename';}

    $supplier_sel = isset( $_REQUEST["supplier_sel"]) ? $_REQUEST["supplier_sel"] : -1;

    if ( $action == 'add')
    {
        supplier_add( $_REQUEST['new_supplier']);
    }
    
    if ( $action == 'delete')
    {
        supplier_delete( $supplier_sel);
    }

    if ( $action == 'rename')
    {
        supplier_rename( $supplier_sel, $_REQUEST["new_name"]);
    }

    $data       = supplier_select( $supplier_sel);
    $name       = $data['name'];

    $size       = min( suppliers_count(), 30);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Lieferanten</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<div class="outer">
    <h2>Lieferant anlegen</h2>
    <div class="inner">
        <form action="" method="post">
            Neuer Lieferant:
            <input type="text" name="new_supplier">
            <input type="submit" name="add" value="Anlegen">
        </form>
    </div>
</div>


<div class="outer">
    <h2>Lieferant umbenennen/l&ouml;schen</h2>
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td rowspan="2">
                        <select name="supplier_sel" size="<?php print $size;?>" onChange="this.form.submit()">
                        <?php suppliers_build_list( $supplier_sel); ?>
                        </select>
                    </td>
                    <td>
                        Neuer Name:<br>
                        <input type="text"   name="new_name" value="<?php print $name; ?>">
                        <input type="submit" name="rename" value="Umbenennen">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="delete" value="L&ouml;schen">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

</body>
</html>
