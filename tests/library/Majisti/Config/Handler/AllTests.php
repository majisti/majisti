<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

/**
 * @desc Majisti - Config - Handler - All tests
 * @author Steven Rosato
 */
class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    /**
     * @desc Executes the suite
     * @return PHPUnit_Framework_TestSuite
     */
    static public function suite()
    {
        $suite = new self('Majisti - Config - Handler - All tests');
        $suite->addTestSuite(__NAMESPACE__ . '\CompositeTest');
        $suite->addTestSuite(__NAMESPACE__ . '\MarkupTest');
        $suite->addTestSuite(__NAMESPACE__ . '\ImportTest');
        $suite->addTestSuite(__NAMESPACE__ . '\PropertyTest');
        
        return $suite;
    }
}

AllTests::runAlone();
