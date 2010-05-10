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

    /**
     * @var \Majisti\I18n\Locales
     */
    public $locales;

    /**
     * @var array
     */
    public $options;

    public function setUp()
    {
        $this->resource = new Locale();
        $this->locales  = \Majisti\I18n\Locales::getInstance();
        $this->locales->setLocales(array(new \Zend_Locale('en')));
        $this->locales->reset();
        $this->locales->clearLocales();

        parent::setUp();
    }

    public function tearDown()
    {
        $this->locales->setLocales(array(new \Zend_Locale('en')));
    }

    static public function runAlone()
    {
        \Majisti\Test\PHPUnit\TestCase::setClass(__CLASS__);
        \Majisti\Test\PHPUnit\TestCase::runAlone();
    }

    public function testOnlyOneAvailableLocale()
    {
        $locales    = $this->locales;
        $resource   = $this->resource;
        $options    = array('available' => array('fr'));

        $resource->setOptions($options);
        $locale = $resource->init();

        $expectedLocale = new \Zend_Locale('fr');
        $this->assertTrue($expectedLocale->equals($locale));
        $this->assertTrue($expectedLocale->equals($locales->getDefaultLocale()));
        $this->assertTrue($expectedLocale->equals($locales->getCurrentLocale()));

        $this->assertTrue(\Zend_Registry::isRegistered('Zend_Locale'));
        $this->assertTrue($expectedLocale->equals(
            \Zend_Registry::get('Zend_Locale')));
    }

    public function testThatNoSpecifiedDefaultLocaleAssumesFirstAvailableLocale()
    {
        $locales    = $this->locales;
        $resource   = $this->resource;
        $options    = array('available' => array('en', 'fr', 'it'));

        $resource->setOptions($options);
        $locale = $resource->init();

        $expectedLocale = new \Zend_Locale('en');
        $this->assertTrue($expectedLocale->equals($locale));
        $this->assertTrue($expectedLocale->equals($locales->getDefaultLocale()));
        $this->assertTrue($expectedLocale->equals($locales->getCurrentLocale()));

        $this->assertTrue(\Zend_Registry::isRegistered('Zend_Locale'));
        $this->assertTrue($expectedLocale->equals(
            \Zend_Registry::get('Zend_Locale')));
    }

    public function testNoOptions()
    {
        $locales    = $this->locales;
        $resource   = $this->resource;

        $resource->setOptions(array());
        $resource->init();

        $this->assertFalse($locales->isEmpty());
        $this->assertEquals(1, $locales->count());
    }
}

LocaleTest::runAlone();
