<?php
    
    include ('../lib.php');
                
    // config stuff            
    $path         = "footprints/";
    $max_elements = 18;


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
                
                
    // catch all usable footprints
    $pic = array();
    $verzeichnis = @opendir( $path);
    if ( !$verzeichnis) die("Kann Verzeichnis $path nicht öffnen");
    rewinddir( $verzeichnis);
    while ( $file = readdir( $verzeichnis)) 
    {
        if( $file != "." and $file != ".." and $file != ".db" and $file != ".svn") 
        {
            array_push($pic, "$file");
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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Footprints</title>
    <link rel="StyleSheet" href="../css/partdb.css" type="text/css">
</head>
<body class="body">

<div class="outer">
    <h2>Footprints</h2>
    <div class="inner">
        <table>
        <?php
            $rowcount = 0;
            foreach( $groups as $group)
            {
                $rowcount++;
                print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">". PHP_EOL;
                print "<td>". PHP_EOL;
                // debug output
                // print count( $group).": ".implode( ', ', $group)."<p>";
                foreach( $group as $pic)
                {
                    $file  = $pic; 
                    $title = basename( $pic, '.png');
                    print "<div class=\"footprint\">".
                        "<img src=\"".$path.$file."\" title=\"".$title."\" alt=\"\">".
                        "<p>".$title.
                        "</div>". PHP_EOL;
                }
                print "</td></tr>". PHP_EOL;
            }
        ?>
        </table>
    </div>
</div>

</body>
</html>
