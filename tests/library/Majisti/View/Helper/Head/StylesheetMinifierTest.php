<?php

namespace Majisti\View\Helper\Head;

require_once 'TestHelper.php';

/**
 * @desc
 * @author
 */
class StylesheetMinifierTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var Array
     */
    public $files;

    /**
     * @var \Zend_View
     */
    public $view;

    /**
     * @var StylesheetMinifier
     */
    public $minifier;

    /**
     * @var string
     */
    public $url = '/majisti/tests/library/Majisti/View/Helper/_files';

    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->files = dirname(__FILE__) . '/../_files';

        $this->view = new \Zend_View();
        $this->view->addHelperPath(
                'Majisti/View/Helper',
                'Majisti_View_Helper'
        );

        $this->minifier = new \Majisti\View\Helper\Head\StylesheetMinifier();

        \Zend_Controller_Front::getInstance()->setRequest(
            new \Zend_Controller_Request_Http());

//        \Majisti\Util\Compression\Yui::$jarFile = MAJISTI_ROOT . '/externals/yuicompressor-2.4.2.jar';
//        \Majisti\Util\Compression\Yui::$tempDir = '/tmp';
    }

    public function tearDown()
    {
        @unlink($this->files . '/themes.css');
        @unlink($this->files . '/all.css');
        @unlink($this->files . '/.cached-stylesheets');
        $this->view->headLink()->exchangeArray(array());
    }

    public function testMinify()
    {
        /* @var $headlink \Majisti_View_Helper_HeadLink */
        $headlink   = $this->view->headLink();
        $minifier   = $this->minifier;
        $url        = $this->url;

        $minifier->setEnabled();

        /* append and bundle stylesheets */
        $headlink->appendStylesheet("{$url}/styles/theme1.css");
        $headlink->appendStylesheet("{$url}/styles/theme2.css");
        $headlink->minify(
                $this->files . '/themes.css',
                $url . '/themes.css',
                $minifier
        );

        $this->assertBundled('themes');
    }

    /**
     * @desc Asserts that any added files with mapped uris will get bundled
     * under the same master file under the same url.
     * 
     * Any stylesheets found with the same uri within the HeadLink must
     * be removed.
     */
    public function testAddFileRemapOverideHeadLinkDuplicates()
    {
       $headlink    = $this->view->headLink();
       $minifier    = $this->minifier;
       $url         = $this->url;

       $minifier->setEnabled();

       $request = \Zend_Controller_Front::getInstance()->getRequest();
       $uri     = $request->getScheme() . ':/' . $url;

       $headlink->appendStylesheet($uri . '/styles/theme1.css');
       $headlink->appendStylesheet($url . '/styles/theme2.css');
       
       $minifier->uriRemap($uri . '/styles/theme1.css',
               $this->files  . '/styles/theme1.css');

       $minifier->minify($headlink, $this->files . '/themes.css',
               $url . '/themes.css');

       $this->assertBundled('themes');
    }

    protected function assertBundled($filename)
    {
        $headlink   = $this->view->headlink();
        $url        = $this->url;

        /* file link should contain only the bundled file */
        $this->assertEquals(
                '<link href="' . $url .
                "/{$filename}.css?v=". filemtime($this->files . "/{$filename}.css") .
                '" media="screen" rel="stylesheet" type="text/css" >',
                $headlink->__toString()
        );

        /* files should contain the correct content */
        $this->assertTrue(file_exists($this->files . "/{$filename}.css"));
        $this->assertEquals(
                file_get_contents($this->files . "/{$filename}.expected.css"),
                file_get_contents($this->files . "/{$filename}.css")
        );
    }
}

StylesheetMinifierTest::runAlone();
