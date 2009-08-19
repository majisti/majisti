<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Steven Rosato
 */
class ImportTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * @var \Zend_Config
     */
    protected $_validConfig;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->_validConfig = new \Zend_Config(array());
    }
    
    public function test__construct()
    {
        new Import();
    }
    
    public function test__handler()
    {
        $import = new Import();
        $import->handle($this->_validConfig);
    }
}

ImportTest::runAlone();
