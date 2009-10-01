<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

/**
 * @desc Property test case
 * @author Steven Rosato
 * 
 * TODO: test xml and array configuration as well
 */
class ImportTest extends \Majisti\Test\PHPUnit\TestCase 
{
    static protected $_class = __CLASS__;
    
    public $basePath;
    public $serverDir;
    
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
        $this->basePath     = dirname(__FILE__) . '/../_files/import';
        $this->serverDir    = getcwd();
        
        chdir(realpath(dirname(__FILE__) . '/../..'));
        
        $this->_validImport = new \Zend_Config_Ini($this->basePath .
            '/validImports.ini', 'production' ,true);
        $this->_invalidImport = new \Zend_Config_Ini($this->basePath . 
            '/invalidImports.ini', 'production', true);
        
        $this->_propertyHandler = new Property();
        $this->_importHandler   = new Import();
    }
    
    public function tearDown()
    {
        chdir($this->serverDir);
    }
    
    /**
     * TODO: Doc.
     */
    public function testHandle()
    {
        $handler = $this->_importHandler;
        
        /*
         * Parsing first for the properties then importing the external ini files.
         */
        $config = $handler->handle($this->_validImport, new Composite($this->_propertyHandler));
        
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
    
    public function testGettersAndSetters()
    {
        /* getUrls() test */
        $importHandler = $this->_importHandler;
        $testArray['foo'] = array(
            'parent'    => 'fooParent', 
            'children'  => array('fooChildOne', 'fooChildTwo'));
        $testArray['br'] = array('parent' => 'barParent', 
            'children'  => array('barChildOne', 'barChildTwo'));
        
        $flat = $importHandler->getUrls($testArray);
        
        $this->assertEquals(6, count($flat));
        $this->assertSame('fooParent', $flat[0]);
        $this->assertSame('fooChildTwo', $flat[2]);
        $this->assertSame('barParent', $flat[3]);
        $this->assertSame('barChildTwo', $flat[5]);
        
        $importHandler->setConfigType(new \Zend_Config_Ini($this->basePath .
            '/validImports.ini'));
        
        $this->assertSame("Zend_Config_Ini", $importHandler->getConfigType());
        
        /* getCompositeHandler() test */
        $importHandler->handle($this->_validImport, 
            new Composite($this->_propertyHandler));
        $this->assertTrue($importHandler->getCompositeHandler() instanceof Composite);
        
         
        /* getImportsHierarchy() */
        $hierarchy = $importHandler->getImportsHierarchy();
        
        $this->assertContains($hierarchy['bar']['children'][0], 
            'Config/_files/imports/fourthLevelImport.ini');
    }
}

ImportTest::runAlone();
