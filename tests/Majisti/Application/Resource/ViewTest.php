<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

/**
 * @desc Asserts that the view resource setups a default view for Majisti
 * applications
 *
 * @author Majisti
 */
class ViewTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Majisti\Application\Resource\View
     */
    public $resource;

    /**
     * @var Array
     */
    public $expectedPaths = array(
        'Zend_View_Helper_'             => array('Zend/View/Helper/'),
        'Majisti_View_Helper_'          => array('Majisti/View/Helper/'),
        'Majisti\View\Helper\\'         => array('Majisti/View/Helper/'),
        'MajistiX_View_Helper_'         => array('MajistiX/View/Helper/'),
        'MajistiX\View\Helper\\'        => array('MajistiX/View/Helper/'),
        'Majisti_Test_View_Helper_'     => array('/views/helpers/'),
        'Majisti_Test\View\Helper\\'    => array('/views/helpers/'),
        'ZendX_JQuery_View_Helper_'     => array('ZendX/JQuery/View/Helper/'),
    );

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $config = \Zend_Registry::get('Majisti_Config');
        $this->resource = new View($config->resources->view);

        /* prepend library paths to those keys */
        $keys = array('Majisti_Test_View_Helper_', 'Majisti_Test\View\Helper\\');
        foreach( $keys as $key ) {
            $this->expectedPaths[$key][0] =
                    APPLICATION_LIBRARY . $this->expectedPaths[$key][0];
        }
    }

    /**
     * @desc Asserts that the view gets initialized correctly
     */
    public function testInit()
    {
        $resource = $this->resource;

        $view = $this->resource->init();

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

        $this->resource = new View($jquery);
        $view = $this->resource->init();

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

        $this->resource = new View($jqueryWithPathsOnly);
        $view = $this->resource->init();

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

        $this->resource = new View($jquery);
        $view = $this->resource->init();

        $this->assertFalse($view->jQuery()->isEnabled());
        $this->assertFalse($view->jQuery()->uiIsEnabled());

        $this->assertEquals($localPath, $view->jQuery()->getLocalPath());
        $this->assertEquals($uiLocalPath, $view->jQuery()->getUiLocalPath());
    }
}

ViewTest::runAlone();
