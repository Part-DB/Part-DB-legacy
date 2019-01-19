<?php

/**
 *
 * Part-DB Version 0.4+ "nextgen"
 * Copyright (C) 2016 - 2018 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 *
 */

namespace PartDB\Label;

use PartDB\Exceptions\NotImplementedException;
use PartDB\Interfaces\ILabel;
use PartDB\User;
use TCPDF;

abstract class BaseLabel
{
    //Label type definitions
    const TYPE_TEXT = 0;
    const TYPE_QR = 1;
    const TYPE_BARCODE = 2;
    const TYPE_C39 = 3; //Code 128 Barcode
    //const TYPE_INFO = 3;

    const SIZE_50X30 = '50x30';
    const SIZE_62X30 = '62x30';
    const SIZE_CUSTOM = 'custom';

    const PRESET_CUSTOM = 'custom';

    /* @var ILabel */
    protected $element;
    /* @var $string */
    protected $size;
    /* @var int */
    protected $type;
    protected $preset;

    /* @var TCPDF */
    protected $pdf;

    protected $options;

    /**
     * Creates a new BaseLabel object.
     * @param $element ILabel The element from which the label data should be derived
     * @param $type int A type for the Label, use TYPE_ consts for that.
     * @param $size string The size the label should have, use SIZE_ consts.
     * @param $preset string The name of the preset for the lines, that should be used for this label. Use PRESET_CUSTOM for custom lines, passed via $options.
     * @param $options array An array containing various advanced options.
     */
    public function __construct(ILabel $element, int $type, string $size, string $preset, $options = null)
    {
        if (!\in_array($type, static::getSupportedTypes())) {
            throw new \InvalidArgumentException(_('Der gewählte Labeltyp wird von dem aktuellem Labelgenerator nicht unterstützt!'));
        }

        if ($size != 'custom' &&  !\in_array($size, static::getSupportedSizes())) {
            throw new \InvalidArgumentException(_('Die gewählte Labelgröße wird von dem aktuellem Labelgenerator nicht unterstützt!'));
        }

        $this->element = $element;
        $this->size = $size;
        $this->type = $type;
        $this->preset = $preset;

        $this->options = $options;

        $this->createTCPDFConfig();
    }

    protected function generateLines() : array
    {
        $lines = array();
        if ($this->preset == 'custom') {
            if (isset($this->options['custom_rows'])) {
                $lines = explode("\n", $this->options['custom_rows']);
            }
        } else {
            foreach (static::getLinePresets() as $preset) {
                if ($preset['name'] == $this->preset) {
                    $lines = $preset['lines'];
                }
            }
        }
        foreach ($lines as &$line) {
            $line = static::replacePlaceholderWithInfos($line);
            $line = $this->element->replacePlaceholderWithInfos($line);
        }

        return $lines;
    }

    protected function generateLabel(bool $download = false)
    {
        // add a page
        $this->pdf->AddPage();

        $text_style = '';
        if (isset($this->options['text_bold']) && $this->options['text_bold']) {
            $text_style .= 'b';
        }
        if (isset($this->options['text_italic']) && $this->options['text_italic']) {
            $text_style .= 'i';
        }
        if (isset($this->options['text_underline']) && $this->options['text_underline']) {
            $text_style .= 'u';
        }

        $text_size = 8;
        if (isset($this->options['text_size'])) {
            $text_size = $this->options['text_size'];
        }

        $this->pdf->SetFont('dejavusansmono', $text_style, $text_size);

        $lines = $this->generateLines();

        //Parse Option for text alignment
        $text_position = 'L';
        if (isset($this->options['text_alignment'])) {
            switch ($this->options['text_alignment']) {
                case 'left':
                    $text_position = 'L';
                    break;
                case 'center':
                    $text_position = 'C';
                    break;
                case 'right':
                    $text_position = 'R';
                    break;
            }
        }

        foreach ($lines as $line) {
            if (isset($this->options['force_text_output']) && $this->options['force_text_output']) {
                $this->pdf->Cell(0, 0, $line, 0, 0, $text_position);
            } else {
                $this->pdf->writeHTMLCell(0, 0, '', '', $line, 0, 0, false, true, $text_position);
            }
            $this->pdf->Ln();
        }

        //Parse Option for barcode position
        $barcode_position = 'C';
        if (isset($this->options['barcode_alignment'])) {
            switch ($this->options['barcode_alignment']) {
                case 'left':
                    $barcode_position = 'L';
                    break;
                case 'center':
                    $barcode_position = 'C';
                    break;
                case 'right':
                    $barcode_position = 'R';
                    break;
            }
        }

        $y_pos = $this->pdf->GetY() + 1;

        if ($this->options['logo_path'] != '') {
            $path = BASE . '/' . $this->options['logo_path'];

            if (isPathabsoluteAndUnix($path)) {
                $this->pdf->setJPEGQuality(100);


                $this->pdf->Image($path, '3', $this->pdf->GetY() + 1, '10', '', '', '', 'R', true, 300);
            }
        }

        if ($this->type == static::TYPE_BARCODE ||
            $this->type == static::TYPE_C39) {
            //Create barcode config.
            $style = array(
                'position' => $barcode_position,
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

            switch ($this->type) {
                case static::TYPE_BARCODE:
                    $type = 'EAN8';
                    $width = '';
                    break;
                case static::TYPE_C39:
                    $type = 'C39';
                    $width = '36';
                    break;
                default:
                    $width = '';
                    throw new \InvalidArgumentException(sprintf(_('Der Barcodetyp %s wird nicht unterstützt!'), $this->type));
            }

            $this->pdf->write1DBarcode($this->element->getBarcodeContent($type), $type, '', '', $width, 15, '', $style, 'N');
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
        if ($this->size == 'custom') {
            $size = array($this->options['custom_width'], $this->options['custom_height']);
        } else {
            $size = explode('x', $this->size);
        }
        $this->pdf = new TCPDF('L', 'mm', $size, true, 'UTF-8', false);

        // set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Part-DB');
        $this->pdf->SetTitle('PartDB Label: ' . $this->element->getName() . ' (ID: ' . $this->element->getID() . ')');
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
     * Generate a label with the given settings and show it in browser to the user.
     */
    public function generate()
    {
        $this->generateLabel();
    }

    /**
     * Generate a label with the given settings and download it.
     */
    public function download()
    {
        $this->generateLabel(true);
    }

    /******************************************************************************
     *
     * Static functions
     *
     ******************************************************************************/

    /**
     * Returns all available line presets, that are supported by this class.
     * @return array An array containing the name in "name" key and the lines as string array in "lines" key.
     */
    public static function getLinePresets() : array
    {
        throw new NotImplementedException(_('getLinePresets() ist nicht implementiert'));
    }

    /**
     * Returns all label sizes, that are supported by this class.
     * @return string[] A array containing all sizes that are supported by this class.
     */
    public static function getSupportedSizes() : array
    {
        throw new NotImplementedException(_('getSupportedSizes() ist nicht implementiert'));
    }

    /**
     * Returns all label types, that are supported by this class.
     * @return int[] A array containing all sizes that are supported by this class.
     */
    public static function getSupportedTypes() : array
    {
        throw new NotImplementedException(_('getSupportedTypes() ist nicht implementiert'));
    }

    /**
     * Replaces placeholder in the format %PLACEHOLDER% with info.
     *
     * This provides some generic placeholders. Compare with replacePlaceholderWithInfos() of ILabel.
     *
     * @param $string string The string which contains the placeholder.
     * @return string A string with the filled placeholders.
     * @throws \Exception
     */
    public static function replacePlaceholderWithInfos(string $string) : string
    {
        $user = User::getLoggedInUser();
        global $config;

        $string = str_replace('%USERNAME%', $user->getName(), $string);
        $string = str_replace('%USERNAME_FULL%', $user->getFullName(), $string);

        $string = str_replace('%DATETIME%', formatTimestamp(time()), $string);
        $string = str_replace('%INSTALL_NAME%', $config['partdb_title'], $string);

        return $string;
    }
}
