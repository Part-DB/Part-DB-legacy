<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 17.12.2017
 * Time: 13:49
 */

namespace PartDB\Label;


use PartDB\Base\NamedDBElement;
use PartDB\Exceptions\NotImplementedException;
use PartDB\Interfaces\ILabel;
use TCPDF;

abstract class BaseLabel
{
    //Label type definitions
    const TYPE_TEXT = 0;
    const TYPE_QR = 1;
    const TYPE_BARCODE = 2;
    const TYPE_INFO = 3;

    const SIZE_50x30 = "50x30";
    const SIZE_62x30 = "62x30";

    /* @var ILabel */
    protected $element;
    /* @var $string */
    protected $size;
    /* @var int */
    protected $type;
    protected $preset;

    /* @var TCPDF */
    protected $pdf;

    /**
     * Creates a new BaseLabel object.
     * @param $element NamedDBElement The element from which the label data should be derived
     * @param $type int A type for the Label, use TYPE_ consts for that.
     * @param $size string The size the label should have, use SIZE_ consts.
     */
    public function __construct($element, $type, $size, $preset)
    {
        if(! $element instanceof ILabel) {
            throw new \InvalidArgumentException(_('$element ist kein gültiges ILabel-Objekt!'));
        }

        if (!in_array($type, static::getSupportedTypes())) {
            throw new \InvalidArgumentException(_('Der gewählte Labeltyp wird von dem aktuellem Labelgenerator nicht unterstützt!'));
        }

        if (!in_array($size, static::getSupportedSizes())) {
            throw new \InvalidArgumentException(_('Die gewählte Labelgröße wird von dem aktuellem Labelgenerator nicht unterstützt!'));
        }

        $this->element = $element;
        $this->size = $size;
        $this->type = $type;
        $this->preset = $preset;

        static::createTCPDFConfig();
    }

    protected function generateLines()
    {
        $lines = array();
        foreach (static::getLinePresets() as $preset) {
            if($preset["name"] == $this->preset) {
                $lines = $preset["lines"];
            }
        }

        foreach ($lines as &$line) {
            $line = $this->element->replacePlaceholderWithInfos($line);
        }

        return $lines;
    }

    protected function generateLabel($download = false)
    {
        // add a page
        $this->pdf->AddPage();
        $this->pdf->SetFont('dejavusansmono', '', 8);

        $lines = $this->generateLines();

        foreach ($lines as $line) {
            $this->pdf->Cell(0, 0, $line);
            $this->pdf->Ln();
        }

        if ($this->type == static::TYPE_BARCODE) {
            $style = array(
                'position' => 'C',
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

            $this->pdf->write1DBarcode($this->element->getBarcodeContent(), "EAN8", "", "", "", 15, "", $style, 'N');
        }

        //Output the labels

        if ($download) {
            $this->pdf->Output('label_'.$this->element->getID().'.pdf', 'D');
        } else {
            //Close and output PDF document
            $this->pdf->Output('label_'.$this->element->getID().'.pdf', 'I');
        }
    }

    protected function createTCPDFConfig()
    {
        // create new PDF document
        $size = explode("x", $this->size);
        $this->pdf = new TCPDF('L', 'mm', $size, true, 'UTF-8', false);

        // set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Part-DB');
        $this->pdf->SetTitle('PartDB Label: ' . $this->element->getName() . " (ID: " . $this->element->getID() . ")");
        $this->pdf->SetSubject('Part-DB label with barcode');

        // remove default header/footer
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont('dejavusansmono');

        // set margins
        $this->pdf->SetMargins(2, 2.5, 2);

        // set auto page breaks
        $this->pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }

    /**
     * Generates this label with the given settings
     */
    public function generate()
    {
        $this->generateLabel();
    }

    public function download()
    {
        $this->generateLabel(true);
    }

    /******************************************************************************
     *
     * Static functions
     *
     ******************************************************************************/

    public static function getLinePresets()
    {
        throw new NotImplementedException(_("getLinePresets() ist nicht implementiert"));
    }

    /**
     * Returns all label sizes, that are supported by this class.
     * @return string[] A array containing all sizes that are supported by this class.
     */
    public static function getSupportedSizes()
    {
        throw new NotImplementedException(_("getSupportedSizes() ist nicht implementiert"));
    }

    /**
     * Returns all label types, that are supported by this class.
     * @return int[] A array containing all sizes that are supported by this class.
     */
    public static function getSupportedTypes()
    {
        throw new NotImplementedException(_("getSupportedTypes() ist nicht implementiert"));
    }
}