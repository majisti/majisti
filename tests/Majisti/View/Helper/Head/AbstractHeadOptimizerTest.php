<?php

namespace Majisti\View\Helper\Head;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Tests that the stylesheet optimizer can bundle and minify
 * css stylesheets correctly.
 *
 * @author Majisti
 */
abstract class AbstractHeadOptimizerTest extends \Majisti\Test\TestCase
{
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

    /**
     * @desc MinifierMock
     */
    protected $minifier;

    public function  __construct($name = NULL, array $data = array(),
        $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->filesPath = realpath(__DIR__ . '/../_files');
    }

    public function setUp()
    {
        $this->filesUrl  = $this->getHelper()->getMajistiBaseUrl() .
            '/tests/Majisti/View/Helper/_files';

        $this->view     = new \Zend_View();
        $this->minifier = new MinifierMock();

        /* clearing head data */
        $this->clearHead();

        \Zend_Controller_Front::getInstance()->setRequest(
            new \Zend_Controller_Request_Http());
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
        $this->view->headStyle()->exchangeArray(array());
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

    /**
     * @desc Enables bundling, appends files to the head and bundles everything
     */
    protected function appendFilesAndExecute($action, $output, $files = array())
    {
        $path      = $this->filesPath;
        $url       = $this->filesUrl;
        $optimizer = $this->optimizer;
        $ext       = $this->extension;
        $return    = null;

        if( !empty($files) ) {
            $this->appendFilesToHead($files);

            if( 'bundle' === $action || 'optimize' === $action ) {
                $optimizer->setBundlingEnabled();

                if( 'optimize' === $action ) {
                    $optimizer->setMinifyingEnabled();
                }

                $return = $optimizer->$action(
                    $path . "/{$output}{$ext}",
                    $url  . "/{$output}{$ext}"
                );
            } else if ('minify' === $action) {
                $optimizer->setMinifyingEnabled();
                $return = $optimizer->minify($output);
            }
        }

        return $return;
    }

    protected abstract function getHeaderOutput($filename);
    protected abstract function getFilesObjects($files = array(), $url = null);

    /**
     * @desc Asserts that files get bundled in a master file.
     */
    public function testBundle()
    {
        $this->appendFilesAndExecute('bundle', 'all', $this->files);
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

        /* files should exist and contain the correct content */
        $this->assertTrue(file_exists($path .
            "/{$filename}{$ext}"));

        $this->assertEquals(
            file_get_contents($path . "/{$filename}.bundled.expected{$ext}"),
            file_get_contents($path . "/{$filename}{$ext}")
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
       $optimizer   = $this->optimizer;
       $url         = $this->filesUrl;
       $ext         = $this->extension;
       $path        = $this->filesPath;
       $folder      = $this->folder;

       $request = \Zend_Controller_Front::getInstance()->getRequest();
       $uri     = $request->getScheme() . ':/' . $url;

       $files = $this->getFilesObjects(array("file1{$ext}", "file2{$ext}"));

//       $optimizer->uriRemap("{$url}/{$folder}/file1{$ext}",
//           "{$path}/{$folder}/file1{$ext}");

       $this->appendFilesAndExecute('bundle', 'files', $files);
       $this->assertBundled('files');
    }

    /**
     * Tests the optimize function and asserts that the core, file1 and file2
     * files have been minified and bundled.
     */
    public function testOptimize()
    {
        $optimizer = $this->optimizer;
        $ext       = $this->extension;
        $path      = $this->filesPath;

        MinifierMock::setState('all');

        $this->appendFilesAndExecute('optimize', 'all', $this->files);

        /* optimize() function returns absolute paths from server root */
        $cachedFilesPaths = array(
            "{$path}/{$this->folder}/core{$ext}",
            "{$path}/{$this->folder}/file1{$ext}",
            "{$path}/{$this->folder}/file2{$ext}"
        );

        /*
         * cached file paths should contain all files given to the optimize
         * function
         */
        $this->assertEquals($cachedFilesPaths, $optimizer->getCachedFilePaths());
        $this->assertOptimized('all');
    }

    /**
     * @desc Asserts that the dot min file exists and that the content output
     * is the same as the expected one.
     */
    protected function assertOptimized($filename)
    {
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
      * @desc Tests that uri remaps setters and getters behave as expected.
      */
     public function testUriRemappingGettersAndSetters()
     {
         $optimizer = $this->optimizer;
         $uris      = array('http://www.foo.com/uri1' => 'path/to/file/A',
            'http://www.foo.com/uri2'                 => 'path/to/file/B',
            'http://www.majisti.com/testing/foo/uri3' => 'path/to/file/C',
            'http://www.majisti.com/testing/bar/uri4' => 'path/to/file/D'
         );

         foreach( $uris as $uri => $path) {
             $optimizer->uriRemap($uri, $path);
         }

         $default = $optimizer->getDefaultOptions();

         /*
          *  asserting that remappedUris contain both default and recently
          *  remapped uris
          */
         $this->assertEquals(array_merge($uris, $default['remappedUris']),
                 $optimizer->getRemappedUris());

         /* removing a random uri to verify it will be removed from remappedUris */
         $optimizer->removeUriRemap('http://www.foo.com/uri2');
         $this->assertArrayNotHasKey('http://www.foo.com/uri2',
                 $optimizer->getRemappedUris());

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

         /* since production is default, it should be enabled */
         $this->assertTrue($optimizer->isBundlingEnabled());
         $this->assertTrue($optimizer->isMinifyingEnabled());

         /* using direct methods for enabling */
         $optimizer->setBundlingEnabled();
         $optimizer->setMinifyingEnabled();

         $this->assertTrue($optimizer->isBundlingEnabled());
         $this->assertTrue($optimizer->isMinifyingEnabled());

         $optimizer->setBundlingEnabled(false);
         $optimizer->setMinifyingEnabled(false);

         $this->assertFalse($optimizer->isBundlingEnabled());
         $this->assertFalse($optimizer->isMinifyingEnabled());

         $optimizer->setBundlingEnabled(true);
         $optimizer->setMinifyingEnabled(true);

         $this->assertTrue($optimizer->isBundlingEnabled());
         $this->assertTrue($optimizer->isMinifyingEnabled());
         /* using optimization */
         $optimizer->setOptimizationEnabled(false);

         $this->assertFalse($optimizer->isBundlingEnabled());
         $this->assertFalse($optimizer->isMinifyingEnabled());

         $optimizer->setOptimizationEnabled();

         $this->assertTrue($optimizer->isBundlingEnabled());
         $this->assertTrue($optimizer->isMinifyingEnabled());

         $optimizer->setOptimizationEnabled(false);
         $optimizer->setOptimizationEnabled(true);

         $this->assertTrue($optimizer->isBundlingEnabled());
         $this->assertTrue($optimizer->isMinifyingEnabled());
     }

     public function testDisableWillNotOptimize()
     {
         $optimizer = $this->optimizer;

         $optimizer->setOptimizationEnabled(false);
         $this->assertFalse($optimizer->optimize('foo', 'bar'));
     }

     /**
      * @desc Utility function that grabs the content of the output file and the
      * modification time of both the output and the cache files. Used in:
      *
      * - testThatNothingIsRecachedIfOrigFilesHaveNoChangesWhenOptimizing()
      * - testThatNothingIsRecachedIfOrigFilesHaveNoChangesWhenBundling()
      * - testThatNothingIsRecachedIfOrigFilesHaveNoChangesWhenMinifying()
      */
     protected function getInfos($output, $minify = false)
     {
         $infos     = new \StdClass();
         $path      = $this->filesPath;
         $ext       = $this->extension;
         $folder    = $this->folder;
         $cacheName = $this->cacheName;

         if( !$minify ) {
             $infos->path      = $path . "/{$output}{$ext}";
             $infos->content   = file_get_contents($infos->path);
             $infos->fileTime  = filemtime($infos->path);
         } else {
             $infos->path      = $path . "/{$folder}/{$output}{$ext}";
             $infos->content   = file_get_contents($infos->path);
             $infos->fileTime  = filemtime($infos->path);
         }

         $infos->cacheTime = filemtime($path . "/{$folder}/{$cacheName}_all");

         return $infos;
     }

     /**
      * @desc Tests that bundling will not overwrite the master file
      * nor the cached files if cache is enabled
      * and no modifications were found in the original files.
      */
     public function testThatNothingIsRecachedIfOrigFilesHaveNoChangesWhenOptimizing()
     {
         MinifierMock::setState('all');

         $this->appendFilesAndExecute('optimize', 'all', $this->files);

         $infos1  = $this->getInfos('all.min');

         /* assure content stays same, that way we know file was not rewritten */
         $content = $infos1->content . 'MAJISTI_TEST';
         file_put_contents($infos1->path, $content);

         /*
          * clearing head and re-adding same files, then re-optimizing and asserting
          * that nothing should have been rewritten
          */
         $this->clearHead();
         $this->appendFilesAndExecute('optimize', 'all', $this->files);

         $infos2 = $this->getInfos('all.min');

         $this->assertSame($content, $infos2->content);
         $this->assertEquals($infos1->fileTime,  $infos2->fileTime);
         $this->assertEquals($infos1->cacheTime, $infos2->cacheTime);
    }

    public function testThatNothingIsRecachedIfOrigFilesHaveNoChangesWhenBundling()
    {
         $urlBundle1 = $this->appendFilesAndExecute('bundle', 'all', $this->files);

         $infos1 = $this->getInfos('all');

         $data = $infos1->content . PHP_EOL . 'TEST';
         file_put_contents($infos1->path, PHP_EOL . 'TEST', FILE_APPEND);

         /*
          * clearing head and re-adding same files, then re-bundling and asserting
          * that nothing should have been rewritten
          */
         $this->clearHead();

         $urlBundle2 = $this->appendFilesAndExecute('bundle', 'all', $this->files);

         $infos2 = $this->getInfos('all');

         $this->assertEquals($data, $infos2->content);
         $this->assertEquals($infos1->fileTime, $infos2->fileTime);
         $this->assertEquals($infos1->cacheTime, $infos2->cacheTime);
         $this->assertEquals($urlBundle1, $urlBundle2);
    }

    public function testThatNothingIsRecachedIfOrigFilesHaveNoChangesWhenMinifying()
    {
         $urlMinify1 = $this->appendFilesAndExecute('minify', 'all', $this->files);
         $infos1 = $this->getInfos('core.min', true);

         $data = $infos1->content . PHP_EOL . 'TEST';
         file_put_contents($infos1->path, PHP_EOL . 'TEST', FILE_APPEND);

         $this->clearHead();

         $urlMinify2 = $this->appendFilesAndExecute('minify', 'all', $this->files);
         $infos2 = $this->getInfos('core.min', true);

         $this->assertEquals($data, $infos2->content);
         $this->assertEquals($infos1->fileTime, $infos2->fileTime);
         $this->assertEquals($infos1->cacheTime, $infos2->cacheTime);
         $this->assertEquals($urlMinify1, $urlMinify2);
    }

    /**
     * @desc Making sure that every file we give to the minifier will output
     * a <filename>.min.<extension> file.
     */
    public function testThatEveryFileGivenToTheMinifierOutputsADotMinFile()
    {
        $path = $this->filesPath;
        $this->appendFilesAndExecute('minify', 'all', $this->files);

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

        $optimizer->setOptimizationEnabled(false);

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
        $urlMinify   = $optimizer->minify('all');

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
        $ext = $this->extension;

        /* appending only an empty file to the head....BAD */
        $files = $this->getFilesObjects(array("empty{$ext}"));

        /* will throw exception */
        $this->appendFilesAndExecute('optimize', 'all', $files);
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
        $optimizer->setBundlingEnabled();
        $optimizer->setMinifyingEnabled();

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
        $path      = $this->filesPath;
        $ext       = $this->extension;
        $minifier  = $this->minifier;

        /*
         * appending core and file1 to the head, will add theme2 after
         * optimizing
         */
        $minifier::setState("coreFile1");
        $files = $this->getFilesObjects(array("core{$ext}", "file1{$ext}"));
        $urlOptimize1 = $this->appendFilesAndExecute('optimize', 'all', $files);
        $infos1 = $this->getInfos('all.min');

        /* appending a new file after having optimized */
        $this->clearHead();

        $minifier::setState("all");
        $urlOptimize2 = $this->appendFilesAndExecute('optimize', 'all', $this->files);
        $infos2 = $this->getInfos('all.min');

        /*
         * asserting that the two calls to optimize() return different
         * versions and not false
         */
        $this->assertNotEquals(false, $urlOptimize2);
        $this->assertNotSame($infos1->content, $infos2->content);
        $this->assertNotEquals(
                false,
                array_search($path . "/{$this->folder}/file2{$ext}",
                    $optimizer->getCachedFilePaths()
                )
        );
    }

    public function testThatNoLayoutViewWillNotOptimize()
    {
        $view      = $this->view;
        $optimizer = $this->optimizer;

        /* this would normally trigger the optimizer */
        $optimizer->setOptions($this->options + array('environment' => 'production'));

        /* but this won't */
        $view->layout()->disableLayout();

        $this->assertFalse($optimizer->optimize('foo', 'bar'));
        $this->assertFalse($optimizer->bundle('foo', 'bar'));
        $this->assertFalse($optimizer->minify('foo'));
    }
}
