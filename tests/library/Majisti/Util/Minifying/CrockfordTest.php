<?php

namespace Majisti\Util\Minifying;

require_once 'TestHelper.php';

/**
 * @desc
 * @author
 */
class CrockfordTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    public $minifier;

    public function setUp()
    {
        $this->minifier = new Crockford();
    }

    public function testMinifyCss()
    {
        $minifier = $this->minifier;

        $minifiedCss = $minifier->minifyCss('.foo { color: red; }');

        $this->assertEquals('.foo{color:red}', $minifiedCss);
    }

    public function testMinifyJs()
    {
        $minifier = $this->minifier;

        $minifiedJs = $minifier->minifyJs(
            "function helloWord() { window.print ('hello world');}");

        $this->assertEquals(
            "\nfunction helloWord(){window.print('hello world');}",
            $minifiedJs
        );
    }
}

CrockfordTest::runAlone();
