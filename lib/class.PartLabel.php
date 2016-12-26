<?php

//Include TCPDF library
/** @noinspection PhpIncludeInspection */
require_once(BASE.'/lib/tcpdf/tcpdf.php');

/**
 * A class describing a Part Label (for example a 1D Barcode or an QR-Code)
 *
 * @version 1.0
 * @author janhb
 */
class PartLabel
{

    //Label type definitions
    const TYPE_TEXT = 0;
    const TYPE_QR = 1;
    const TYPE_BARCODE = 2;
    const TYPE_INFO = 3;

    private $database;
    private $current_user;
    private $log;
    private $pid;
    private $type = 0;
    private $part;
    private $footprint;
    private $storelocation;
    private $manufacturer;
    private $category;
    private $all_orderdetails;
    private $lines;

    private $size = "S";

    /**
     * Initializes a new Part-Label
     * @param mixed $database
     * @param mixed $current_user
     * @param mixed $log
     */
    public function __construct($database, $current_user, $log, $pid = null)
    {
        $this->database = $database;
        $this->current_user = $current_user;
        $this->log = $log;
        $this->lines = array();
        if(isset($pid))
        {
            $this->set_pid($pid);
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Gets the current label type. Compare with PartLabel::TYPE_*
     * @return int the current label type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Summary of get_lines
     * @return string[] the current lines
     */
    public function get_lines()
    {
        return $this->lines;
    }


    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    public function set_type($new_type)
    {
        $this->type = $new_type;
    }

    /**
     * Sets the part id of the label
     * @param mixed $new_pid the id of the new part.
     */
    public function set_pid($new_pid)
    {
        $this->pid = $new_pid;
        $this->part               = new Part($this->database, $this->current_user, $this->log, $this->pid);
        $this->footprint          = $this->part->get_footprint();
        $this->storelocation      = $this->part->get_storelocation();
        $this->manufacturer       = $this->part->get_manufacturer();
        $this->category           = $this->part->get_category();
        $this->all_orderdetails   = $this->part->get_orderdetails();
    }

    public function set_size()
    {

    }

    public function set_lines($new_lines)
    {
        $tmp = array();
        foreach($new_lines as $line)
        {
            $tmp[] = replace_placeholder_with_infos($line,$this->part);
        }
        $this->lines = $tmp;
    }

    /********************************************************************************
     *
     *   Basic Methods
     *
     *********************************************************************************/

    private function build_barcode_config()
    {
        if(true)
        { //case S
            $c = array("size" => array(50,30),
		     "margins" => array(1,2,1),
		     "fontsize" => 8
             );

            return $c;
         }
        return "";
    }

    public function generate_barcode($download = false)
    {
        // create new PDF document
        $pdf = new TCPDF('L', 'mm', array(50,30) , true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Part-DB');
        $pdf->SetTitle('PartDB Label: ' . $this->part->get_name() . " (ID: " . $this->part->get_id() . ")");
        $pdf->SetSubject('Part-DB label with barcode');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont('dejavusansmono');

        // set margins
        $pdf->SetMargins(2, 1, 2);

        // set auto page breaks
        $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // add a page
        $pdf->AddPage();
        $pdf->SetFont('dejavusansmono', '', 8);

        foreach($this->lines as $line)
        {
            $pdf->Cell(0, 0, $line);
            $pdf->Ln();
        }

        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8 );

        $pdf->write1DBarcode($this->part->get_barcode_content(),"EAN8","","","","","",$style,'N');

        //$pdf->write2DBarcode($this->part->get_barcode_content("QR"),"QRCODE,Q");

        if($download)
        {
            $pdf->Output('label_'.$this->part->get_id().'.pdf', 'D');
        }
        else
        {
            //Close and output PDF document
            $pdf->Output('label_'.$this->part->get_id().'.pdf', 'I');
        }
    }

    /**
     * Returns all presets for lines
     */
    public function get_line_presets()
    {
        $presets = array();

        //Preset A: format like label generator from F. Thiessen
        //Link: http://fthiessen.de/part-db-etiketten-drucken/
        $lines = array();
        $lines[] = "%name% - %cat%";
        $lines[] = "%storeloc%";
        $lines[] = "%foot%";
        $lines[] = "%order_nr% - %supplier%";
        $presets[] = $lines;

        //Preset B: Like A, full storelocation path
        $lines = array();
        $lines[] = "%name% - %cat%";
        $lines[] = "%storeloc_full%";
        $lines[] = "%foot%";
        $lines[] = "%order_nr% - %supplier%";
        $presets[] = $lines;

        //Presets C: Show description in second line, Order infos may be cutted...
        $lines = array();
        $lines[] = "%name% - %cat%";
        $lines[] = "%desc%";
        $lines[] = "%storeloc%";
        $lines[] = "%foot%";
        $lines[] = "%order_nr% - %supplier%";
        $presets[] = $lines;

        //Presets C: With labels
        $lines = array();
        $lines[] = "BAUTEIL : %name% - %cat%";
        $lines[] = "LAGER   : %storeloc%";
        $lines[] = "GEH�USE : %foot%";
        $lines[] = "BEST-NR : %order_nr% - %supplier%";
        $presets[] = $lines;

        return $presets;
    }

    /**
     * Generates this label with the given settings
     */
    public function generate()
    {
        $this->generate_barcode();
    }

    public function download()
    {
        $this->generate_barcode(true);
    }




}