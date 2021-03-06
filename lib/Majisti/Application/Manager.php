<?php

namespace Majisti\Application;

use \Majisti\Config\Selector,
    \Doctrine\Common\ClassLoader
;

require_once dirname(dirname(__DIR__)) .
    '/vendor/doctrine2-common/lib/Doctrine/Common/ClassLoader.php';

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
        $this->initDependentIncludePaths();
        $this->initClassLoaders();

        $options = new \Zend_Config($options,
            array('allowModifications' => true));

        $this->initOptions($options->majisti);

        set_include_path($options->majisti->app->path . PATH_SEPARATOR
            . $options->majisti->path . '/lib' . PATH_SEPARATOR . get_include_path());

        /* setup config and call parent */
        $config = $this->getConfiguration($options);

        $application = new \Zend_Application(
            $options->majisti->app->env,
            $config
        );

        /* further config handling using the ConfigHandler resource */
        $bootstrap = $application->getBootstrap();
        $bootstrap->bootstrap('configHandler');

        /* set options and options passed as args overrides all */
        $config = new \Zend_Config($bootstrap->getOptions(), array(
            'allowModifications' => true,
        ));
        $config = $config->merge($options);
        $config->setReadOnly();
        $bootstrap->setOptions($config->toArray());

        \Zend_Registry::set('Majisti_Config', $config);
        \Zend_Registry::set('Majisti_Config_Selector', new Selector($config));

        /* add locales to the application */
        $bootstrap->bootstrap('Locales');

        $this->_application = $application;
    }

    /**
     * @desc Inits the class autoloaders for this application
     *
     * @param \Zend_Config $majisti
     */
    protected function initClassLoaders()
    {
        $libRoot = dirname(dirname(__DIR__));
        $loaders = array();

        /*
         * Doctrine
         */

        /* special cases */
        $loaders[] = new ClassLoader('Doctrine\DBAL\Migrations',
            "{$libRoot}/vendor/doctrine2-migrations/lib");
        $loaders[] = new ClassLoader('Doctrine\Common\DataFixtures',
            "{$libRoot}/vendor/doctrine2-data-fixtures/lib");
        $loaders[] = new ClassLoader('Gedmo',
            "{$libRoot}/vendor/doctrine2-extensions-gedmo/lib");

        /* generic cases */
        $loaders[] = new ClassLoader('Doctrine\ORM',
            "{$libRoot}/vendor/doctrine2-orm/lib");
        $loaders[] = new ClassLoader('Doctrine\DBAL',
            "{$libRoot}/vendor/doctrine2-dbal/lib");
        $loaders[] = new ClassLoader('Doctrine\Common', 
            "{$libRoot}/vendor/doctrine2-common/lib");
        $loaders[] = new ClassLoader('Doctrine\Common',
            "{$libRoot}/vendor/doctrine2-data-fixtures/lib");
        $loaders[] = new ClassLoader('DoctrineExtensions',
            "{$libRoot}/vendor/doctrine2-extensions/lib");

        /* symfony */
        $loaders[] = new ClassLoader('Symfony\Component',
            "{$libRoot}/vendor/symfony2/src");

        foreach( $loaders as $loader ) {
            $loader->register();
        }

        /* phpunit */
        $loader = new ClassLoader('PHPUnit',
            "{$libRoot}/vendor/phpunit");
        $loader->setNamespaceSeparator('_');
        $loader->register();

        /* zend */
        $loader = new ClassLoader('Zend',
            "{$libRoot}/vendor/zend/library");
        $loader->setNamespaceSeparator('_');
        $loader->register();

        /* zendx */
        $loader = new ClassLoader('ZendX',
            "{$libRoot}/vendor/zend/extras/library");
        $loader->setNamespaceSeparator('_');
        $loader->register();
    }

    /**
     * @desc Inits the dependent include paths.
     */
    protected function initDependentIncludePaths()
    {
        $libRoot = dirname(dirname(__DIR__));

        $paths = array(
            "{$libRoot}/vendor/zend/library",
            "{$libRoot}/vendor/zend/extras/library",
            "{$libRoot}/vendor/phpunit",
        );

        set_include_path(implode(PATH_SEPARATOR,
                $paths) . get_include_path());
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

        if( !isset($options->app->baseUrl) ) {
            $options->app->baseUrl = $request->getBaseUrl();
        }

        if( !isset($options->app->url) ) {
            $options->app->url     = "{$request->getScheme()}://" .
                "{$request->getHttpHost()}{$options->app->baseUrl}";
        }

        if( $url = $selector->find("url.{$options->app->env}", false) ) {
            $options->url = $url;
        } else {
            $options->url = $options->app->baseUrl . '/majisti';
        }

        //@deprecated all constants will be completely removed in 0.5
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

        $concreteConfig = new \Zend_Config_Ini(
            $concreteConfigPath,
            $app->env, true
        );

        return $defaultConfig->merge($concreteConfig)->merge($options);
    }
}
