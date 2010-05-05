<?php

namespace Majisti\Loader;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class PluginLoaderTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    private $_pluginLoader;
    
    /**
     * Seupts the test case
     */
    public function setUp()
    {
        $pluginLoader = new PluginLoader();
        $pluginLoader->addPrefixPath('Majisti\Loader',
            'Majisti/Loader/_classes');
        $pluginLoader->addPrefixPath('Majisti\Loader',
            'Majisti/Loader/_classes2');
        $pluginLoader->addPrefixPath('OutOfScopeNamespace\\',
            'Majisti/Loader/_classes2');
        $pluginLoader->addPrefixPath('Majisti\Application\Resource',
            'Majisti/Application/Resource');
        $pluginLoader->addPrefixPath('Zend_View_Helper',
            'Zend/View/Helper');
        
        $this->_pluginLoader = $pluginLoader;
    }
    
    public function testLoad()
    {
        $pluginLoader = $this->_pluginLoader;
        
        $class = $pluginLoader->load('Foo');
        new $class();
        
        $class = $pluginLoader->load('Bar');
        new $class();
        
        $class = $pluginLoader->load('BaseUrl');
        new $class();
        
        $class = $pluginLoader->load('Baz');
        new $class();
        
        $class = $pluginLoader->load('confighandler');
        new $class();
    }
}

PluginLoaderTest::runAlone();
