<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Majisti
 */
class ExtensionsTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * Setups the test case
     */
    public function setUp()
    {

    }

    public function testFoo()
    {
        $this->markTestIncomplete('Waiting for pair programming design');
    }
}

ExtensionsTest::runAlone();
