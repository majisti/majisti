<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

/**
 * @desc Property test case. Currently working only for INI config files.
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class PropertyTest extends \Majisti\Test\TestCase 
{
    /* files paths */
    protected $_validProperties;
    protected $_secondValidProperties;
    protected $_invalidProperty;
    protected $_invalidNestedProperty;
    protected $_invalidCalledProperty;
    
    /**
     * @var Property
     */
    public $propertyHandler;

    /**
     * @var string
     */
    public $basePath;
    
    /**
     * @desc Setups the required files
     */
    public function setUp()
    {
        $this->basePath = __DIR__ . '/../_files/property';
        
        $this->_validProperties = new \Zend_Config_Ini(
            $this->basePath . '/validProperties.ini', 'production' ,true);
        $this->_secondValidProperties = new \Zend_Config_Ini(
            $this->basePath . '/secondValidProperties.ini', 'production' ,true);
        $this->_invalidProperty = new \Zend_Config_Ini(
            $this->basePath . '/invalidProperty.ini', 'production', true);
        $this->_invalidNestedProperty = new \Zend_Config_Ini(
            $this->basePath . '/invalidNestedProperty.ini', 'production', true);
        $this->_invalidCalledProperty = new \Zend_Config_Ini(
            $this->basePath . '/invalidCalledProperty.ini', 'production', true);
        $this->_noProperties = new \Zend_Config_Ini(
            $this->basePath . '/noProperties.ini', 'production', true);
        
        $this->propertyHandler = new Property();
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
        $handler = $this->propertyHandler;
        $handler->handle($this->_validProperties);

        /* should not have made modifications on original object */
        $this->assertNotSame('/var/www/someProject/public',
                $this->_validProperties->majisti->property->applicationPath);

        $config = $handler->handle($this->_validProperties);

        /* properties should have been loaded correctly */
        $this->assertNotEquals(array(), $handler->getProperties());
        $this->assertNull($config->majisti->property);
        
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
        
        /* make sure that passing true as second parameter clears the properties */
        $handler->handle($this->_noProperties, true);
        $this->assertEquals(array(), $handler->getProperties());
    }
    
    /**
     * @desc Properties must stack up or override others as long as 
     * {@link Property::cleared()} is not called
     */
    public function testHandleConfigMoreThanOneTimeWillStackUpAndOverrideProperties()
    {
        $handler = $this->propertyHandler;
        
        $handler->handle($this->_validProperties);
        $handler->handle($this->_secondValidProperties);
        $handler->handle($this->_noProperties);
        
        $expectedProperties = array(
            'applicationPath'   => '/var/www',
            'baseUrl'           => '/var/www/someProject/public',
            'foo'               => 'foo'
        );
        
        $this->assertSame($expectedProperties, $handler->getProperties());
    }
    
    /**
     * @desc Asserts that calling handle will always parse a given config
     * with the stacked up properties
     */
    public function testHandleWithMultipleFilesAlwaysReplacesPreviouslyDeclaredProperties()
    {
        $handler = $this->propertyHandler;
        
        $handler->handle($this->_validProperties);
        
        $config = $handler->handle($this->_secondValidProperties);
        $this->assertEquals('/var/www/bar', $config->bar);
        
        $config = $handler->handle($this->_noProperties);
        $this->assertEquals('/var/www/baz', $config->baz);
        $this->assertEquals('foo/bazz', $config->bazz);
        $handler->handle($this->_noProperties);
    }
    
    /**
     * @desc Asserts that the properties get cleared.
     */
    public function testClear()
    {
        $handler = $this->propertyHandler;
        
        $handler->handle($this->_validProperties);
        $handler->clear();
        $this->assertSame(array(), $handler->getProperties());
    }
    
    /**
     * @desc Asserts that the config is loaded correctly and that an exception
     * is thrown when a invalid property is declared.
     * 
     * TODO: multiple config type testing
     * 
     * @expectedException Majisti\Config\Handler\Exception
     */
    public function testHandleWithInvalidProperty()
    {
        $this->propertyHandler->handle($this->_invalidProperty);
    }
    
    /**
     * @desc Asserts that the config is loaded correctly and that
     * an exception is thrown when an invalid nested
     * property is declared.
     * 
     * @expectedException Majisti\Config\Handler\Exception
     */
    public function testHandleWithInvalidNestedProperty()
    {
        $this->propertyHandler->handle($this->_invalidNestedProperty);
    }
    
    /**
     * @desc Asserts that the config is loaded correctly and that an
     * exception is thrown when an invalid property is called.
     * 
     * @expectedException Majisti\Config\Handler\Exception
     */
    public function testHandleWithInvalidCalledProperty()
    {
        $this->propertyHandler->handle($this->_invalidCalledProperty);
    }
    
    /**
     * @desc Test the properties accessing and mutating functions
     */
    public function testPropertiesAccessorsMutators()
    {
        $handler = $this->propertyHandler;
        $handler->handle($this->_validProperties);
        $expectedProperties = array(
            'applicationPath'     => '/var/www',
            'baseUrl'            => '/var/www/someProject/public'
        );
        
        $this->assertEquals(2, count($handler->getProperties()));
        $this->assertTrue($handler->hasProperties());
        $this->assertSame($expectedProperties, $handler->getProperties());
        $this->assertSame('/var/www', $handler->getProperty('applicationPath'));
        $this->assertNull($handler->getProperty('nonExistantProperty'));
        
        $handler->setProperties($expectedProperties);
        $this->assertSame($expectedProperties, $handler->getProperties());
    }
    
    /**
     * @desc Test multiple property syntaxs for property parsing
     */
    public function testGetSetSyntax() 
    {
        $handler = $this->propertyHandler;
        $expectedSyntax = array(
            'prefix'    => '#{',
            'postfix'   => '}'
        );
        
        $this->assertSame($expectedSyntax, $handler->getSyntax());
        
        $handler->handle($this->_validProperties);
        $handler->setSyntax('#{', '}');
        
        $this->assertSame($expectedSyntax, $handler->getSyntax());
    }

    /**
     * @desc Test that no properties get resolved with a different syntax
     */
    public function testWrongSyntaxWillNotLoadProperties()
    {
        $handler = $this->propertyHandler;

        $handler->setSyntax('${', '}');
        $handler->clear();
        $handler->handle($this->_validProperties);

        $this->assertEquals(array(
            'applicationPath'   => '/var/www',
            'baseUrl'           => '#{applicationPath}/someProject/public'
        ), $handler->getProperties());
    }
}

PropertyTest::runAlone();
