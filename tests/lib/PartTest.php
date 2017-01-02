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

    public function test_is_visible()
    {
        $part = $this->getPart(1);
        //Every part should have visible of 0
        $this->assertEquals(0, $part->get_visible());
    }

    /**
     * Try to get the order_orderdetails of a part which dont have one.
     */
    public function test_get_order_orderdetails_not_existing()
    {
        $part = $this->getPart(6);
        $this->assertNull($part->get_order_orderdetails());
    }


    /**
     * Test the result of a part which is not obsolete
     */
    public function test_get_obsolete_false()
    {
        $part = $this->getPart(1);
        $this->assertFalse($part->get_obsolete());
    }

    /**
     * Test the result of a part which is not obsolete
     */
    public function test_get_obsolete_true()
    {
        $part = $this->getPart(8);
        $this->assertTrue($part->get_obsolete());
    }

    /**
     * Tests if the correct order quantity is returned.
     */
    public function test_get_orderquantity()
    {
        $part = $this->getPart(2);
        $this->assertEquals(7, $part->get_order_quantity());
    }


    public function test_get_min_order_quantity()
    {
        $part = $this->getPart(2);
        $this->assertEquals(7, $part->get_min_order_quantity(false));
    }

    /*
     * Test if the manual order flag is read correctly
     */
    public function test_get_manual_order()
    {
        $part = $this->getPart(7);
        $this->assertEquals(1, $part->get_manual_order());
    }

    /**
     * Test the ability to create a part URL to a manufacturers Website
     */
    public function test_get_manufacturer_product_url()
    {
        $part = $this->getPart(1);
        $this->assertEquals("http://manu.de/part?name=Part 1", $part->get_manufacturer_product_url());
    }

    /**
     * Test if Part returns the last modified date correctly
     */
    public function test_get_last_modified()
    {
        $part = $this->getPart(1);
        $this->assertEquals("2017-01-02 14:08:06", $part->get_last_modified());
    }

    /**
     * Test if Part returns the creation date correctly
     */
    public function test_get_datetime_added()
    {
        $part = $this->getPart(1);
        $this->assertEquals("2017-01-02 13:44:00", $part->get_datetime_added());
    }

    /**
     * Test if the with get_category returned obj. is really a Category
     */
    public function test_get_category_type()
    {
        $part = $this->getPart(1);
        $this->assertInstanceOf(Category::class, $part->get_category());
    }

    /**
     * Test if the get_category values are correct
     * @depends test_get_category_type
     */
    public function test_get_category_correct()
    {
        $part = $this->getPart(1);
        $this->assertEquals(4, $part->get_category()->get_id());
    }

    /**
     * Try to get a footprint of a part which have not one.
     */
    public function test_get_footprint_not_existing()
    {
        $part = $this->getPart(2);
        $this->assertNull($part->get_footprint());
    }

    /**
     *  Tests if the from get_footprint returned obj really is a Footprint obj.
     */
    public function test_get_footprint_type()
    {
        $part = $this->getPart(1);
        $this->assertInstanceOf(Footprint::class, $part->get_footprint());
    }

    /**
     * Test if the returned values from get_footprints are correct
     * @depends test_get_footprint_type
     */
    public function test_get_footprint_correct()
    {
        $part = $this->getPart(1);
        $this->assertEquals(1, $part->get_footprint()->get_id());
    }

    /**
     * Try to get the Storelocation of a part which doesnt have one.
     */
    public function test_get_storelocation_not_existing()
    {
        $part = $this->getPart(2);
        $this->assertNull($part->get_storelocation());
    }

    public function test_get_storelocation_type()
    {
        $part = $this->getPart(1);
        $this->assertInstanceOf(Storelocation::class,$part->get_storelocation());
    }

    /**
     * @depends test_get_storelocation_type
     */
    public function test_get_storelocation_correct()
    {
        $part = $this->getPart(1);
        $this->assertEquals(4,$part->get_storelocation()->get_id());
    }


    // get_manufacturer()

    public function test_get_manufacturer_not_existing()
    {
        $part = $this->getPart(2);
        $this->assertNull($part->get_manufacturer());
    }

    public function test_get_manufacturer_type()
    {
        $part = $this->getPart(1);
        $this->assertInstanceOf(Manufacturer::class, $part->get_manufacturer());
    }

    public function test_get_manufacturer_correct()
    {
        $part = $this->getPart(1);
        $this->assertEquals(1, $part->get_manufacturer()->get_id());
    }


    public function test_get_master_picture_attachement_not_existing()
    {
        $part = $this->getPart(1);
        $this->assertNull($part->get_master_picture_attachement());
    }

    public function test_get_orderdetails_not_existing()
    {
        $part = $this->getPart(3);
        $this->assertEmpty($part->get_order_orderdetails());
    }

    /**
     * Check if the from get_orderdetails returned array really contains only Orderdetails obj.
     */
    public function test_get_orderdetails_type()
    {
        $part = $this->getPart(1);
        foreach($part->get_orderdetails() as $detail) {
            $this->assertInstanceOf(Orderdetails::class, $detail);
        }
    }

    /**
     * Check if the from get_orderdetails returned content is correct.
     */
    public function test_get_orderdetails_correct()
    {
        $part = $this->getPart(1);
        $this->assertEquals(1, $part->get_orderdetails()[0]->get_id());
    }

    /**
     * Check if the obsolete orderdetails are really hidden when this is requested
     */
    public function test_get_orderdetails_hide_obsolete()
    {
        $part = $this->getPart(2);
        // With the obsolete orderdetails there should be 1 detail...
        $this->assertEquals(1, count($part->get_orderdetails()));
        //Without there should be only 0 details...
        $this->assertEquals(0, count($part->get_orderdetails(true)));
    }

    // get_devices

    /**
     * Try to get the devices to a part which doesnt have one.
     */
    public function test_get_devices_not_existing()
    {
        $part = $this->getPart(1);
        $this->assertEmpty($part->get_devices());
    }

    /**
     * Check if every element of the array is really a Device obj
     */
    public function test_get_devices_type()
    {
        $part = $this->getPart(8);
        foreach($part->get_devices() as $device)
        {
            $this->assertInstanceOf(Device::class, $device);
        }
    }

    public function test_get_device_correct()
    {
        $part = $this->getPart(8);
        $this->assertEquals(1, $part->get_devices()[0]->get_id());
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