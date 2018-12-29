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

namespace PartDB\Tools;

/**
 * The constants in this class determines how BBCode should be returned.
 * @package PartDB\Tools
 */
class BBCodeParsingLevel
{
    //The false and true definitions, remains backwards compatibility.

    /**
     * Returns the raw value, like it is saved in the DB. Example: "[b]Test[/b]"
     */
    const RAW = false;
    /**
     * Returns a version of the BBCode parsed to HTML code. Example: "<b>Test</b>"
     */
    const PARSE = true;
    /**
     * Returns a version without any BBCode or HTML tags (pure text). Example: "Test"
     */
    const STRIP = 2;
}
