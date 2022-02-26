<?php declare(strict_types=1);
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

/** @noinspection PhpIncludeInspection */
use DebugBar\DataCollector\PDO\TraceablePDO;
use Exception;
use PartDB\Exceptions\DatabaseException;
use PartDB\Exceptions\ElementNotExistingException;
use PartDB\Exceptions\TableNotExistingException;
use PartDB\LogSystem\DatabaseUpdatedEntry;
use PartDB\Tools\PDBDebugBar;
use PDO;
use PDOException;

/** @noinspection PhpIncludeInspection */
include_once BASE.'/updates/db_update_steps.php';

/**
 * @file Database.php
 * @brief class Database
 *
 * @class Database
 *  Class Database
 *
 * This class is:
 *      - for managing all database access
 *      - for managing database updates
 *      - NOT for the update steps: they are in the file "db_update_steps.php".
 *
 * @note Supported database types are listed in config_defaults.php.
 *
 * @warning     You have to install the PDO module of the database which you want to use (e.g. "PDO_MYSQL")!
 *              Normally this is done by installing the php-database-package (e.g. "php5-mysql") in your linux system.
 *              PDO needs PHP version 5.1 or higher. For more details, see
 *              @link http://www.php.net/manual/de/book.pdo.php http://www.php.net/manual/de/book.pdo.php @endlink
 *              (for example)
 *
 * @todo        Activate the MySQL strict mode. This requires that Part-DB uses always correct data types to work correctly.
 *
 * @todo        If the user rolls back his database to an older version after an error occured while updating it,
 *              the update process will start at the position where the error occurs. This will produce
 *              a lot of new errors, the update will fail again and again. So we have to recognize if the user
 *              has rolled back his database, then we can start the next update from the beginning, even if the
 *              last update was not successfully.
 *
 * @author kami89
 */
class Database
{
    /********************************************************************************
     *
     *   Attributes
     *
     *********************************************************************************/

    /** @var PDO PHP Data Object */
    private $pdo = null;

    /** @var bool  */
    private $transaction_active = false;
    /** @var integer    See Database::begin_transaction(), Database::commit() and Database::rollback() */
    private $active_transaction_id = 0;

    /** @var string The SQL Mode */
    private $sql_mode = '';//'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE'; TODO!

    /** @var int We use this var to cache the current version of database, so we dont need to check this so often.
     *  This information is static so we can use it between multiple Database objects
     */
    private static $current_version = -1;

    /* @var array We use this, to cache the tables we already know that they exist. */
    private static $table_cache = array();

    /********************************************************************************
     *
     *   Constructor / Destructor
     *
     *********************************************************************************/

    /**
     * Constructor

     * @note    You don't have to supply database connection data because
     *          the data from the config.php will be used.
     *
     * @throws Exception if the database couldn't connect successfully
     */
    public function __construct()
    {
        global $config;

        // connect with database
        try {
            switch ($config['db']['type']) {
                case 'mysql': // MySQL
                    //Split hostname in adress and port
                    $host = parse_url($config['db']['host'], PHP_URL_HOST);
                    $port = parse_url($config['db']['host'], PHP_URL_PORT);

                    if ($host == null || $port == null) {
                        $host = $config['db']['host'];
                        $port = '3306';
                    }

                    //If a unix socket is given in $dbo
                    if (isPathabsoluteAndUnix($config['db']['host'], false)) {
                        $this->pdo = new PDO(
                            'mysql:unix_socket=' . $config['db']['host'] . ';dbname=' . $config['db']['name'] . ';charset=utf8',
                            $config['db']['user'],
                            $config['db']['password'],
                            array(PDO::MYSQL_ATTR_INIT_COMMAND    => 'SET NAMES utf8',
                                PDO::ATTR_PERSISTENT            => false)
                        );
                    } else if ($config['db']['space_fix'] == false) {
                        $this->pdo = new PDO(
                            'mysql:host='.$host.';port='.$port.';dbname='.$config['db']['name'].';charset=utf8',
                            $config['db']['user'],
                            $config['db']['password'],
                            array(PDO::MYSQL_ATTR_INIT_COMMAND    => 'SET NAMES utf8',
                                PDO::ATTR_PERSISTENT            => false)
                        );
                    } else { //Include space between mysql and host in dsn string.
                        $this->pdo = new PDO(
                            'mysql: host='.$host.';port='.$port.';dbname='.$config['db']['name'].';charset=utf8',
                            $config['db']['user'],
                            $config['db']['password'],
                            array(PDO::MYSQL_ATTR_INIT_COMMAND    => 'SET NAMES utf8',
                                PDO::ATTR_PERSISTENT            => false)
                        );
                    }

                    break;

                    //case 'sqlite': // SQLite 3
                    //case 'sqlite2': //SQLite 2
                    //$filename = realpath($config['db']['name']) ? realpath($config['db']['name']) : $config['db']['name'];
                    //$this->pdo = new PDO($config['db']['type'].':'.$filename, NULL, NULL);
                    break;

                default:
                    throw new DatabaseException(_('Unbekannter Datenbanktyp: "').$config['db']['type'].'"');
                    break;
            }

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("SET SQL_MODE='" . $this->sql_mode . "'");
        } catch (PDOException $e) {
            debug(
                'error',
                'Konnte nicht mit Datenbank verbinden: '.$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );

            throw new DatabaseException(_("Es konnte nicht mit der Datenbank verbunden werden! \n".
                    'Überprüfen Sie, ob die Zugangsdaten korrekt sind.') . "\n\n".
                _('Details: ') . $e->getMessage());
        }

        if (PDBDebugBar::isActivated()) {
            $this->pdo = new TraceablePDO($this->pdo);
            PDBDebugBar::getInstance()->registerPDO($this->pdo);
        }
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     *  Get current database version (from database table "internal")
     *
     * @param $force_check bool Set this to true, if no cached value should be used.
     * The value will be freshly retrieved from the DB.
     *
     * @return integer      current database version
     *
     * @throws Exception if there was an error
     */
    public function getCurrentVersion($force_check = false) : int
    {
        //When we already cached the version number, we dont need it here anymore.
        if (!$force_check && static::$current_version > 0) {
            return static::$current_version;
        }

        if (! $this->doesTableExist('internal', true)) {
            return 0;
        } // Empty table --> return version 0 to create tables with the update mechanism

        $query_data = $this->query('SELECT keyValue FROM internal WHERE keyName LIKE ?', array('dbVersion'));

        if (count($query_data) !== 1) { // Empty table --> return version 0 to create tables with the update mechanism
            return 0;
            //throw new Exception(_('Eintrag "dbVersion" existiert nicht in der Tabelle "internal"!'));
        }

        $tmp = (int)$query_data[0]['keyValue'];
        static::$current_version = $tmp;

        return $tmp;
    }


    /**
     *  Get latest database version (from updates/db_update_steps.php)
     *
     * @return integer      latest database version
     *
     * @throws Exception if there was an error
     */
    public function getLatestVersion() : int
    {
        if (! \defined('LATEST_DB_VERSION')) {
            throw new DatabaseException(_('Konstante "LATEST_DB_VERSION" ist nicht definiert!'));
        }

        return LATEST_DB_VERSION; // this constant is defined in "db_update_steps.php"
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     *  Set current database version
     *
     * @param integer $new_current_version          the new current version
     *
     * @throws Exception if there was an error
     */
    /*public function set_current_version($new_current_version)
    {
        $this->execute('UPDATE internal set keyValue=? WHERE keyName=?',
                        array($new_current_version, 'dbVersion'));
    }*/

    /********************************************************************************
     *
     *   Database Update Methods
     *
     *********************************************************************************/

    /**
     *  Check if a database update is required
     *
     * @return boolean      @li true if update is required
     *                      @li false if we have the latest version
     *
     * @throws Exception if there was an error
     */
    public function isUpdateRequired() : bool
    {
        $current = $this->getCurrentVersion(true);
        $latest = $this->getLatestVersion();

        return ($current < $latest);
    }

    /**
     *  Converts an MySQL query into a query of the used database type
     *
     * This function will convert a MySQL query in a query for the used database type.
     *
     * @note    If MySQL is the used database type, this function will directly return the parameter $query
     *
     * @param string $query     The MySQL query
     *
     * @return string           The query for the used database type
     *
     * @throws Exception if there was an error
     */
    private function convertMysqlQuery(string $query) : string
    {
        global $config;

        //TODO: Implement a correctly working version:
        return $query;
    }

    /**
     *  Update the database to the latest version
     *
     * @warning     Database Transactions won't work for the update process,
     *              because transactions don't work with "DROP TABLE" and "CREATE TABLE"!!
     *              So we use the config "$config['update']['next_step']" to memorize an error.
     *
     * @param boolean $continue_last_attempt        @li if true and the last update attempt was not successfully,
     *                                                 the update will continue at the last step which has produced an error
     *                                              @li if false, the update will start with the first update step,
     *                                                  even if there was an error. this is used if the user has loaded a
     *                                                  new database (backup last imported) after an update error.
     *
     * @return array       the update log as an array: 'text' as string and 'error' as boolean (if this message is associated with an error).
     *
     * @throws Exception if there was an error
     *
     * @todo    the parameter "$continue_last_attempt" is not very pretty, it would be better if this function
     *          detects automatically if the user has loaded a new database.
     */
    public function update(bool $continue_last_attempt = true) : array
    {
        global $config;
        $error = false;
        $log = array();
        //Lambda function to simply add a message to log
        $add_log = function ($msg, $err = false) use (&$log) {
            $log[] = array('text' => $msg, 'error' => $err);
        };

        $current = $this->getCurrentVersion(true);
        $old_current = $this->getCurrentVersion(); //This one is used for the Database Update log entry
        $latest = $this->getLatestVersion();

        if ($this->transaction_active) {
            throw new DatabaseException(_('Ein Datenbankupdate kann nicht mitten in einer offenen Transaktion durchgeführt werden!'));
        }

        // Later in the updateprocess, we will store the position of the update step in the config.php if an error occurs,
        // so the next attempt can start at the same position. But if the user has no write access to the config.php,
        // this will not work. So we will try to write the configs to the config.php now. If this is not successfully,
        // the function "save_config()" will throw an exception and the update proccess is aborted.
        saveConfig();

        debug('hint', 'Update von Datenbankversion "'.$current.'" auf Version "'.$latest.'" wird gestartet...');
        $add_log('Ihre Datenbank wird von der Version '. $current .' auf die Version '. $latest .' aktualisiert:');

        if (! \in_array($config['db']['type'], array('sqlite', 'sqlite2'))) { // @todo: Can we also lock/unlock a SQLite Database?
            // Lock Database
            try {
                $add_log('Datenbank wird gesperrt...');
                $query_data = $this->query("SELECT GET_LOCK('UpdatePartDB', 3)");
            } catch (Exception $exception) {
                $add_log(_('FEHLER: Wird zur Zeit schon ein Update durchgeführt?'), true);
                $add_log(_('Fehlermeldung: ').$exception->getMessage(), true);
                $error = true;
            }
        }

        // change SQL mode
        try {
            $add_log(_('SQL_MODE wird gesetzt...'));
            $this->execute("SET SQL_MODE=''");
        } catch (Exception $exception) {
            $add_log('FEHLER!', true);
            $add_log('Fehlermeldung: '.$exception->getMessage(), true);
            $error = true;
        }

        while (($current < $latest) && (! $error)) {
            $add_log('');
            $add_log('Update v'.$current.' --> v'.($current+1).'...');

            $steps = get_db_update_steps($current);

            if (count($steps) == 0) {
                $add_log(sprintf(_('FEHLER: Keine Updateschritte für Version %s gefunden!'), $current), true);
                $error = true;
                break;
            }

            if ($config['db']['update_error']['version'] == $current) {
                $start_position = $config['db']['update_error']['next_step'];
            } // there was an error in the last update process
            else {
                $start_position = 0;
            } // no error, start with the first update step

            for ($steps_pos = $start_position; ($steps_pos < count($steps)) && (! $error); $steps_pos++) {
                $query = $this->convertMysqlQuery($steps[$steps_pos]);

                if ($query === null) { // for "dummys" (steps which are removed afterwards)
                    continue;
                }

                try {
                    $this->pdo->beginTransaction();
                    $this->pdo->exec($query);
                    if($this->pdo->inTransaction()) {
                        $this->pdo->commit();
                    }
                    $add_log(sprintf(_('Schritt: %s ...OK'), $query));
                } catch (PDOException $e) {
                    try {
                        $this->pdo->rollback();
                    } catch (PDOException $e2) {
                    } // rollback last query, ignore exceptions
                    //Ignore "Column already exists:" errors
                    if (!strcontains($e->getMessage(), 'Column already exists:')) {
                        debug('error', '$query="' . $query . '"', __FILE__, __LINE__, __METHOD__);
                        debug('error', _('Fehlermeldung: "') . $e->getMessage() . '"', __FILE__, __LINE__, __METHOD__);
                        $add_log(sprintf(_('Schritt: %s ...FEHLER!'), $query), true);
                        $add_log(_('Fehlermeldung: ') . $e->getMessage(), true);
                        $error = true;
                    }
                    break;
                }
            }

            // set the new database version
            if (! $error) {
                try {
                    if ($current != 0) { // The DB Version was set in the first update step, so we mustn't set the version here!!
                        $pdo_statement = $this->pdo->prepare("UPDATE internal SET keyValue=? WHERE keyName='dbVersion'");
                        $pdo_statement->bindValue(1, $current + 1);
                        $pdo_statement->execute();
                    }
                } catch (Exception $exception) {
                    $add_log(_('FEHLER: Die neue Version konnte nicht gesetzt werden!'), true);
                    $add_log(_('Fehlermeldung: ').$exception->getMessage(), true);
                    $error = true;
                    break;
                }
            }

            try {
                // memorize the current steps position
                if ($error) {
                    $config['db']['update_error']['next_step'] = $steps_pos;
                    $config['db']['update_error']['version'] = $current;
                } else {
                    $config['db']['update_error']['next_step'] = 0;
                    $config['db']['update_error']['version'] = -1;
                }

                saveConfig();
            } catch (Exception $exception) {
                $add_log(_('FEHLER: Die aktuelle Update-Position konnte nicht in der config.php gespeichert werden!'), true);
                $add_log(_('Fehlermeldung: ').$exception->getMessage(), true);
                $error = true;
                break;
            }

            if (! $error) {
                try {
                    $current = $this->getCurrentVersion(true);
                } catch (Exception $exception) {
                    $add_log(_('FEHLER: Die aktuelle Version konnte nicht gelesen werden!'), true);
                    $add_log(_('Fehlermeldung: ').$exception->getMessage(), true);
                    $error = true;
                    break;
                }
            }

            if (($current <= 1) && (! $error)) {
                $add_log(_('FEHLER: Die neue Version konnte nicht gesetzt werden!'), true);
                $error = true;
                break;
            }
        }

        $add_log('');

        // change SQL mode
        try {
            $add_log(_('SQL_MODE wird gesetzt...'));
            $this->execute("SET SQL_MODE='".$this->sql_mode."'");
        } catch (Exception $exception) {
            $add_log(_('FEHLER!'), true);
            $add_log(_('Fehlermeldung: ').$exception->getMessage(), true);
            $error = true;
        }

        // Release Database
        if (! \in_array($config['db']['type'], array('sqlite', 'sqlite2'))) {
            try {
                $add_log(_('Datenbank wird freigegeben...'));
                $query_data = $this->query("SELECT RELEASE_LOCK('UpdatePartDB')");
            } catch (Exception $exception) {
                $add_log(_('FEHLER: Die Datenbank konnte nicht entsperrt werden!'), true);
                $add_log(_('Fehlermeldung: ').$exception->getMessage(), true);
                $error = true;
            }
        }

        if ($error) {
            debug('error', _('ABBRUCH: Das Update konnte nicht durchgeführt werden!'));
            debug('error', _('Zweitletzte Zeile: ').$log[count($log)-2]['text']);
            debug('error', _('Letzte Zeile: ').$log[count($log)-1]['text']);
            $add_log(_('ABBRUCH: Das Update konnte nicht durchgeführt werden!'), true);
        } else {
            debug('success', _('Das Update wurde erfolgreich durchgeführt.'));
            $add_log(_('Das Update wurde erfolgreich durchgeführt.'));
        }

        //Try to create an DatabaseUpdateEntry Log entry
        try {
            $l = new Log($this);
            DatabaseUpdatedEntry::add($this, User::getLoggedInUser(), $l, $old_current, $latest, !$error);
        } catch (Exception $ex) {
            //When an error happen, the DB log table was not created yet. There is nothing we can do here, so do nothing.
        }

        return $log;
    }

    /********************************************************************************
     *
     *   Transactions
     *
     *   Note:
     *       You do not have to use transactions if you only want to insert/update/delete
     *       only ONE record. But if you want to make multiple changes (e.g. delete a
     *       whole Part with all its files, orderdetails, pricedetails,...) you REALLY
     *       SHOULD USE transactions! Otherwise, you risk the inconsistency of the database!
     *
     *********************************************************************************/

    /*
     * Template for the implementation of transactions (Example)
     */
    /*public function foo()
    {
        try
        {
            $transaction_id = $this->database->begin_transaction(); // start transaction

            // do something with the object: delete, update, ...
            // ...simply throw an Exception if there is a problem...

            $this->database->commit($transaction_id); // commit transaction
        }
        catch (Exception $e)
        {
            $this->database->rollback(); // rollback transaction

            // restore the settings from BEFORE the transaction
            $this->reset_attributes();

            throw new Exception("Die Teile konnten nicht abgefasst werden!\nGrund: ".$e->getMessage());
        }
    }*/

    /**
     *  Begin a new Transaction
     *
     * @return  integer     The ID of the new transaction (like a "Ticket Number" for Database::commit())
     *
     * @throws Exception if there was an error
     */
    public function beginTransaction() : int
    {
        if (! $this->transaction_active) {
            // start a new transaction
            try {
                $this->active_transaction_id++;
                $this->pdo->beginTransaction();
                $this->transaction_active = true;
                return $this->active_transaction_id;
            } catch (PDOException $e) {
                throw new DatabaseException(_('PDO::begin_transaction() lieferte einen Fehler: ').$e->getMessage());
            }
        }

        return 0; // this is not a new transaction
    }

    /**
     *  Commit an active transaction
     *
     * @note    The commit will not really be executed immediately if there are other active transactions.
     *          Only after the commit of the last active transaction, the commit will really be executed.
     *
     * @param integer $transaction_id   The ID which you got by Database::begin_transaction()
     *
     * @throws Exception if there was an error
     */
    public function commit($transaction_id)
    {
        if (! $this->transaction_active) {
            throw new DatabaseException(_('Es wurde noch keine Transaktion gestartet!'));
        }

        if ($transaction_id == 0) {
            return;
        }

        if ($transaction_id != $this->active_transaction_id) {
            throw new DatabaseException(_('Die übermittelte Transaktions-ID ist nicht korrekt!'));
        }

        // all OK, we commit the transaction
        try {
            $this->transaction_active = false;
            if ($this->pdo->inTransaction()) {
                $this->pdo->commit();
            }
        } catch (PDOException $e) {
            try {
                // try to roll back
                $this->pdo->rollback();
            } catch (PDOException $e) {
            }

            throw new DatabaseException(_('PDO::commit() lieferte einen Fehler: ').$e->getMessage());
        }
    }

    /**
     *  Rollback ALL (!) active transactions
     *
     * @note    This method should not throw an exception because this sucks :-D
     *          SO we will return only true or false...
     *
     * @return  boolean     true if success, false if there was an error
     */
    public function rollback() : bool
    {
        if (! $this->transaction_active) {
            return false;
        }

        try {
            $this->transaction_active = false;
            $this->pdo->rollback();
        } catch (PDOException $e) {
            return false;
        }

        return true;
    }

    /********************************************************************************
     *
     *   Queries
     *
     *********************************************************************************/

    /**
     *  Execute an SQL statement
     *
     * @note        There is no returned data (but count), so don't use it if you expect returned data!
     *              Use this method only for UPDATE, REPLACE, DELETE and INSERT statements!
     *              If you expect data, use Database::query() instead.
     * @note        If you execute an INSERT statement, you will get the ID of the new record!
     *
     * @param string $query the query string, but with the symbol "?" (without ")
     *                                  as place holder for the values
     * @param array $values @li one-dimensional array of values (mixed types) [0..*]
     * @li for each placeholder in $query, there must be an array element!
     * @li The order must be the same as the placeholders in $query!
     *
     * @return  integer     @li count of elements which were modified
     * @li or if the query was an INSERT command,
     *                          the ID of the new record will be returned
     *
     * @throws DatabaseException If there was an error executing the query.
     * @throws Exception If there was an exception with the underlying PDO Statment.
     */
    public function execute(string $query, array $values = array()) : int
    {
        if (! \is_array($values)) {
            throw new \InvalidArgumentException(_('$values ist kein Array!'));
        }

        if (strncasecmp($query, 'INSERT', 6) === 0) {
            $is_insert_statement = true;
        } else {
            $is_insert_statement = false;
        }

        try {
            $pdo_statement = $this->pdo->prepare($query);

            if (! $pdo_statement) {
                debug('error', 'PDO Prepare Fehler!', __FILE__, __LINE__, __METHOD__);
                throw new DatabaseException(_('PDO prepare Fehler!'));
            }

            // bind all values
            $index = 1;
            foreach ($values as $value) {
                if (! $pdo_statement->bindValue($index, $value)) {
                    debug(
                        'error',
                        'PDO bindValue Fehler: $index="'.$index.'", $value="'.$value.'"',
                        __FILE__,
                        __LINE__,
                        __METHOD__
                    );
                    throw new DatabaseException(_('PDO: Wert konnte nicht gebunden werden!'));
                }
                $index++;
            }

            if (! $pdo_statement->execute()) {
                debug('error', 'PDO Execute Fehler!', __FILE__, __LINE__, __METHOD__);
                throw new DatabaseException(_('PDO execute lieferte einen Fehler!'));
            }

            if ($is_insert_statement == true) {
                $result = $this->pdo->lastInsertId('id');
            } else {
                $result = $pdo_statement->rowCount();
            }
        } catch (PDOException $e) {
            debug('error', '$query="'.$query.'"', __FILE__, __LINE__, __METHOD__);
            debug('error', '$values="'.print_r($values, true).'"', __FILE__, __LINE__, __METHOD__);
            debug('error', 'Fehlermeldung: "'.$e->getMessage().'"', __FILE__, __LINE__, __METHOD__);
            throw new DatabaseException(_("Datenbankfehler: \n").$e->getMessage()._("\n\n SQL-Query:\n ").$query);
        }

        return (int) $result;
    }

    /**
     *  Make a query and fetch all data
     *
     * @note    Use this method only if you expect returned data!
     *          If you don't expect returned data (but count of changes), use Databas::exec() instead.
     * @note    So use this method only for SELECT or SHOW statements!!
     *
     * @param string $query the query string, but with the symbol "?" (without ")
     *                                      as place holder for the values
     * @param array $values @li one-dimensional array of values (mixed types) [0..*]
     * @li for each placeholder in $query, there must be an array element!
     * @li The order must be the same as the placeholders in $query!
     * @param integer $fetch_style @li The style of the returned array.
     * @li Examples: PDO::FETCH_ASSOC, PDO::FETCH_BOTH
     * @li see @link http://php.net/manual/de/pdostatement.fetch.php
     *                                          http://php.net/manual/de/pdostatement.fetch.php @endlink
     *
     * @return array            @li 2D data array [0..*]
     * @li Example:
     *                              array([0] => array(['id'] => 1, ['name'] => 'foo'), [1] => array(...))
     *
     * @throws DatabaseException If there was an error executing the query
     * @throws Exception If there was an error with the underlying PDO statement
     */
    public function  query(string $query, array $values = array(), int $fetch_style = PDO::FETCH_ASSOC) : array
    {
        try {
            $pdo_statement = $this->pdo->prepare($query);

            if (! $pdo_statement) {
                debug('error', _('PDO Prepare Fehler!'), __FILE__, __LINE__, __METHOD__);
                throw new DatabaseException(_('PDO prepare Fehler!'));
            }

            // bind values
            $index = 1;
            foreach ($values as $value) {
                if (! $pdo_statement->bindValue($index, $value)) {
                    debug(
                        'error',
                        'PDO bindValue Fehler: $index="'.$index.'", $value="'.$value.'"',
                        __FILE__,
                        __LINE__,
                        __METHOD__
                    );
                    throw new DatabaseException(_('PDO: Wert konnte nicht gebunden werden!'));
                }
                $index++;
            }

            if (! $pdo_statement->execute()) {
                debug('error', 'PDO Execute Fehler!', __FILE__, __LINE__, __METHOD__);
                throw new DatabaseException(_('PDO execute lieferte einen Fehler!'));
            }

            $data = $pdo_statement->fetchAll($fetch_style);
        } catch (PDOException $e) {
            debug('error', '$query="'.$query.'"', __FILE__, __LINE__, __METHOD__);
            debug('error', '$values="'.print_r($values, true).'"', __FILE__, __LINE__, __METHOD__);
            debug('error', 'Fehlermeldung: "'.$e->getMessage().'"', __FILE__, __LINE__, __METHOD__);
            throw new DatabaseException("Datenbankfehler:\n".$e->getMessage()."\n\nSQL-Query:\n".$query."\n\nParameter:\n".print_r($values, true));
        }

        if ($data == null) {
            $data = array();
        } // an empty array is better than NULL...

        if (! \is_array($data)) {
            throw new DatabaseException(_('PDO Ergebnis ist kein Array!'));
        }

        return $data;
    }



    /********************************************************************************
     *
     *   Basic Database Methods
     *
     *********************************************************************************/

    /**
     *  Check if a database table exists
     *
     * @param string $tablename         the name of the table
     * @param boolean $forcecheck       Force a real check against the database, without using the whitelist.
     *
     * @return boolean      @li true if there is at least one table with this name
     *                      @li false if there is no table with this name
     *
     * @throws DatabaseException If there was an error.
     */
    public function doesTableExist(string $tablename, bool $forcecheck = false) : bool
    {
        //A whitelist of tables, we know that exists, so we dont need to check with a DB Request
        //Dont include "internal" here, because otherwise it leads to problems, when starting with a fresh database.
        $whitelist = array('parts', 'categories', 'footprints', 'storelocations', 'suppliers', 'pricedetails',
            'orderdetails', 'manufacturers', 'attachements', 'attachement_types', 'devices', 'device_parts', 'users', '`groups`', 'log');

        global $config;

        //Only allow check if database, installation is complete... Else this lead to problems, when starting with a fresh database.
        if (!$forcecheck && $config['installation_complete']['database'] && \in_array($tablename, static::$table_cache, true)) {
            return true;
        }

        //does not work with SQLite!
        $query_data = $this->query('SHOW TABLES LIKE ?', array($tablename));

        if (count($query_data) >= 1) {
            // We now know that this table exists.
            static::$table_cache[] = $tablename;
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Get the count of records in a table
     *
     * @param string $tablename the name of the table
     *
     * @return int      count of records
     *
     *
     * @throws DatabaseException If there was an error getting the data from DB.
     */
    public function getCountOfRecords(string $tablename) : int
    {
        $query_data = $this->query('SELECT count(*) as count FROM `' . $tablename . '`');

        return (int) $query_data[0]['count'];
    }

    /**
     *  Get all data of a record (by tablename + ID)
     *
     * @param string    $tablename      the name of the table
     * @param integer   $id             ID of the element
     * @param integer   $fetch_style    @li The style of the returned array.
     *                                  @li see Database::query()
     *
     * @return array    @li data array (with all table columns) [0..*]
     *                  @li Example: @code array(['id'] => 4, ['name'] => 'foo', ...) @endcode
     *
     * @throws Exception if there is no element with that ID
     * @throws Exception if there was an error
     */
    public function getRecordData(string $tablename, int $id, int $fetch_style = PDO::FETCH_ASSOC) : array
    {
        $query_data = $this->query('SELECT * FROM `'. $tablename .
            '` WHERE id=?', array($id), $fetch_style);

        if (count($query_data) == 0) {
            throw new ElementNotExistingException(sprintf(_('Es existiert kein Datensatz mit der ID "%d" in der Tabelle "%s"!'), $id, $tablename));
        }

        return $query_data[0];
    }

    /**
     *  Delete a database record
     *
     * @param string    $tablename      the name of the table
     * @param integer   $id             ID of the element which will be deleted
     *
     * @throws Exception if there was an error
     */
    public function deleteRecord(string $tablename, int $id)
    {
        $this->execute('DELETE FROM `'.$tablename.'` WHERE id=? LIMIT 1', array($id));
    }

    /**
     * Inserts the given data as a new record into the given table.
     * @param string $tablename The table to which the dataset should be added.
     * @param array $new_values An associative array, containing the dataset. The keys are the coloum names.
     * @return int The ID of the newly created row.
     * @throws DatabaseException If the entry could not be created
     * @throws TableNotExistingException If the table, in which should be inserted, does not exists.
     */
    public function insertRecord(string $tablename, array $new_values) : int
    {
        if (empty($new_values)) {
            throw new \InvalidArgumentException(_('$new_values darf nicht leer sein!'));
        }

        if (! $this->doesTableExist($tablename)) {
            throw new TableNotExistingException(sprintf(_('Die Tabelle "%s" existiert nicht!'), $tablename));
        }

        // create the query string
        $query = 'INSERT INTO `' . $tablename . '` (' . implode(', ', array_keys($new_values)) . ') '.
            'VALUES (?' . str_repeat(', ?', count($new_values) -1) . ')';

        // now we can insert the new data into the database
        $id = $this->execute($query, $new_values);

        if ($id == null) {
            throw new DatabaseException(_('Der Datenbankeintrag konnte nicht angelegt werden.'));
        }

        return $id;
    }

    /**
     *  Set (change) values of a database record
     *
     * @param string    $tablename      the name of the table where the element is located
     * @param integer   $id             id of the element which should be edited
     * @param array     $values         @li one-dimensional array of values [1..*]
     *                                  @li Example: @code
     *                                      array(['name'] => 'foo', ['is_master'] => true, ...) @endcode
     *
     * @throws Exception if there was an error
     */
    public function setDataFields(string $tablename, int $id, array $values)
    {
        if ((! \is_array($values)) || (count($values) < 1)) {
            debug('error', '$values="'.print_r($values, true).'"', __FILE__, __LINE__, __METHOD__);
            throw new \InvalidArgumentException(_('$values ist kein gültiges Array!'));
        }

        $query =    'UPDATE `'.$tablename.'` SET '.
            implode('=?, ', array_keys($values)).'=? '.
            'WHERE id=? LIMIT 1';

        $values[] = $id; // for the last placeholder in "WHERE id=?"

        $this->execute($query, $values);
    }

    /**
     * Get the size of the whole Database in MB.
     * @return float The Database size in MB.
     * @throws Exception
     */
    public function getDatabaseSize() : float
    {
        global $config;

        if ($config['db']['type'] != 'mysql') {
            throw new \Exception(_('Datenbankgröße kann nur für MySQL Datenbanken ermittelt werden!'));
        }

        $query = 'SELECT '
            . ' ROUND(SUM(data_length + index_length) / 1024 / 1024, 3) AS `size`'
            . ' FROM information_schema.TABLES'
            . ' WHERE table_schema = ?'
            . ' GROUP BY table_schema; ';

        $values[] = $config['db']['name'];

        $data = $this->query($query, $values);

        return (float) $data[0]['size'];
    }
}
