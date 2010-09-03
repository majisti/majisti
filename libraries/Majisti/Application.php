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

    /**
     * @var Application\AddonsManager
     */
    protected $_addonsManager;

    static protected $_options;

    /**
     * @desc Constructs the application based on merged configuration files
     */
    protected function __construct(\Zend_Config $options)
    {
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

        $application->setOptions($configArray);
        $bootstrap->setOptions($configArray);

        /* add locales to the application */
        $bootstrap->bootstrap('Locale');

        static::$_application = $application;

        static::$_options = $options;
    }

    protected function initOptions(\Zend_Config $options)
    {
        $request = new \Zend_Controller_Request_Http();
        $selector = new \Majisti\Config\Selector($options);

        $options->path = dirname(dirname(dirname((__FILE__))));

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

        define('MA',             $options->path);
        define('MA_URL',         $options->url);
        define('MA_APP',         $options->app->path);
        define('MA_APP_ENV',     $options->app->env);
        define('MA_APP_NS',      $options->app->namespace);
        define('MA_APP_URL',     $options->app->url);
        define('MA_APP_BASEURL', $options->app->baseUrl);

        /*
         * Majisti's library public directory, a static url could had been
         * provided in the configuration.
         * exemple: http://static.mydomain.com
         */
//        if( $url = $selector->find('majisti.url', false) ) {
//            $majisti->url = "{$request->getScheme()}://$url";
//        } else {
//            $majisti->url = str_replace(
//                realpath(rtrim($_SERVER['DOCUMENT_ROOT'], '/')),
//                '',
//                $majisti->public
//            );
//        }

//        define('MAJISTI_URL', $majisti->url);
        /* majisti paths */
//        $majisti = $options->majisti;

//        $majisti->path   = dirname(__FILE__);
//        $majisti->root   = dirname(dirname($majisti->path));
//        $majisti->public = "{$majisti->root}/public/majisti";
//        $majisti->folder = basename($majisti->root);

        /* majistix paths */
//        $majistix = $options->majistix = new \Zend_Config(array(),
//            array('allowModifications' => true));

//        $majistix->path       = "{$majisti->root}/library/MajistiX";
//        $majistix->public     = "{$majisti->root}/public/majistix";
//        $majistix->modules    = "{$majistix->path}/Modules";
//        $majistix->extensions = "{$majistix->path}/Extensions";

        /* application paths */
//        $app = $majisti->application;

//        $app->library     = realpath("{$app->path}/../library");
//        $app->root        = realpath("{$app->path}/..");
//        $app->public      = "{$app->root}/public";
//        $app->folder      = basename($app->root);

        /* application urls */
//        $app->baseUrl     = $request->getBaseUrl();
//        $app->url         = "{$request->getScheme()}://{$app->baseUrl}";

//        $this->defineConstants($options);
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

        define('MAJISTI',         $majisti->path);
        define('MAJISTI_ROOT',    $majisti->root);
        define('MAJISTI_PUBLIC',  $majisti->public);

        define('MAJISTIX',             $majistix->path);
        define('MAJISTIX_PUBLIC',      $majistix->public);

        /** @staticvar Majisti's application path */
        define('MAJISTI_APPLICATION',             $app->path);
        define('MAJISTI_APPLICATION_NAMESPACE',   $app->namespace);
        define('MAJISTI_APPLICATION_ROOT',        $app->root);
        define('MAJISTI_APPLICATION_PUBLIC',      $app->public);
        define('MAJISTI_APPLICATION_LIBRARY',     $app->library);
        define('MAJISTI_APPLICATION_ENVIRONMENT', $app->environment);

        define('MAJISTI_APPLICATION_BASEURL',     $app->baseUrl);
        define('MAJISTI_APPLICATION_URL',         $app->url);
    }

    protected function defineEnvironmentDependantConstants(\Zend_Config $options)
    {

    }

    protected function initEnvironmentDependantOptions(\Zend_Config $options)
    {
//        $selector = new \Majisti\Config\Selector($options);
//        $majisti  = $options->majisti;
//        $app      = $majisti->application;

//        $request = new \Zend_Controller_Request_Http();

        /*
         * Majisti's library public directory, a static url could had been
         * provided in the configuration.
         * exemple: http://static.mydomain.com
         */
//        if( $url = $selector->find('majisti.url', false) ) {
//            $majisti->url = "{$request->getScheme()}://$url";
//        } else {
//            $majisti->url = str_replace(
//                realpath(rtrim($_SERVER['DOCUMENT_ROOT'], '/')),
//                '',
//                $majisti->public
//            );
//        }

//        define('MAJISTI_URL', $majisti->url);

//        $majistix = $majisti->majistix;

//        $majistix->url = "{$majisti->url}x";

        /*
         * the majisti[x]'s public folders according
         * to the static url previously setup
         */
//        define('MAJISTIX_URL', $majistix->url);

        /* jQuery urls */
//        $majisti->jquery = new \Zend_Config(array(), array('allowModifications' => true));
//
//        define('MAJISTI_JQUERY',          MAJISTI_PUBLIC . '/jquery');
//        define('MAJISTI_JQUERY_UI',       MAJISTI_JQUERY . '/ui');
//        define('MAJISTIX_JQUERY_PLUGINS', MAJISTIX_URL   . '/jquery/plugins');
//        define('MAJISTIX_JQUERY_STYLES',  MAJISTIX_URL   . '/jquery/styles');
//        define('MAJISTIX_JQUERY_THEMES',  MAJISTIX_URL   . '/jquery/themes');

        /* locales */
//        $locales        = \Majisti\Application\Locales::getInstance();
//        $currentLocale  = strtolower($locales->getCurrentLocale()->toString());
//        $defaultLocale  = strtolower($locales->getDefaultLocale()->toString());

//        define('MAJISTI_LOCALE_CURRENT', $currentLocale);
//        define('MAJISTI_LOCALE_DEFAULT', $defaultLocale);
    }

//        define('MAJ_APP_URL', );
//        define('MAJ_APP_SCRIPTS', APPLICATION_URL_SCRIPTS);
//        define('MAJ_APP_STYLES', APPLICATION_URL_STYLES);
//
//        define('MAJ_ROOT', MAJISTI_ROOT);
//        define('MAJ_LIB',  MAJISTI_LIBRARY);
//        define('MAJ_PATH', MAJISTI_PATH);
//        define('MAJ_PUB',  MAJISTI_PUBLIC);
//
//        define('MAJX_PATH', MAJISTIX_PATH);
//        define('MAJX_EXT',  MAJISTIX_EXTENSIONS);
//        define('MAJX_MOD',  MAJISTIX_MODULES);
//
//        define('MAJ_URL',  MAJISTI_URL);
//        define('MAJX_URL', MAJISTIX_URL);
//
//        define('MAJ_STYLES' , MAJISTI_URL_STYLES);
//        define('MAJ_SCRIPTS', MAJISTI_URL_SCRIPTS);
//        define('MAJ_IMG'    , MAJISTI_URL_IMAGES);
//        define('MAJ_IMG_LOC', MAJISTI_URL_IMAGES_LOCALE);
//
//        define('MAJX_STYLES', MAJISTIX_URL_STYLES);
//        define('MAJX_SCRIPTS', MAJISTIX_URL_SCRIPTS);
//        define('MAJX_IMG', MAJISTIX_URL_IMAGES);
//
//        define('JQ', JQUERY);
//        define('JQ_STYLES', JQUERY_STYLES);
//        define('JQ_PLUGINS', JQUERY_PLUGINS);
//        define('JQ_THEMES', JQUERY_THEMES);

    /**
     * @desc Returns a merged Majisti's default configuration with
     * the application's configuration, the later overwriting the former.
     *
     * @return \Zend_Config
     */
    protected function getConfiguration(\Zend_Config $options)
    {
        $app = $options->majisti->app;

        $defaultConfig = new \Zend_Config_Ini(dirname(__FILE__) .
            '/Application/Configs/core.ini', $app->env, true);

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

    static public function setOptions(array $options)
    {
        if( null !== static::$_application ) {
            throw new Exception("Cannot reconfigure options, application
                already instanciated.");
        }

        static::$_options = $options;
    }

    static public function getOptions()
    {
        if( null === static::$_application ) {
            throw new Exception("Application must be constructed first using
                the getInstance() static function");
        }

        return static::$_options;
    }

    /**
     * @desc Returns the Zend application, the application options must be set prior
     * calling this function.
     *
     * @return \Zend_Application The instance
     */
    static public function getInstance()
    {
        if( null === static::$_application ) {
            if( null === ($options = static::$_options) ) {
                throw new Exception("Options were never set.");
            }

            new self(new \Zend_Config(
                $options,
                array('allowModifications' => true)
            ));
        }

        return static::$_application;
    }

    /**
     * @desc Returns the addons manager.
     * @return Application\Addons\Manager The addons manager
     */
    public function getAddonsManager()
    {
        if( null === $this->_addonsManager ) {
            $addonsManager = new Application\Addons\Manager();
            $addonsManager->registerAddonsPath(MAJISTIX_PATH, 'majistix');
            $this->_addonsManager = $addonsManager;
        }

        return $this->_addonsManager;
    }

    /**
     * @desc Loads an extension for this application.
     *
     * @param string $name The extension name
     * @param string $namespace[opt; def=MajistiX] The namespace it operates under
     * @return undertermined yet
     * @throws Exception If the bootstrap file is not readable, non existant,
     * has wrong namespaced class name or is not implementing IAddonsBoostrapper
     */
    public function loadExtension($name, $namespace = 'MajistiX')
    {
        return $this->getAddonsManager()->loadExtension($name, $namespace);
    }

    /**
     * @desc Loads a fallback module for this application.
     *
     * @param string $name The module name
     * @param string $namespace[opt; def=MajistiX] The namespace it operates under
     * @return undetermined yet
     * @throws Exception If the modules's controllers directory is not
     * readable or existant.
     */
    public function loadModule($name, $namespace = 'MajistiX')
    {
        return $this->getAddonsManager()->loadModule($name, $namespace);
    }
}
