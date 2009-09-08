<?php

namespace Majisti;

require_once 'TestHelper.php';

/**
 * @desc
 * @author 
 */
class ViewTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    public $basePath;
    
    public $view;
    
    public $dispatcher;
    
    public $front;
    
    public $request;
    
    public $response;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->basePath = dirname(__FILE__) . 
            str_replace('/', DIRECTORY_SEPARATOR, '/Controller/_files');
        
        $this->front = \Zend_Controller_Front::getInstance();
            
        $this->_prepareDispatcher();
        $this->_prepareFront();
        
        \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')
            ->setView($this->_getView());
    }
    
    protected function _getView()
    {
        $view = new View();
        
        return $view;
    }
    
    protected function _prepareFront()
    {
        $this->front->setDispatcher($this->dispatcher);
        
        $this->request  = new \Zend_Controller_Request_Http();
        $this->response = new \Zend_Controller_Response_Http();
        
        $this->front->setControllerDirectory(array(
            'default' => $this->basePath,
            'users' => $this->basePath . str_replace(
                '/', DIRECTORY_SEPARATOR, '/anApplicationUsersModule/controllers'),
        ));
    }
    
    protected function _prepareDispatcher()
    {
        $this->dispatcher = new \Majisti\Controller\Dispatcher\Standard();
        
        $this->dispatcher->addFallbackControllerDirectory($this->basePath . 
            DIRECTORY_SEPARATOR . 'aLibraryUsersModule/controllers', 'users');
        $this->dispatcher->addNamespace('\aLibrary\Controllers\\', 'users');
        $this->dispatcher->addNamespace('\anApplication\Controllers\\', 'users');
    }
    
    public function testScriptNotContainedInApplicationButInLibraryDispatchesCorrectly()
    {
        $this->request
            ->setModuleName('users')
            ->setControllerName('present-in-both')
            ->setActionName('index');
            
        try {
            $this->dispatcher->dispatch($this->request, $this->response);    
        } catch( \Zend_View_Exception $e ) {
            $this->fail('present-in-both/index.phtml should be found in the alibrary view scripts');
        }
        
        $this->assertContains('aLibrary\Users_PresentInBothController::index.phtml script was called',
            $this->response->getBody());
        $this->assertNotContains('anApplication', $this->response->getBody());    
    }
}

ViewTest::runAlone();
