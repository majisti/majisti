<?php

namespace Majisti\Controller\Dispatcher;

require_once 'TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc
 * @author Steven Rosato
 */
class StandardTest extends \Zend_Controller_Dispatcher_StandardTest
{
    protected $_filesPath;
    
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->_filesPath = realpath(dirname(__FILE__) . '/../_files');
    }
    
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
        $dispatcher = new Standard();
        
        $dispatcher->setControllerDirectory($this->_dispatcher->getControllerDirectory());
        $dispatcher->addControllerDirectory(
            $this->_filesPath . '/anApplicationUsersModule/controllers',
            'users'
        );
        
        $dispatcher->addFallbackControllerDirectory($this->_filesPath . '/aLibraryUsersModule/controllers');
        $dispatcher->addNamespace('\aLibrary\Controllers\\', 'users');
        $dispatcher->addNamespace('\anApplication\Controllers\\', 'users');
        
        $this->_dispatcher = $dispatcher;
    }
    
    public function testDispatchNamespacedController()
    {
        $request = new \Zend_Controller_Request_Http();
        $request->setModuleName('users')
                ->setControllerName('manage');
            
        $response = new \Zend_Controller_Response_Cli();
        
        $this->_dispatcher->dispatch($request, $response);
        $this->assertContains('anApplication\Controllers\Users_ManageController::index', $response->getBody());
    }
    
    public function testDispatchNonExistantControllerFallbacksToOtherDirectories()
    {
        $this->markTestSkipped();
        
        $request = new \Zend_Controller_Request_Http();
        $request->setModuleName('users')
                ->setControllerName('list');
            
        $response = new \Zend_Controller_Response_Cli();
        
        $this->_dispatcher->dispatch($request, $response);
        $this->assertContains('aLibrary\Controllers\Users_ListController::index', $response->getBody());
    }
    
    public function testSetGetControllerDirectory()
    {
        $this->markTestSkipped();
    }
}

StandardTest::runAlone();