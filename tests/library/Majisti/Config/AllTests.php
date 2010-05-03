<?php

namespace Majisti\Config;

require_once 'TestHelper.php';

/**
 * @desc Majisti - Config - All Tests
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    /**
     * @desc Executes the suite
     * @return PHPUnit_Framework_TestSuite
     */
    static public function suite()
    {
        $suite = new self('Majisti - Config - All tests');
        $suite->addTest(Handler\AllTests::suite());

        $suite->addTestSuite(__NAMESPACE__ . '\SelectorTest');
        
        return $suite;
    }
}

AllTests::runAlone();
