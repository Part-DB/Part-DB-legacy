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

    public function getPart($pid)
    {
        return new Part($this->database, $this->current_user, $this->log, $pid);
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

    /**
     * Try to delete a part which is used in a device.
     * @expectedException Exception
     */
    public function test_delete_on_device_part()
    {
        $part = $this->getPart(8);
        $part->delete();
    }

    public function test_force_delete_on_device_part()
    {
        $part = $this->getPart(8);
        $part->delete(false, true);
        $part = null;
        try {
            $part = new Part($this->database, $this->current_user, $this->log, 8);
        }
        catch (Exception $e)
        {

        }
        //When an exception occured above, then the part was deleted.
        $this->assertNull($part);
    }

    /**
     * Test the generation of a EAN8 Barcode Content
     */
    public function test_barcode_ean8()
    {
        $part = $this->getPart(1);
        $this->assertSame("0000001",$part->get_barcode_content("EAN8"));
    }

    /**
     *  Test generation of a QR-Code content
     */
    public function test_barcode_qr()
    {
        $part = $this->getPart(1);
        $this->assertEquals("Part-DB; Part: 1", $part->get_barcode_content("QR"));
    }

    /**
     * Test Barcode content generation with an invalid type
     * @expectedException Exception
     */
    public function test_barcode_invalid()
    {
        $part = $this->getPart(1);
        $part->get_barcode_content("rdep");
    }

    public function test_get_obsolete_false()
    {
        $part = $this->getPart(1);
        $this->assertFalse($part->get_obsolete());
    }


    /**
     * Try to delete a part and check if it is really deleted.
     * @expectedException Exception
     */
    public function test_delete_part()
    {
        $part = $this->getPart(1);
        $part->delete();
        new Part($this->database, $this->current_user, $this->log, 1);
    }

    /**
     * Test the behaviour on invalid pid such as -1
     * @expectedException Exception
     */
    public function test_invalid_pid()
    {
        new Part($this->database, $this->current_user, $this->log, -1);
    }

    /**
     * Test the behaviour on pid which dont have a part associated.
     * @expectedException Exception
     */
    public function test_out_of_bound_pid()
    {
        new Part($this->database, $this->current_user, $this->log, 100);
    }
}