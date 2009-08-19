<?php
/**
 * Majisti Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@majisti.com so we can send you a copy immediately.
 *
 * @category   Majisti
 * @package    Majisti_View
 * @copyright  Copyright (c) 2009 Majisti Inc. (http://www.majisti.com)
 * @license    http://framework.majisti.com/license/new-bsd     New BSD License
 */

/** Zend_Loader */
require_once 'Zend/Loader.php';

/** Zend_Loader_PluginLoader */
require_once 'Zend/Loader/PluginLoader.php';

/** Majisti_Model_Interface */
require_once 'Majisti/Model/Interface.php';

/**
 * Abstract class for Majisti_Model to help enforce private constructs.
 *
 * @category   Majisti
 * @package    Majisti_Model
 * @copyright  Copyright (c) 2009 Majisti Inc. (http://www.majisti.com)
 * @license    http://framework.majisti.com/license/new-bsd     New BSD License
 */
class Majisti_Model_Abstract implements Majisti_View_Interface
{
    
    /**
     * Instances of model helper objects.
     *
     * @var array
     */
    private $_helper = array();

    /**
     * Helper plugin loader
     * @var Zend_Loader_PluginLoader
     */
    private $_loader;

    /**
     * Strict variables flag; when on, undefined variables accessed in the view
     * scripts will trigger notices
     * @var boolean
     */
    private $_strictVars = false;


    /**
     * Display a notice when accessing unknown property from this model
     *
     * @param  string $key
     * @return null
     */
    public function __get($key)
    {
        trigger_error('Cannot access specified property : ' . $key, E_USER_NOTICE);
    }
    
    /**
     * Allows testing with empty() and isset() to work inside
     * templates.
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset($key)
    {
        if ('_' != substr($key, 0, 1)) {
            return isset($this->$key);
        }

        return false;
    }

    /**
     * Assign a value to a declared member of this model.
     * 
     * The method will throw an exception if the member is private, protected, or
     * was not explicitly declared inside this model; setting new members outside
     * this scope is prohibited. 
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     * @throws Zend_View_Exception if an attempt to set a private, protected or undefined
     * member is detected
     */
    public function __set($key, $val)
    {
        if ('_' != substr($key, 0, 1)) {
            if (isset($this->$key)) {
                $this->$key = $val;
                return;
            } else {
                require_once 'Majisti/Model/Exception.php';
                throw new Majisti_Model_Exception('Setting custom class members is not allowed', $this);
            }
        }

        require_once 'Majisti/Model/Exception.php';
        throw new Majisti_Model_Exception('Setting private or protected class members is not allowed', $this);
    }

    /**
     * Deny unser to all properties
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        require_once 'Majisti/Model/Exception.php';
        throw new Majisti_Model_Exception('Unsetting class members is not allowed', $this);
    }

    /**
     * Accesses a helper object from within a script.
     *
     * If the helper class has a 'model' property, sets it with the current model
     * object.
     *
     * @param string $name The helper name.
     * @param array $args The parameters for the helper.
     * @return string The result of the helper output.
     */
    public function __call($name, $args)
    {
        // is the helper already loaded?
        $helper = $this->getHelper($name);
        
        // call the helper method
        return call_user_func_array(
            array($helper, $name),
            $args
        );
    }

    /**
     * Retrieve plugin loader for the the model helpers
     *
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader() {
        if ( null === $this->_loader ) {
            // set default paths
            $prefix     = 'Zend_View_Helper';
            $pathPrefix = 'Zend/View/';

            $this->_loader = new Zend_Loader_PluginLoader(array(
                $prefix => $pathPrefix
            ));
        }
        
        return $this->_loader;
    }

    
    /**
     * Adds to the stack of helper paths in LIFO order.
     *
     * @param string|array The directory (-ies) to add.
     * @param string $classPrefix Class prefix to use with classes in this
     * directory; defaults to Zend_View_Helper
     * @return Majisti_Model_Abstract
     */
    public function addHelperPath($path, $classPrefix = 'Zend_View_Helper')
    {
        $loader = $this->getPluginLoader();
        foreach ((array) $paths as $path) {
            $loader->addPrefixPath($classPrefix, $path);
        }
        return $this;
    }

    /**
     * Resets the stack of helper paths.
     *
     * To clear all paths, use Zend_View::setHelperPath(null).
     *
     * @param string|array $path The directory (-ies) to set as the path.
     * @param string $classPrefix The class prefix to apply to all elements in
     * $path; defaults to Zend_View_Helper
     * @return Zend_View_Abstract
     */
    public function setHelperPath($path, $classPrefix = 'Zend_View_Helper_')
    {
        unset($this->_loader);
        return $this->addHelperPath($path, $classPrefix);
    }

    /**
     * Get full path to a helper class file specified by $name
     *
     * @param  string $name
     * @return string|false False on failure, path on success
     */
    public function getHelperPath($name)
    {
        $loader = $this->getPluginLoader();
        if ($loader->isLoaded($name)) {
            return $loader->getClassPath($name);
        }

        try {
            $loader->load($name);
            return $loader->getClassPath($name);
        } catch (Zend_Loader_Exception $e) {
            return false;
        }
    }

    /**
     * Returns an array of all currently set helper paths
     *
     * @return array
     */
    public function getHelperPaths()
    {
        return $this->getPluginLoader()->getPaths();
    }

    /**
     * Get a helper by name
     *
     * @param  string $name
     * @return object
     */
    public function getHelper($name)
    {
        $name = ucfirst($name);
        
        if (!isset($this->_helper[$name])) {
            $class = $this->getPluginLoader()->load($name);
            $this->_helper[$name] = new $class();
            if (method_exists($this->_helper[$name], 'setModel')) {
                $this->_helper[$name]->setModel($this);
            }
        }

        return $this->_helper[$name];
    }

    /**
     * Get array of all active helpers
     *
     * Only returns those that have already been instantiated.
     *
     * @return array
     */
    public function getHelpers()
    {
        return $this->_helper;
    }
        
}