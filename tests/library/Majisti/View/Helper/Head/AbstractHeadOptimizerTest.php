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
     * files and clearing the head object.
     */
    public function tearDown()
    {
        $ext    = $this->extension;
        $folder = $this->folder;

        $optimizeOutput = array("files{$ext}", "all{$ext}", "all.min{$ext}");

        foreach($optimizeOutput as $file) {
            @unlink($this->filesPath . "/{$file}");
        }

        foreach( $this->outputFiles as $file ) {
            @unlink( $this->filesPath . "/{$folder}/{$file}" );
        }

        $this->clearHead();
    }

    /**
     * @desc Clears headLink and headScript containers.
     */
    protected function clearHead()
    {
        $this->view->headLink()->exchangeArray(array());
        $this->view->headScript()->exchangeArray(array());
    }

    /**
     * @desc Appends \stdClass objects to the head.
     */
    protected function appendFilesToHead($files = array())
    {
        foreach( $files as $file ) {
            $this->headObject->append($file);
        }
    }

    protected abstract function getHeaderOutput($filename);
    protected abstract function getFilesObjects($files = array());

    /**
     * @desc Asserts that files get bundled in a master file.
     */
    public function testBundle()
    {
        $optimizer  = $this->optimizer;
        $url        = $this->filesUrl;
        $path       = $this->filesPath;
        $ext        = $this->extension;

        $optimizer->setBundlingEnabled();

        /* append and bundle files */
        $this->appendFilesToHead($this->files);

        $optimizer->bundle(
                $path . "/all{$ext}",
                $url  . "/all{$ext}"
        );

        $this->assertBundled('all');
    }

    /**
     * @desc Asserts that the head contains only the master file
     * and that the master file in question contains all the files bundled.
     *
     * @param string $filename
     */
    protected function assertBundled($filename)
    {
        $headObj = $this->headObject;
        $ext     = $this->extension;
        $path    = $this->filesPath;

        /* file link should contain only the bundled file */
        $this->assertEquals($this->getHeaderOutput($filename),
                $headObj->__toString());

        /* files should  exist and contain the correct content */
        $this->assertTrue(file_exists($path .
                "/{$filename}{$ext}"));

        $this->assertEquals(
                file_get_contents($path .  "/{$filename}.bundled.expected{$ext}"),
                file_get_contents($path .  "/{$filename}{$ext}")
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
       $path        = $this->filesPath;

       $optimizer->setBundlingEnabled();

       $request = \Zend_Controller_Front::getInstance()->getRequest();
       $uri     = $request->getScheme() . ':/' . $url;

       $files = $this->getFilesObjects(array("file1{$ext}", "file2{$ext}"));
       $this->appendFilesToHead($files);

       $optimizer->uriRemap($uri . "/{$this->folder}/file1{$ext}",
               $path  . "/{$this->folder}/file1{$ext}");

       $optimizer->bundle($path . "/files{$ext}",
               $url . "/files{$ext}");

       $this->assertBundled('files');
    }

    /**
     * Tests the optimize function and asserts that the core, file1 and file2
     * files have been minified and bundled.
     */
    public function testOptimize()
    {
        $optimizer = $this->optimizer;
        $url       = $this->filesUrl;
        $ext       = $this->extension;
        $path      = $this->filesPath;

        /* setting optimization on */
        $optimizer->setOptimizationEnabled();

        /* appending files to the head */
        $this->appendFilesToHead($this->files);

        $optimizer->optimize(
                $path .  "/all{$ext}",
                $url  . "/all{$ext}"
        );

        /* optimize() function returns absolute paths from server root */
        $cachedFilesPaths = array(
                "{$path}/{$this->folder}/core{$ext}",
                "{$path}/{$this->folder}/file1{$ext}",
                "{$path}/{$this->folder}/file2{$ext}",
                "{$path}/all{$ext}"
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
        $url     = $this->filesUrl;
        $ext     = $this->extension;
        $path    = $this->filesPath;

        /* files should contain the correct content */
        $this->assertTrue(file_exists($path . "/{$filename}.min{$ext}"));
        $this->assertEquals(
                file_get_contents($path .
                    "/{$filename}.optimized.expected{$ext}"),
                file_get_contents($path . "/{$filename}.min{$ext}")
        );
     }

     /**
      * @desc Tests that setting a new cache file name will change the cache
      * file path.
      */
     public function testSettingNewCacheFileName()
     {
         $optimizer = $this->optimizer;
         $path      = $this->filesPath;
         $optimizer->setOptions(array(
             'cacheFile' => '.foo-cache',
             'path'      => $path . "/{$this->folder}"
         ));

         $this->assertEquals($path . "/{$this->folder}/.foo-cache",
                             $optimizer->getCacheFilePath());
     }

     /**
      * @desc Tests that uri remaps setters and getters behave as expected.
      */
     public function testUriRemappingGettersAndSetters()
     {
         $optimizer = $this->optimizer;
         $uris      = array('http://www.foo.com/uri1'   => 'path/to/file/A',
                            'http://www.foo.com/uri2'   => 'path/to/file/B',
                            'http://www.majisti.com/testing/foo/uri3' => 'path/to/file/C',
                            'http://www.majisti.com/testing/bar/uri4' => 'path/to/file/D'
         );

         foreach( $uris as $uri => $path) {
             $optimizer->uriRemap($uri, $path);
         }

         $default = $optimizer->getDefaultOptions();
         $this->assertEquals(array_merge($uris, $default['remappedUris']),
                 $optimizer->getRemappedUris());

         $optimizer->removeUriRemap('http://www.foo.com/uri2');

         $this->assertEquals(6, count($optimizer->getRemappedUris()));
         $this->assertArrayNotHasKey('http://www.foo.com/uri2',
                 $optimizer->getRemappedUris());

         $optimizer->clearUriRemaps();

         $this->assertEquals(0, count($optimizer->getRemappedUris()));
     }

     /**
      * @desc Tests that adding an uri remapping while providing an unexistant
      * path will throw an exception.
      *
      * @expectedException Exception
      */
     public function testThatGivingAnInvalidPathToUriRemapWillThrowException()
     {
         $optimizer = $this->optimizer;
         $url = "http://www.somedomain.com/foo/bar";
         $path = "/public/tests/invalid/file.ext";

         $optimizer->uriRemap($url, $path);
     }

     /**
      * @desc Tests that adding an url remapping when providing a file path will
      * throw an exception.
      *
      * @expectedException Exception
      */
     public function testThatProvidingAFilePathToUriRemappingWillThrowException()
     {
         $optimizer = $this->optimizer;
         $url = "http://www.somedomain.com/foo/bar";
         $path = $this->filesPath . "all.bundled.expected.css";

         $optimizer->uriRemap($url, $path);
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
      * @desc Tests that bundling will not overwrite the master file
      * nor the cached files if cache is enabled
      * and no modifications were found in the original files.
      */
     public function testThatNothingIsOverridenIfOrigFilesHaveNoChanges()
     {
         $optimizer  = $this->optimizer;
         $url        = $this->filesUrl;
         $ext        = $this->extension;
         $path       = $this->filesPath;

         $optimizer->setOptimizationEnabled();

         $this->appendFilesToHead($this->files);

         $urlOptimize = $optimizer->optimize(
                $path . "/all{$ext}",
                $url  . "/all{$ext}"
         );
         $content1      = file_get_contents($path . "/all.min{$ext}");
         $filemtime1    = filemtime($path . "/all.min{$ext}");
         $cachemtime1   = filemtime($path .  "/{$this->folder}/{$this->cacheName}");

         /* assure one second has passed */
         sleep(1);

         $this->clearHead();
         $this->appendFilesToHead($this->files);

         $optimizer->optimize(
                $path . "/all{$ext}",
                $url  . "/all{$ext}"
         );
         $content2      = file_get_contents($path . "/all.min{$ext}");
         $filemtime2    = filemtime($path . "/all.min{$ext}");
         $cachemtime2   = filemtime($path .  "/{$this->folder}/{$this->cacheName}");

         $this->assertSame($content1, $content2);
         $this->assertEquals($filemtime1, $filemtime2);
         $this->assertEquals($cachemtime1, $cachemtime2);
    }

     /**
      * @desc Making sure that every file we give to the minifier will output
      * a <filename>.min.<extension> file.
      */
     public function testThatEveryFileGivenToTheMinifierOutputsADotMinFile()
     {
         $optimizer  = $this->optimizer;
         $url        = $this->filesUrl;
         $ext        = $this->extension;
         $path       = $this->filesPath;

         /* setting minifying and bundling on */
         $optimizer->setOptimizationEnabled();

         $this->appendFilesToHead($this->files);

         $optimizer->minify();

         foreach( $this->outputFiles as $file ) {
             $this->assertTrue(file_exists($path .  "/{$this->folder}/{$file}"));
         }
     }

     

     /**
      * @desc Tests that no action is taken if bundling, minifying or both are
      * disabled.
      */
     public function testThatNoActionIsDoneIfBundlingAndOrMinifyingIsDisabled()
     {
         $optimizer = $this->optimizer;
         $url       = $this->filesUrl;
         $path      = $this->filesPath;
         $ext       = $this->extension;

         /*
          * not supposed to do anything except returning false since nothing
          * is enabled
          */
         $urlOptimize = $optimizer->optimize(
                    $path .  "/all{$ext}",
                    $url  . "/all{$ext}"
         );
         $urlBundle   = $optimizer->bundle(
                    $path .  "/all{$ext}",
                    $url  . "/all{$ext}"
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
         $optimizer = $this->optimizer;
         $url       = $this->filesUrl;
         $path      = $this->filesPath;
         $ext       = $this->extension;

         /* setting minifying and bundling on */
         $optimizer->setOptimizationEnabled();

         /* appending only an empty file to the head....BAD */
         $this->appendFilesToHead($this->getFilesObjects(array("empty{$ext}")));

         /* will throw exception */
         $urlOptimize = $optimizer->optimize(
                    $path . "/all{$ext}",
                    $url  . "/all{$ext}"
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
         /* head is not a valid instance */
         $this->headObject   = new \stdClass();
         $optimizer          = $this->optimizer;
         $url                = $this->filesUrl;
         $path               = $this->filesPath;
         $ext                = $this->extension;

         /* setting minifying and bundling on */
         $optimizer->setOptimizationEnabled();

         /* will throw Exception! */
         $optimizer->optimize($path . "/all{$ext}", $url . "/all{$ext}");
     }

     /**
      * @desc Tests that when calling optimize and adding a new file afterwards
      * the master file will be rewritten to include the newly added file.
      */
     public function testThatAddingAFileAfterOptimizingWillRewriteMasterFile()
     {
         $optimizer = $this->optimizer;
         $url       = $this->filesUrl;
         $path      = $this->filesPath;
         $ext       = $this->extension;

         /* setting minifying and bundling on */
         $optimizer->setOptimizationEnabled();

         /*
          * appending core and file1 to the head, will add theme2 after
          * optimizing
          */
         $this->appendFilesToHead($this->getFilesObjects(array("core{$ext}",
                 "file1{$ext}")));

         $urlOptimize = $optimizer->optimize(
                    $path . "/all{$ext}",
                    $url  . "/all{$ext}"
         );
         $content1 = file_get_contents($path . "/all.min{$ext}");

         /* appending a new file after having optimized */
         $this->clearHead();
         $this->appendFilesToHead($this->files);

         /* optimizing a second time after the file2 css file was added */
         $urlSecondOptimize = $optimizer->optimize(
                 $path . "/all{$ext}",
                 $url  . "/all{$ext}");
         $content2 = file_get_contents($path . "/all.min{$ext}");

         /*
          * asserting that the two calls to optimize() return different
          * versions and not false
          */
         $this->assertNotEquals(false, $urlSecondOptimize);
         $this->assertNotSame($content1, $content2);
         $this->assertNotEquals(
                 false,
                 array_search($path . "/{$this->folder}/file2{$ext}",
                 $optimizer->getCachedFilePaths()
                 )
         );
     }
}