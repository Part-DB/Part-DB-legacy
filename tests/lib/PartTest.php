<?php

/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 02.01.2017
 * Time: 16:06
 */
class PartTest extends DBTest
{

    protected $database;
    protected $log;
    protected $current_user = null;
    /** @var  Part */
    protected $part;

    public function setUp(){

        parent::setUp();

        $this->database           = $this->getDatabase();
        $this->log                = new Log($this->database);
        $this->current_user       = new User($this->database, $this->current_user, $this->log, 1); // admin
        $this->part               = new Part($this->database, $this->current_user, $this->log, 1);
    }

    public function test_Instock()
    {
        $this->assertEquals(10, $this->part->get_instock());
    }

    public function test_MinStock()
    {
        $this->assertEquals(3, $this->part->get_mininstock());
    }

    public function test_Name()
    {
        $this->assertEquals("Part 1", $this->part->get_name());
    }

    /**
     * Check for correct parsing of the description bbcode
     */
    public function test_Description_parsed()
    {
        $this->assertEquals("<strong>Part 1</strong> description", $this->part->get_description(true));
    }

    public function test_Description_raw()
    {
        $this->assertEquals("[b]Part 1[/b] description", $this->part->get_description(false));
    }

    public function test_Comment_parsed()
    {
        $this->assertEquals("<strong>bold</strong> normal", $this->part->get_comment(true));
    }

    public function test_Comment_raw()
    {
        $this->assertEquals("[b]bold[/b] normal", $this->part->get_comment(false));
    }
}