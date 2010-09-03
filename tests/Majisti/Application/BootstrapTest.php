<?php

namespace Majisti\Application;

require_once 'TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc Tests that the bootstrap initializes everything correctly.
 * @author Majisti
 */
class BootstrapTest extends \Zend_Application_Bootstrap_BootstrapTest
{
    /**
     * @var \Majisti\Application\Bootstrap 
     */
    public $majistiBootstrap;

    public function setUp()
    {
        parent::setUp();

        $this->majistiBootstrap = new Bootstrap($this->application);
    }

    static public function runAlone()
    {
        \Majisti\Test\TestCase::setClass(__CLASS__);
        \Majisti\Test\TestCase::runAlone();
    }

    /**
     * @desc Asserts that the library auto loader in initialized
     */
    public function testLibraryAutoloaderInitialized()
    {
        $this->majistiBootstrap->bootstrap();

        $autoloader  = \Zend_Loader_Autoloader::getInstance();
        $autoloaders = $autoloader->getAutoloaders();

        $this->assertEquals(1, count($autoloaders));
        $this->assertType('Zend_Application_Module_Autoloader', $autoloaders[0]);
    }

    /**
     * @desc Asserts that action helper is initialized
     */
    public function testActionHelperInitialized()
    {
        $this->majistiBootstrap->bootstrap();

        $paths = \Zend_Controller_Action_HelperBroker::getPluginLoader()->getPaths();
        $this->assertArrayHasKey('Majisti_Controller_ActionHelper_', $paths);
    }
}

BootstrapTest::runAlone();
