<?php

namespace Majisti\Controller\Plugin;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AllTests extends \Majisti\Test\TestSuite
{
    /**
     * @desc Runs all the tests for this directory, running all
     * tests cases and AllTest classes under the first level
     * of directories.
     * 
     * @return \Majisti\Test\TestSuite
     */
    public static function suite()
    {
        $suite = new self('Majisti Library - Controller - Plugin - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\I18nTest');
        $suite->addTestCase(__NAMESPACE__ . '\LayoutSwitcherTest');
        
        return $suite;
    }
}

AllTests::runAlone();
