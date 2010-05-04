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

    public $locales;
    public $request;

    /**
     *
     * @var I18n
     */
    public $i18n;
    public $config;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->i18n = new I18n();

        /* setting up request object */
        $this->request = new \Zend_Controller_Request_Http();
        $this->request->setActionName('fooAction');
        $this->request->setControllerName('barController');
        $this->request->setModuleName('bazModule');

        /* setting up locales */
        $this->locales = \Majisti\I18n\Locales::getInstance();
        $this->locales->addLocale(new \Zend_Locale('en'));
        $this->locales->addLocale(new \Zend_Locale('fr'));
        $this->locales->switchLocale(new \Zend_Locale('en'));

        /* front controller */
        $front = \Zend_Controller_Front::getInstance();
        $front->setRequest($this->request);
        $front->setResponse(new \Zend_Controller_Response_Http());

        /* stub redirector to cancel the redirection */
        $stub = $this->getMock('Zend_Controller_Action_Helper_Redirector');
        $stub->expects($this->once())
             ->method('goToSimpleAndExit');
        \Zend_Controller_Action_HelperBroker::getStack()->Redirector = $stub;
       
        /* add default route */
        \Zend_Controller_Front::getInstance()->getRouter()->addRoute(
            'default',
            new \Zend_Controller_Router_Route_Module(
                array(),
                $front->getDispatcher(),
                $this->request
            )
        );

        /* request config */
        $this->config = new \Zend_Config(array('plugins'      => array (
                                                'i18n'        => array(
                                                'requestParam'=> 'request'))));
    }

    public function tearDown()
    {
        $this->i18n->setConfig(new \Zend_Config(array()));
    }

    /**
     * Tests that preDispatch() switches to a supported locale when
     * supplying a request named accordingly to config settings.
     */
    public function testLocaleIsSwitchedOnPost()
    {
        $this->request->setParam('request', 'fr');
        $this->i18n->setConfig($this->config);
        $this->i18n->preDispatch($this->request);

        $this->assertEquals(new \Zend_Locale('fr'), $this->locales->getCurrentLocale());
    }

    /**
     * Tests that preDispatch() throws an exception if requestParam is not
     * set.
     *
     * @expectedException Exception
     */
    public function testThatExceptionIsThrownIfRequestParamIsUnset()
    {
        $this->i18n->preDispatch($this->request);
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
