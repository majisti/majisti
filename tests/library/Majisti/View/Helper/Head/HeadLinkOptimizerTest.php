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
        /* files path */
        $this->files = realpath(dirname(__FILE__) . '/../_files');

        $this->view = new \Zend_View();
        $this->view->addHelperPath(
            'Majisti/View/Helper',
            'Majisti_View_Helper'
        );

        /* optimizer options setting path to the styles */
        $options = array(
            'path' => $this->files . '/styles',
        );

        $this->optimizer = new HeadLinkOptimizer($this->view->headLink(),
            $options);
        $this->optimizer->clearCache();

        /* clearing headlink data */
        $this->view->headLink()->exchangeArray(array());

        \Zend_Controller_Front::getInstance()->setRequest(
            new \Zend_Controller_Request_Http());

        /* setting optimizer jarfile and temporary directory */
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
        $files  = array('themes.css', 'all.css', 'all.min.css');
        $styles = array('theme1.min.css', 'theme2.min.css', 'core.min.css');

        foreach($files as $file) {
            @unlink($this->files . "/{$file}");
        }

        foreach($styles as $style) {
            @unlink($this->files . "/styles/{$style}");
        }

        $this->view->headLink()->exchangeArray(array());
    }

    /**
     * @desc Convenience function that appends stylesheets to the given
     * headlink object.
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
        $headlink   = $this->appendHeadlinkStylesheets($headlink,
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
                "/{$filename}.css?v=" . filemtime($this->files .
                "/{$filename}.css")   .
                '" media="screen" rel="stylesheet" type="text/css" />',
                $headlink->__toString()
        );

        /* files should  exist and contain the correct content */
        $this->assertTrue(file_exists($this->files . "/{$filename}.css"));
        $this->assertEquals(
                file_get_contents($this->files .
                "/{$filename}.bundled.expected.css"),
                file_get_contents($this->files . "/{$filename}.css")
        );
    }

    /**
     * Tests the optimize function and asserts that the core, theme1 and theme2
     * css files have been minified and bundled.
     */
    public function testOptimize()
    {
        $headlink   = $this->view->headLink();
        $optimizer  = $this->optimizer;
        $url        = $this->url;

        /* setting optimization on */
        $optimizer->setOptimizationEnabled();

        /* appending stylesheets to the headlink */
        $this->appendHeadlinkStylesheets($headlink,
                array('core', 'theme1', 'theme2'));

        /* appending an invalid link that should be preserved */
        $headlink->appendAlternate('/feed/', 'application/rss+xml', 'RSS Feed');
        $headlink->appendAlternate('/mydocument.pdf', "application/pdf", "foo",
                array('media'=>'print'));
        $headlink->appendAlternate('/mydocument2.pdf', "application/pdf", "bar",
                array('media'=>'screen'));

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

        /*
         * cached file paths should contain all files given to the optimize
         * function
         */
        $this->assertEquals($cachedFilesPaths, $optimizer->getCachedFilePaths());
        $this->assertTrue($optimizer->isBundlingEnabled());
        $this->assertTrue($optimizer->isMinifyingEnabled());
        $this->assertOptimized('all');
    }

    /**
     * @desc Asserts that the dot min file exists and that the content output
     * is the same as the expected one.
     */
    protected function assertOptimized($filename)
    {
        /** @var Majisti_View_Helper_HeadLink */
        $headlink   = $this->view->headlink();
        $url        = $this->url;

        /* asserting that invalid link was preserved */
        $array = (array)$headlink->getContainer();
        $this->assertEquals(4, count($array));
        $this->assertEquals('alternate', $array[0]->rel);

        /* files should contain the correct content */
        $this->assertTrue(file_exists($this->files . "/{$filename}.min.css"));
        $this->assertEquals(
                file_get_contents($this->files .
                    "/{$filename}.optimized.expected.css"),
                file_get_contents($this->files . "/{$filename}.min.css")
        );
     }

     /**
      * @desc Tests that setting a new cache file name will change the cache
      * file path.
      */
     public function testSettingNewCacheFileName()
     {
         $optimizer = $this->optimizer;
         $optimizer->setOptions(array(
             'cacheFile' => '.foo-cache',
             'path'      => $this->files . '/styles'
         ));

         $this->assertEquals($this->files . '/styles/.foo-cache',
                             $optimizer->getCacheFilePath());
     }

     /**
      * @desc Tests that uri remaps setters and getters behave as expected.
      */
     public function testUriRemappingGettersAndSetters()
     {
         $optimizer = $this->optimizer;
         $uris      = array('uri1' => 'pathA',
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
      * @desc Tests that the enabling/disabling the bundling/minifying behaves
      * as expected
      */
     public function testEnablingAndDisabling()
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

         $optimizer->setOptimizationEnabled();

         $this->assertTrue($optimizer->isBundlingEnabled());
         $this->assertTrue($optimizer->isMinifyingEnabled());
         $this->assertTrue($optimizer->isOptimizationEnabled());

         $optimizer->setOptimizationEnabled(false);

         $this->assertFalse($optimizer->isBundlingEnabled());
         $this->assertFalse($optimizer->isMinifyingEnabled());
         $this->assertFalse($optimizer->isOptimizationEnabled());
     }

     /**
      * @desc Tests that bundling will not overwrite the CSS master file
      * nor the cached stylesheet if * cache is enabled
      * and no modifications were found in the original files.
      */
     public function testThatNothingIsOverridenIfOrigFilesHaveNoChanges()
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

         $urlOptimize = $optimizer->optimize(
                    $this->files. '/all.css',
                    $url. '/all.css'
         );
         $content1      = file_get_contents($this->files . '/all.min.css');
         $filemtime1    = filemtime($this->files . '/all.min.css');
         $cachemtime1   = filemtime($this->files . '/styles/.stylesheets-cache');

         /* assure one second has passed */
         sleep(1);

         $headlink->exchangeArray(array());
         $this->appendHeadlinkStylesheets($headlink, array('core', 'theme1',
             'theme2'));

         $optimizer->optimize(
                    $this->files. '/all.css',
                    $url. '/all.css'
         );
         $content2      = file_get_contents($this->files . '/all.min.css');
         $filemtime2    = filemtime($this->files . '/all.min.css');
         $cachemtime2   = filemtime($this->files . '/styles/.stylesheets-cache');

         $this->assertSame($content1, $content2);
         $this->assertEquals($filemtime1, $filemtime2);
         $this->assertEquals($cachemtime1, $cachemtime2);
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

     /**
      * @desc Tests that the optimize function appends a version to the master
      * file generated.
      */
     public function testThatOptimizeFunctionAppendsAVersionToMasterFile()
     {
         $headlink  = $this->view->headLink();
         /** @var Majisti_View_Helper_HeadLink */
         $optimizer = $this->optimizer;
         $url       = $this->url;

         /* setting minifying and bundling on */
         $optimizer->setBundlingEnabled();
         $optimizer->setMinifyingEnabled();

         $this->appendHeadlinkStylesheets($headlink,
                 array('core', 'theme1', 'theme2'));

         $urlOptimize = $optimizer->optimize(
                    $this->files . '/all.css',
                    $url . '/all.css'
         );

         /* running optimize() a second time and asserting it returns false */
         $this->assertEquals($urlOptimize, $optimizer->optimize(
                 $this->files . '/all.css',
                 $url . '/all.css'
         ));

         /*
          * grabbing the master file object from the headlink after calling
          * optimize() twice
          */
         $twiceOptimized = $headlink->getIterator()->current();

         /* asserting that when running once, optimize() appends ?v=... */
         $this->assertTrue((boolean)substr_count($urlOptimize, '?v='));

         /*
          * asserting that when running more than once, optimize() also appends
          * ?v=... from the cache file.
          */
         $this->assertTrue((boolean)substr_count($twiceOptimized->href, '?v='));
     }

     /**
      * @desc Tests that no action is taken if bundling, minifying or both are
      * disabled.
      */
     public function testThatNoActionIsDoneIfBundlingAndOrMinifyingIsDisabled()
     {
         /** @var Majisti_View_Helper_HeadLink */
         $headlink  = $this->view->headLink();
         $optimizer = $this->optimizer;
         $url       = $this->url;

         /*
          * not supposed to do anything except returning false since nothing
          * is enabled
          */
         $urlOptimize = $optimizer->optimize(
                    $this->files. '/all.css',
                    $url . '/all.css'
         );
         $urlBundle   = $optimizer->bundle(
                    $this->files. '/all.css',
                    $url . '/all.css'
         );
         $urlMinify   = $optimizer->minify();

         $this->assertFalse($urlOptimize);
         $this->assertFalse($urlBundle);
         $this->assertFalse($urlMinify);
     }

     /**
      * @desc Tests that attempting to optimize, bundle and/or minify empty
      * files will throw exceptions.
      *
      * @expectedException Exception
      */
     public function testThatDealingWithEmptyFilesThrowsException()
     {
         /** @var Majisti_View_Helper_HeadLink */
         $headlink  = $this->view->headLink();
         $optimizer = $this->optimizer;
         $url       = $this->url;

         /* setting minifying and bundling on */
         $optimizer->setBundlingEnabled();
         $optimizer->setMinifyingEnabled();

         /* appending only an empty css file to the head....BAD */
         $this->appendHeadlinkStylesheets($headlink,
                 array('empty'));

         /* will throw exception */
         $urlOptimize = $optimizer->optimize(
                    $this->files. '/all.css',
                    $url. '/all.css'
         );
     }

     /**
      * @desc Tests that providing an invalid head object will throw an
      * exception once in the parseHeader() function.
      *
      * @expectedException Exception
      */
     public function testThatProvidingAnInvalidHeadObjectThrowsException()
     {
         /* headlink is not an instance of \Zend_View_Helper_HeadLink */
         $headlink  = new \stdClass();
         $optimizer = $this->optimizer;
         $url       = $this->url;

         /* setting minifying and bundling on */
         $optimizer->setBundlingEnabled();
         $optimizer->setMinifyingEnabled();

         /* will throw Exception! */
         $optimizer->optimize($this->files. '/all.css', $url. '/all.css');
     }

     /**
      * @desc Tests that when calling optimize and adding a new file afterwards
      * the master file will be rewritten to include the newly added file.
      */
     public function testThatAddingAFileAfterOptimizingWillRewriteMasterFile()
     {
         /** @var Majisti_View_Helper_HeadLink */
         $headlink  = $this->view->headLink();
         $optimizer = $this->optimizer;
         $url       = $this->url;

         /* setting minifying and bundling on */
         $optimizer->setBundlingEnabled();
         $optimizer->setMinifyingEnabled();

         /*
          * appending core and theme1 css to the head, will add theme2 after
          * optimizing
          */
         $this->appendHeadlinkStylesheets($headlink,
                 array('core', 'theme1'));

         $urlOptimize = $optimizer->optimize(
                    $this->files. '/all.css',
                    $url. '/all.css'
         );
         $content1 = file_get_contents($this->files . '/all.min.css');

         /* appending a new css file after having optimized */
         $headlink->exchangeArray(array());
         $this->appendHeadlinkStylesheets($headlink, array('core', 'theme1',
                                                           'theme2'));

         /* optimizing a second time after the theme2 css file was added */
         $urlSecondOptimize = $optimizer->optimize(
                 $this->files . '/all.css',
                 $url . '/all.css');
         $content2 = file_get_contents($this->files . '/all.min.css');

         /*
          * asserting that the two calls to optimize() return different
          * versions and not false
          */
         $this->assertNotEquals(false, $urlSecondOptimize);
         $this->assertNotSame($content1, $content2);
         $this->assertNotEquals(
                 false,
                 array_search($this->files . '/styles/theme2.css',
                 $optimizer->getCachedFilePaths()
                 )
         );
     }
}

HeadLinkOptimizerTest::runAlone();
