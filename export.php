<?php
/*
    $Id: export.php 433 2012-05-09 22:13:52Z bubbles.red@gmail.com $
*/
    
    include ("lib.php");
    partdb_init();

    // database query

    $keyword    = smart_escape_for_search( $_REQUEST['keyword']);
    $search_nam = isset( $_REQUEST['search_nam']) ? $_REQUEST['search_nam'] == 'true' : false;
    $search_cat = isset( $_REQUEST['search_cat']) ? $_REQUEST['search_cat'] == 'true' : false;
    $search_des = isset( $_REQUEST['search_des']) ? $_REQUEST['search_des'] == 'true' : false;
    $search_com = isset( $_REQUEST['search_com']) ? $_REQUEST['search_com'] == 'true' : false;
    $search_sup = isset( $_REQUEST['search_sup']) ? $_REQUEST['search_sup'] == 'true' : false;
    $search_snr = isset( $_REQUEST['search_snr']) ? $_REQUEST['search_snr'] == 'true' : false;
    $search_loc = isset( $_REQUEST['search_loc']) ? $_REQUEST['search_loc'] == 'true' : false;
    $search_fpr = isset( $_REQUEST['search_fpr']) ? $_REQUEST['search_fpr'] == 'true' : false;

    $result = parts_select_search( $keyword, $search_nam, $search_cat, $search_des, $search_com, $search_sup, $search_snr, $search_loc, $search_fpr, true);

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


    if (( $action == "output") && ( $format == 'XML'))
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
    

    if (( $action == "output") && ( $format == 'CSV'))
    {

        // header
        $CSVDoc = "# Kategorie; Name; Beschreibung; Anzahl; Footprint; Lagerort; Lieferant; Bestellnummer; Kommentar\n";
     
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


    if (( $action == "output") && ( $format == 'DokuWIKI'))
    {

        // header
        $CSVDoc = "^ Kategorie^ Name^ Beschreibung^ Anzahl^ Footprint^ Lagerort^ Lieferant^ Bestellnummer^ Kommentar^ \n|";
     
        //  catch SQL results, form DokuWIKI (CSV) output 
        while( $dbrow = mysql_fetch_row( $result))
        {
            $CSVDoc .= implode( " |", $dbrow) . "\n|";

        }
        // output
        header("Content-Type: text/plain");
        header("Content-disposition: attachment; filename=\"". $filename .".txt\"");
        header("Pragma: no-cache");

        print $CSVDoc;
    }


    if (( $action == "output") && ( $format == 'DymoCSV'))
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

