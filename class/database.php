<?php 
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

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

    $Id: database.php 480 2012-07-07 12:23:24Z kami89@gmx.ch $
*/

    include_once('config.php');
    include_once('db_update_steps.php');

    /*
     * Class Database
     *
     * This class is:
     *      - for managing all database acces
     *      - for managing database updates
     *      - NOT for the update steps: they are in the file "db_update_steps.php".
     *
     * Supported database types of this class are:
     *      - mysql (MySQL)
     *      At the moment there are no more databases implemented. Pleasy add more databases to init().
     *
     * PLEASE NOTE:
     *      You have to install the PDO module of the database which you want to use (e.g. "PDO_MYSQL")!
     *      Normally this is done by installing the php-database package (e.g. "php5-mysql") in your linux system.
     *      PDO needs PHP version 5.1 or higher.
     *      For more info have a look at "http://www.php.net/manual/de/pdo.installation.php" (for example)
     *
     */

    class Database
    {
        /********************************************************************************
        *
        *   Attributes
        *
        *********************************************************************************/

        // connection data
        private $type =         NULL;
        private $server =       NULL;
        private $user =         NULL;
        private $password =     NULL;
        private $databasename = NULL;
        private $charset =      NULL;

        // PHP Data Objects
        private $pdo =          NULL;

        /********************************************************************************
        *
        *   Constructor / Destructor
        *
        *********************************************************************************/        
        
        /*
         * Constructor
         *
         * Parameters:
         *      $type:          database type (e.g. 'mysql')
         *      $server:        database server
         *      $user:          database user
         *      $password:      database password
         *      $databasename:  database name
         *      $charset        database charset (e.g. 'utf8')
         *
         * If there are no arguments, the defines from config.php will be used.
         */
        public function __construct($type = DB_TYPE, $server = DB_SERVER, $user = DB_USER,
                                    $password = DB_PASSWORD, $databasename = DB_NAME, $charset = DB_CHARSET)
        {
            $this->type         = $type;
            $this->server       = $server;
            $this->user         = $user;
            $this->password     = $password;
            $this->databasename = $databasename;
            $this->charset      = $charset;

            $this->init();
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /*
         * Get current database version
         *
         * Return:
         *      current database version
         */
        public function get_current_version()
        {
            $query_data = $this->query('SELECT keyValue FROM internal WHERE keyName LIKE ?', 'dbVersion');

            if ($query_data == NULL)
                return 1; // not found, old version? prepare for database update...

            return intval($query_data[0]['keyValue']);
        }

         /*
         * Get latest database version
         *
         * Return:
         *      latest database version
         */
        public function get_latest_version()
        {
            global $latest_db_version;
            return $latest_db_version;
        }

        /*
         * Check if automatic updates are turned on
         *
         * If there is no such field in the database, it will be created (with valus=false).
         *
         * Return:
         *        true:   if automatic updates are turned on
         *        false:  if automatic updates are turned off
         */
        public function get_automatic_updates_status()
        {
            $query_data = $this->query('SELECT keyValue FROM internal WHERE keyName LIKE ?', 'dbAutoUpdate');

            if ($query_data == NULL)
            {
                // there is no such record in the database? we will create it...
                $this->execute('INSERT INTO internal SET keyName=?, keyValue=?', 'dbAutoUpdate', false);
                return false;
            }

            $value = $query_data[0]['keyValue'];

            if ($value == true)
                return true;
            else
                return false;
        }

        /********************************************************************************
        *
        *   Setters
        *
        *********************************************************************************/

        /*
         * Set current database version
         *
         * Parameters:
         *      $new_current_version:   the new current version
         *
         * Return:
         *      true:   if success
         *      false:  if there was an error
         */
        public function set_current_version($new_current_version)
        {
            $query_count = $this->execute('UPDATE internal set keyValue=? WHERE keyName=?',
                                            $new_current_version, 'dbVersion');

            if ($query_count === NULL)
                return false;

            return true;
        }

        /*
         * Turn the automatic updates on or off
         *
         * Parameters:
         *      $active:        if true, automatic updates will be turned on
         *                      if false, automatic updates will be turned off
         *
         * Return:
         *      true:   if success
         *      false:  if there was an error
         */
        public function set_automatic_updates_status($active)
        {
            $query_count = $this->execute('UPDATE internal SET keyValue=? WHERE keyName LIKE ?',
                                        $active, 'dbAutoUpdate');

            if ($query_count === NULL)
                return false;

            return true;
        }

        /********************************************************************************
        *
        *   General Database Functions
        *
        *********************************************************************************/    
    
        /*
         * Database init
         */
        public function init()
        {
            $this->pdo = NULL;

            try
            {
                $options = array();

                // I don't know really if we need to set the charset...
                if ($this->charset != NULL)
                    $options['PDO::MYSQL_ATTR_INIT_COMMAND'] = 'SET NAMES '.$this->charset;

                $this->pdo = new PDO($this->type.':host='. $this->server .';dbname='. $this->databasename,
                                        $this->user, $this->password, $options);

                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } 
            catch (PDOException $e)
            {
                die('PDO Database Error: ' . $e->getMessage() . '<br/>');
            }
        }

        /********************************************************************************
        *
        *   Database Backup and Restore Functions
        *
        *********************************************************************************/  

        /*
         * Make a backup of the whole database
         *
         * Return:
         *      true:       if success
         *      string:     error information if there was an error
         */
        public function backup()
        {
            // TODO     
        }

        /*
         * Restore a backup (and override the current database!)
         *
         * Return:
         *      true:       if success
         *      string:     error information if there was an error
         */
        public function restore()
        {
            // TODO     
        }

        /********************************************************************************
        *
        *   Database Update Functions
        *
        *********************************************************************************/  

        /*
         * Check if a database update is required
         *
         * Parameters:
         *      $print_status:    if true, thstatusps will be printed, otherwise this function is silent
         *
         * Return:
         *      true:   if update is required
         *      false:  if we have the latest version
         */
        public function is_update_required($print_status = false)
        {
            $current = $this->get_current_version();
            $latest = $this->get_latest_version();

            if ($current > $latest)
            {
                if ($print_status == true)
                    print 'WARNUNG: Ihre Datenbank Version '.$current.
                            ' ist neuer als die von part-DB unterst&uuml;tzte Version '.$latest.'!<br>';
                return false;
            }
            elseif ($current < $latest)
            {
                if ($print_status == true)
                    print 'Es ist ein Datenbankupdate von ihrer Version '.$current.
                            ' auf die neuste Version '.$latest.' vorhanden.<br>';
                return true;
            }
            else
            {
                if ($print_status == true)
                    print 'Ihre Datenbank Version '.$current.' ist auf dem neusten Stand.<br>';
                return false;
            }                
        }

        /*
         * Update the database to the latest version
         *
         * Parameters:
         *      $print_steps:   if true, the steps will be printed, otherwise this function is silent
         *
         * Return:
         *      true:   if success
         *      false:  if there was an error
         */
        public function update($print_steps)
        {  
            $error = false;
            $prints = array();

            $current = $this->get_current_version();
            $latest = $this->get_latest_version();

            $prints[] = 'Ihre Datenbank wird von der Version '. $current .' auf die Version '. $latest .' aktualisiert:<br>';
            $prints[] = 'Datenbank wird gesperrt...<br>';

            $query_data = $this->query("SELECT GET_LOCK('UpdatePartDB', 3)");

            if ($query_data == NULL)
            {
                $prints[] = 'FEHLER: Wird zur Zeit schon ein Update durchgef&uuml;hrt?<br>';
                $error = true;
            }

            while (($current < $latest) && ($error == false))
            {
                $steps = get_db_update_steps($current);

                if (count($steps) == NULL)
                {
                    $prints[] = 'FEHLER: Keine Updateschritte f&uuml;r Version '.$current.' gefunden!<br>';
                    $error = true;
                    break;
                }

                foreach($steps as $query)
                {
                    $query_count = $this->execute($query);

                    if ($query_count === NULL)
                    {
                        $prints[] = 'FEHLER: Konnte Schritt nicht durchf&uuml;hren!<br>';
                        $error = true;
                        break;
                    }

                    $prints[] = 'Schritt: '.$query.'...OK<br>';
                }

                if ($error == true)
                    break;

                if ($this->set_current_version($current + 1) == false)
                {
                    $prints[] = 'FEHLER: Die neue Version konnte nicht gesetzt werden!<br>';
                    $error = true;
                    break;
                }

                $current = $this->get_current_version();

                if ($current <= 1)
                {
                    $prints[] = 'FEHLER: Die neue Version konnte nicht gesetzt werden!<br>';
                    $error = true;
                    break;
                }
            }

            if ($error == false)
            {
                $prints[] = 'Datenbank wird freigegeben...<br>';
                $query_data = $this->query("SELECT RELEASE_LOCK('UpdatePartDB')");

                if ($query_data == NULL)
                {
                    $prints[] = 'FEHLER: Die Datenbank konnte nicht entsperrt werden!<br>';
                    $error = true;
                }
            }

            if ($error == true)
            {
                $prints[] = 'ABBRUCH: Das Update konnte nicht durchgef&uuml;hrt werden!<br>';

                if ($print_steps == true)
                {
                    foreach ($prints as $print)
                        print $print;

                    // TODO: print the SQL error from PDO here
                }

                return false;
            }
            else 
            {
                $prints[] = 'Das Update wurde erfolgreich durchgef&uuml;hrt.<br>';

                if ($print_steps == true)
                {
                    foreach ($prints as $print)
                        print $print;
                }

                return true;
            }
        }

        /********************************************************************************
        *
        *   Query Functions
        *
        *********************************************************************************/ 

        /*
         * Bind values recursive
         * This function is used by exec() and query().
         * 
         * Parameters:
         *      $pdo_statement:     reference to the PDO statement
         *      $values:            reference to the values which we want to bind ([multidimensional] array of values)
         *                          Important: the order have to correspond with the 'question mark' of the query tring!!
         *      $index:             reference to the index of the first element we want to bind
         *
         * Return:
         *      true:   if success
         *      false: if there was an error
         */
        private function bind_values(&$pdo_statement, &$values, &$index)
        {
            foreach ($values as $value)
            {
                if (is_array($value) == true)
                {
                    if ($this->bind_values($pdo_statement, $value, $index) == false)
                        return false;
                }
                else
                {
                    if ($pdo_statement->bindValue($index, $value) == false)
                        return false;

                    $index++;
                }
            } 

            return true;
        }

        /*
         * Execute an SQL statement
         * There is no returned data (but count), so don't use it if you expect returned data!
         * Use this function only for UPDATE, REPLACE, DELETE and INSERT statements!
         * If you execute an INSERT statemen, you will get the ID of the new record.
         *
         * Parameters:
         *      $query:         the query string, but with the symbol ? as place holder for the values
         *                  OR: n-dimensional array with the query string as first element, and values as other elements
         *      behind $query:  variable count of arguments; for each place holder in $query, there must be an argument
         *
         * Return:
         *      count of elements which were modified or deleted:   if success
         *  OR: if the query was an INSERT command, the ID of the new record will be returned
         *      NULL or die():                                      if there was an error
         *
         *      please notice that the return value '0' (success) is not the same as 'NULL' (error)!
         *      check it with the '===' operator!
         */
        public function execute($query)
        {
            if (is_array($query) == true)
            {
                $values = $query;
                $query = $values[0];
            }
            else
                $values = func_get_args();

            unset($values[0]); // remove the query string from array, we have it in "$query"

            if (stripos($query, 'INSERT') === 0)
                $is_insert_statement = true;
            else 
                $is_insert_statement = false;

            $result = NULL;

            try
            {
                $this->pdo->beginTransaction();

                $pdo_statement = $this->pdo->prepare($query);

                if ($this->bind_values($pdo_statement, $values, $index=1) == false)
                {
                    $this->pdo->rollBack();
                    return NULL;
                }

                if ($pdo_statement->execute() == false)
                {
                    $this->pdo->rollBack();
                    return NULL;
                }

                if ($is_insert_statement == true)
                    $result = $this->pdo->lastInsertId('id');
                else
                    $result = $pdo_statement->rowCount();

                $this->pdo->commit();
            } 
            catch (PDOException $e)
            {
                $this->pdo->rollBack();
                die('<br>PDO Database Error (query): ' . $e->getMessage() . '<br><br>'. 
                    'SQL Query: '.$pdo_statement->queryString);
            }

            if ((is_int($result) == true) || (ctype_digit($result) == true))
                return $result;
            else 
                return NULL;
        }

        /*
         * Make a query, escape and fetch all data
         * Don't use this function if you don't expect returned data (but count)!
         * So use this function only for SELECT statements!
         *
         * Parameters:
         *      $query:         the query string, but with the symbol ? as place holder for the values
         *                      OR: n-dimensional array with the query string as first element, and values as other elements
         *      behind $query:  variable count of arguments; for each place holder in $query, there must be an argument
         *
         * Return:
         *      2D data array (numeric + associative):  if success
         *      NULL:                                   if there is no return from query
         *      die():                                  if there was an error
         */
        public function query($query)
        {
            if (is_array($query) == true)
            {
                $values = $query;
                $query = $values[0];
            }
            else
                $values = func_get_args();

            unset($values[0]); // remove the query string from array, we have it in "$query"

            $data = NULL;

            try
            {
                $this->pdo->beginTransaction();

                $pdo_statement = $this->pdo->prepare($query);

                if ($pdo_statement == false)
                {
                    $this->pdo->rollBack();
                    return NULL;
                }

                if ($this->bind_values($pdo_statement, $values, $index=1) == false)
                {
                    $this->pdo->rollBack();
                    return NULL;
                }

                if ($pdo_statement->execute() == false)
                {
                    $this->pdo->rollBack();
                    return NULL;
                }

                $data = $pdo_statement->fetchAll();

                $this->pdo->commit();
            } 
            catch (PDOException $e)
            {
                $this->pdo->rollBack();
                die('<br>PDO Database Error (query): ' . $e->getMessage() . '<br><br>'. 
                    'SQL Query: '.$pdo_statement->queryString);
            }

            if ($data == false)
                return NULL;

            if (is_array($data) == false)
                return NULL;

            /* Does we have to unescape all elements in $data here?
             * If we have to do it, we can do it with this command:
             *
             *   array_walk_recursive($data, 'self::unescape');
             *
             * ...and this function:
             *
             *   public function unescape(&$value)
             *   {
             *       $value = stripslashes($value);
             *   }
             *
             */

            return $data;
        }

        /********************************************************************************
        *
        *   Basic Database Functions
        *
        *********************************************************************************/ 

        /*
         * Check if a database table exists
         *
         * Parameters:
         *      $tablename:     the name of the table
         *
         * Return:
         *      true:   there is at least one table with this name
         *      false:  there is no table with this name
         *      NULL:   if there was an error
         */
        public function does_table_exist($tablename)
        {
            $query_data = $this->query("SHOW TABLES LIKE ?", $tablename);

            if ($query_data == NULL)
                return NULL;
            
            if (count($query_data) >= 1)
                return true;
            else
                return false;
        }

        /*
         * Get count of records in a table
         *
         * Parameters:
         *      $tablename:     name of the table
         *
         * Return:
         *      count of records:   if success
         *      NULL:               if there was an error
         */
        public function get_count_of_records($tablename)
        {
            $query_data = $this->query('SELECT count(*) as count FROM '. $tablename);

            if ($query_data == NULL)
                return NULL;

            return intval($query_data[0]['count']);
        }

        /*
         * Get all data of a record (by ID)
         *
         * Parameters:
         *      $tablename: the name of the table
         *      $id:        ID of the element
         *
         * Return:
         *      data array [with all table columns]:    if success
         *      NULL:                                   if there is no element with this ID, or if there was an error
         */
        public function get_record_data($tablename, $id)
        {
            $query_data = $this->query( 'SELECT * FROM '. $tablename .
                                        ' WHERE id=?', $id);

            if ($query_data == NULL)
                return NULL;

            if (count($query_data) == 0)
                return NULL;

            return $query_data[0];
        }

        /*
         * Delete a database element
         *
         * Parameters:
         *      $tablename:     name of the table where the element is located
         *      $id:            id of the element which should be deleted
         *
         * Return:
         *      true:   if success
         *      false:  if there was an error
         */
        public function delete_record($tablename, $id)
        {
            $query_count = $this->execute( 'DELETE FROM '. $tablename .
                                           ' WHERE id=? LIMIT 1', $id);

            if ($query_count === NULL)
                return false;
            else
                return true;
        }

        /*
         * Set a value in a table field
         *
         * Parameters:
         *      $tablename:     name of the table where the element is located
         *      $id:            id of the element which should be deleted
         *      $row:           name of the datarow
         *      $value:         the new value
         *
         * Return:
         *      true:   if success
         *      false:  if there was an error
         */
        public function set_data_field($tablename, $id, $row, $value)
        {
            $query_count = $this->execute(  'UPDATE '. $tablename .
                                            ' SET '. $row .'=? WHERE id=? LIMIT 1',
                                            $value, $id);

            if ($query_count === NULL)
                return false;
            else
                return true;
        }  

    }

?>
