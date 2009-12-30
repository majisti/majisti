<?php

namespace Majisti;

require_once 'TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ViewTest extends \Zend_ViewTest
{
    public $basePath;
    
    public $view;
    
    public $dispatcher;
    
    public $front;
    
    public $request;
    
    public $response;
    
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
        
        $this->basePath = dirname(__FILE__) . 
            str_replace('/', DIRECTORY_SEPARATOR, '/Controller/_files');
        
        $this->front = \Zend_Controller_Front::getInstance();
            
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
        
        $this->front->setRequest($this->request);
        $this->front->setResponse($this->response);
        
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
        $this->_prepareDispatcher();
        $this->_prepareFront();
        
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
    
    public function testRenderSubTemplates()
    {
        $this->markTestSkipped();
    }
}

ViewTest::runAlone();
