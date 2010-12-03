<?php

namespace Majisti\Application\Addons;

require_once 'TestHelper.php';

/**
 * @desc Test case for Manager class.
 * @author Majisti
 */
class ManagerTest extends \Majisti\Test\TestCase
{
    /**
     * @var Manager
     */
    public $manager;

    /**
     * Setups the test case
     */
    public function setUp()
    {
       $this->manager     = new Manager();
       $options           = $this->getHelper()->getOptions();
       $maj               = $options['majisti'];

//       \Zend_Controller_Front::getInstance()->setDispatcher(
//           new \Majisti\Controller\Dispatcher\Multiple());
    }

    /**
     * Tears down the test case.
     */
    public function tearDown()
    {
        \Zend_Controller_Front::getInstance()->setDispatcher(
            new \Zend_Controller_Dispatcher_Standard());
    }

    /**
     * @desc Tests that the loadModule() function adds the controller
     * fallback directory in the dispatcher.
     */
//    public function testThatLoadModuleAddsControllerFallbackDirectory()
//    {
//        $manager = $this->manager;
//
//        /** @var $dispatcher \Majisti\Controller\Dispatcher\Multiple **/
//        $dispatcher = \Zend_Controller_Front::getInstance()->getDispatcher();
//
//        $manager->registerAddonsPath($this->testMajistiXPath, 'MajistiX');
//        $this->assertTrue($manager->hasAddonsNamespace('MajistiX'));
//
//        $manager->loadModule('majistixModule1', 'MajistiX');
//        $manager->loadModule('majistixModule2', 'MajistiX');
//
//        $this->assertTrue(array_key_exists('majistixModule1',
//                          $dispatcher->getFallbackControllerDirectories()));
//        $this->assertTrue(array_key_exists('majistixModule2',
//                          $dispatcher->getFallbackControllerDirectories()));
//    }

    /**
     * @desc Tests that attempting to load an invalid (not found) module throws
     * an exception.
     * @expectedException Exception
     */
//    public function testThatInvalidModuleNameThrowsException()
//    {
//        $manager = $this->manager;
//
//        $manager->registerAddonsPath($this->testMajistiXPath, 'MajistiX');
//        $this->assertTrue($manager->hasAddonsNamespace('MajistiX'));
//
//        /* throws exception here */
//        $manager->loadModule('invalidModule', 'MajistiX');
//    }

    /**
     * @desc Tests that loading an extension will call the bootstrap's laod
     * function.
     */
    public function testThatLoadingExtensionCallsLoadFunction()
    {
       $this->assertTrue($this->manager->loadExtension('Foo'));
    }

    /**
     * @desc Tests that attempting to load an invalid extension, in this case
     * an extension without a bootstrap file, will throw an exception.
     * @expectedException Exception
     */
    public function testThatLoadingAnExtensionWOABootstrapFileWillThrowException()
    {
        $this->manager->loadExtension('Bar');
    }

    /**
     * @desc Tests that attempting to load an invalid extension, in this case
     * an extension with a bootstrap file not holding a bootstrap class,
     * will throw an exception.
     * @expectedException Exception
     */
    public function testThatLoadingAnExtensionWOABootstrapNamespaceWillThrowException()
    {
        $this->manager->loadExtension('Baz');
    }

    /**
     * @desc Tests that attempting to load an invalid extension, in this case
     * an extension with a bootstrap file holding a bootstrap class not
     * implementing IAddonsBootstrapper interface, will throw an exception.
     *
     * @expectedException Exception
     */
    public function testThatLoadingAnExtensionWithABootstrapClassNotImplementingInterfaceWillThrowException()
    {
        $this->manager->loadExtension('Baz2');
    }
}

ManagerTest::runAlone();
