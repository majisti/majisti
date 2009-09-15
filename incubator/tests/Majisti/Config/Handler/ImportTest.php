<?php

namespace Majisti\Config\Handler;
use Majisti as Majisti;

require_once 'TestHelper.php';

/**
 * @desc Property test case
 * @author Steven Rosato
 * 
 * TODO: test xml and array configuration as well
 */
class ImportTest extends Majisti\Test\PHPUnit\TestCase 
{
    static protected $_class = __CLASS__;
    
    protected $_validImport;
    protected $_invalidImport;
    
    /**
     * @var Import
     */
    protected $_importHandler;
    
    /**
     * @var Property
     */
    protected $_propertyHandler;
    
    /**
     * Setups
     */
    public function setUp()
    {
        $this->_validImport = new \Zend_Config_Ini(
            dirname(__FILE__) . '/../_files/imports/validImports.ini', 
            'production' ,true);
        $this->_invalidImport = new \Zend_Config_Ini(
            dirname(__FILE__) . '/../_files/imports/invalidImports.ini', 
            'production', true);
        
        $this->_propertyHandler = new Property();
        $this->_importHandler   = new Import();
    }
    
    /**
     * TODO: Doc.
     */
    public function testHandle()
    {
//        $this->markTestIncomplete(); //added by steven, fails on AllTest facade
        $handler = $this->_importHandler;
        
        /*
         * Parsing first for the properties then importing the external ini files.
         */
        $config = $this->_propertyHandler->handle($this->_validImport);
        $config = $handler->handle($config, true);
        $config = $this->_propertyHandler->handle($config);
        
        /* config content should have been replaced if duplicate entries are found, else new entries are appended. */
        $this->assertSame('/var/www', $config->app->dir->applicationPath);
        $this->assertSame('/var/www/someProject/public/images/OVERRIDEN', 
            $config->app->dir->images);
        $this->assertSame('/', $config->app->dir->root);
        $this->assertSame('dir/baz', $config->app->dir->baz);
        $this->assertSame('foo/OVERRIDEN', //Appended as new entry then also overriden
            $config->app->dir->foo);
        $this->assertNull($config->app->dir->nonExistantNode);
    }
    
     /**
     * @desc Asserts that the Ini is loaded correctly and that an exception
     * is thrown when an invalid import path is declared.
     * 
     * @expectedException Exception
     */
    public function testHandleWithInvalidImportPath()
    {
        $config = $this->_propertyHandler->handle($this->_invalidImport);
        $this->_importHandler->handle($config);
        
        $this->assertEquals(0, count($config->toArray()));
    }
    
    public function testGetFlatArray()
    {
        $handler = $this->_importHandler;
        $testArray['foo'] = array('parent' => 'fooParent', 'children' => array('fooChildOne', 'fooChildTwo'));
        $testArray['br'] = array('parent' => 'barParent', 'children' => array('barChildOne', 'barChildTwo'));
        
        $flat = $handler->getFlatArray($testArray);
        
        $this->assertEquals(6, count($flat));
        $this->assertSame('fooParent', $flat[0]);
        $this->assertSame('fooChildTwo', $flat[2]);
        $this->assertSame('barParent', $flat[3]);
        $this->assertSame('barChildTwo', $flat[5]);
    }
}
ImportTest::runAlone();