<?php

namespace Majisti\Test;

use \Majisti\Application as Application,
    \Majisti\Test\Util\ServerInfo;

/**
 * @desc The test case serves as a simplified manner to extend PHPUnit TestCases.
 * It provides support for single running a test or running it as a part
 * of a TestSuite. Moreover, it easily enables Mvc testing with
 * the mvc option set through the helper singleton on by calling enableMvc()
 * in the setUp function.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class FunctionalTestCase extends \Zend_Test_PHPUnit_ControllerTestCase
                         implements Test
{
    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function setUp()
    {
        parent::setUp();

        /* won't instanciate on multiple call but will instanciate on each test */
        if ( null === $this->bootstrap ) {
            $manager = new Application\Manager($this->getHelper()->getOptions());
            $this->bootstrap = $manager->getApplication();
            $this->bootstrap->bootstrap();
        }
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function run(\PHPUnit_Framework_TestResult $result = NULL)
    {
        $result = parent::run($result);

        /*
         * exceptions should be thrown
         * this makes life for extreme programmers easier (those who write test
         * before code so testing broken code in controllers will properly
         * throw exceptions)
         */
        if( $this->getResponse()->isException() ) {
            $stack = $this->getResponse()->getException();
            $result->addError($this, $stack[0], microtime());
        }

        return $result;
    }

    /**
     * @desc Runs a test alone.
     *
     * @param array $arguments [opt; def=Runner's default] The Runner's arguments
     */
    static public function runAlone($arguments = array())
    {
        Standalone::runAlone(get_called_class(), $arguments);
    }

    /**
     * @desc Returns the helper instance
     *
     * @return Helper
     */
    public function getHelper()
    {
        return \Majisti\Test\Helper::getInstance();
    }
}
