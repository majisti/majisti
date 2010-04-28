<?php

namespace Majisti;

/**
 * @desc Majisti's Application is the facade to rich applications where
 * a default configuration along with a concrete configuration are merged
 * together to form the application's configuration as a whole.
 *
 * Moreover, it applies some needed constants for application development
 * see {@link Application\Constants}.
 *
 * Any post bootstraping is defined by a concrete Bootstrap class
 * that extends Majisti\Bootstrap\Bootstrap declared in the configuration.
 * Refer to documentation for more details and examples.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Application
{
    static protected $_application;
    static protected $_applicationPath;

    /**
     * @desc Constructs the application based on merged configuration file
     *
     * @param $applicationPath The application's path
     */
    protected function __construct($applicationPath)
    {
        Application\Constants::defineConstants($applicationPath);

        /* setup config and call parent */
        $config = $this->_loadConfiguration();
        \Zend_Registry::set('Majisti_Config', $config);

        $application = new \Zend_Application(APPLICATION_ENVIRONMENT, $config);

        /* further config handling */
        $bootstrap = $application->getBootstrap();
        if( $bootstrap->hasPluginResource('ConfigHandler') ) {
            $bootstrap->bootstrap('ConfigHandler');
            $application->setOptions($config->toArray());
        }

        /* declare yet more constants */
        Application\Constants::defineConfigurableConstants();
        Application\Constants::defineAliases();

        static::$_application = $application;
    }

    /**
     * @desc Returns a merged Majisti's default configuration with
     * the application's configuration, the later overwriting the former.
     * @return \Zend_Config
     *
     * TODO: factory method for supporting Zend_Config_Xml AND Zend_Config_Ini?
     */
    protected function _loadConfiguration()
    {
        $defaultConfig = new \Zend_Config_Ini( dirname(__FILE__) .
            '/Application/Configs/core.ini', APPLICATION_ENVIRONMENT, true);

        $concreteConfigPath = APPLICATION_PATH . '/configs/core.ini';
        if( !file_exists($concreteConfigPath) ) {
            throw new Exception("The core.ini under " . dirname($concreteConfigPath)
                . "/ is mandatory!");
        }

        return $defaultConfig->merge(new \Zend_Config_Ini(
            $concreteConfigPath, APPLICATION_ENVIRONMENT, true));
    }

    /**
     * @desc Returns the application path.
     *
     * @return string The application path
     */
    static public function getApplicationPath()
    {
        return static::$_applicationPath;
    }

    /**
     * @desc Sets the application path
     *
     * @param string $applicationPath The application path
     */
    static public function setApplicationPath($applicationPath)
    {
        static::$_applicationPath = (string)$applicationPath;
    }

    /**
     * @desc Returns the application, the application path must be set prior
     * calling this function.
     *
     * @return \Majisti\Application the instance
     */
    static public function getInstance()
    {
        if( null === static::$_application ) {
            new static(static::getApplicationPath());
        }

        return static::$_application;
    }
}
