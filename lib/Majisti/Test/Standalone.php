<?php

namespace Majisti\Test;

use \Majisti\Application as Application,
    \Majisti\Test\Util\ServerInfo;

/**
 * Utility class for running a test case as standalone.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Standalone
{
    /**
     * @desc Runs a test alone.
     *
     * @param array $arguments [opt; def=Runner's default] The Runner's arguments
     */
    static public function runAlone($class, $arguments = array())
    {
        if( !(ServerInfo::isTestCaseRunning()
                || ServerInfo::isPhpunitRunning()) )
        {
            if( !count($arguments) ) {
                $arguments = \Majisti\Test\Runner::getDefaultArguments();
            }

            \Majisti\Test\Runner::run(new TestSuite($class), $arguments);
        }
    }
}
