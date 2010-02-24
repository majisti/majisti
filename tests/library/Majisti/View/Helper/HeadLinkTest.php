<?php

namespace Majisti\View\Helper;

require_once 'TestHelper.php';

/**
 * @desc Test case for the HeadLink class.
 *
 * @author Majisti
 */
class HeadLinkTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Zend_View
     */
    public $view;

    /**
     * Setups the test case
     */
    public function setUp()
    {
        
    }

    /**
     * Asserts that stylesheets bundle correctly
     */
    public function testBundle()
    {
        
    }
}

HeadLinkTest::runAlone();
