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

namespace PartDB\Interfaces;

interface ILabel
{
    public function getID() : int;

    public function getName() : string;

    /**
     * Gets the content for a 1D/2D barcode for this part
     * @param string $barcode_type the type of the barcode ("EAN8" or "QR")
     * @return string
     * @throws \Exception An Exception is thrown if you selected a unknown barcode type.
     */
    public function getBarcodeContent(string $barcode_type = 'EAN8') : string;

    public function replacePlaceholderWithInfos(string $string) : string;
}
