<?php

namespace Majisti\View\Helper\Head;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Tests that the stylesheet optimizer can bundle and minify
 * css stylesheets correctly.
 *
 * @author Majisti
 */
class HeadLinkOptimizerTest extends AbstractHeadOptimizerTest
{
    /**
     * Setups the test case
     */
    public function setUp()
    {
        parent::setUp();

        /* needed concrete variables */
        $this->options = array('path' => $this->filesPath . '/styles');
        $this->folder         = 'styles';
        $this->files          = $this->getFilesObjects(array(
            'core.css', 'file1.css', 'file2.css'));
        $this->outputFiles    = array(
            'file1.min.css', 'file2.min.css', 'core.min.css');
        $this->extension      = '.css';
        $this->cacheName      = '.stylesheets-cache';
        $this->headObject     = $this->view->headLink();
        $this->inclusionVar   = 'href';

        $this->optimizer = new HeadLinkOptimizer($this->view, $this->options);
        $this->optimizer->setMinifier($this->minifier);
        $this->optimizer->clearCache();

    }

    /**
     * @desc Converts an array for <filename>.<extension> strings to stdClass
     * objects.
     */
    protected function getFilesObjects($files = array(), $url = null)
    {
        $objects = array();
        foreach( $files as $file ) {
            $object = new \stdClass();
            $object->rel   = "stylesheet";
            $object->type  = "text/css";
            if( null !== $url ) {
                $object->href  = $url;
            } else {
                $object->href  = "{$this->filesUrl}/{$this->folder}/{$file}";
            }
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
                '" media="screen" rel="stylesheet" type="text/css" >';
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

        /* appending invalid files that should be preserved */
        $this->appendInvalidFiles($headObj);

        $this->appendFilesAndExecute('optimize', 'all', $this->files);

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
        $ext       = $this->extension;
        $minifier  = $this->minifier;

        $style = ".inline {
                    color: white;
                  }";

        $headStyle->appendStyle($style);

        $minifier::setState("allInline");
        $this->appendFilesAndExecute('optimize', 'all', $this->files);

        /* head link should contain only the bundled file and the style
         * from the head style should have been removed.
         */
        $this->assertEquals(0, $headStyle->count());

        $this->assertEquals(
                file_get_contents($path . "/all.optimized.inc.style.expected{$ext}"),
                file_get_contents($path . "/all.min{$ext}"));
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

         $urlOptimize = $this->appendFilesAndExecute('optimize', 'all', $this->files);

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
     
    public function testAlotOfFilesWillNotAffectPerformance()
    {
        $optimizer = $this->optimizer;
        $url       = $this->filesUrl;
        $path      = $this->filesPath;
        $ext       = $this->extension;
        $folder    = $this->folder;
        
        $numberOfFilesToAdd = 100;
        $fileObjects = array();
        
        MinifierMock::setState(MinifierMock::PERFORMANCE_STATE);
        
        for ($i = 3 ; $i <= $numberOfFilesToAdd ; $i++) {
            $hugeContent = '';
            for ($j = 0 ; $j <= $numberOfFilesToAdd; $j++) {
                $hugeContent .= ".class{$i}_{$j}{color: red;}" . PHP_EOL;
            }
            file_put_contents("{$path}/{$folder}/file{$i}{$ext}", $hugeContent);
            $fileObjects[] = "file{$i}{$ext}";
        }

        $files = $this->getFilesObjects($fileObjects);
        $this->appendFilesAndExecute(
            'optimize', 'all', $files);
        
        for ($i = 3 ; $i <= $numberOfFilesToAdd ; $i++) {
            unlink("{$path}/{$folder}/file{$i}{$ext}");
        }
    }
}

HeadLinkOptimizerTest::runAlone();
