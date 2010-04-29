<?php

namespace Majisti\Controller\Plugin;

require_once 'TestHelper.php';

/**
 * @desc Test case used to validate LayoutSwitcher's behaviour. This means it
 * does not test the returned values from the preDispatch() function (since it
 * does not return anything), but rather imitates it's behaviour and look at
 * the results this behaviour gives.
 * @author
 */
class LayoutSwitcherTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    public $request;
    public $plugin;

    /**
     * Setups the test case
     */
    public function setUp()
    {
        /* Empty request object */
        $this->request = new \Zend_Controller_Request_Http();

        /* Mocking a concrete project Layout object with a
         * layouts path given as a string. */
        $this->plugin  = new LayoutSwitcher(new \Zend_Layout(APPLICATION_PATH .
                "/modules/company/views/layouts"));

        /* Chosen module name for testing is company */
        $this->request->setModuleName('company');
    }

    /**
     * @desc Mimics the behaviour of LayoutSwitcher when only a layout name
     * is provided in a configuration file.
     */
    public function testThatEvenWithoutLayoutPathThePathIsSet()
    {
        $moduleName = $this->request->getModuleName();

        /* Buidling a configuration selector with testing layout options */
        $options = array('company' => array('resources' =>
                   array('layout' => array('layout' => 'default'))));
        $selector = new \Majisti\Config\Selector( new \Zend_Config($options) );

        /* Layout test options */
        $configProperty = "{$moduleName}.resources.layout";

        /* Asserting module name is OK and that the configs are read by the
         * selector */
        $this->assertEquals('company', $moduleName);
        $this->assertEquals('default', 
                $selector->find("{$configProperty}.layout"),
                "Layout path was not found in config file.");

        /* Asserting that even without specifying the layout path it is still
         * set */
        $layout = $this->plugin->getLayout();
        $this->assertEquals(APPLICATION_PATH . "/modules/{$moduleName}/views/
                layouts", $layout->getLayoutPath());
    }

    /**
     * @desc Mimics the LayoutSwitcher's behaviour when both the layout name and
     * path are given in a configuration file.
     */
    public function testThatLayoutPathIsRightWhenSpecifyingOne()
    {
        /* Buidling a configuration selector with testing layout options */
        $options = array('company' => array('resources' => array('layout'
                => array('layout' => 'default', 'layoutPath' => 'foo'))));
        $selector = new \Majisti\Config\Selector( new \Zend_Config($options) );

        /* Asserting that even without specifying the layout path it is still
         * set */
        $layout = $this->plugin->getLayout();
        $this->assertEquals('foo',
                $selector->find("company.resources.layout.layoutPath", false));
    }
}
LayoutSwitcherTest::runAlone();
