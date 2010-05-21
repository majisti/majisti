<?php

namespace Majisti\View\Helper\Head;

require_once 'TestHelper.php';

/**
 * @desc Tests that the stylesheet optimizer can bundle and minify
 * css stylesheets correctly.
 *
 * @author Majisti
 */
abstract class AbstractHeadOptimizerTest extends \Majisti\Test\TestCase
{

    static protected $_class = __CLASS__;

    /**
     * @var string
     */
    protected $filesPath;

    /**
     * @var string
     */
    protected $filesUrl;

    /**
     * @var array
     */
    protected $files = array();

    /**
     * @var array
     */
    protected $outputFiles = array();

    /**
     * @var string
     */
    protected $folder;

    /**
     * @var \Zend_View
     */
    protected $view;

    /**
     * @var AbstractOptimizer
     */
    protected $optimizer;

    /**
     * @var \Majisti_View_Helper_Abstract
     */
    protected $headObject;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var array
     */
    protected $options = array();

    public function __construct()
    {
        $this->filesPath = realpath(dirname(__FILE__) . '/../_files');
        $this->filesUrl  = '/majisti/tests/library/Majisti/View/Helper/_files';
    }

    /**
     * @desc Unit test tear down: removing the bundle() and minify() output
     * files and clearing the headlink object.
     */
    public function tearDown()
    {
        foreach( $this->outputFiles as $file ) {
            @unlink( $this->filesPath . "/{$folder}/{$file}" );
        }

        $this->clearHead();
    }

    /**
     * @desc TODO
     */
    protected function clearHead()
    {
        $this->view->headLink()->exchangeArray(array());
        $this->view->headScript()->exchangeArray(array());
    }

    /**
     * @desc TODO
     */
    protected function appendFilesToHead($files = array())
    {
        foreach( $files as $file ) {
            $this->headObject->append($file);
        }
    }

    protected abstract function getHeaderOutput($filename);
    protected abstract function getFilesObjects($files = array());
    protected abstract function appendInvalidFiles($headObject);

    /**
     * @desc Asserts that stylesheets get bundled in a master file
     */
    public function testBundle()
    {
        /* @var $headlink \Majisti_View_Helper_HeadLink */
        $headObj    = $this->headObject;
        $optimizer  = $this->optimizer;
        $url        = $this->filesUrl;

        $optimizer->setBundlingEnabled();

        /* append and bundle files */
        $this->appendFilesToHead($this->files);

        $optimizer->bundle(
                $this->filesPath . "/all{$this->extension}",
                $url . "/all{$this->extension}"
        );

        $this->assertBundled('all');
    }

    /**
     * @desc Asserts that the headlink contains only the master CSS file
     * and that the master file in question contains all the CSS files bundled.
     *
     * @param string $filename
     */
    protected function assertBundled($filename)
    {
        $headObj = $this->headObject;
        $ext     = $this->extension;

        /* file link should contain only the bundled file */
        $this->assertEquals($this->getHeaderOutput($filename),
                $headObj->__toString());

        /* files should  exist and contain the correct content */
        $this->assertTrue(file_exists($this->filesPath .
                "/{$filename}{$ext}"));
        $this->assertEquals(
                file_get_contents($this->filesPath .
                "/{$filename}.bundled.expected{$ext}"),
                file_get_contents($this->filesPath .
                        "/{$filename}{$ext}")
        );
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
       $headObj     = $this->headObject;
       $optimizer   = $this->optimizer;
       $url         = $this->filesUrl;
       $ext         = $this->extension;

       $optimizer->setBundlingEnabled();

       $request = \Zend_Controller_Front::getInstance()->getRequest();
       $uri     = $request->getScheme() . ':/' . $url;

       $files = $this->getFilesObjects(array("file1{$ext}", "file2{$ext}"));
       $this->appendFilesToHead($files);

       $optimizer->uriRemap($uri . "/{$this->folder}/file1{$ext}",
               $this->filesPath  . "/{$this->folder}/file1{$ext}");

       $optimizer->bundle($this->filesPath . "/files{$ext}",
               $url . "/files{$ext}");

       $this->assertBundled('files');
    }

    /**
     * Tests the optimize function and asserts that the core, theme1 and theme2
     * css files have been minified and bundled.
     */
    public function testOptimize()
    {
        $headObj   = $this->headObject;
        $optimizer = $this->optimizer;
        $url       = $this->filesUrl;
        $ext       = $this->extension;

        /* setting optimization on */
        $optimizer->setOptimizationEnabled();

        /* appending files to the head */
        $this->appendFilesToHead($this->files);

        $optimizer->optimize(
                $this->filesPath. "/all{$ext}",
                $url. "/all{$ext}"
        );

        /* optimize() function returns absolute paths from server root */
        $cachedFilesPaths = array(
                "{$this->filesPath}/{$this->folder}/core{$ext}",
                "{$this->filesPath}/{$this->folder}/file1{$ext}",
                "{$this->filesPath}/{$this->folder}/file2{$ext}",
                "{$this->filesPath}/all{$ext}"
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
        $headObj = $this->headObject;
        $url     = $this->filesUrl;
        $ext     = $this->extension;

        /* files should contain the correct content */
        $this->assertTrue(file_exists($this->filesPath . "/{$filename}.min{$ext}"));
        $this->assertEquals(
                file_get_contents($this->filesPath .
                    "/{$filename}.optimized.expected{$ext}"),
                file_get_contents($this->filesPath . "/{$filename}.min{$ext}")
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
             'path'      => $this->filesPath . "/{$this->folder}"
         ));

         $this->assertEquals($this->filesPath . "/{$this->folder}/.foo-cache",
                             $optimizer->getCacheFilePath());
     }

     /**
      * @desc Tests that uri remaps setters and getters behave as expected.
      */
     public function testUriRemappingGettersAndSetters()
     {
         $this->markTestSkipped('implementation changed');

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
         /* @var $headObj \Majisti_View_Helper_HeadLink */
         $headObj    = $this->headObject;
         $optimizer  = $this->optimizer;
         $url        = $this->filesUrl;
         $ext        = $this->extension;

         /* setting minify on */
         $optimizer->setOptimizationEnabled();

         $this->appendFilesToHead($this->files);

         $urlOptimize = $optimizer->optimize(
                $this->filesPath . "/all{$ext}",
                $url . "/all{$ext}"
         );
         $content1      = file_get_contents($this->filesPath . "/all.min{$ext}");
         $filemtime1    = filemtime($this->filesPath . "/all.min{$ext}");
         $cachemtime1   = filemtime($this->filesPath .
                 "/{$this->folder}/{$this->cacheName}");

         /* assure one second has passed */
         sleep(1);

         $headObj->exchangeArray(array());
         $this->appendFilesToHead($this->files);

         $optimizer->optimize(
                $this->filesPath. "/all{$ext}",
                $url. "/all{$ext}"
         );
         $content2      = file_get_contents($this->filesPath . "/all.min{$ext}");
         $filemtime2    = filemtime($this->filesPath . "/all.min{$ext}");
         $cachemtime2   = filemtime($this->filesPath .
                 "/{$this->folder}/{$this->cacheName}");

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
         /* @var $headObj \Majisti_View_Helper_HeadLink */
         $headObj    = $this->headObject;
         $optimizer  = $this->optimizer;
         $url        = $this->filesUrl;
         $ext        = $this->extension;

         /* setting minifying and bundling on */
         $optimizer->setOptimizationEnabled();

         $this->appendFilesToHead($this->files);

         $optimizer->minify();

         $this->assertTrue(file_exists($this->filesPath .
                 "/{$this->folder}/core.min{$ext}"));
         $this->assertTrue(file_exists($this->filesPath .
                 "/{$this->folder}/file1.min{$ext}"));
         $this->assertTrue(file_exists($this->filesPath .
                 "/{$this->folder}/file2.min{$ext}"));
     }
}