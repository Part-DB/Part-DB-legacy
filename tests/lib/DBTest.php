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
    //protected $conn = null;

    public function getConnection()
    {
        if($this->pdo === null) {
            if($GLOBALS['DB_TYPE'] == "sqlite") {
                $this->pdo = new PDO('sqlite::memory:');
            }
            else {
                $this->pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            }
        }
        if($GLOBALS['DB_TYPE'] == "sqlite") {
            return $this->createDefaultDBConnection($this->pdo, ':memory:');
        }
        else {
            return $this->createDefaultDBConnection($this->pdo, $GLOBALS['DB_DBNAME']);
        }
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
        /*$ds = $this->getDataSet();
        foreach ($ds->getTableNames() as $table) {
            // drop table
            //$pdo->exec("DROP TABLE IF EXISTS `$table`;");
            // recreate table
            $meta = $ds->getTableMetaData($table);
            $create = "CREATE TABLE IF NOT EXISTS `$table` ";
            $cols = array();
            foreach ($meta->getColumns() as $col) {
                $cols[] = "`$col` VARCHAR(200)";
            }
            $create .= '('.implode(',', $cols).');';
            $pdo->exec($create);
        }*/

        //Setup Tables

        $pdo->exec("CREATE TABLE IF NOT EXISTS `attachements` (
          `id` int(11) NOT NULL,
          `name` tinytext NOT NULL,
          `class_name` varchar(255) NOT NULL,
          `element_id` int(11) NOT NULL,
          `type_id` int(11) NOT NULL,
          `filename` mediumtext NOT NULL,
          `show_in_table` tinyint(1) NOT NULL DEFAULT '0'
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `attachement_types` (
          `id` int(11) NOT NULL,
          `name` tinytext NOT NULL,
          `parent_id` int(11) DEFAULT NULL
        )");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `categories` (".
                "`id` int(11) NOT NULL,".
                "`name` tinytext NOT NULL,".
                "`parent_id` int(11) DEFAULT NULL,".
                "`disable_footprints` tinyint(1) NOT NULL DEFAULT '0',".
                "`disable_manufacturers` tinyint(1) NOT NULL DEFAULT '0',".
                "`disable_autodatasheets` tinyint(1) NOT NULL DEFAULT '0')");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `devices` (
              `id` int(11) NOT NULL,
              `name` tinytext NOT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `order_quantity` int(11) NOT NULL DEFAULT '0',
              `order_only_missing_parts` tinyint(1) NOT NULL DEFAULT '0',
              `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP)");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `device_parts` (
              `id` int(11) NOT NULL,
              `id_part` int(11) NOT NULL DEFAULT '0',
              `id_device` int(11) NOT NULL DEFAULT '0',
              `quantity` int(11) NOT NULL DEFAULT '0',
              `mountnames` mediumtext NOT NULL)");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `footprints` (
              `id` int(11) NOT NULL,
              `name` tinytext NOT NULL,
              `filename` mediumtext NOT NULL,
              `filename_3d` mediumtext NOT NULL,
              `parent_id` int(11) DEFAULT NULL
            )");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `internal` (
              `keyName` char(30) NOT NULL,
              `keyValue` varchar(255) DEFAULT NULL
            )");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `manufacturers` (
              `id` int(11) NOT NULL,
              `name` tinytext NOT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `address` mediumtext NOT NULL,
              `phone_number` tinytext  NOT NULL,
              `fax_number` tinytext NOT NULL,
              `email_address` tinytext NOT NULL,
              `website` tinytext NOT NULL,
              `auto_product_url` tinytext NOT NULL,
              `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
          )");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `orderdetails` (
              `id` int(11) NOT NULL,
              `part_id` int(11) NOT NULL,
              `id_supplier` int(11) NOT NULL DEFAULT '0',
              `supplierpartnr` tinytext NOT NULL,
              `obsolete` tinyint(1) DEFAULT '0',
              `supplier_product_url` tinytext NOT NULL,
              `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `parts` (
              `id` int(11) NOT NULL,
              `id_category` int(11) NOT NULL DEFAULT '0',
              `name` mediumtext  NOT NULL,
              `description` mediumtext NOT NULL,
              `instock` int(11) NOT NULL DEFAULT '0',
              `mininstock` int(11) NOT NULL DEFAULT '0',
              `comment` mediumtext NOT NULL,
              `visible` tinyint(1) NOT NULL,
              `id_footprint` int(11) DEFAULT NULL,
              `id_storelocation` int(11) DEFAULT NULL,
              `order_orderdetails_id` int(11) DEFAULT NULL,
              `order_quantity` int(11) NOT NULL DEFAULT '1',
              `manual_order` tinyint(1) NOT NULL DEFAULT '0',
              `id_manufacturer` int(11) DEFAULT NULL,
              `id_master_picture_attachement` int(11) DEFAULT NULL,
              `manufacturer_product_url` tinytext  NOT NULL,
              `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
            )");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `pricedetails` (
              `id` int(11) NOT NULL,
              `orderdetails_id` int(11) NOT NULL,
              `price` decimal(9,5) DEFAULT NULL,
              `price_related_quantity` int(11) NOT NULL DEFAULT '1',
              `min_discount_quantity` int(11) NOT NULL DEFAULT '1',
              `manual_input` tinyint(1) NOT NULL DEFAULT '1',
              `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `storelocations` (
              `id` int(11) NOT NULL,
              `name` tinytext NOT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `is_full` tinyint(1) NOT NULL DEFAULT '0',
              `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `suppliers` (
              `id` int(11) NOT NULL,
              `name` tinytext NOT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `address` mediumtext  NOT NULL,
              `phone_number` tinytext NOT NULL,
              `fax_number` tinytext NOT NULL,
              `email_address` tinytext NOT NULL,
              `website` tinytext NOT NULL,
              `auto_product_url` tinytext NOT NULL,
              `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");

        parent::setUp();
    }


    public function getDatabase()
    {
        $pdo = $this->getConnection()->getConnection();
        return $database           = new Database($pdo);
    }

    public function tearDown() {
        /* $allTables =
            $this->getDataSet()->getTableNames();
        foreach ($allTables as $table) {
            // drop table
            $conn = $this->getConnection();
            $pdo = $conn->getConnection();
            $pdo->exec("DROP TABLE IF EXISTS `$table`;");
        }
        */
        parent::tearDown();
    }


}
