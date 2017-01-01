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
        $this->assertEquals($version->get_version_type(),"stable");
    }

    /*
     * Test if SystemVersion detects if a Version is a unstable one.
     */
    public function test_unstable()
    {
        $version = new SystemVersion("0.3.4RC1");
        $this->assertEquals($version->get_version_type(),"unstable");
    }
}
