<?php

namespace Majisti\Application;

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
        \Zend_Registry::set('Majisti_Config', $config);

        $application = new \Zend_Application(
            $options->majisti->app->env,
            $config
        );

        /* further config handling using the ConfigHandler resource */
        $bootstrap = $application->getBootstrap();
        $bootstrap->bootstrap('ConfigHandler');

        /* set options */
        $config      = \Zend_Registry::get('Majisti_Config');
        $configArray = $config->toArray();

//        $application->setOptions($configArray);
        $bootstrap->setOptions($configArray);

        /* add locales to the application */
        $bootstrap->bootstrap('Locale');

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
            $options->url = str_replace(
                realpath(rtrim($_SERVER['DOCUMENT_ROOT'], '/')),
                '',
                $options->path . '/public'
            );
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
     * @desc Majisti is miniming the use of global constants to avoid
     * the constantitis antipattern. Those constants represent vital
     * constants that need to be used within an application.
     * Those constants should not be used outside
     * of configuration files, where this application's options cannot
     * be reached. Using them outside this scope in this library is violating the
     * dependency injection Majisti is trying to achieve.
     */
    protected function defineConstants(\Zend_Config $options)
    {
        $majisti  = $options->majisti;
        $majistix = $options->majistix;
        $app      = $majisti->application;

        @define('MAJISTI',         $majisti->path);
        @define('MAJISTI_ROOT',    $majisti->root);
        @define('MAJISTI_PUBLIC',  $majisti->public);

        @define('MAJISTIX',             $majistix->path);
        @define('MAJISTIX_PUBLIC',      $majistix->public);

        /** @staticvar Majisti's application path */
        @define('MAJISTI_APPLICATION',             $app->path);
        @define('MAJISTI_APPLICATION_NAMESPACE',   $app->namespace);
        @define('MAJISTI_APPLICATION_ROOT',        $app->root);
        @define('MAJISTI_APPLICATION_PUBLIC',      $app->public);
        @define('MAJISTI_APPLICATION_LIBRARY',     $app->library);
        @define('MAJISTI_APPLICATION_ENVIRONMENT', $app->environment);

        define('MAJISTI_APPLICATION_BASEURL',     $app->baseUrl);
        define('MAJISTI_APPLICATION_URL',         $app->url);
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
