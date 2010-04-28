<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class DispatcherTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    public $dispatcher;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->dispatcher = new Dispatcher(array(
            'bootstrap' => new \Majisti\Application\Bootstrap(
                \Majisti\Application::getInstance())
        ));
    }

    /**
     * Asserts that the dispatcher get initialized correctly, meaning that
     * the default module's controller directory should be added as well
     * as MajistiX default module's controller directory as a fallback one.
     */
    public function testInit()
    {
        $dispatcher = $this->dispatcher->init();

        $this->assertType('\Majisti\Controller\Dispatcher\Multiple', $dispatcher);
        $this->assertArrayHasKey('default', $dispatcher->getControllerDirectory());
        $this->assertEquals(1, count($dispatcher->getFallbackControllerDirectory()));
    }
}

DispatcherTest::runAlone();
