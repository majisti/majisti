<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc
 * @author
 */
class LocaleTest extends \Zend_Application_Resource_LocaleTest
{
    static protected $_class = __CLASS__;

    /**
     * @var Locale
     */
    public $resource;

    public $options;

    public function setUp()
    {
        $this->resource = new Locale();
        $this->options = array('available' => array('en', 'fr', 'it'));

        parent::setUp();
    }

    static public function runAlone()
    {
        \Majisti\Test\PHPUnit\TestCase::setClass(__CLASS__);
        \Majisti\Test\PHPUnit\TestCase::runAlone();
    }

    public function testThatNoSpecifiedDefaultLocaleAssumesFirstAddedLocale()
    {
        $resource = $this->resource;

        $resource->setOptions($this->options);
        $locale = $resource->init();
        $expectedLocale = new \Zend_Locale('en');

        $this->assertTrue($expectedLocale->equals($locale));
    }
}

LocaleTest::runAlone();
