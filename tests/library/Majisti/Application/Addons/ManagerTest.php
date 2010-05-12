<?php

namespace Majisti\Application\Addons;

require_once 'TestHelper.php';

/**
 * @desc Test case for Manager class.
 * @author Majisti
 */
class ManagerTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var Manager
     */
    public $manager;

    /**
     * @var string
     */
    public $basePath;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var array
     */
    public $addonsPaths;

    /**
     * @var string
     */
    public $testMajistiXPath;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
       $this->manager     = new Manager();
       $this->basePath    = 'path0';
       $this->namespace   = 'namespace0';

       $this->addonsPaths = array('namespace1' => 'path1',
                                  'namespace2' => 'path2',
                                  'namespace3' => 'path3'
                            );
       $this->testMajistiXPath = realpath(MAJISTI_ROOT . '/../' .
                                    "/tests/library/MajistiX/_addons");

       $this->manager->registerAddonsPath($this->basePath, $this->namespace);
    }

    /**
     * @desc Tests that the manager registers the addons paths as expected and
     * adds it to the registered addons path container.
     */
    public function testThatRegisterAddonsPathAddsPathsToTheRegisteredPathsContainer()
    {
        $manager = $this->manager;
        $this->assertEquals(1, count($manager->getAddonsPaths()));

        $manager->registerAddonsPaths($this->addonsPaths);
        $this->assertEquals(4, count($manager->getAddonsPaths()));
        $this->assertTrue($manager->hasAddonsNamespace('namespace0'));
        $this->assertTrue($manager->hasAddonsNamespace('namespace1'));
        $this->assertTrue($manager->hasAddonsNamespace('namespace2'));
        $this->assertTrue($manager->hasAddonsNamespace('namespace3'));

        $this->assertEquals($this->basePath,
                            $manager->getAddonsPath($this->namespace));
    }

    /**
     * @desc Tests that the setter overrides all addons path values with the
     * given path array
     */
    public function testThatSetFunctionOverridesAllPathsWithGivenArray()
    {
        $manager = $this->manager;
        $manager->setAddonsPaths($this->addonsPaths);

        /* overriding the current values with the array */
        $this->assertEquals(3, count($manager->getAddonsPaths()));
        $this->assertTrue($manager->hasAddonsNamespace('namespace1'));
        $this->assertTrue($manager->hasAddonsNamespace('namespace2'));
        $this->assertTrue($manager->hasAddonsNamespace('namespace3'));

        $this->assertEquals('path2', $manager->getAddonsPath('namespace2'));
    }

    /**
     * @desc Tests that the loadModule() function adds the controller
     * fallback directory in the dispatcher.
     */
    public function testThatLoadModuleAddsControllerFallbackDirectory()
    {
        $manager = $this->manager;

        /** @var $dispatcher \Majisti\Controller\Dispatcher\Multiple **/
        $dispatcher = \Zend_Controller_Front::getInstance()->getDispatcher();

        $manager->registerAddonsPath($this->testMajistiXPath, 'MajistiX');
        $this->assertTrue($manager->hasAddonsNamespace('MajistiX'));

        $manager->loadModule('majistixModule1', 'MajistiX');
        $manager->loadModule('majistixModule2', 'MajistiX');

        $this->assertTrue(array_key_exists('majistixModule1',
                          $dispatcher->getFallbackControllerDirectories()));
        $this->assertTrue(array_key_exists('majistixModule2',
                          $dispatcher->getFallbackControllerDirectories()));
    }

    /**
     * @desc Tests that attempting to load an invalid (not found) module throws
     * an exception.
     * @expectedException Exception
     */
    public function testThatInvalidModuleNameThrowsException()
    {
        $manager = $this->manager;

        $manager->registerAddonsPath($this->testMajistiXPath, 'MajistiX');
        $this->assertTrue($manager->hasAddonsNamespace('MajistiX'));

        /* throws exception here */
        $manager->loadModule('invalidModule', 'MajistiX');
    }

    /**
     * @desc Tests that loading an extension will call the bootstrap's laod
     * function.
     */
    public function testThatLoadingExtensionCallsLoadFunction()
    {
       $manager = $this->manager;
       $manager->registerAddonsPath($this->testMajistiXPath, 'MajistiX');
       $this->assertTrue($manager->hasAddonsNamespace('MajistiX'));

       $hasLoadedExtension = $manager->loadExtension('AValidExtension',
                                                     'MajistiX');
       $this->assertTrue($hasLoadedExtension);
    }

    /**
     * @desc Tests that attempting to load an invalid extension, in this case
     * an extension without a bootstrap file, will throw an exception.
     * @expectedException Exception
     */
    public function testThatLoadingAnExtensionWOABootstrapFileWillThrowException()
    {
        $manager = $this->manager;
        $manager->registerAddonsPath($this->testMajistiXPath, 'MajistiX');

        /* will trow an exception */
        $manager->loadExtension('AnInvalidExtension1', 'MajistiX');
    }

    /**
     * @desc Tests that attempting to load an invalid extension, in this case
     * an extension with a bootstrap file not holding a bootstrap class,
     * will throw an exception.
     * @expectedException Exception
     */
    public function testThatLoadingAnExtensionWOABootstrapClassWillThrowException()
    {
        $manager = $this->manager;
        $manager->registerAddonsPath($this->testMajistiXPath, 'MajistiX');

        /* will trow an exception */
        $manager->loadExtension('AnInvalidExtension2', 'MajistiX');
    }

    /**
     * @desc Tests that attempting to load an invalid extension, in this case
     * an extension with a bootstrap file holding a bootstrap class not
     * implementing IAddonsBootstrapper interface, will throw an exception.
     * @expectedException Exception
     */
    public function testThatLoadingAnExtensionWithABootstrapClassNotImplementingInterfaceWillThrowException()
    {
        $manager = $this->manager;
        $manager->registerAddonsPath($this->testMajistiXPath, 'MajistiX');

        /* will trow an exception */
        $manager->loadExtension('AnInvalidExtension3', 'MajistiX');
    }
}

ManagerTest::runAlone();
