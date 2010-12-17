<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

/**
 * @desc Asserts that the view resource setups a default view for Majisti
 * applications.
 *
 * @author Majisti
 */
class ViewTest extends \Majisti\Test\TestCase
{
    /**
     * @var \Majisti\Application\Resource\View
     */
    public $resource;

    /**
     * @var Array
     */
    public $expectedPaths = array(
        'Zend_View_Helper_'             => array('Zend/View/Helper/'),
        'ZendX_JQuery_View_Helper_'     => array('ZendX/JQuery/View/Helper/'),
        'Majisti\View\Helper\\'         => array('Majisti/View/Helper/'),
        'MajistiT\View\Helper\\'        => array('/views/helpers/'),
    );

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $helper  = $this->getHelper();
        $options = $helper->getOptions();

        /* prepend library paths to those keys */
        $keys = array('MajistiT\View\Helper\\');
        foreach( $keys as $key ) {
            $this->expectedPaths[$key][0] =
                $options['majisti']['app']['path'] .
                    '/library' . $this->expectedPaths[$key][0];
        }
    }

    /**
     * @desc Creates the resource.
     * @param array $options The options
     *
     * @return View The view
     */
    protected function createResource($options = null)
    {
        $view = new View($options);
        $view->setBootstrap($this->getHelper()->createBootstrapInstance());

        return $view;
    }

    /**
     * @desc Asserts that the view gets initialized correctly
     */
    public function testInit()
    {
        $resource = $this->createResource();
        $view = $resource->init();

        /* jquery must not be enabled */
        $this->assertFalse($view->jQuery()->isEnabled());
        $this->assertFalse($view->jQuery()->uiIsEnabled());

        /* paths */
        $this->assertEquals($this->expectedPaths, $view->getHelperPaths());

        $this->assertEquals(\Zend_View_Helper_Doctype::XHTML1_STRICT,
                $view->doctype()->getDoctype());

        $this->assertTrue(\Zend_Registry::isRegistered('Zend_View'));
    }

    /**
     * @desc Asserts that jquery is enabled accordingly
     */
    public function testInitWithJQueryIsEnabled()
    {
        $jquery = array(
            'jquery' => array(
                'enable' => 1,
                'ui' => array('enable' => 1)
            )
        );

        $resource = $this->createResource($jquery);
        $view = $resource->init();

        $this->assertTrue($view->jQuery()->isEnabled());
        $this->assertTrue($view->jQuery()->uiIsEnabled());
    }

    /**
     * @desc Asserts that when specifying paths only, jquery gets also
     * enabled along the way.
     */
    public function testInitWithJQueryPathsOnlyEnables()
    {
        $localPath = 'http://example.com/jquery.js';
        $uiLocalPath = 'http://example.com/ui.js';

        $jqueryWithPathsOnly = array(
            'jquery' => array(
                'localPath' => 'http://example.com/jquery.js',
                'ui' => array(
                    'localPath' => 'http://example.com/ui.js'
                )
            )
        );

        $resource = $this->createResource($jqueryWithPathsOnly);
        $view = $resource->init();

        $this->assertTrue($view->jQuery()->isEnabled());
        $this->assertTrue($view->jQuery()->uiIsEnabled());

        $this->assertEquals($localPath, $view->jQuery()->getLocalPath());
        $this->assertEquals($uiLocalPath, $view->jQuery()->getUiLocalPath());
    }

    /**
     * @desc Asserts that even if path are specified, if jquery is explicitely
     * disabled, it should be disabled
     */
    public function testInitWithJQueryPathsButDisabledInConfigStaysDisabled()
    {
        $localPath      = 'http://example.com/jquery.js';
        $uiLocalPath    = 'http://example.com/ui.js';

        $jquery = array(
            'jquery' => array(
                'enable' => 0,
                'localPath' => 'http://example.com/jquery.js',
                'ui' => array(
                    'enable' => 0,
                    'localPath' => 'http://example.com/ui.js'
                )
            )
        );

        $resource = $this->createResource($jquery);
        $view = $resource->init();

        $this->assertFalse($view->jQuery()->isEnabled());
        $this->assertFalse($view->jQuery()->uiIsEnabled());

        $this->assertEquals($localPath, $view->jQuery()->getLocalPath());
        $this->assertEquals($uiLocalPath, $view->jQuery()->getUiLocalPath());
    }
}

ViewTest::runAlone();
