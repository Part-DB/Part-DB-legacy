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

    $Id$

    Changelog (sorted by date):
        [DATE]      [NICKNAME]      [CHANGES]
        2012-08-??  kami89          - created
        2012-09-28  kami89          - added doxygen comments
*/

    /**
     * @file class.Log.php
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

        /** (Database) the Database object for the database access of the logs */
        private $database = NULL;

        /********************************************************************************
        *
        *   Constructor / Destructor
        *
        *********************************************************************************/        
        
        /**
         * @brief Constructor
         *
         * @param Database  &$database      reference to the database
         * 
         * @throws Exception if there was an error
         */
        public function __construct(&$database)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt');

            $this->database = $database;
        }

    }
    
?>
