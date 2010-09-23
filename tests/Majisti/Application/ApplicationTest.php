<?php

namespace Majisti\Application;

require_once 'TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc Assert that the application was instanciated correctly.
 * It was already instanciated in the TestHelper
 *
 * @author Majisti
 */
class ApplicationTest extends \Zend_Application_ApplicationTest
{
    static public function runAlone()
    {
        \Majisti\Test\TestCase::setClass(__CLASS__);
        \Majisti\Test\TestCase::runAlone();
    }

    public function setUp()
    {
        parent::setUp();

        $helper = \Majisti\Test\Helper::getInstance();
        $helper->initAutoloaders();

        $this->majistiApplication = $helper->createApplicationInstance();
    }

    /**
     * @desc Test that configuration
     */
    public function testConfigurationMergedProperly()
    {
//        $config     = new \Zend_Config($this->majistiApplication->getOptions());
//        $selector   = new \Majisti\Config\Selector($config);

        /* majisti's config */
//        $this->assertEquals('Bootstrap', $selector->find('bootstrap.class'));
//        $this->assertEquals('UTF-8', $selector->find('resources.view.encoding'));

        /* user defined config */
//        $this->assertEquals('baz', $selector->find('foo.bar', false));
    }

    public function testPassingZfVersionAutoloaderInformationConfiguresAutoloader()
    {}
}

ApplicationTest::runAlone();
