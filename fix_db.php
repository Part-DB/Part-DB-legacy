<?PHP

    // for fixing the database

    include ("lib.php");
    partdb_init();
          
    $action = 'default';
    if ( isset( $_REQUEST["set"]))    { $action = 'set';}

          $result = mysql_query($query);
        
    if ( $action == 'set')
    {
        $query = "UPDATE internal set keyValue = ".smart_escape($_REQUEST["version"]).";";
        mysql_query($query);
    }

?>
