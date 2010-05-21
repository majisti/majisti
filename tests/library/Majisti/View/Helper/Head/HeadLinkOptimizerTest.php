<?php

namespace Majisti\View\Helper\Head;

require_once 'TestHelper.php';

/**
 * @desc Tests that the stylesheet optimizer can bundle and minify
 * css stylesheets correctly.
 *
 * @author Majisti
 */
class HeadLinkOptimizerTest extends AbstractHeadOptimizerTest
{
    static protected $_class = __CLASS__;

    /**
     * Setups the test case
     */
    public function setUp()
    {
        /* Needed concrete variables */
        $this->folder      = 'styles';
        $this->files       = $this->getFilesObjects(array('core.css', 'file1.css', 'file2.css'));
        $this->outputFiles = array('file1.min.css', 'file2.min.css', 'core.min.css');
        $this->options     = array('path' => $this->filesPath . '/styles');
        $this->extension   = '.css';
        $this->cacheName   = '.stylesheets-cache';

        $this->view = new \Zend_View();
        $this->view->addHelperPath(
            'Majisti/View/Helper',
            'Majisti_View_Helper'
        );

        $this->headObject = $this->view->headLink();

        $this->optimizer = new HeadLinkOptimizer($this->view, $this->options);
        $this->optimizer->clearCache();

        /* clearing headlink data */
        $this->clearHead();

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
        $optimizeOutput = array('files.css', 'all.css', 'all.min.css');

        foreach($optimizeOutput as $file) {
            @unlink($this->filesPath . "/{$file}");
        }

        $this->clearHead();
    }

    /**
     * @desc TODO
     */
    protected function getFilesObjects($files = array())
    {
        $objects = array();
        foreach( $files as $file ) {
            $object = new \stdClass();
            $object->rel   = "stylesheet";
            $object->type  = "text/css";
            $object->href  = "{$this->filesUrl}/{$this->folder}/{$file}";
            $object->media = "screen";
            $object->conditionalStylesheet = false;
            $objects[] = $object;
        }
        return $objects;
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
        $url = $this->filesUrl;
        if( !empty ( $sheets) ) {
            foreach($sheets as $sheet) {
                $headlink->appendStylesheet("{$url}/styles/{$sheet}.css");
            }
        }

        return $headlink;
    }

    protected function getHeaderOutput($filename)
    {
        return '<link href="' . $this->filesUrl .
                "/{$filename}{$this->extension}?v=" . filemtime($this->filesPath .
                "/{$filename}{$this->extension}")   .
                '" media="screen" rel="stylesheet" type="text/css" />';
    }

    protected function appendInvalidFiles($headObj)
    {
        $headObj->appendAlternate('/feed/', 'application/rss+xml', 'RSS Feed');
        $headObj->appendAlternate('/mydocument.pdf', "application/pdf", "foo",
                array('media'=>'print'));
        $headObj->appendAlternate('/mydocument2.pdf', "application/pdf", "bar",
                array('media'=>'screen'));

        return $headObj;
    }

    public function testThatInvalidFilesArePreserved()
    {
        $headObj   = $this->headObject;
        $optimizer = $this->optimizer;
        $url       = $this->filesUrl;
        $ext       = $this->extension;

        /* setting optimization on */
        $optimizer->setOptimizationEnabled();

        /* appending files to the head */
        $this->appendFilesToHead($this->getFilesObjects(array("core{$ext}",
                "file1{$ext}", "file2{$ext}")));

        /* appending invalid files that should be preserved */
        $this->appendInvalidFiles($headObj);

        $optimizer->optimize(
                $this->filesPath. "/all{$ext}",
                $url. "/all{$ext}"
        );

        /* asserting that invalid files inclusions were preserved */
        $array = (array)$headObj->getContainer();
        $this->assertEquals(4, count($array));
        $this->assertEquals('alternate', $array[0]->rel);
    }

//
//     /**
//      * @desc Tests that the optimize function appends a version to the master
//      * file generated.
//      */
//     public function testThatOptimizeFunctionAppendsAVersionToMasterFile()
//     {
//       $this->markTestSkipped();
//         $headlink  = $this->view->headLink();
//         /** @var Majisti_View_Helper_HeadLink */
//         $optimizer = $this->optimizer;
//         $url       = $this->filesUrl;
//
//         /* setting minifying and bundling on */
//         $optimizer->setBundlingEnabled();
//         $optimizer->setMinifyingEnabled();
//
//         $this->appendHeadlinkStylesheets($headlink,
//                 array('core', 'theme1', 'theme2'));
//
//         $urlOptimize = $optimizer->optimize(
//                    $this->files . '/all.css',
//                    $url . '/all.css'
//         );
//
//         /* running optimize() a second time and asserting it returns false */
//         $this->assertEquals($urlOptimize, $optimizer->optimize(
//                 $this->files . '/all.css',
//                 $url . '/all.css'
//         ));
//
//         /*
//          * grabbing the master file object from the headlink after calling
//          * optimize() twice
//          */
//         $twiceOptimized = $headlink->getIterator()->current();
//
//         /* asserting that when running once, optimize() appends ?v=... */
//         $this->assertTrue((boolean)substr_count($urlOptimize, '?v='));
//
//         /*
//          * asserting that when running more than once, optimize() also appends
//          * ?v=... from the cache file.
//          */
//         $this->assertTrue((boolean)substr_count($twiceOptimized->href, '?v='));
//     }
//
//     /**
//      * @desc Tests that no action is taken if bundling, minifying or both are
//      * disabled.
//      */
//     public function testThatNoActionIsDoneIfBundlingAndOrMinifyingIsDisabled()
//     {
//       $this->markTestSkipped();
//         /** @var Majisti_View_Helper_HeadLink */
//         $headlink  = $this->view->headLink();
//         $optimizer = $this->optimizer;
//         $url       = $this->filesUrl;
//
//         /*
//          * not supposed to do anything except returning false since nothing
//          * is enabled
//          */
//         $urlOptimize = $optimizer->optimize(
//                    $this->files. '/all.css',
//                    $url . '/all.css'
//         );
//         $urlBundle   = $optimizer->bundle(
//                    $this->files. '/all.css',
//                    $url . '/all.css'
//         );
//         $urlMinify   = $optimizer->minify();
//
//         $this->assertFalse($urlOptimize);
//         $this->assertFalse($urlBundle);
//         $this->assertFalse($urlMinify);
//     }
//
//     /**
//      * @desc Tests that attempting to optimize, bundle and/or minify empty
//      * files will throw exceptions.
//      *
//      * @expectedException Exception
//      */
//     public function testThatDealingWithEmptyFilesThrowsException()
//     {
//       $this->markTestSkipped();
//         /** @var Majisti_View_Helper_HeadLink */
//         $headlink  = $this->view->headLink();
//         $optimizer = $this->optimizer;
//         $url       = $this->filesUrl;
//
//         /* setting minifying and bundling on */
//         $optimizer->setBundlingEnabled();
//         $optimizer->setMinifyingEnabled();
//
//         /* appending only an empty css file to the head....BAD */
//         $this->appendHeadlinkStylesheets($headlink,
//                 array('empty'));
//
//         /* will throw exception */
//         $urlOptimize = $optimizer->optimize(
//                    $this->files. '/all.css',
//                    $url. '/all.css'
//         );
//     }
//
//     /**
//      * @desc Tests that providing an invalid head object will throw an
//      * exception once in the parseHeader() function.
//      *
//      * @expectedException Exception
//      */
//     public function testThatProvidingAnInvalidHeadObjectThrowsException()
//     {
//       $this->markTestSkipped();
//         /* headlink is not an instance of \Zend_View_Helper_HeadLink */
//         $headlink  = new \stdClass();
//         $optimizer = $this->optimizer;
//         $url       = $this->filesUrl;
//
//         /* setting minifying and bundling on */
//         $optimizer->setBundlingEnabled();
//         $optimizer->setMinifyingEnabled();
//
//         /* will throw Exception! */
//         $optimizer->optimize($this->files. '/all.css', $url. '/all.css');
//     }
//
//     /**
//      * @desc Tests that when calling optimize and adding a new file afterwards
//      * the master file will be rewritten to include the newly added file.
//      */
//     public function testThatAddingAFileAfterOptimizingWillRewriteMasterFile()
//     {
//       $this->markTestSkipped();
//         /** @var Majisti_View_Helper_HeadLink */
//         $headlink  = $this->view->headLink();
//         $optimizer = $this->optimizer;
//         $url       = $this->filesUrl;
//
//         /* setting minifying and bundling on */
//         $optimizer->setBundlingEnabled();
//         $optimizer->setMinifyingEnabled();
//
//         /*
//          * appending core and theme1 css to the head, will add theme2 after
//          * optimizing
//          */
//         $this->appendHeadlinkStylesheets($headlink,
//                 array('core', 'theme1'));
//
//         $urlOptimize = $optimizer->optimize(
//                    $this->files. '/all.css',
//                    $url. '/all.css'
//         );
//         $content1 = file_get_contents($this->files . '/all.min.css');
//
//         /* appending a new css file after having optimized */
//         $headlink->exchangeArray(array());
//         $this->appendHeadlinkStylesheets($headlink, array('core', 'theme1',
//                                                           'theme2'));
//
//         /* optimizing a second time after the theme2 css file was added */
//         $urlSecondOptimize = $optimizer->optimize(
//                 $this->files . '/all.css',
//                 $url . '/all.css');
//         $content2 = file_get_contents($this->files . '/all.min.css');
//
//         /*
//          * asserting that the two calls to optimize() return different
//          * versions and not false
//          */
//         $this->assertNotEquals(false, $urlSecondOptimize);
//         $this->assertNotSame($content1, $content2);
//         $this->assertNotEquals(
//                 false,
//                 array_search($this->files . '/styles/theme2.css',
//                 $optimizer->getCachedFilePaths()
//                 )
//         );
//     }
}

HeadLinkOptimizerTest::runAlone();
