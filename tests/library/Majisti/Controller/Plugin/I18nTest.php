<?php

namespace Majisti\Controller\Plugin;

require_once 'TestHelper.php';

/**
 * @desc Test case for the I18n controller plugin testing that locale switching
 * occurs when a request with the previously set name is set to a supported
 * locale. If no request param is set in the config, makes sure that an
 * exception is thrown. Also verifies that the request param is unset and that
 * a redirection using gotoSimpleAndExit() has been done.
 *
 * @author Majisti
 */
class I18nTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    protected $_locales;
    protected $_request;
    protected $_i18n;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->_i18n = new I18n();

        /* setting up request object */
        $this->_request = new \Zend_Controller_Request_Http();
        $this->_request->setActionName('fooAction');
        $this->_request->setControllerName('barController');
        $this->_request->setModuleName('bazModule');

        /* setting up locales */
        $this->_locales = \Majisti\I18n\Locales::getInstance();
        $this->_locales->addLocale(new \Zend_Locale('en'));

        /* front controller */
        $front = \Zend_Controller_Front::getInstance();
        $front->setRequest($this->_request);
        $front->setResponse(new \Zend_Controller_Response_Http());

        /* stub redirector to cancel the redirection */
        $stub = $this->getMock('Zend_Controller_Action_Helper_Redirector');
        $stub->expects($this->once())
             ->method('redirectAndExit');
        \Zend_Controller_Action_HelperBroker::getStack()->Redirector = $stub;
       
        /* add default route */
        \Zend_Controller_Front::getInstance()->getRouter()->addRoute(
            'default',
            new \Zend_Controller_Router_Route_Module(
                array(),
                $front->getDispatcher(),
                $this->_request
            )
        );
    }

    /**
     * Tests that preDispatch() switches locale when supplying a request
     */
    public function testLocaleIsSwitchedOnPost()
    {
        $config = new \Zend_Config(array('plugins'      => array (
                                        'i18n'          => array(
                                        'requestParam'  => 'request'))));

        /* TODO: use setConfig($config) to set the request param */

        $this->_request->setParam('request', 'fr');
        $this->_i18n->setConfig($config);
        $this->_i18n->preDispatch($this->_request);

        /* TODO: assert that locale has been switched to 'fr' */
        $this->markTestIncomplete();
    }

    /**
     * Tests that preDispatch() throws an exception if requestParam is not
     * set.
     */
    public function testThatExceptionIsThrownIfRequestParamIsUnset()
    {
        $this->markTestIncomplete();
    }

    /**
     * Tests that preDispatch() unsets the request param after switching locale.
     */
    public function testThatRequestParamIsUnsetOnceLocaleHasBeenSwitched()
    {
        $this->markTestIncomplete();
    }

    /**
     * Tests that preDispatch() redirects the user once locale has been switched
     * and that the request is a regular one.
     */
    public function testThatPageRedirectionHasOccured()
    {
        $this->markTestIncomplete();
    }

    /**
     * Tests that an AJAX response is sent if the request is an XmlHttpRequest.
     */
    public function testThatAjaxResponseIsSent()
    {
        $this->markTestIncomplete();
    }
}

I18nTest::runAlone();
