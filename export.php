<?php
    
    include ("lib.php");
    partdb_init();

    // database query

    $keyword = smart_escape_for_search( $_REQUEST['keyword']);

    // build search strings
    if ( $_REQUEST['search_nam'] == "true") { $query_nam = " OR (parts.name LIKE ".           $keyword.")"; } 
    if ( $_REQUEST['search_com'] == "true") { $query_com = " OR (parts.comment LIKE ".        $keyword.")"; }
    if ( $_REQUEST['search_sup'] == "true") { $query_sup = " OR (suppliers.name LIKE ".       $keyword.")"; }
    if ( $_REQUEST['search_snr'] == "true") { $query_snr = " OR (parts.supplierpartnr LIKE ". $keyword.")"; }
    if ( $_REQUEST['search_loc'] == "true") { $query_loc = " OR (storeloc.name LIKE ".        $keyword.")"; }
    if ( $_REQUEST['search_fpr'] == "true") { $query_fpr = " OR (footprints.name LIKE ".      $keyword.")"; }
    $search = $query_nam. $query_com. $query_sup. $query_snr. $query_loc. $query_fpr;

    $query = "SELECT ".
            "categories.name AS 'category',  ".
            "parts.name,                     ".
            "parts.instock   AS 'stock',     ".
            "footprints.name AS 'footprint', ".
            "storeloc.name   AS 'location',  ".
            "suppliers.name  AS 'supplier',  ".
            "parts.supplierpartnr AS 'order_number', ".
            "parts.comment  ".
            "FROM parts ".
            "LEFT JOIN categories ON parts.id_category=categories.id ".
            "LEFT JOIN footprints ON parts.id_footprint=footprints.id ".
            "LEFT JOIN storeloc   ON parts.id_storeloc=storeloc.id ".
            "LEFT JOIN suppliers  ON parts.id_supplier=suppliers.id ".
            "WHERE FALSE ". $search.
            " ORDER BY parts.id_category, parts.name ASC;";

    $result   = mysql_query( $query) or die( mysql_error());

    $filename = "partdb_export_selection_". $_REQUEST["keyword"]; 


    if ( isset( $_REQUEST['format']))
    {
        $format = $_REQUEST['format'];
        $action = "output";
    }
    else
    {
        $action = "error";
        $error  = "Ausgabeformat nicht definiert";
    }


    if (( $action == output) && ( $format == 'XML'))
    {

        // inspiration:
        // http://www.tsql.de/php/mysql_tabelle_export_xml_konvertieren

        $XMLDoc = new SimpleXMLElement( "<?xml version='1.0' encoding='UTF-8' standalone='yes'?><parts></parts>");

        //  catch SQL results, form XML output 
        while( $dbrow = mysql_fetch_object( $result))
        {
            $xmlrow = $XMLDoc->addChild( "part");
     
            foreach( $dbrow as $column => $value)
            {
                $xmlrow->$column = utf8_encode( $value);
            }
        }

        // convert to dom
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML( $XMLDoc->asXML());
     
        // output
        header("Content-Type: text/xml; charset=utf-8");
        header("Content-disposition: attachment; filename=\"". $filename .".xml\"");
        header("Pragma: no-cache");
        print $dom->saveXML();
    }
    

    if (( $action == output) && ( $format == 'CSV'))
    {

        // header
        $CSVDoc = "# Kategorie; Name; Anzahl; Footprint; Lagerort; Lieferant; Bestellnummer; Kommentar\n";
     
        //  catch SQL results, form CSV output 
        while( $dbrow = mysql_fetch_row( $result))
        {
            $CSVDoc .= implode( ";", $dbrow) . "\n";
        }
     

        // output
        header("Content-Type: text/x-csv");
        header("Content-disposition: attachment; filename=\"". $filename .".csv\"");
        header("Pragma: no-cache");

        print $CSVDoc;
    }


    if (( $action == output) && ( $format == 'DokuWIKI'))
    {

        // header
        $CSVDoc = "^ Kategorie^ Name^ Anzahl^ Footprint^ Lagerort^ Lieferant^ Bestellnummer^ Kommentar^\n|";
     
        //  catch SQL results, form DokuWIKI (CSV) output 
        while( $dbrow = mysql_fetch_row( $result))
        {
            $CSVDoc .= implode( "|", $dbrow) . "\n|";
        }
     

        // output
        header("Content-Type: text/plain");
        header("Content-disposition: attachment; filename=\"". $filename .".txt\"");
        header("Pragma: no-cache");

        print $CSVDoc;
    }


    if (( $action == output) && ( $format == 'DymoCSV'))
    {

        // header
        $CSVDoc = "# Name; Footprint; Lagerort;\n";
     
        //  catch SQL results, form DokuWIKI (CSV) output 
        while( $dbrow = mysql_fetch_assoc( $result))
        {
            $CSVDoc .= $dbrow['name'] ."; ". $dbrow['footprint'] ."; ". $dbrow['location'] ."\n"; 
        }
     

        // output
        header("Content-Type: text/plain");
        header("Content-disposition: attachment; filename=\"". $filename .".txt\"");
        header("Pragma: no-cache");

        print $CSVDoc;
    }


    if ( $action == "error" )
    {
?>
        <html>
        <body>
        Fehler: <?php print $error ?>
        </body>
        </html>

<?php
    }
?>

