<?php

namespace Majisti\Application;

require_once 'TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc
 * @author
 */
class BootstrapTest extends \Zend_Application_Bootstrap_BootstrapTest
{
    static public function runAlone()
    {
        \Majisti\Test\PHPUnit\TestCase::setClass(__CLASS__);
        \Majisti\Test\PHPUnit\TestCase::runAlone();
    }
    
    public function testDispatcherInitialized()
    {
        $this->markTestIncomplete();
    }
    
    public function testTranslationInitialized()
    {
        $this->markTestIncomplete();
    }
    
    public function testLibraryAutoloaderInitialized()
    {
        $this->markTestIncomplete();
    }
}

BootstrapTest::runAlone();
