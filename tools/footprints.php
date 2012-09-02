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

    $Id: tools/footprints.php 511 2012-08-05 weinbauer73@gmail.com $
*/

    require_once ('../lib.php');

    function group_footprint_pictures( $pic_array_to_group, $depth)
    {
        $group_array = array();
        foreach( $pic_array_to_group as $pic)
        {
            $key = substr( $pic, 0, $depth);
            $group_array[ $key][] = $pic;
        }
        return( $group_array);
    }


    function get_max_elements( $groups)
    {
        $max_count = 0;
        foreach( $groups as $group)
        {
            $max_count = max( count( $group), $max_count);
        }
        return $max_count;
    }


    function group_footprints( $path, $max_elements = 12)
    {
        // catch all usable footprints
        $pic = array();
        $verzeichnis = @opendir( $path);
        if ( !$verzeichnis) die("Kann Verzeichnis '$path' nicht &ouml;ffnen.");
        rewinddir( $verzeichnis);
        while ( $file = readdir( $verzeichnis))
        {
            if( ( !is_dir( $path.'/'.$file)) and $file != ".db")
            {
                array_push($pic, $path.'/'.$file);
            }
        }

        // sort list
        sort( $pic);


        // generate groups with first character from name
        $groups = group_footprint_pictures( $pic, 1);

        // split groups further
        $depth = 2;
        while ( get_max_elements( $groups) > $max_elements)
        {

            $new_groups = array();
            foreach( $groups as $group_key => $group)
            {
                if ( count( $group) > $max_elements)
                {
                    $new_groups = array_merge( $new_groups, group_footprint_pictures( $group, $depth));
                }
                else
                {
                    $new_groups[] = $group;
                }
            }
            $groups = $new_groups;
            $depth++;
        }
        return $groups;
    }


    function footprint_elements( $path)
    {
        // catch all usable footprints
        $verzeichnis = @opendir( $path);
        if ( !$verzeichnis) die("Kann Verzeichnis $path nicht öffnen");
        rewinddir( $verzeichnis);

        $elements = 0;

        while ( $file = readdir( $verzeichnis))
        {
            if( ( !is_dir( $path.'/'.$file)) and $file != ".db")
            {
                $elements++;
            }
        }

        return $elements;
    }


    function show_table_content( $path)
    {
    $text = '';
        $groups = group_footprints( $path);
        $rowcount = 0;
        foreach( $groups as $group)
        {
            $rowcount++;
            $text .= "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">". PHP_EOL;
            $text .= "<td>". PHP_EOL;
            foreach( $group as $pic)
            {
                $file  = $pic;
                $title = basename( $pic, '.png');
                $footprint_exists = footprint_exists( $title) ? " checked" : "";
                $id               = footprint_exists( $title) ? footprint_get_id( $title) : -1;
                $text .= "<div class=\"footprint\">".
                    "<img src=\"". $file ."\" title=\"". $title ."\" alt=\"\">".

                    // TODO: Hinzufügen/Löschen von Footprints ist vorübergehend deaktiviert,
                    // da die Funktion noch an die neue Footprint-Datenbankstruktur angepasst werden muss.
                    // Ausserdem sollte das Löschen von Footprints nicht so einfach gestaltet werden.
                    // Eine Sicherheitsabfrage wäre hier angebracht, da sonst schnell etwas aus versehen gelöscht wird.

                    //"<p><input type=\"checkbox\" onclick=\"process(this,'". $title ."',". $id .");\"". $footprint_exists .">". $title.

                    "<p>" . $title. // Vorübergehend wird nur noch der Name (ohne Checkbox) angezeigt

                    "</div>". PHP_EOL;
            }
            $text .= "</td></tr>". PHP_EOL;
        }
        return $text;
    }


    function show_footprint_table( $path)
    {
        $path_nice = str_replace( "//", "/", $path);
        $path_nice = ucfirst( str_replace( "/", " : ", $path_nice));

        return '<div class="outer"><h2>'. $path_nice .'</h2><div class="inner"><table>'.show_table_content( $path).'</table></div></div>';
    }


    function generate_footprint_tree( $dir)
    {
        $dir_array = array();
        $d = dir( $dir);
        while ( false !== ( $entry = $d->read()))
        {
            if( $entry != '.' && $entry != '..' && $entry != '.svn' && is_dir( $dir.$entry))
            {
                $dir_array[ $entry] = generate_footprint_tree( $dir.$entry.'/');
            }
        }
        $d->close();
        return $dir_array;
    }


    function print_footprint_tree( $dir_array, $path = "") {
        if ( footprint_elements( $path) > 0)
        {
            echo show_footprint_table( $path);
        }

        foreach( $dir_array as $dir => $value)
        {
            if( is_array( $value))
            {
                $sub_dir = $path."/".$dir;
                print_footprint_tree( $value, $sub_dir);
            }
        }
    }

$html = new HTML;
$html -> set_html_meta ( array('title'=>$title) );
$html -> print_html_header();

ob_start();
print_footprint_tree( generate_footprint_tree( "footprints/"), "footprints/");
$list = ob_get_contents();
$tmpl -> pparse();

$html -> parse_html_template( 'footprints', array('list'=>$list) );
$html -> print_html_footer();

?>

