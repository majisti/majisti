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

    /**
     * @var \Majisti\Application\Locales
     */
    public $locales;

    /**
     * @var \Zend_Controller_Request_Http
     */
    public $request;

    /**
     * @var I18n
     */
    public $i18n;

    /**
     * @var \Zend_Config
     */
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
        $this->locales = \Majisti\Application\Locales::getInstance();
        $this->locales->addLocale(new \Zend_Locale('en'));
        $this->locales->addLocale(new \Zend_Locale('fr'));
        $this->locales->switchLocale(new \Zend_Locale('en'));

        /* front controller */
        $front = \Zend_Controller_Front::getInstance();
        $front->setRequest($this->request);
        $front->setResponse(new \Zend_Controller_Response_Http());

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
        $this->config = new \Zend_Config(array(
            'plugins' => array(
                'i18n' => array(
                    'requestParam' => 'lang'
                )
             )
        ));
    }

    /**
     * @desc Prepares a mock redirector
     */
    public function prepareMockRedirector()
    {
        /* stub redirector to cancel the redirection */
        $stub = $this->getMock('Zend_Controller_Action_Helper_Redirector');
        $stub->expects($this->once())
             ->method('goToSimpleAndExit')
             ->with($this->equalTo('fooAction'), 
                    $this->equalTo('barController'),
                    $this->equalTo('bazModule')
             )
        ;
        \Zend_Controller_Action_HelperBroker::getStack()->Redirector = $stub;
    }

    /**
     * Tests that preDispatch() switches to a supported locale when
     * supplying a request named accordingly to config settings.
     * Also, tests that it redirects the user once the locale has been switched
     * and that the request is a regular one.
     */
    public function testLocaleIsSwitchedOnPost()
    {
        /* assures that a redirection occured */
        $this->prepareMockRedirector();

        $this->request->setParam('lang', 'fr');
        $this->i18n->setConfig($this->config);
        $this->i18n->preDispatch($this->request);

        $this->assertEquals(new \Zend_Locale('fr'),
                $this->locales->getCurrentLocale());
    }

    /**
     * Tests that preDispatch() throws an exception if requestParam is not
     * set.
     *
     * @expectedException Exception
     */
    public function testThatExceptionIsThrownIfRequestParamIsUnset()
    {
        $this->i18n->setConfig(new \Zend_Config(array()));
        $this->i18n->preDispatch($this->request);
    }

    /**
     * Tests that an AJAX response is sent if the request is an XmlHttpRequest.
     */
    public function testThatAjaxResponseIsSent()
    {
        $this->i18n->setConfig($this->config);

        /*
         * request stub for fooling the i18n object into thinking were using
         * X_REQUEST_WITH XmlHttpRequest
         */

        /* @var $requestStub \Zend_Controller_Request_Http */
        $requestStub = $this->getMock('Zend_Controller_Request_Http',
                array('isXmlHttpRequest'));

        $requestStub->expects($this->once())
                    ->method('isXmlHttpRequest')
                    ->will($this->returnValue(true));

        /*
         * json stub for fooling the i18n object into thinking that it sent
         * its data to the action helper. We verify that the output is sent
         * correctly
         */
        $jsonStub = $this->getMock('Zend_Controller_Action_Helper_Json');
        $jsonStub->expects($this->once())
                 ->method('sendJson')
                 ->with($this->equalTo(array(
                     'switched' => true
                 )))
        ;
        \Zend_Controller_Action_HelperBroker::getStack()->Json = $jsonStub;

        $requestStub->setParam('lang', 'fr');
        $this->i18n->preDispatch($requestStub);

        $this->assertEquals(new \Zend_Locale('fr'),
                $this->locales->getCurrentLocale());
    }
}

I18nTest::runAlone();
