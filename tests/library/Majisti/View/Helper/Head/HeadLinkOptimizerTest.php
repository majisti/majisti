<?php

namespace Majisti\View\Helper\Head;

require_once 'TestHelper.php';

/**
 * @desc Tests that the stylesheet optimizer can bundle and minify
 * css stylesheets correctly.
 *
 * @author Majisti
 */
class HeadLinkOptimizerTest extends \Majisti\Test\TestCase
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
     * @var HeadLinkOptimizer
     */
    public $optimizer;

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
        $this->optimizer = new HeadLinkOptimizer($this->view->headLink(),
            $options);
        $this->optimizer->clearCache();

        \Zend_Controller_Front::getInstance()->setRequest(
            new \Zend_Controller_Request_Http());

        \Majisti\Util\Minifying\Yui::$jarFile = MAJISTI_ROOT .
             '/../externals/yuicompressor-2.4.2.jar';
        \Majisti\Util\Minifying\Yui::$tempDir = '/tmp';
    }

    /**
     * @desc Unit test tear down: removing the bundle() and minify() output
     * files and clearing the headlink object.
     */
    public function tearDown()
    {
        $files = array('themes.css', 'all.css', 'all.min.css',
            '.cache-stylesheets');

        $minified = array('theme1.min.css', 'theme2.min.css', 'core.min.css');

        foreach($files as $file) {
            @unlink($this->files . "/{$file}");
        }

        foreach($minified as $file) {
            @unlink($this->files . "/styles/{$file}");
        }

        $this->view->headLink()->exchangeArray(array());
    }

    /**
     *
     * @param \Majisti_View_Helper_Headlink $headlink
     * @param array $sheets an array of stylesheets name located in _files/styles
     * @return \Majisti_View_Helper_Headlink headlink with appenend stylesheets
     */
    protected function appendHeadlinkStylesheets($headlink, $sheets = array())
    {
        $url = $this->url;
        if( !empty ( $sheets) ) {
            foreach($sheets as $sheet) {
                $headlink->appendStylesheet("{$url}/styles/{$sheet}.css");
            }
        }

        return $headlink;
    }

    /**
     * @desc Asserts that stylesheets get bundled in a master file
     */
    public function testBundle()
    {
        /* @var $headlink \Majisti_View_Helper_HeadLink */
        $headlink   = $this->view->headLink();
        $optimizer  = $this->optimizer;
        $url        = $this->url;

        $optimizer->setBundlingEnabled();

        /* append and bundle stylesheets */
        $headlink = $this->appendHeadlinkStylesheets($headlink,
                array('theme1', 'theme2'));

        $optimizer->bundle(
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
       $optimizer   = $this->optimizer;
       $url         = $this->url;

       $optimizer->setBundlingEnabled();

       $request = \Zend_Controller_Front::getInstance()->getRequest();
       $uri     = $request->getScheme() . ':/' . $url;

       $headlink->appendStylesheet($uri . '/styles/theme1.css');
       $headlink->appendStylesheet($url . '/styles/theme2.css');
       
       $optimizer->uriRemap($uri . '/styles/theme1.css',
               $this->files  . '/styles/theme1.css');

       $optimizer->bundle($this->files . '/themes.css',
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
     * Tests the optimize function and asserts that the core, theme1 and theme2
     * css files have been minified and bundled.
     */
    public function testOptimize()
    {
        /* @var $headlink \Majisti_View_Helper_HeadLink */
        $headlink   = $this->view->headLink();
        $optimizer  = $this->optimizer;
        $url        = $this->url;

        /* setting minify on */
        $optimizer->setBundlingEnabled();
        $optimizer->setMinifyingEnabled();

        /* URLs */
        $cssFilesUrls = array(
                "{$url}/styles/core.css",
                "{$url}/styles/theme1.css",
                "{$url}/styles/theme2.css"
        );

        /* append and bundle stylesheets */
        foreach( $cssFilesUrls as $path) {
            $headlink->appendStylesheet($path);
        }

        $optimizer->optimize(
                $this->files. '/all.css',
                $url. '/all.css'
        );

        /* optimize() function returns absolute paths from server root */
        $cachedFilesPaths = array(
                "{$this->files}/styles/core.css",
                "{$this->files}/styles/theme1.css",
                "{$this->files}/styles/theme2.css",
                "{$this->files}/all.css"
        );
        $this->assertEquals($cachedFilesPaths, $optimizer->getCachedFilePaths());
        $this->assertTrue($optimizer->isBundlingEnabled());
        $this->assertTrue($optimizer->isMinifyingEnabled());
        $this->assertOptimized('all');
    }

    /**
     * @desc Asserts that core, theme1 and theme2 CSS have been minified and
     * that the cachedFilePaths array has been set with the right files.
     */
    protected function assertOptimized($filename)
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
         $optimizer = $this->optimizer;
         $optimizer->setOptions(array(
             'cacheFile'        => '.foo-cache',
             'stylesheetsPath'  => $this->files . '/styles'
         ));

         $this->assertEquals($this->files . '/styles/.foo-cache',
                             $optimizer->getCacheFilePath());
     }

     /**
      * @desc Tests that uri remaps setters and getters behave as expected.
      */
     public function testUriRemappingGettersAndSetters()
     {
         $optimizer  = $this->optimizer;

         $uris        = array('uri1' => 'pathA',
                              'uri2' => 'pathB',
                              'uri3' => 'pathC',
                              'uri4' => 'pathD'
         );

         foreach( $uris as $uri => $path) {
             $optimizer->uriRemap($uri, $path);
         }

         $this->assertEquals($uris, $optimizer->getRemappedUris());

         $optimizer->removeUriRemap('uri2');

         $this->assertEquals(3, count($optimizer->getRemappedUris()));
         $this->assertArrayNotHasKey('uri2', $optimizer->getRemappedUris());

         $optimizer->clearUriRemaps();

         $this->assertEquals(0, count($optimizer->getRemappedUris()));
     }

     /**
      * @desc Tests that the enabling/disabling behaves as expected
      */
     public function testEnablingAndDisablingBundlingAndMinifying()
     {
         $optimizer = $this->optimizer;
         $optimizer->setBundlingEnabled();
         $optimizer->setMinifyingEnabled();

         $this->assertTrue($optimizer->isBundlingEnabled());
         $this->assertTrue($optimizer->isMinifyingEnabled());

         $optimizer->setBundlingEnabled(false);
         $optimizer->setMinifyingEnabled(false);

         $this->assertFalse($optimizer->isBundlingEnabled());
         $this->assertFalse($optimizer->isMinifyingEnabled());
     }

     /**
      * @desc Tests that bundling will not overwrite the CSS master file if
      * cache is enabled and no modifications were found in the original files.
      */
     public function testThatMasterIsNotOverridenIfOrigFilesHaveNoChanges()
     {
         /* @var $headlink \Majisti_View_Helper_HeadLink */
         $headlink   = $this->view->headLink();
         $optimizer  = $this->optimizer;
         $url        = $this->url;

         /* setting minify on */
         $optimizer->setBundlingEnabled();
         $optimizer->setMinifyingEnabled();

         $this->appendHeadlinkStylesheets($headlink,
                 array('core', 'theme1', 'theme2'));

         $url1 = $optimizer->optimize(
                    $this->files. '/all.css',
                    $url. '/all.css'
         );

         $headlink->exchangeArray(array());
         $this->appendHeadlinkStylesheets($headlink, array('core', 'theme1',
             'theme2'));

         $optimizer->optimize(
                    $this->files. '/all.css',
                    $url. '/all.css'
         );
    }

     /**
      * @desc Making sure that every file we give to the minifier will output
      * a <filename>.min.css file.
      */
     public function testThatEveryFileGivenToTheMinifierOutputsADotMinFile()
     {
         /* @var $headlink \Majisti_View_Helper_HeadLink */
         $headlink   = $this->view->headLink();
         $optimizer  = $this->optimizer;
         $url        = $this->url;

         /* setting minifying and bundling on */
         $optimizer->setBundlingEnabled();
         $optimizer->setMinifyingEnabled();

         $this->appendHeadlinkStylesheets($headlink,
                 array('core', 'theme1', 'theme2'));

         $optimizer->minify();

         $this->assertTrue(file_exists($this->files . '/styles/core.min.css'));
         $this->assertTrue(file_exists($this->files . '/styles/theme1.min.css'));
         $this->assertTrue(file_exists($this->files . '/styles/theme2.min.css'));
     }
}

HeadLinkOptimizerTest::runAlone();
