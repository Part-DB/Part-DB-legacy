<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 17.12.2017
 * Time: 20:24
 */

namespace PartDB\Interfaces;

interface ILabel
{
    public function getID();

    public function getName();

    /**
     * Gets the content for a 1D/2D barcode for this part
     * @param string $barcode_type the type of the barcode ("EAN8" or "QR")
     * @return string
     * @throws \Exception An Exception is thrown if you selected a unknown barcode type.
     */
    public function getBarcodeContent($barcode_type = "EAN8");

    public function replacePlaceholderWithInfos($string);
}
