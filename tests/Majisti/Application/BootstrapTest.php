<?php

namespace Majisti\Application;

require_once __DIR__ . '/TestHelper.php';

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

        $helper = \Majisti\Test\Helper::getInstance();

        $helper->initAutoloaders();
        $this->majistiBootstrap = $helper->createBootstrapInstance();
    }

    static public function runAlone()
    {
        \Majisti\Test\TestCase::setClass(__CLASS__);
        \Majisti\Test\TestCase::runAlone();
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
