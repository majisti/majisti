<?php

namespace Majisti\Application;

require_once 'TestHelper.php';

/**
 * @desc Tests that application constants are defined correctly
 * with correct values.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ConstantsTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    public $applicationPath;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->applicationPath = dirname(__FILE__);
        \Zend_Registry::set('Majisti_Config', new \Zend_Config(array()));
        @define('MAJISTI_FOLDER_NAME', 'majisti');
        putenv('APPLICATION_ENVIRONMENT=development');
    }
    
    /**
     * @desc Asserts that constants are correctly defined
     * and that their values match exactly as the expected value.
     *
     * @param array $constants Constants to assert
     * as an array of name => value pairs
     */
    protected function _assertConstants(array $constants)
    {
        foreach ($constants as $name => $value) {
            $this->assertTrue(defined($name),
                "Constant {$name} not defined");
            $this->assertEquals($value, constant($name),
                "Constant {$name} value's is incorrect");
        }
    }
    
    /**
     * @return Returns the expected static constants
     */
    public function getExpectedStaticConstants()
    {
        return array(
            'APPLICATION_PATH'          => $this->applicationPath,
            'APPLICATION_ENVIRONMENT'   => 'development',
        );
    }
    
    /**
     * @return Returns the expected static constants
     */
    public function getExpectedDynamicConstants()
    {
        return array();
    }
    
    public function getExpectedAliases()
    {
        return array();
    }
    
    public function testStaticConstantsAreAllDefinedCorrectly()
    {
        Constants::defineStaticConstants($this->applicationPath);
        $this->_assertConstants($this->getExpectedStaticConstants());
    }
    
    public function testDynamicConstantsAreAllDefinedCorrectly()
    {
        Constants::defineDynamicConstants();
        $this->_assertConstants($this->getExpectedDynamicConstants());
    }
    
    public function testAliasesAreAllDefinedCorrectly()
    {
        $this->_assertConstants($this->getExpectedAliases());
    }
    
    public function testAliasesDisabled()
    {
        $this->markTestIncomplete();
    }
    
    public function testAliasesDisabledInConfig()
    {
        $this->markTestIncomplete();
    }
}

ConstantsTest::runAlone();
