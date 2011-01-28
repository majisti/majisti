<?php

namespace Majisti\Application\Resource;

require_once __DIR__ . '/TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc
 * @author
 */
class LocalesTest extends \Zend_Application_Resource_LocaleTest
{
    static protected $_class = __CLASS__;

    /**
     * @var Locale
     */
    public $resource;

    /**
     * @var \Majisti\Application\Locales
     */
    public $locales;

    /**
     * @var array
     */
    public $options;

    public function setUp()
    {
        $this->resource = new Locales();
        $this->resource->setBootstrap(
            \Majisti\Test\Helper::getInstance()->createBootstrapInstance()
        );

        $this->locales = $this->resource->getBootstrap()
            ->bootstrap('Locales')
            ->getResource('Locales');
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
        \Majisti\Test\TestCase::setClass(__CLASS__);
        \Majisti\Test\TestCase::runAlone();
    }

    public function testOnlyOneAvailableLocale()
    {
        $locales    = $this->locales;
        $resource   = $this->resource;
        $options    = array('available' => array('fr'));

        $resource->setOptions($options);
        $resource->init();

        $expectedLocale = new \Zend_Locale('fr');
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
        $resource->init();

        $expectedLocale = new \Zend_Locale('en');
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

LocalesTest::runAlone();
