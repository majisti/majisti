<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

class JavascriptTest extends \Majisti\Test\TestCase
{
    /**
     * @desc Creates the resource.
     * @param array $options The options
     *
     * @return View The view
     */
    protected function createResource($options = null)
    {
        $js = new Javascript($options);
        $js->setBootstrap($this->getHelper()->createBootstrapInstance());

        return $js;
    }

    public function testInit()
    {
        $resource = $this->createResource();
        $resource->init();
        $view = $resource->getBootstrap()->getResource('view');

        $this->assertNotNull($view);

        /* jquery must not be enabled */
        $this->assertFalse($view->jQuery()->isEnabled());
        $this->assertFalse($view->jQuery()->uiIsEnabled());
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
        $resource->init();
        $view = $resource->getBootstrap()->getResource('view');

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
                'path' => 'http://example.com/jquery.js',
                'ui' => array(
                    'path' => 'http://example.com/ui.js'
                )
            )
        );

        $resource = $this->createResource($jqueryWithPathsOnly);
        $resource->init();
        $view = $resource->getBootstrap()->getResource('view');

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
                'path' => 'http://example.com/jquery.js',
                'ui' => array(
                    'enable' => 0,
                    'path' => 'http://example.com/ui.js'
                )
            )
        );

        $resource = $this->createResource($jquery);
        $resource->init();
        $view = $resource->getBootstrap()->getResource('view');

        $this->assertFalse($view->jQuery()->isEnabled());
        $this->assertFalse($view->jQuery()->uiIsEnabled());

        $this->assertEquals($localPath, $view->jQuery()->getLocalPath());
        $this->assertEquals($uiLocalPath, $view->jQuery()->getUiLocalPath());
    }
}

JavascriptTest::runAlone();
