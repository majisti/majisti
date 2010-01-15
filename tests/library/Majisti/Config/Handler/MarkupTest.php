<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

class MarkupTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    public $handler;

    public function setUp()
    {

    }

    /**
     * @desc Asserts that every node that uses markups be replaced
     * with their proper text
     */
    public function testHandle()
    {
        $this->markTestIncomplete();
    }
}

MarkupTest::runAlone();
