<?php

namespace Majisti\Test;

require_once 'TestHelper.php';

/**
 * @desc Test case for the Helper class.
 *
 * @author Majisti
 */
class HelperTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var Helper
     */
    public $helper;

    public function setUp()
    {
        $this->enableMvc();
        parent::setUp();
    }

    public function testMvcAutoloading()
    {
//        $this->dispatch('/foo/index/index');
//        $this->assertModule('foo');
//        $this->assertController('index');
//        $this->assertAction('index');

        $this->assertTrue(class_exists('MajistiT\Main\Handler\Foo')); //default module
        $this->assertTrue(class_exists('MajistiT\Foo\Handler\Foo')); //foo module
        $this->assertTrue(class_exists('MajistiT\Handler\Foo'));     //lib
      
//        \Zend_Debug::dump(\Zend_Loader_Autoloader::getInstance());
    }
}

HelperTest::runAlone();
