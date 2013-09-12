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
     * @file class.System.php
     * @brief class System
     *
     * @class System
     * @brief Class System
     *
     * This class is used for managing system versions and system updates.
     *
     * @author kami89
     *
     * @todo system updates are not implemented yet
     */
    class System
    {
        /********************************************************************************
        *
        *   Attributes
        *
        *********************************************************************************/

        /** (Database) the Database object for the database access of the logs */
        private $database                   = NULL;
        /** (Log) the Log object for logging */
        private $log                        = NULL;

        /********************************************************************************
        *
        *   Constructor / Destructor
        *
        *********************************************************************************/

        /**
         * @brief Constructor
         *
         * @param Database  &$database      reference to the Database object
         * @param Log       &$log           reference to the Log object
         *
         * @throws Exception if there was an error
         */
        public function __construct(&$database, &$log)
        {
            if (get_class($database) != 'Database')
                throw new Exception('$database ist kein Database-Objekt!');

            if (get_class($log) != 'Log')
                throw new Exception('$log ist kein Log-Objekt!');

            $this->database = $database;
            $this->log = $log;

            debug('hint', 'System initialisiert: Version '.SystemVersion::get_installed_version()->as_string(false, false, false, true),
                            __FILE__, __LINE__, __METHOD__);
        }

        /********************************************************************************
        *
        *   System Updates
        *
        *********************************************************************************/

        /*
         * @brief Update the system
         *
         * @retval Array    An Template-Loop-Array with an update log
         */
        public function update(&$update_steps)
        {
            $error = false;
            $log = array();
            $log[] = array('message' => 'Starte System-Update...');

            $current_version = SystemVersion::get_installed_version();

            // check if there are updates in $update_steps
            if (count($update_steps) == 0)
            {
                $log[] = array('color' => 'red',  'message' => 'Es wurden keine Updates ausgewählt!');
                $error = true;
            }

            // make some backups?

            if ( ! $error)
            {
                try
                {
                    foreach ($update_steps as $update)
                    {
                        $from = $update->get_from_version();
                        $to = $update->get_to_version();

                        $log[] = array('message' => 'Starte Update von "'.$from->as_string(false).'" auf "'.$to->as_string(false).'"...');

                        // check if this update matches with the currently installed version
                        if ( ! $from->is_equal_to($current_version))
                            throw new Exception('Das Update  ist nicht für die System-Version "'.$current_version->as_string(false).'" bestimmt!');

                        // run update
                        $update->install($log); // throws an exception on error
                        $current_version = $to; // update $current_version

                        // update successfully installed
                        $log[] = array('color' => 'darkgreen', 'message' => 'Update erfolgreich beendet!');
                    }
                }
                catch (Exception $e)
                {
                    // restore backups?

                    $log[] = array('color' => 'red',  'message' => 'FEHLER: '.$e->getMessage());
                    $error = true;
                }
            }

            if ( ! $error)
            {
                // success
                $log[] = array('color' => 'darkgreen', 'message' => 'System-Update erfolgreich beendet!');
            }
            else
            {
                // error
                $log[] = array('color' => 'red',  'message' => 'System-Update wegen Fehler beendet!');
            }

            return $log;
        }

    }

?>
