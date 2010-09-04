<?php

namespace Majisti;

require_once 'TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc Test the view class.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ViewTest extends \Zend_ViewTest
{
    /**
     * @var string
     */
    public $basePath;

    /**
     * @var View
     */
    public $view;
   
    /**
     * @var \Majisti\Controller\Dispatcher\Multiple
     */
    public $dispatcher;

    /**
     * @var \Zend_Controller_Front
     */
    public $front;

    /**
     * @var \Zend_Controller_Request_Http
     */
    public $request;

    /**
     * @var \Zend_Controller_Response_Http
     */
    public $response;
    
    static public function runAlone()
    {
        \Majisti\Test\TestCase::setClass(__CLASS__);
        \Majisti\Test\TestCase::runAlone();
    }
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->basePath = __DIR__ . 
            str_replace('/', DIRECTORY_SEPARATOR, '/Controller/_files');
        
        $this->front = \Zend_Controller_Front::getInstance();
            
        \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')
            ->setView($this->getView());
    }
    
    protected function getView()
    {
        $view = new View();
        
        return $view;
    }
    
    protected function prepareFront()
    {
        $this->front->setDispatcher($this->dispatcher);
        
        $this->request  = new \Zend_Controller_Request_Http();
        $this->response = new \Zend_Controller_Response_Http();
        
        $this->front->setRequest($this->request);
        $this->front->setResponse($this->response);
        
        $this->front->setControllerDirectory(array(
            'default'   => $this->basePath,
            'users'     => $this->basePath . str_replace(
                '/', DIRECTORY_SEPARATOR,
                '/anApplicationUsersModule/controllers'),
        ));
    }
    
    protected function prepareDispatcher()
    {
        $this->dispatcher = new \Majisti\Controller\Dispatcher\Multiple();
        
        $this->dispatcher->addFallbackControllerDirectory(
            '\aLibrary\Controllers\\',
            $this->basePath .  DIRECTORY_SEPARATOR .
            'aLibraryUsersModule/controllers', 'users');
    }
    
    public function testScriptNotContainedInApplicationButInLibraryDispatchesCorrectly()
    {
        $this->prepareDispatcher();
        $this->prepareFront();

        $this->request
            ->setModuleName('users')
            ->setControllerName('present-in-both')
            ->setActionName('index');

        try {
            $this->dispatcher->dispatch($this->request, $this->response);
        } catch( \Zend_View_Exception $e ) {
            $this->fail('present-in-both/index.phtml should be found' .
                ' in the alibrary view scripts');
        }

        $this->assertContains('Users_PresentInBothController::' .
            'index.phtml script was called', $this->response->getBody());
        $this->assertNotContains('anApplication', $this->response->getBody());
    }

    /**
     * @desc Not needed to run this test class. Output buffering fails.
     */
    public function testZf995UndefinedPropertiesReturnNull() {}

    public function testAddingStreamSchemeAsScriptPathShouldNotReverseSlashesOnWindows()
    {
        if (true === strstr(strtolower(PHP_OS), 'windows')) {
            parent::testAddingStreamSchemeAsScriptPathShouldNotReverseSlashesOnWindows();
        }
    }
}

ViewTest::runAlone();
