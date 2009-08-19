<?php

namespace Majisti;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Steven Rosato
 */
class ClassNameTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * Seupts the test case
     */
    public function setUp()
    {
        
    }
}

ClassNameTest::runAlone();
