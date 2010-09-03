<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

/**
 * @desc Import test case
 * @author Majisti
 */
class ImportTest extends \Majisti\Test\TestCase 
{
    static protected $_class = __CLASS__;

    /**
     * @var String
     */
    public $basePath;

    /**
     * @var String
     */
    public $serverDir;

    /**
     * @var \Zend_Config_Ini
     */
    protected $_validImport;

    /**
     * @var \Zend_Config_Ini
     */
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
     * @desc Setups
     */
    public function setUp()
    {
        $this->basePath     = __DIR__ . '/../_files/import';
        $this->serverDir    = getcwd();
        
        chdir(realpath(__DIR__ . '/../..'));
        
        $this->_validImport = new \Zend_Config_Ini($this->basePath .
            '/validImports.ini', 'production' ,true);
        $this->_invalidImport = new \Zend_Config_Ini($this->basePath . 
            '/invalidImports.ini', 'production', true);
        
        $this->_propertyHandler = new Property();
        $this->_importHandler   = new Import();
    }

    /**
     * @desc Tears down tests
     */
    public function tearDown()
    {
        chdir($this->serverDir);
    }
    
    /**
     * @desc Tests that the handle function digs recursively, imports external
     * config files and merges everything to return a one-size-fits-all config
     * object.
     */
    public function testHandle()
    {
        $handler = $this->_importHandler;
        $params  = array("parent" => "Config/_files/import/validImports.ini");
        
        /*
         * parsing first for the properties then importing
         * the external ini files.
         */
        $config = $handler->handle($this->_validImport,
                new Composite($this->_propertyHandler), $params);
        
        /*
         * config content should have been replaced if common keys are found,
         * else new entries are appended.
         */
        $this->assertSame('/var/www', $config->app->dir->applicationPath);
        $this->assertSame('/', $config->app->dir->root);
        $this->assertSame('dir/baz', $config->app->dir->baz);
        $this->assertSame('/var/www/someProject/public/newFolder',
                          $config->app->dir->new);
                          
        /* param "parent" prevents round import */
        $this->assertSame('/var/www/someProject/public/images/OVERRIDEN',
                          $config->app->dir->images);
        
        /* appended as new entry then also overriden */
        $this->assertSame('foo/OVERRIDEN', $config->app->dir->foo);
        
        /* unexistant key */
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

    /**
     * @desc Tests that the ImportHandler's getters and setters get/set data
     * as expected.
     */
    public function testGettersAndSetters()
    {
        $handler = $this->_importHandler;
        
        /* getConfigType test */
        $handler->setConfigType(new \Zend_Config_Ini($this->basePath .
            '/validImports.ini'));
        $this->assertSame("Zend_Config_Ini", $handler->getConfigType());
        
        /* getCompositeHandler() test */
        $handler->handle($this->_validImport, 
                               new Composite($this->_propertyHandler));
        $this->assertTrue($handler->getCompositeHandler() instanceof Composite);
        
         
        /* getImportPaths() test */
        $paths = $handler->getImportPaths();
        $this->assertEquals(5, sizeof($paths));
        $this->assertSame("Config/_files/import/twoLevelImport.ini", $paths[0]);
        $this->assertSame("Config/_files/import/validImports.ini", $paths[1]);
        $this->assertSame("Config/_files/import/threeLevelImport.ini", $paths[2]);
    }
}

ImportTest::runAlone();
