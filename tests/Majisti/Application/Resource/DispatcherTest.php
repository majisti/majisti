<?php

namespace Majisti\Application\Resource;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class DispatcherTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    public $dispatcher;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->dispatcher = new Dispatcher(array(
            'bootstrap' => $this->getHelper()->createBootstrapInstance()
        ));

        $this->dispatcher->setOptions(array('fallback' => array(
            'auth' => array('foo' => 'bar')
        )));
    }

    /**
     * @desc Asserts that a fallback in the config gets added correctly.
     */
    public function testInit()
    {
        $dispatcher = $this->dispatcher->init();
        $dirs       = $dispatcher->getFallbackControllerDirectories();

        $this->assertType('\Majisti\Controller\Dispatcher\Multiple', $dispatcher);
        $this->assertArrayHasKey('auth', $dirs);

        /* auth */
        $dir = $dispatcher->getFallbackControllerDirectory('auth');
        $this->assertEquals(1, count($dir));
        $this->assertEquals('foo', $dir[0][0]); //namespace
        $this->assertEquals('bar', $dir[0][1]); //path
    }
}

DispatcherTest::runAlone();
