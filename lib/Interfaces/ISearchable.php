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

namespace PartDB\Interfaces;

use Exception;
use PartDB\Database;
use PartDB\Log;
use PartDB\User;

interface ISearchable
{
    /**
     * Search elements by name.
     *
     * @param Database  &$database              reference to the database object
     * @param User      &$current_user          reference to the user which is logged in
     * @param Log       &$log                   reference to the Log-object
     * @param string    $keyword                the search string
     * @param boolean   $exact_match            @li If true, only records which matches exactly will be returned
     *                                          @li If false, all similar records will be returned
     *
     * @return array    all found elements as a one-dimensional array of objects,
     *                  sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function search(&$database, &$current_user, &$log, $keyword, $exact_match);
}
