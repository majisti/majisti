<?php

namespace Majisti\Config;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Majisti - Config - All Tests
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AllTests extends \Majisti\Test\TestSuite
{
    /**
     * @desc Executes the suite
     * @return PHPUnit_Framework_TestSuite
     */
    static public function suite()
    {
        $suite = new self('Majisti - Config - All tests');
        $suite->addTestSuite(Handler\AllTests::suite());

        $suite->addTestCase(__NAMESPACE__ . '\SelectorTest');
        $suite->addTestCase(__NAMESPACE__ . '\ConfigurationTest');
        
        return $suite;
    }
}

AllTests::runAlone();
