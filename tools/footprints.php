<?php
    
    include ('../lib.php');
    partdb_init();
                

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
        if ( !$verzeichnis) die("Kann Verzeichnis $path nicht öffnen");
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
        $groups = group_footprints( $path);
        $rowcount = 0;
        foreach( $groups as $group)
        {
            $rowcount++;
            print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">". PHP_EOL;
            print "<td>". PHP_EOL;
            foreach( $group as $pic)
            {
                $file  = $pic; 
                $title = basename( $pic, '.png');
                $footprint_exists = footprint_exists( $title) ? " checked" : ""; 
                $id               = footprint_exists( $title) ? footprint_get_id( $title) : -1; 
                print "<div class=\"footprint\">".
                    "<img src=\"". $file ."\" title=\"". $title ."\" alt=\"\">".
                    "<p><input type=\"checkbox\" onclick=\"process(this,'". $title ."',". $id .");\"". $footprint_exists .">". $title.
                    "</div>". PHP_EOL;
            }
            print "</td></tr>". PHP_EOL;
        }
    }


    function show_footprint_table( $path)
    {
        $path_nice = str_replace( "/", " : ", $path);
        print '<div class="outer">'. PHP_EOL;
        print '    <h2>'. $path_nice .'</h2>'. PHP_EOL;
        print '    <div class="inner">'. PHP_EOL;
        print '        <table>'. PHP_EOL;
        show_table_content( $path);
        print '        </table>'. PHP_EOL;
        print '    </div>'. PHP_EOL;
        print '</div>'. PHP_EOL;
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
            show_footprint_table( $path);
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


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Footprints</title>
    <link rel="StyleSheet" href="../css/partdb.css" type="text/css">
</head>
<body class="body">
            
    <script type="text/javascript">
        function process( element, footprint, id)
        {
            var xmlHttpReq = false;
            var self = this;
            var send;

            // Mozilla/Safari
            if (window.XMLHttpRequest) {
                self.xmlHttpReq = new XMLHttpRequest();
            }
            // IE
            else if (window.ActiveXObject) {
                self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
            }

            self.xmlHttpReq.open( 'POST', '../fpmgr.php', true);
            self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            if ( element.checked)
            {
                send = 'add=&new_footprint=' + footprint + '&parent_node=0';
            }
            else
            {
                send = 'delete=&footprint_sel=' + id;
            }
            self.xmlHttpReq.setRequestHeader('Content-length', send.length);
            self.xmlHttpReq.send( send); 
        }
    </script>

    <form>
    <?php
        $footprint_tree = generate_footprint_tree( "footprints/");
        print_footprint_tree( $footprint_tree, "Footprints");
    ?>
    </form>

</body>
</html>
