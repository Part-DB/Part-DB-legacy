<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

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

namespace PartDB;

use Exception;

/**
 * @file Log.php
 * @brief class Log
 *
 * @class Log
 * @brief Class Log
 *
 * This class manages all log types.
 * With one instance of this class, you have access to all supported log types.
 *
 * @author kami89
 *
 * @todo There are no log types implemented yet.
 */
class Log
{
    /********************************************************************************
     *
     *   Attributes
     *
     *********************************************************************************/

    /** @var Database the Database object for the database access of the logs */
    private $database = null;

    /********************************************************************************
     *
     *   Constructor / Destructor
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @param Database  &$database      reference to the database
     *
     * @throws Exception if there was an error
     */
    public function __construct(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt'));
        }

        $this->database = $database;
    }
}
