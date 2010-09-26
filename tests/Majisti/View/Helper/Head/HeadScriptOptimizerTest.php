<?php

namespace Majisti\View\Helper\Head;

require_once 'TestHelper.php';

/**
 * @desc Tests that the stylesheet optimizer can bundle and minify
 * css stylesheets correctly.
 *
 * @author Majisti
 */
class HeadScriptOptimizerTest extends AbstractHeadOptimizerTest
{
    /**
     * Setups the test case
     */
    public function setUp()
    {
        parent::setUp();

        /* needed concrete variables */
        $this->options = array('path' => $this->filesPath . '/scripts');
        $this->folder      = 'scripts';
        $this->files       = $this->getFilesObjects(array(
            'core.js', 'file1.js', 'file2.js'));
        $this->outputFiles = array('file1.min.js', 'file2.min.js', 'core.min.js');
        $this->extension   = '.js';
        $this->cacheName   = '.scripts-cache';
        $this->headObject  = $this->view->headScript();

        $this->optimizer = new HeadScriptOptimizer($this->view, $this->options);
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
            $object->type       = "text/javascript";
            $object->source      = null;
            if( null !== $url) {
                $object->src = $url;
                $object->attributes['src'] = $url;
            } else {
                $object->src = "{$this->filesUrl}/{$this->folder}/{$file}";
                $object->attributes['src'] = "{$this->filesUrl}/{$this->folder}/{$file}";
            }
            $object->attributes = array();
            $objects[] = $object;
        }
        return $objects;
    }

    protected function getHeaderOutput($filename)
    {
        return '<script type="text/javascript" src="' . $this->filesUrl .
                "/{$filename}{$this->extension}?v=" . filemtime($this->filesPath .
                "/{$filename}{$this->extension}") . '"></script>';
                
    }

    /**
     * @desc Tests that adding inline scripts will work just like adding a file
     * in the optimization process.
     */
    public function testThatInlineScriptGetsOptimizedJustLikeFilesDo()
    {
        $headObj   = $this->headObject;
        $ext       = $this->extension;
        $path      = $this->filesPath;
        $minifier  = $this->minifier;

        $script = "function helloWorld() {
                        window.print('Hello World!');
                   }";
        $headObj->appendScript($script);

        $minifier::setState("allInline");
        $urlOptimize = $this->appendFilesAndExecute('optimize', 'all', $this->files);

        /* head script should contain only the bundled file */
        $this->assertEquals($urlOptimize,
                $headObj->getIterator()->current()->attributes['src']);

        $this->assertEquals(
                file_get_contents($path . "/all.optimized.inc.script.expected{$ext}"),
                file_get_contents($path . "/all.min{$ext}")
        );
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
         $this->assertTrue((bool)substr_count($urlOptimize, '?v='));

         /*
          * asserting that when running more than once, optimize() also appends
          * ?v=... from the cache file.
          */
         $this->assertTrue((bool)substr_count($twiceOptimized->attributes['src'], '?v='));
     }
}

HeadScriptOptimizerTest::runAlone();
