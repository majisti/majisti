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
    
    private $_validImport;
    private $_invalidImport;
    
    /**
     * @var Import
     */
    private $_importHandler;
    
    /**
     * @var Property
     */
    private $_propertyHandler;
    
    /**
     * Setups
     */
    public function setUp()
    {
        $this->_validImport = new \Zend_Config_Ini(
            dirname(__FILE__) . '/../_files/imports/validImports.ini', 
            'production' ,true);
        $this->_invalidImport = new \Zend_Config_Ini(
            dirname(__FILE__) . '/../_files/invalidProperty.ini', 
            'production', true);
        
        $this->_propertyHandler = new Property();
        $this->_importHandler   = new Import();
    }
    
    /**
     * TODO: Doc.
     */
    public function testHandle()
    {
        $handler = $this->_importHandler;
        
        $config = $this->_propertyHandler->handle($this->_validImport);
        $config = $handler->handle($this->_validImport);
        
//        /* Imports loaded */
//        $this->assertEquals(2, count($handler->getAllImportUrl()));
//        $this->assertSame('/var/www', $handler->getImportUrl('applicationPath'));
//        $this->assertSame('/var/www/someProject/public', 
//            $handler->getImportUrl('baseUrl'));
//        
//        /* config content should have been replaced */
//        $this->assertSame('/var/www', $config->app->dir->applicationPath);
//        $this->assertSame('/var/www/someProject/public/images', 
//            $config->app->dir->images);
//        $this->assertSame('/', $config->app->dir->root);
//        $this->assertSame('/foo/bar/var/www/baz', 
//            $config->app->dir->inBetween);
//        $this->assertSame('/var/var/www/someProject/public/foo/var/www/bar', 
//            $config->app->dir->dualInBetween);
//        $this->assertNull($config->app->dir->nonExistantNode);
    }
}
ImportTest::runAlone();