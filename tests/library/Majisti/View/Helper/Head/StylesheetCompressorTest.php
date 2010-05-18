<?php

namespace Majisti\View\Helper\Head;

require_once 'TestHelper.php';

/**
 * @desc Tests that the stylesheet compressor can bundle and minify
 * css stylesheets correctly.
 *
 * @author Majisti
 */
class StylesheetCompressorTest extends \Majisti\Test\TestCase
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
     * @var StylesheetCompressor
     */
    public $compressor;

    /**
     * @var string
     */
    public $url = '/majisti/tests/library/Majisti/View/Helper/_files';

    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->files = realpath(dirname(__FILE__) . '/../_files');

        $this->view = new \Zend_View();
        $this->view->addHelperPath(
            'Majisti/View/Helper',
            'Majisti_View_Helper'
        );

        $options = array(
            'stylesheetsPath' => $this->files . '/styles',
        );
        $this->compressor = new StylesheetCompressor($options);

        \Zend_Controller_Front::getInstance()->setRequest(
            new \Zend_Controller_Request_Http());

        \Majisti\Util\Minifying\Yui::$jarFile = MAJISTI_ROOT .
             '/../externals/yuicompressor-2.4.2.jar';
        \Majisti\Util\Minifying\Yui::$tempDir = '/tmp';
    }

    /**
     * @desc Unit test tear down.
     */
    public function tearDown()
    {
        @unlink($this->files . '/themes.css');
        @unlink($this->files . '/all.css');
        @unlink($this->files . '/.cached-stylesheets');
        $this->view->headLink()->exchangeArray(array());
    }

    /**
     * @desc Asserts that stylesheets get bundled in a master file
     */
    public function testBundle()
    {
        /* @var $headlink \Majisti_View_Helper_HeadLink */
        $headlink   = $this->view->headLink();
        $compressor = $this->compressor;
        $url        = $this->url;

        $compressor->setBundlingEnabled();

        /* append and bundle stylesheets */
        $headlink->appendStylesheet("{$url}/styles/theme1.css");
        $headlink->appendStylesheet("{$url}/styles/theme2.css");

        $compressor->bundle(
                $headlink,
                $this->files. '/themes.css',
                $url . '/themes.css'
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
       $compressor  = $this->compressor;
       $url         = $this->url;

       $compressor->setBundlingEnabled();

       $request = \Zend_Controller_Front::getInstance()->getRequest();
       $uri     = $request->getScheme() . ':/' . $url;

       $headlink->appendStylesheet($uri . '/styles/theme1.css');
       $headlink->appendStylesheet($url . '/styles/theme2.css');
       
       $compressor->uriRemap($uri . '/styles/theme1.css',
               $this->files  . '/styles/theme1.css');

       $compressor->bundle($headlink, $this->files . '/themes.css',
               $url . '/themes.css');

       $this->assertBundled('themes');
    }

    /**
     * @desc Asserts that the headlink contains only the master CSS file
     * and that the master file in question contains all the CSS files bundled.
     *
     * @param string $filename
     */
    protected function assertBundled($filename)
    {
        $headlink   = $this->view->headlink();
        $url        = $this->url;

        /* file link should contain only the bundled file */
        $this->assertEquals(
                '<link href="' . $url .
                "/{$filename}.css?v=". filemtime($this->files . "/{$filename}.css") .
                '" media="screen" rel="stylesheet" type="text/css" />',
                $headlink->__toString()
        );

        /* files should contain the correct content */
        $this->assertTrue(file_exists($this->files . "/{$filename}.css"));
        $this->assertEquals(
                file_get_contents($this->files . "/{$filename}.bundled.expected.css"),
                file_get_contents($this->files . "/{$filename}.css")
        );
    }

    /**
     * Tests the compress function and asserts that the core, theme1 and theme2
     * css files have been minified and bundled.
     */
    public function testCompress()
    {
        /* @var $headlink \Majisti_View_Helper_HeadLink */
        $headlink   = $this->view->headLink();
        $compressor = $this->compressor;
        $url        = $this->url;

        /* setting minify on */
        $compressor->setBundlingEnabled();
        $compressor->setMinifyEnabled();

        /* URLs */
        $cssFilesUrls = array(
                "{$url}/styles/core.css",
                "{$url}/styles/theme1.css",
                "{$url}/styles/theme2.css"
        );

        /* append and bundle stylesheets */
        foreach( $cssFilesUrls as $path) {
            $headlink->appendStyleSheet($path);
        }

        $compressor->compress(
                $headlink,
                $this->files. '/all.css',
                $url. '/all.css'
        );

        /* compress() function returns absolute paths from server root */
        $cachedFilesPaths = array(
                "{$this->files}/styles/core.css",
                "{$this->files}/styles/theme1.css",
                "{$this->files}/styles/theme2.css"
        );
        $this->assertEquals($cachedFilesPaths, $compressor->getCachedFilePaths());
        $this->assertTrue($compressor->isBundlingEnabled());
        $this->assertTrue($compressor->isMinifyingEnabled());
        $this->assertMinified('all');
    }

    /**
     * @desc Asserts that core, theme1 and theme2 CSS have been minified and
     * that the cachedFilePaths array has been set with the right files.
     */
    protected function assertCompressed($fileName)
    {
        $headlink   = $this->view->headlink();
        $url        = $this->url;

        /* files should contain the correct content */
        $this->assertTrue(file_exists($this->files . "/{$filename}.min.css"));
        $this->assertEquals(
                file_get_contents($this->files . "/{$filename}.minified.expected.css"),
                file_get_contents($this->files . "/{$filename}.min.css")
        );
     }

     /**
      * Tests that setting a new cache file name will change the cache file
      * path.
      */
     public function testSettingNewCacheFileName()
     {
         $compressor = $this->compressor;
         $compressor->setOptions(array(
             'cacheFile'        => '.foo-cache',
             'stylesheetsPath'  => $this->files . '/styles'
         ));

         $this->assertEquals($this->files . '/styles/.foo-cache',
                             $compressor->getCacheFilePath());
     }

     /**
      * @desc Tests that uri remaps setters and getters behave as expected.
      */
     public function testUriRemappingGettersAndSetters()
     {
         $compressor  = $this->compressor;

         $uris        = array('uri1' => 'pathA',
                              'uri2' => 'pathB',
                              'uri3' => 'pathC',
                              'uri4' => 'pathD'
         );

         foreach( $uris as $uri => $path) {
             $compressor->uriRemap($uri, $path);
         }

         $this->assertEquals($uris, $compressor->getRemappedUris());

         $compressor->removeUriRemap('uri2');

         $this->assertEquals(3, count($compressor->getRemappedUris()));
         $this->assertArrayNotHasKey('uri2', $compressor->getRemappedUris());

         $compressor->clearUriRemaps();

         $this->assertEquals(0, count($compressor->getRemappedUris()));
     }

     /**
      * @desc Tests that the enabling/disabling behaves as expected
      */
     public function testEnablingAndDisablingBundlingAndMinifying()
     {
         $compressor = $this->compressor;
         $compressor->setBundlingEnabled();
         $compressor->setMinifyingEnabled();

         $this->assertTrue($compressor->isBundlingEnabled());
         $this->assertTrue($compressor->isMinifyingEnabled());

         $compressor->setBundlingEnabled(false);
         $compressor->setMinifyingEnabled(false);

         $this->assertFalse($compressor->isBundlingEnabled());
         $this->assertFalse($compressor->isMinifyingEnabled());
     }

     /**
      * @desc Tests that bundling will not overwrite CSS master file if
      * cache is enabled and no modifications were found in the master content.
      */
     public function testThatCacheIsNotOverridenIfMasterFileHasNoChanges()
     {
         $this->markTestIncomplete('Cache support not yet implemented!');
         /* @var $headlink \Majisti_View_Helper_HeadLink */
         $headlink   = $this->view->headLink();
         $compressor = $this->compressor;
         $url        = $this->url;

         /* setting minify on */
         $compressor->setBundlingEnabled();
         $compressor->setMinifyEnabled();

         $cssFiles = array(
             "{$url}/styles/core.css",
             "{$url}/styles/theme1.css",
             "{$url}/styles/theme2.css"
         );

         /* append and bundle stylesheets */
         foreach( $cssFiles as $path) {
             $headlink->appendStyleSheet($path);
         }

         $url1 = $compressor->compress(
                    $headlink,
                    $this->files. '/all.css',
                    $url. '/all.css'
         );

         $url2 = $compressor->compress(
                    $headlink,
                    $this->files. '/all.css',
                    $url. '/all.css'
         );

        $this->assertEquals($url1, $url2);
     }
}
StylesheetCompressorTest::runAlone();