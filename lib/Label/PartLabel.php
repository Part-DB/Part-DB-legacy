<?php
/*
    Part-DB Version 0.4+ "nextgen"
    Copyright (C) 2017 Jan Böhmer
    https://github.com/jbtronics

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
*/

namespace PartDB\Label;

/** @noinspection PhpIncludeInspection */

use PartDB\Base\NamedDBElement;
use PartDB\Part;
use TCPDF;

/**
 * A class describing a Part Label (for example a 1D Label or an QR-Code)
 *
 * @version 1.0
 * @author janhb
 */
class PartLabel extends BaseLabel
{
    /********************************************************************************
     *
     *   Basic Methods
     *
     *********************************************************************************/


    /*
    public function generateBarcode($download = false)
    {
        // create new PDF document
        $pdf = new TCPDF('L', 'mm', array(50,30), true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Part-DB');
        $pdf->SetTitle('PartDB Label: ' . $this->part->getName() . " (ID: " . $this->part->getID() . ")");
        $pdf->SetSubject('Part-DB label with barcode');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont('dejavusansmono');

        // set margins
        $pdf->SetMargins(2, 1, 2);

        // set auto page breaks
        $pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // add a page
        $pdf->AddPage();
        $pdf->SetFont('dejavusansmono', '', 8);

        foreach ($this->lines as $line) {
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

        $pdf->write1DBarcode($this->part->getBarcodeContent(), "EAN8", "", "", "", "", "", $style, 'N');

        //$pdf->write2DBarcode($this->part->get_barcode_content("QR"),"QRCODE,Q");

        if ($download) {
            $pdf->Output('label_'.$this->part->getID().'.pdf', 'D');
        } else {
            //Close and output PDF document
            $pdf->Output('label_'.$this->part->getID().'.pdf', 'I');
        }
    } */

    /**
     * Returns all presets for lines
     */
    public static function getLinePresets()
    {
        $presets = array();

        //Preset A: format like label generator from F. Thiessen
        //Link: http://fthiessen.de/part-db-etiketten-drucken/
        $lines = array();
        $lines[] = "<h3>%NAME% - %CAT%</h3>";
        $lines[] = "%STORELOC%";
        $lines[] = "%FOOT%";
        $lines[] = "%ORDER_NR% - %SUPPLIER%";
        $presets[] = array("name" => "Preset A", "lines" => $lines);

        //Preset B: Like A, full storelocation path
        $lines = array();
        $lines[] = "<h3>%NAME% - %CAT%</h3>";
        $lines[] = "%STORELOC_FULL%";
        $lines[] = "%FOOT%";
        $lines[] = "%ORDER_NR% - %SUPPLIER%";
        $presets[] = array("name" => "Preset B", "lines" => $lines);

        //Presets C: Show description in second line, Order infos may be cutted...
        $lines = array();
        $lines[] = "<h3>%NAME% - %CAT%</h3>";
        $lines[] = "%DESC%";
        $lines[] = "%STORELOC%";
        $lines[] = "%FOOT%";
        $lines[] = "%ORDER_NR% - %SUPPLIER%";
        $presets[] = array("name" => "Preset C", "lines" => $lines);

        //Presets C: With labels
        $lines = array();
        $lines[] = "BAUTEIL : %NAME% - %CAT%";
        $lines[] = "LAGER   : %STORELOC%";
        $lines[] = "GEHÄUSE : %FOOT%";
        $lines[] = "BEST-NR : %ORDER_NR% - %SUPPLIER%";
        $presets[] = array("name" => "Preset D", "lines" => $lines);

        return $presets;
    }

    /**
     * Returns all label sizes, that are supported by this class.
     * @return string[] A array containing all sizes that are supported by this class.
     */
    public static function getSupportedSizes()
    {
        return array(static::SIZE_50X30, static::SIZE_62X30);
    }

    /**
     * Returns all label types, that are supported by this class.
     * @return int[] A array containing all sizes that are supported by this class.
     */
    public static function getSupportedTypes()
    {
        return array(static::TYPE_BARCODE, static::TYPE_TEXT);
    }
}
