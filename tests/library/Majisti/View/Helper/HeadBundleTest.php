<?php

namespace Majisti\View\Helper;

require_once 'TestHelper.php';

/**
 * @desc
 * @author
 */
class HeadBundleTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    public $files;

    /**
     * @var \Zend_View
     */
    public $view;

    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->files = dirname(__FILE__) . '/_files';

        $this->view = new \Zend_View();

        $this->view->addHelperPath('Majisti/View/Helper', 'Majisti_View_Helper');

//        \Majisti\Util\Compression\Yui::$jarFile = MAJISTI_ROOT . '/externals/yuicompressor-2.4.2.jar';
//        \Majisti\Util\Compression\Yui::$tempDir = '/tmp';
    }

    public function testBundleCss()
    {
        $bundler = $this->view->headBundle();
        $styles  = $this->files . '/styles';

        $bundler->appendBundle('themeStyles', 'HeadLink', $this->files . '/themes.css');

        $bundler->bundle('themeStyles')->appendStylesheet("{$styles}/theme1.css");
        $bundler->bundle('themeStyles')->appendStylesheet("{$styles}/theme2.css");

        $this->assertEquals(
            '<link href="' . $this->files .
            '/themes.css" media="screen" rel="stylesheet" type="text/css" />',
            $bundler->__toString()
        );

        $this->assertTrue(file_exists($this->files . '/themes.css'));

        $this->assertEquals(
            ".theme1 {\n    color: green;}\n.theme2 {\n    color: blue;}",
            file_get_contents($this->files . '/themes.css')
        );


//        \Majisti\Util\Compression\Yui::minifyCss($content)
    }
}

HeadBundleTest::runAlone();
