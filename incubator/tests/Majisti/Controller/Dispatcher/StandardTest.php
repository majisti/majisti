<?php

namespace Majisti\Controller\Dispatcher;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Steven Rosato
 */
class StandardTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    private $_dispatcher;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->_dispatcher = new Standard();
    }
    
    public function testDispatch()
    {
        $this->markTestIncomplete();
    }
}

StandardTest::runAlone();
