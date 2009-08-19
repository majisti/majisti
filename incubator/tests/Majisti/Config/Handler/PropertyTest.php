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
class PropertyTest extends Majisti\Test\PHPUnit\TestCase 
{
    static protected $_class = __CLASS__;
    
    private $_validProperties;
    private $_invalidProperty;
    private $_invalidNestedProperty;
    private $_invalidCalledProperty;
    
    /**
     * @var Property
     */
    private $_propertyHandler;
    
    /**
     * Setups
     */
    public function setUp()
    {
        $this->_validProperties = new \Zend_Config_Ini(
            dirname(__FILE__) . '/../_files/validProperties.ini', 
            'production' ,true);
        $this->_invalidProperty = new \Zend_Config_Ini(
            dirname(__FILE__) . '/../_files/invalidProperty.ini', 
            'production', true);
        $this->_invalidNestedProperty = new \Zend_Config_ini(
            dirname(__FILE__) . '/../_files/invalidNestedProperty.ini', 
            'production', true);
        $this->_invalidCalledProperty = new \Zend_Config_ini(
            dirname(__FILE__) . '/../_files/invalidCalledProperty.ini', 
            'production', true);
        
        $this->_propertyHandler = new Property();
    }
    
    /**
     * @desc Asserts that everything declared in
     * the properties scope should be loaded. The properties scope should 
     * not be present in the configuration. Assert that every nodes that 
     * declares a property get replaced and those that do not don't get
     * replaced.
     */
    public function testHandle()
    {
        $handler = $this->_propertyHandler;
        $config = $handler->handle($this->_validProperties);
        
        $this->assertNull($config->properties);
        
        /* properties loaded */
        $this->assertEquals(2, count($handler->getProperties()));
        $this->assertSame('/var/www', $handler->getProperty('applicationPath'));
        $this->assertSame('/var/www/someProject/public', 
            $handler->getProperty('baseUrl'));
        $this->assertSame('/var/www/someProject/public/images', 
            $config->app->dir->images);
        
        /* config content should have been replaced */
        $this->assertSame('/var/www', $config->app->dir->applicationPath);
        $this->assertSame('/var/www/someProject/public/images', 
            $config->app->dir->images);
        $this->assertSame('/', $config->app->dir->root);
        $this->assertSame('/foo/bar/var/www/baz', 
            $config->app->dir->inBetween);
        $this->assertSame('/var/var/www/someProject/public/foo/var/www/bar', 
            $config->app->dir->dualInBetween);
        $this->assertNull($config->app->dir->nonExistantNode);
    }
    
    /**
     * @desc Asserts that the Ini is loaded correctly and that an exception
     * is thrown when a invalid property is declared.
     * 
     * @expectedException Majisti\Config\Handler\Exception
     */
    public function testHandleWithInvalidProperty()
    {
        $this->_propertyHandler->handle($this->_invalidProperty);
    }
    
    /**
     * @desc Asserts that the Ini is loaded correctly and that
     * an exception is thrown when an invalid nested
     * property is declared.
     * 
     * @expectedException Majisti\Config\Handler\Exception
     */
    public function testHandleWithInvalidNestedProperty()
    {
        $this->_propertyHandler->handle($this->_invalidNestedProperty);
    }
    
    /**
     * @desc Asserts that the Ini is loaded correctly and that an
     * exception is thrown when an invalid property is called.
     * 
     * @expectedException Majisti\Config\Handler\Exception
     */
    public function testHandleWithInvalidCalledProperty()
    {
        $this->_propertyHandler->handle($this->_invalidCalledProperty);
    }
    
    /**
     * @desc Test the properties accessing and mutating functions
     */
    public function testPropertiesAccessorsMutators()
    {
        $handler = $this->_propertyHandler;
        $handler->handle($this->_validProperties);
        
        $this->assertEquals(2, count($handler->getProperties()));
        $this->assertTrue($handler->hasProperties());
        $this->assertSame(array(
            'applicationPath'     => '/var/www',
            'baseUrl'            => '/var/www/someProject/public'
        ), $handler->getProperties());
        $this->assertSame('/var/www', $handler->getProperty('applicationPath'));
        
        $this->assertNull($handler->getProperty('nonExistantProperty'));
    }
    
    /**
     * Test multiple property syntaxs for property parsing
     */
    public function testSyntax() 
    {
        $handler = $this->_propertyHandler;
        $handler->handle($this->_validProperties);
        
        $handler->setSyntax('#{', '}');
        
        $this->markTestIncomplete();
    }
}

PropertyTest::runAlone();
