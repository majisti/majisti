<?php

namespace Majisti;

/**
 * @desc Majisti's Application is the facade to rich applications where
 * a default configuration along with a concrete configuration are merged
 * together to form the application's configuration as a whole.
 * 
 * Moreover, it applies some needed constants for application development
 * see {@link Application::_defineConstants()} method documentation for
 * declared constants.
 * 
 * Any post bootstraping is defined by a concrete Bootstrap class
 * that extends Majisti\Bootstrap\Bootstrap
 * declared in the configuration. Refer to documentation for more
 * details and examples.
 * 
 * @author Steven Rosato
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
        $this->_defineConstants($applicationPath);
        
        $config = $this->_loadDefaultConfig()->{APPLICATION_ENVIRONMENT};
        
        \Zend_Registry::set('Majisti_Config', $config);
        
        parent::__construct(APPLICATION_ENVIRONMENT, $config);
    }
    
    /**
     * @desc Returns a merged Majisti's default configuration with
     * the application's configuration, the later overwriting the former.
     * @return \Zend_Config
     * 
     * TODO: factory method for supporting Zend_Config_Xml AND Zend_Config_Ini?
     */
    protected function _loadDefaultConfig()
    {
        $defaultConfig = new \Zend_Config_Ini( dirname(__FILE__) . 
            '/Application/Configs/default.ini', null, true);
        
        $concreteConfigPath = APPLICATION_PATH . '/configs/application.ini'; 
        if( !file_exists($concreteConfigPath) ) {
            throw new Exception("The application.ini under 
                application/configs/ in mendatory!");
        }
        
        $defaultConfig->merge(new \Zend_Config_Ini($concreteConfigPath, null, true));
        
        return $defaultConfig;
    }
    
    /**
     * @desc Defines all the needed constants for an application.
     * 
     * Currently defined constants:
     * 
     * - APPLICATION_PATH       -> Application's path
     * - APPLICATION_LIBRARY    -> Application's library 
     * - APPLICATION_ENVIRONMENT-> Envoronment mode
     * 
     * - BASE_URL               -> Application's public folder
     * 
     * - MAJISTI_               -> Library's path
     * - MAJISTIX_MODULES_PATH  -> dropped in modules under library/Modules
     * - MAJISTI_URL            -> public library
     * 
     * TODO: move in Application/Constants class
     * 
     * @param $applicationPath The application path
     */
    protected function _defineConstants($applicationPath)
    {
        define('APPLICATION_PATH', $applicationPath);
        define('APPLICATION_LIBRARY', realpath(APPLICATION_PATH . '/../library'));
        defined('APPLICATION_ENVIRONMENT')  /* should have been defined in .htaccess */
            || define('APPLICATION_ENVIRONMENT', (getenv('APPLICATION_ENVIRONMENT') 
                ? getenv('APPLICATION_ENVIRONMENT') 
                : 'production'));
                
        $request = new \Zend_Controller_Request_Http();
        if( !defined('BASE_URL') ) {
            define('BASE_URL', $request->getBaseUrl());
        }

        define('MAJISTI_ROOT', dirname(dirname(__FILE__)));
        define('MAJISTI_PATH', MAJISTI_ROOT . '/Majisti');
        
        define('MAJISTIC_PATH', MAJISTI_ROOT . '/MajistiC');
        
        define('MAJISTIX_PATH', MAJISTI_ROOT . '/MajistiX');
        define('MAJISTIX_MODULES_PATH', MAJISTIX_PATH . '/Modules');
        
        /* 
         * TODO: support overriding with custom virtual host such as
         * static.local or static.majisti.com and etc...
         */
        define('MAJISTI_URL', $request->getScheme() 
            . '://' . $request->getHttpHost() 
            . '/' . MAJISTI_FOLDER_NAME 
            . '/public');
            
        $this->_defineAliases();
    }
    
    /**
     * @desc Define aliases if it is supported in the configuration
     * 
     * TODO: move in Application/Constants class
     */
    protected function _defineAliases()
    {
        /* TODO: support disable aliases */
        define('APP_PATH', APPLICATION_PATH);
        define('APP_LIB', APPLICATION_LIBRARY);
        define('APP_ENV', APPLICATION_ENVIRONMENT);
        
        define('MA_ROOT', MAJISTI_ROOT);
        define('MA_PATH', MAJISTI_PATH);
        
        /* those constants are way too confusing, something better must be thought of */
        define('MAC_PATH', MAJISTIC_PATH);
        
        define('MAX_PATH', MAJISTIX_PATH);
        define('MAX_MODULES_PATH', MAJISTIX_MODULES_PATH);
    }
}
