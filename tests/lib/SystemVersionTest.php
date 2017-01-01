<?php

/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 01.01.2017
 * Time: 16:34
 */
class SystemVersionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    /**
     * Test if SystemVersion detects if a Version is a stable one.
     */
    public function test_stable()
    {
        $version = new SystemVersion("0.3.4");
        $this->assertEquals("stable",$version->get_version_type());
    }

    /*
     * Test if SystemVersion detects if a Version is a unstable one.
     */
    public function test_unstable()
    {
        $version = new SystemVersion("0.3.4.RC1");
        $this->assertEquals("unstable",$version->get_version_type());
    }

    /**
     * @expectedException Exception
     */
    public function test_invalidVersion()
    {
        //$this->expectException(Exception::class);
        $version = new SystemVersion("04.23.3R3.4");
    }

    /*
     * Test the compare between two Versions
     */
    public function test_compare_newer()
    {
        $ver1 = new SystemVersion("0.2.1");
        $ver2 = new SystemVersion("0.2.2");
        $this->assertFalse($ver1->is_newer_than($ver2));
    }

    public function test_compare_older()
    {
        $ver1 = new SystemVersion("0.2.1");
        $ver2 = new SystemVersion("0.2.2");
        $this->assertFalse($ver1->is_newer_than($ver2));
    }

    public function test_compare_equals()
    {
        $ver1 = new SystemVersion("0.2.1");
        $ver2 = new SystemVersion("0.2.1");
        $this->assertFalse($ver1->is_newer_than($ver2));
    }

    /**
     * Test the compare between two RC versions.
     */
    public function test_compare_rc()
    {
        $ver1 = new SystemVersion("0.2.1.RC1");
        $ver2 = new SystemVersion("0.2.2.RC2");
        $this->assertTrue($ver2->is_newer_than($ver1));
    }

    /**
     * Test the compare between a RC version and a final one.
     */
    public function test_compare_rc_stable()
    {
        $ver1 = new SystemVersion("0.2.1.RC1");
        $ver2 = new SystemVersion("0.2.1");
        $this->assertTrue($ver2->is_newer_than($ver1));
    }

    public function test_as_string_internal()
    {
        $ver = new SystemVersion("0.3.4.RC1");
        $this->assertEquals("0.3.4.RC1",$ver->as_string(true));
    }

    public function test_as_string()
    {
        $ver = new SystemVersion("0.3.4.RC1");
        $this->assertEquals("0.3.4 RC1", $ver->as_string(false));
    }

    public function test_as_string_hide_rc()
    {
        $ver = new SystemVersion("0.3.4.RC1");
        $this->assertEquals("0.3.4",$ver->as_string(false,true));
    }

    public function test_as_string_show_type()
    {
        $ver = new SystemVersion("0.3.4.RC1");
        $this->assertEquals("0.3.4 RC1 (unstable)",$ver->as_string(false,false,false,true));
    }

    /**
     * Tests if SystemVersion::get_installed_version() returns a SystemVersion obj.
     */
    public function test_get_installed_version()
    {
        global $config;
        $config['system']['version'] = "0.3.4.RC1";
        $version = SystemVersion::get_installed_version();
        $this->assertInstanceOf(SystemVersion::class, $version);
    }

}
