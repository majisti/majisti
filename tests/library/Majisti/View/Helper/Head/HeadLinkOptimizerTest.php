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
        $this->inclusionVar   = 'href';

        $this->view = new \Zend_View();
        $this->view->addHelperPath(
            'Majisti/View/Helper',
            'Majisti_View_Helper'
        );

        $this->headObject = $this->view->headLink();

        $this->optimizer = new HeadLinkOptimizer($this->view, $this->options);
        $this->optimizer->clearCache();

        /* clearing head data */
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
        parent::tearDown();
        $this->clearHead();
    }

    /**
     * @desc Converts an array for <filename>.<extension> strings to stdClass
     * objects.
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

    /**
     * @desc Tests that "invalid" files (other than CSS) will be preserved
     * in the head container.
     */
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

    /**
     * @desc Tests that adding inline style to the head link will be optimized
     * just like adding files.
     */
    public function testThatAddingInlineStyleToHeadLinkWillAlsoBeOptimized()
    {
        $headObj   = $this->headObject;
        $headStyle = $this->view->headStyle();
        $optimizer = $this->optimizer;
        $path      = $this->filesPath;
        $url       = $this->filesUrl;

        $style = ".inline {
                    color: white;
                  }";

        $optimizer->setOptimizationEnabled();
        $this->appendFilesToHead($this->files);
        $headStyle->appendStyle($style);

        $optimizer->optimize(
                $path . "/all{$ext}",
                $url  . "/all{$ext}"
        );

        /* head link should contain only the bundled file and the style
         * from the head style should have been removed.
         */
        $this->assertEquals(0, $headStyle->count());

        $this->assertEquals(
                file_get_contents($path . "/all.optimized.inc.style.expected{$ext}"),
                file_get_contents($path . "/all{$ext}"));
    }

    /**
      * @desc Tests that the optimize function appends a version to the master
      * file generated.
      */
     public function testThatOptimizeFunctionAppendsAVersionToMasterFile()
     {
         $headObj   = $this->headObject;
         $optimizer = $this->optimizer;
         $url       = $this->filesUrl;
         $ext       = $this->extension;
         $path      = $this->filesPath;

         /* setting minifying and bundling on */
         $optimizer->setOptimizationEnabled();

         $this->appendFilesToHead($this->files);

         $urlOptimize = $optimizer->optimize(
                    $path . "/all{$ext}",
                    $url  . "/all{$ext}"
         );

         /* running optimize() a second time and asserting it returns false */
         $this->assertEquals($urlOptimize, $optimizer->optimize(
                 $path . "/all{$ext}",
                 $url  . "/all{$ext}"
         ));

         /*
          * grabbing the master file object from the headlink after calling
          * optimize() twice
          */
         $twiceOptimized = $headObj->getIterator()->current();

         /* asserting that when running once, optimize() appends ?v=... */
         $this->assertTrue((boolean)substr_count($urlOptimize, '?v='));

         /*
          * asserting that when running more than once, optimize() also appends
          * ?v=... from the cache file.
          */
         $this->assertTrue((boolean)substr_count($twiceOptimized->href, '?v='));
     }
}

HeadLinkOptimizerTest::runAlone();
