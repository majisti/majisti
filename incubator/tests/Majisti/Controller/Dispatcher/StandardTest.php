<?php

namespace Majisti\Controller\Dispatcher;

require_once 'TestHelper.php';

define("PHPUnit_MAIN_METHOD", false);

/**
 * @desc
 * @author Steven Rosato
 */
class StandardTest extends \Zend_Controller_Dispatcher_StandardTest
{
    static public function runAlone()
    {
        \Majisti\Test\PHPUnit\TestCase::setClass(__CLASS__);
        \Majisti\Test\PHPUnit\TestCase::runAlone();
    }
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        parent::setUp();
        $this->_dispatcher = new Standard();
        $this->_dispatcher->setControllerDirectory(array(
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'admin'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin'
        ));
    }
    
//    public function testAddControllerDirectory()
//    {
//        $dispatcher = $this->_dispatcher;
//        
//        $dispatcher->addControllerDirectory('dir1', null);
//        $dispatcher->addControllerDirectory('dir1', null);
//        $dispatcher->addControllerDirectory('dir2', null);
//        $dispatcher->addControllerDirectory('dir2', null);
//    }
    
//    public function testDispatch()
//    {
//        $this->markTestIncomplete();
//    }
}

StandardTest::runAlone();