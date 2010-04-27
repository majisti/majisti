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
class Application extends \Zend_Application
{
    /**
     * @desc Constructs the application based on merged configuration file
     *
     * @param $applicationPath The application's path
     */
    public function __construct($applicationPath)
    {
        Application\Constants::defineConstants($applicationPath);

        /* setup config and call parent */
        $config = $this->_loadConfiguration();
        \Zend_Registry::set('Majisti_Config', $config);

        parent::__construct(APPLICATION_ENVIRONMENT, $config);

        /* further config handling */
        $bootstrap = $this->getBootstrap();
        if( $bootstrap->hasPluginResource('ConfigHandler') ) {
            $bootstrap->bootstrap('ConfigHandler');
            $this->setOptions($config->toArray());
        }

        /* declare yet more constants */
        Application\Constants::defineConfigurableConstants();
        Application\Constants::defineAliases();
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
}
