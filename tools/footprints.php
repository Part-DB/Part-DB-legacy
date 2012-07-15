<?php

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


$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
$tmpl -> setVar('head_title', $title);
$tmpl -> setVar('head_charset', $http_charset);
$tmpl -> setVar('head_css', "../".$css);
$tmpl -> setVar('head_menu', true);
$tmpl -> pparse();

$tmpl = new vlibTemplate(BASE."/templates/$theme/footprints.php/vlib_footprints.tmpl");
ob_start();
print_footprint_tree( generate_footprint_tree( "footprints/"), "footprints/");
$list = ob_get_contents();
$tmpl -> setVar('list', $list);
$tmpl -> pparse();

$tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
$tmpl -> pparse();

?>

