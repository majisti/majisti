<?php

namespace Majisti\Application\Extension;

require_once __DIR__ . '/TestHelper.php';

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
       $helper            = $this->getHelper();
       $bootstrap         = $helper->createBootstrapInstance();
       $this->manager     = new Manager($bootstrap->bootstrap()->getApplication());
       $options           = $helper->getOptions();
       $maj               = $options['majisti'];

       $this->manager->setExtensionPaths(array(
           array(
               'namespace' => $maj['app']['namespace'],
               'path'      => $maj['app']['path'] . '/library/extensions'
           )
       ));
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
     * @desc Tests that loading an extension will call the bootstrap's laod
     * function.
     */
    public function testThatLoadingExtensionCallsLoadFunction()
    {
       $bootstrap = $this->manager->loadExtension('Foo');
       $this->assertEquals('MajistiT\Foo\Bootstrap', get_class($bootstrap));
       $this->assertTrue($bootstrap->run());
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
