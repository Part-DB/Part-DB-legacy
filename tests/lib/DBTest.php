<?php

/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 02.01.2017
 * Time: 12:11
 */
abstract class DBTest extends PHPUnit_Extensions_Database_TestCase
{
    protected $pdo = null;
    protected $conn = null;

    public function getConnection()
    {
        if($this->pdo === null) {
            $this->pdo = new PDO('sqlite::memory:');
            //$this->pdo->exec('create table suppliers()');
            //$this->pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
        }
        return $this->createDefaultDBConnection($this->pdo, ':memory:');
        //$this->conn = $this->createDefaultDBConnection($this->pdo, $GLOBALS['DB_DBNAME']);
        return $this->conn;
    }

    public function getDataSet()
    {
        //return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__."/../part-db.yml");
        //return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
        //    dirname(__FILE__)."/../_files/part-db.yml"
        //);
        return $this->createMySQLXMLDataSet(__DIR__."/../_files/test-db.xml");
    }

    public function loadDataSet($dataSet) {
        // set the new dataset
        $this->getDatabaseTester()->setDataSet($dataSet);
        // call setUp which adds the rows
        $this->getDatabaseTester()->onSetUp();
    }

    public function setUp()
    {
        $conn = $this->getConnection();
        $pdo = $conn->getConnection();

        // set up tables
        $ds = $this->getDataSet();
        foreach ($ds->getTableNames() as $table) {
            // drop table
            $pdo->exec("DROP TABLE IF EXISTS `$table`;");
            // recreate table
            $meta = $ds->getTableMetaData($table);
            $create = "CREATE TABLE IF NOT EXISTS `$table` ";
            $cols = array();
            foreach ($meta->getColumns() as $col) {
                $cols[] = "`$col` VARCHAR(200)";
            }
            $create .= '('.implode(',', $cols).');';
            $pdo->exec($create);
        }

        parent::setUp();
    }


    public function getDatabase()
    {
        $pdo = $this->getConnection()->getConnection();
        return $database           = new Database($pdo);
    }

    public function tearDown() {
        $allTables =
            $this->getDataSet()->getTableNames();
        foreach ($allTables as $table) {
            // drop table
            $conn = $this->getConnection();
            $pdo = $conn->getConnection();
            $pdo->exec("DROP TABLE IF EXISTS `$table`;");
        }

        parent::tearDown();
    }


}
