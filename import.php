<?php

    /*
     * we have multiple states:
     * choose file (default)
     * detect and select entries
     * push entries in database
     */
	require_once ('lib.php');

    /* work around for older php: before 5.3.0
    */
    if ( !function_exists( 'str_getcsv')) {
        function str_getcsv( $str, $delim=',', $enclose='"', $preserve=false) {
            $resArr = array();
            $n = 0;
            $expEncArr = explode( $enclose, $str);
            foreach( $expEncArr as $EncItem)
            {
                if( $n++%2)
                {
                    array_push( $resArr, array_pop( $resArr) . ( $preserve?$enclose:'') . $EncItem.( $preserve?$enclose:''));
                }
                else
                {
                    $expDelArr = explode( $delim, $EncItem);
                    array_push( $resArr, array_pop( $resArr) . array_shift( $expDelArr));
                    $resArr = array_merge( $resArr, $expDelArr);
                }
            }
            return $resArr;
        }
    }

    // set action to default, if not exists
    $action    = ( isset( $_REQUEST['action'])    ? $_REQUEST['action'] : 'default');
    $file_type = ( isset( $_REQUEST['file_type']) ? $_REQUEST['file_type'] : 'CSV');
    $show_file = false;

    // determine action
    // this is a kind of a state machine
    if ( isset( $_REQUEST['check']))  { $action = "check_data";  }
    if ( isset( $_REQUEST['import'])) { $action = "commit_data"; }


    // data processing
    $accepted_coding = array( 'UTF-8', 'ISO-8859-1', 'ISO-8859-15', 'ASCII');
    $accepted_types = array( 'text/plain', 'text/csv', 'text/xml', 'application/vnd.ms-excel');


    // catch data arrays, if defined
    $active      = ( isset( $_REQUEST['active'])      ? $_REQUEST['active']      : array());
    $category    = ( isset( $_REQUEST['category'])    ? $_REQUEST['category']    : array());
    $name        = ( isset( $_REQUEST['name'])        ? $_REQUEST['name']        : array());
    $description = ( isset( $_REQUEST['description']) ? $_REQUEST['description'] : array());
    $nr          = ( isset( $_REQUEST['nr'])          ? $_REQUEST['nr']          : array());
    $count       = ( isset( $_REQUEST['count'])       ? $_REQUEST['count']       : array());
    $footprint   = ( isset( $_REQUEST['footprint'])   ? $_REQUEST['footprint']   : array());
    $storeloc    = ( isset( $_REQUEST['storeloc'])    ? $_REQUEST['storeloc']    : array());
    $supplier    = ( isset( $_REQUEST['supplier'])    ? $_REQUEST['supplier']    : array());
    $sup_part    = ( isset( $_REQUEST['sup_part'])    ? $_REQUEST['sup_part']    : array());
    $comment     = ( isset( $_REQUEST['comment'])     ? $_REQUEST['comment']     : array());


    // try to catch the file
    if ( $action == "import_file")
    {
        if ( is_uploaded_file( $_FILES['import_file']['tmp_name']))
        {
            // read file content
            $filename        = $_FILES['import_file']['name'];
            $filestring      = file_get_contents( $_FILES['import_file']['tmp_name']);
            $filestring_conv = mb_convert_encoding( $filestring, $_REQUEST['coding'], mb_detect_encoding( $filestring, implode( ',', $accepted_coding), true));
            $content_arr     = explode("\n", $filestring_conv);
            $action          = "check_data";
            $show_file       = true;
        }
        else
        {
            $action = "error";
            $error  = "Upload fehlgeschlagen";
        }

        if ( ! in_array( $_FILES["import_file"]["type"], $accepted_types, false))
        {
            $action = "error";
            $error  = "falscher Dateityp: ".$_FILES["import_file"]["type"]." (erwarte: ". implode( ", ", $accepted_types) .")";
        }

        if ($_FILES["import_file"]["error"] > 0)
        {
            $action = "error";
            $error  = $_FILES["file"]["error"];
        }


        // fill data_arr
        $data_arr = array();
        if ( $file_type == 'CSV')
        {
            foreach ($content_arr as $line_num => $line)
            {
                // remove whitespaces etc.
                $line = trim( $line);

                // ignore line with comments, or empty lines
                if ( (strlen( $line) > 0) && ( $line[0] !== '#'))
                {
                    // combine line nr. and stuff to an array
                    $data_arr[] = array_merge( array( 0 => $line_num), str_getcsv( $line, $_REQUEST['divider']));
                }
            }
        }

        if ( $file_type == 'XML')
        {
            $xml   = simplexml_load_string( $filestring_conv);
            $index = 1;
            foreach( $xml->part as $index => $part)
            {
                $data_arr[] = array( $index,
                    $part->category,
                    $part->name,
                    $part->description,
                    (int) $part->stock,
                    $part->footprint,
                    $part->location,
                    $part->supplier,
                    $part->order_number,
                    $part->comment);
            }
        }


        // extract data from data_arr
        // fill the arrays with initial values
        foreach ($data_arr as $key => $data)
        {
            $nr[$key]          = isset( $data[0]) ? $data[0] : '';
            $category[$key]    = isset( $data[1]) ? $data[1] : '';
            $name[$key]        = isset( $data[2]) ? $data[2] : '';
            $description[$key] = isset( $data[3]) ? $data[3] : '';
            $count[$key]       = isset( $data[4]) ? $data[4] : '';
            $footprint[$key]   = isset( $data[5]) ? $data[5] : '';
            $storeloc[$key]    = isset( $data[6]) ? $data[6] : '';
            $supplier[$key]    = isset( $data[7]) ? $data[7] : '';
            $sup_part[$key]    = isset( $data[8]) ? $data[8] : '';
            $comment[$key]     = isset( $data[9]) ? $data[9] : '';
        }

    } // end import_file


    // interpret import file content
    if ( $action == "check_data")
    {
        // predefines
        $ok     = "&nbsp;&#x2714;"; // check mark
        $halfok = "(&#x2714;)";     // (check mark)
        $bad    = "&#x2718;";       // X

        // empty defaults
        $add_category  = array();
        $add_footprint = array();
        $add_storeloc  = array();
        $add_supplier  = array();

        // do sanity checks
        foreach ($nr as $key => $data)
        {
            $active[$key]            = true;
            $missing_name[$key]      = $ok;
            $missing_count[$key]     = $ok;
            $missing_category[$key]  = $ok;
            $missing_footprint[$key] = $ok;
            $missing_storeloc[$key]  = $ok;
            $missing_supplier[$key]  = $ok;


            // empty name?
            if ( strlen( $name[$key]) == 0)
            {
                $active[$key]       = false;
                $missing_name[$key] = $bad;
            }
            // count not numeric?
            if ( !( is_numeric( $count[$key])))
            {
                $active[$key]        = false;
                $missing_count[$key] = $bad;
            }
            // missing category?
            if ( strlen( $category[$key]) == 0)
            {
                $active[$key]            = false;
                $missing_category[$key]  = $bad;
            }
            else
            {
                // category not found in database
                if (! (category_exists( $category[$key])))
                {
                    $missing_category[$key] = $halfok;
                    $add_category[]         = $category[$key];
                }
            }
            // missing footprint?
            if ( strlen( $footprint[$key]) == 0)
            {
                $active[$key]            = false;
                $missing_footprint[$key] = $bad;
            }
            else
            {
                if (! (footprint_exists( $footprint[$key])))
                {
                    $missing_footprint[$key] = $halfok;
                    $add_footprint[]         = $footprint[$key];
                }
            }
            // missing storeloc?
            if ( strlen( $storeloc[$key]) == 0)
            {
                $active[$key]           = false;
                $missing_storeloc[$key] = $bad;
            }
            else
            {
                if (! (location_exists( $storeloc[$key])))
                {
                    $missing_storeloc[$key] = $halfok;
                    $add_storeloc[]         = $storeloc[$key];
                }
            }
            // missing supplier?
            if ( strlen( $supplier[$key]) == 0)
            {
                $active[$key]           = false;
                $missing_supplier[$key] = $bad;
            }
            else
            {
                if (! (supplier_exists( $supplier[$key])))
                {
                    $missing_supplier[$key] = $halfok;
                    $add_supplier[]         = $supplier[$key];
                }
            }
        } // end foreach

        // suppress multiple occurence
        $add_category  = array_unique( $add_category);
        $add_footprint = array_unique( $add_footprint);
        $add_storeloc  = array_unique( $add_storeloc);
        $add_supplier  = array_unique( $add_supplier);
    } // end check_data


    // push all stuff into database
    if ( $action == "commit_data" )
    {
        // fetch missign category, footprint, etc.
        $open_category  = strlen( $_REQUEST["add_category"]) > 0 ? explode( ';', $_REQUEST["add_category"])  : array();
        $open_footprint = strlen( $_REQUEST["add_footprint"])> 0 ? explode( ';', $_REQUEST["add_footprint"]) : array();
        $open_storeloc  = strlen( $_REQUEST["add_storeloc"]) > 0 ? explode( ';', $_REQUEST["add_storeloc"])  : array();
        $open_supplier  = strlen( $_REQUEST["add_supplier"]) > 0 ? explode( ';', $_REQUEST["add_supplier"])  : array();


        // add stuff to database
        // first check if there a parent named 'Import'
        // and generate them if neccesarry
        if ( ! category_exists('Import') && count( $open_category) > 0)
        {
            category_add( 'Import');
        }
        $id = category_get_id( 'Import');
        $add_category = array();
        foreach ($open_category as $entry)
        {
            category_add( $entry, $id);
            $add_category[] = $entry;
            $refreshnav     = true;
        }

        if ( ! footprint_exists('Import') && count( $open_footprint) > 0)
        {
            footprint_add( 'Import', '');
        }
        $id = footprint_get_id( 'Import');
        $add_footprint = array();
        foreach ($open_footprint as $entry)
        {
            footprint_add( $entry, '', $id);
            $add_footprint[] = $entry;
        }

        if ( ! location_exists('Import') && count( $open_storeloc) > 0)
        {
            location_add( 'Import');
        }
        $id = location_get_id( 'Import');
        $add_storeloc = array();
        foreach ($open_storeloc as $entry)
        {
            location_add( $entry, $id);
            $add_storeloc[] = $entry;
        }

        $add_supplier = array();
        foreach ($open_supplier as $entry)
        {
            supplier_add( $entry);
            $add_supplier[] = $entry;
        }

        // add selected parts to database
        foreach ($nr as $key => $data)
        {
            if ($active[$key] == "true")
            {
                // catch the right id's
                $category_id  = category_get_id(  $category[$key]);
                $footprint_id = footprint_get_id( $footprint[$key]);
                $storeloc_id  = location_get_id(  $storeloc[$key]);
                $supplier_id  = supplier_get_id(  $supplier[$key]);

                part_add( $category_id, $name[ $key], $description[ $key], $count[ $key], 0, $comment[ $key], false, $footprint_id, $storeloc_id, $supplier_id, $sup_part[ $key]);

                // collect name for reporting
                $add_part[] = $name[$key];
            }
        }

    } // end commit_data

    // start data presentation

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
    $tmpl -> setVar('head_title', 'Import');
    $tmpl -> setVar('head_charset', $http_charset);
    $tmpl -> setVar('head_css', $css);
    $tmpl -> setVar('head_menu', true);
    $tmpl -> pparse();

?>
<script type="text/javascript">
	<?php
        if ( $refreshnav)
        {
            $refreshnav = false;
            print "parent.frames._nav_frame.location.reload();";
        }
	?>
</script>

<?php
    if ($action == "default") {
?>

<div class="outer">
    <h2>Datei ausw&auml;hlen</h2>
    <div class="inner">
        <form enctype="multipart/form-data" action="" method="post">
            <input type="hidden" name="action" value="import_file">
            Dateityp: <select name="file_type">
                <option>CSV</option>
                <option>XML</option>
            </select>
            &nbsp;&nbsp;&nbsp;
            Trennzeichen: <input type="text" name="divider" size="1" maxlength="1" value=";">
            &nbsp;&nbsp;&nbsp;
            Kodierung:
                <select name="coding" size="1">
                <?php
                    foreach( $accepted_coding as $code)
                    {
                        print '<option>'. $code .'</option>';
                    }
                ?>
                </select>
            <br>
            <input type="file"   name="import_file" size="30">
            &nbsp;&nbsp;&nbsp;
            <input type="submit" value="Importieren">
        </form>
    </div>
</div>


<div class="outer">
    <h2>Beispiel f&uuml;r den Dateiaufbau (CSV)</h2>
    <div class="inner">
        <pre>
# Kategorie; Name; Beschreibung; Anzahl; Footprint; Lagerort; Lieferant; Bestellnummer; Kommentar
Dioden;1N4004;Siliziumdiode 400V/1A;10;THT;Kiste;Reichelt;1N 4004;DO41, 400V 1A
Controller;ATMega 8;Mikrocontroller 8kB Flash, 1 kB RAM;1;DIP28;Kiste;Reichelt;ATMEGA 8-16 DIP
Oszillatoren;Quarzoszillator 8 MHz;;1;THT;Kiste;Reichelt;OSZI 8,000000
Schaltkreise;MAX 232;Schnittstellenwandler RS232-TTL;1;DIP16;Kiste;Reichelt;MAX 232 EPE
        </pre>
    </div>
    <h2>Beispiel f&uuml;r den Dateiaufbau (XML)</h2>
    <div class="inner">
        <pre><?php
            $xml_string = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<parts>
  <part>
    <category>Dioden</category>
    <name>1N4004</name>
    <description>Siliziumdiode 400V/1A</description>
    <stock>10</stock>
    <footprint>THT</footprint>
    <location>Kiste</location>
    <supplier>Reichelt</supplier>
    <order_number>1N 4004</order_number>
    <comment>DO41, 400V 1A</comment>
  </part>
  <part>
    <category>Controller</category>
    <name>ATMega 8</name>
    <description>Mikrocontroller 8kB Flash, 1 kB RAM</description>
    <stock>1</stock>
    <footprint>DIP28</footprint>
    <location>Kiste</location>
    <supplier>Reichelt</supplier>
    <order_number>ATMEGA 8-16 DIP</order_number>
    <comment/>
  </part>
</parts>
XML;
            print htmlentities( $xml_string, (int) ENT_XML1);
        ?>
        </pre>
    </div>
</div>

<?php
    }
    if ( $show_file) {
?>
<div class="outer">
    <h2>Daten importieren (<?php print $filename ?>)</h2>
    <div class="inner">
        <?php
        foreach ( $content_arr as $line_num => $line)
        {
            print "#{$line_num}: ". htmlspecialchars( $line) ."<br>\n";
        }
        ?>
    </div>
</div>
<?php
    }
    if ( $action == "check_data") {
?>


<form action="" method="post" enctype="multipart/form-data">

<input type="hidden" name="add_category"  value='<?php print implode( ';', $add_category); ?>'>
<input type="hidden" name="add_footprint" value='<?php print implode( ';', $add_footprint); ?>'>
<input type="hidden" name="add_storeloc"  value='<?php print implode( ';', $add_storeloc); ?>'>
<input type="hidden" name="add_supplier"  value='<?php print implode( ';', $add_supplier); ?>'>

<div class="outer">
    <h2>Daten pr&uuml;fen</h2>
    <div class="inner">
        <?php
            if ( sizeof($add_category) > 0 )
            {
                print "fehlende Kategorien: ". implode(', ', $add_category) ."<br>";
            }

            if ( sizeof($add_footprint) > 0 )
            {
                print "fehlende Footprints: ". implode(', ', $add_footprint) ."<br>";
            }

            if ( sizeof($add_storeloc) > 0 )
            {
                print "fehlende Lagerorte: ". implode(', ', $add_storeloc) ."<br>";
            }

            if ( sizeof($add_supplier) > 0 )
            {
                print "fehlende Lieferanten: ". implode(', ', $add_supplier) ."<br>";
            }
          ?>
    </div>
    <table class="table">
    <tr class="trcat">
        <td>Import</td>
        <td>#</td>
        <td>Kategorie</td>
        <td>Name</td>
        <td>Beschreibung</td>
        <td>Anzahl<br>
        <td>Footprint</td>
        <td>Lagerort</td>
        <td>Lieferant</td>
        <td>Bestellnr.</td>
        <td>Kommentar</td>
    </tr>
<?php
        $rowcount = 1;
        foreach ($nr as $key => $data)
        {
            $rowcount++;
            print "<tr class=\"trlist". (($rowcount % 2) + 1) ."\">";

            // active and valid checkbox
            print "<td class=\"tdrow0\"><input type=\"checkbox\" name=\"active[{$key}]\" value=\"true\"";
            if ( $active[$key] )
            {
                print " checked";
            }
            else
            {
                print " disabled";
            }
            print "></td>\n";

            // line number
            print "<td class=\"tdrow1\"><input type=\"hidden\" name=\"nr[{$key}]\" value=\"{$nr[$key]}\">{$nr[$key]}</td>\n";

            // category
            print "<td class=\"tdrow2\" style=\"text-align:left\">";
            print "<input type=\"text\" style=\"width:80%\" name=\"category[$key]\" size=\"15\" value=\"{$category[$key]}\">{$missing_category[$key]}</td>\n";

            // name
            print "<td class=\"tdrow2\" style=\"text-align:left\">";
            print "<input type=\"text\" style=\"width:80%\" name=\"name[$key]\" size=\"12\" value=\"{$name[$key]}\">{$missing_name[$key]}</td>\n";

            // description
            print "<td class=\"tdrow2\" style=\"text-align:left\">";
            print "<input type=\"text\" style=\"width:95%\" name=\"description[$key]\" size=\"25\" value=\"{$description[$key]}\"></td>\n";

            // count (in stock)
            print "<td class=\"tdrow2\" style=\"text-align:left\">";
            print "<input type=\"text\" style=\"width:60%\" name=\"count[$key]\" size=\"3\" value=\"{$count[$key]}\">{$missing_count[$key]}</td>\n";

            // footprint
            print "<td class=\"tdrow2\" style=\"text-align:left\">";
            print "<input type=\"text\" style=\"width:60%\" name=\"footprint[$key]\" size=\"5\" value=\"{$footprint[$key]}\">{$missing_footprint[$key]}</td>\n";

            // storeloc
            print "<td class=\"tdrow2\" style=\"text-align:left\">";
            print "<input type=\"text\" style=\"width:75%\" name=\"storeloc[$key]\" size=\"8\" value=\"{$storeloc[$key]}\">{$missing_storeloc[$key]}</td>\n";

            // supplier
            print "<td class=\"tdrow2\" style=\"text-align:left\">";
            print "<input type=\"text\" style=\"width:75%\" name=\"supplier[$key]\" size=\"8\" value=\"{$supplier[$key]}\">{$missing_supplier[$key]}</td>\n";

            // supplierpartnr
            print "<td class=\"tdrow2\" style=\"text-align:left\">";
            print "<input type=\"text\" style=\"width:90%\" name=\"sup_part[$key]\" size=\"10\" value=\"{$sup_part[$key]}\"></td>\n";

            // comment
            print "<td class=\"tdrow2\" style=\"text-align:left\">";
            print "<input type=\"text\" style=\"width:90%\" name=\"comment[$key]\" size=\"10\" value=\"{$comment[$key]}\"></td>\n";

            print "</tr>\n";
        }

    ?>
        </td>
    </tr>
    <tr>
    <td colspan="10" class="trtext" align="center">
        <input type="submit" name="check"  value="Daten pr&uuml;fen">
        <input type="submit" name="import" value="Import">
    </td>
    </tr>
</table>
</form>
</div>


<?php
    }
    if ($action == "commit_data") {

?>
<div class="outer">
    <h2>Datenbank aktualisiert</h2>
    <div class="inner">
        <?php
            if ( sizeof($add_category) > 0 )
            {
                print "Kategorien hinzugef&uuml;gt: ". implode(', ', $add_category) ."<br>";
            }
            if ( sizeof($add_footprint) > 0 )
            {
                print "Footprints hinzugef&uuml;gt: ". implode(', ', $add_footprint) ."<br>";
            }
            if ( sizeof($add_storeloc) > 0 )
            {
                print "Lagerorte hinzugef&uuml;gt: ". implode(', ', $add_storeloc) ."<br>";
            }
            if ( sizeof($add_supplier) > 0 )
            {
                print "Lieferanten hinzugef&uuml;gt: ". implode(', ', $add_supplier) ."<br>";
            }
            if ( sizeof($add_part) > 0 )
            {
                print "Bauteile hinzugef&uuml;gt: ". implode(', ', $add_part) ."<br>";
            }
        ?>
    </div>
</div>
<?php
    }
    if ($action == "error") {

?>
<div class="outer red">
    <h2>Fehler</h2>
    <div class="inner">
        <?php print $error ?>
    </div>
</div>

<?php
    }

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
    $tmpl -> pparse();
?>
