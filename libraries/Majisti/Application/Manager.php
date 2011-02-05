<?php

namespace Majisti\Application;

use \Majisti\Config\Selector;

/**
 * @desc Majisti's Application Manager is the facade to rich applications where
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
class Manager
{
    protected $_application;

    /**
     * @desc Constructs the application based on merged configuration files
     */
    public function __construct(array $options)
    {
        $options = new \Zend_Config($options,
            array('allowModifications' => true));

        $this->initOptions($options->majisti);

        /* setup config and call parent */
        $config = $this->getConfiguration($options);

        $application = new \Zend_Application(
            $options->majisti->app->env,
            $config
        );

        /* further config handling using the ConfigHandler resource */
        $bootstrap = $application->getBootstrap();
        $bootstrap->bootstrap('Confighandler');

        /* set options */
        $config = new \Zend_Config($bootstrap->getOptions());
        \Zend_Registry::set('Majisti_Config', $config);
        \Zend_Registry::set('Majisti_Config_Selector', new Selector($config));

        /* add locales to the application */
        $bootstrap->bootstrap('Locales');

        $this->_application = $application;
    }

    /**
     * Returns the application.
     *
     * @return \Zend_Application The application
     */
    public function getApplication()
    {
        return $this->_application;
    }

    /**
     * @desc Inits the options.
     *
     * @param \Zend_Config $options The options
     */
    protected function initOptions(\Zend_Config $options)
    {
        $request  = new \Zend_Controller_Request_Http();
        $selector = new \Majisti\Config\Selector($options);

        $options->path = dirname(dirname(dirname(__DIR__)));

        $options->app->baseUrl = $request->getBaseUrl();
        $options->app->url     = "{$request->getScheme()}://" .
            "{$request->getHttpHost()}{$options->app->baseUrl}";

        if( $url = $selector->find("url.{$options->app->env}", false) ) {
            $options->url = $url;
        } else {
            $options->url = $options->app->baseUrl . '/majisti';
        }

        @define('MA',             $options->path);
        @define('MA_URL',         $options->url);
        @define('MA_APP',         $options->app->path);
        @define('MA_APP_ENV',     $options->app->env);
        @define('MA_APP_NS',      $options->app->namespace);
        @define('MA_APP_URL',     $options->app->url);
        @define('MA_APP_BASEURL', $options->app->baseUrl);
    }

    /**
     * @desc Returns a merged Majisti's default configuration with
     * the application's configuration, the later overwriting the former.
     *
     * @return \Zend_Config
     */
    protected function getConfiguration(\Zend_Config $options)
    {
        $app = $options->majisti->app;

        $defaultConfig = new \Zend_Config_Ini(__DIR__ .
            '/Configs/core.ini', $app->env, true);

        $concreteConfigPath = $app->path . '/application/configs/core.ini';
        if( !file_exists($concreteConfigPath) ) {
            throw new Exception("The core.ini under " . 
                dirname($concreteConfigPath) . "/ is mandatory!");
        }

        return $defaultConfig->merge($options)
            ->merge(new \Zend_Config_Ini(
                $concreteConfigPath,
                $app->env, true
            ))
        ;
    }
}
