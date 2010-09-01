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
     * @desc Constructs the application based on merged configuration file
     */
    protected function __construct(\Zend_Config $options)
    {
        $this->defineSettings($options);

        /* setup config and call parent */
        $config = $this->loadConfiguration($options);
        \Zend_Registry::set('Majisti_Config', $config);

        $application = new \Zend_Application($options->environment, $config);

        /* further config handling using the ConfigHandler resource */
        $bootstrap = $application->getBootstrap();
        $bootstrap->bootstrap('ConfigHandler');
        $config = \Zend_Registry::get('Majisti_Config');
        $configArray = $config->toArray();

        $application->setOptions($configArray);
        $bootstrap->setOptions($configArray);

        /* add locales to the application */
        $bootstrap->bootstrap('Locale');

        /* declare yet more constants */
        $this->defineConfigurableSettings($config, $options);

        if( $options->useAliases ) {
            $this->defineSettingsAliases($options);
        }

        static::$_application = $application;

        static::$_options = $options;
    }

    protected function defineSettings(\Zend_Config $settings)
    {
        $request = new \Zend_Controller_Request_Http();

        /* application paths */
        $app = $settings->application;

        $app->library     = "{$app->path}/../library";
        $app->root        = realpath("{$app->path}/..");
        $app->public      = "{$app->root}/public";

        define('MAJISTI_APPLICATION',           $app->path);
        define('MAJISTI_APPLICATION_NAMESPACE', $app->namespace);
        define('MAJISTI_APPLICATION_LIBRARY',   $app->library);
        define('MAJISTI_APPLICATION_ROOT',      $app->root);
        define('MAJISTI_APPLICATION_PUBLIC',    $app->public);

        /* environment */
        define('MAJISTI_APPLICATION_ENVIRONMENT', $app->environment);

        /* application urls */
        $app->baseUrl     = $request->getBaseUrl();
        $app->urlPrefix   = $request->getScheme() . '://' .  $request->getHttpHost();
        $app->styles      = "{$app->baseUrl}/styles";
        $app->scripts     = "{$app->baseUrl}/scripts";
        $app->images      = "{$app->baseUrl}/images";

        define('MAJISTI_APPLICATION_BASEURL',     $app->baseUrl);
        define('MAJISTI_APPLICATION_URL',         $app->urlPrefix . $app->baseUrl);
        define('MAJISTI_APPLICATION_URL_STYLES',  $app->styles);
        define('MAJISTI_APPLICATION_URL_SCRIPTS', $app->scripts);
        define('MAJISTI_APPLICATION_URL_IMAGES',  $app->images);

        /* majisti paths */
        $majisti = $settings->majisti = new \Zend_Config(array(),
            array('allowModifications' => true));

        $majisti->path   = dirname(__FILE__);
        $majisti->root   = dirname(dirname($majisti->path));
        $majisti->public = "{$majisti->root}/public/majisti";
        $majisti->folder = basename($majisti->root);

        define('MAJISTI',             $majisti->path . '/Majisti');
        define('MAJISTI_ROOT',        $majisti->root);
        define('MAJISTI_PUBLIC_URL',  "{$majisti->root}/public/majisti");

        /* majistix paths */
        $majistix = $settings->majistix = new \Zend_Config(array(),
            array('allowModifications' => true));

        $majistix->path       = "{$majisti->root}/library/MajistiX";
        $majistix->public     = "{$majisti->root}/public/majistix";
        $majistix->modules    = "{$majistix->path}/Modules";
        $majistix->extensions = "{$majistix->path}/Extensions";

        define('MAJISTIX',             $majistix->path);
        define('MAJISTIX_PUBLIC_URL',  $majistix->public);
        define('MAJISTIX_MODULES',     $majistix->modules);
        define('MAJISTIX_EXTENSIONS',  $majistix->extensions);
    }

    protected function defineConfigurableSettings(\Zend_Config $config,
        \Zend_Config $settings)
    {
        $selector = new \Majisti\Config\Selector($config);
        $app      = $settings->application;
        $majisti  = $settings->majisti;

        /*
         * Majisti's library public directory, a static url could had been
         * provided in the configuration.
         * exemple: http://static.mydomain.com
         *
         * otherwise it is mapped to public/development
         * for development and testing environments
         * and public/production for staging and production ones.
         */
        $majisti->public = $selector->find('majisti.public', false);

        if( !$majisti->public ) {
            $majisti->public = "{$app->urlPrefix}/{$majisti->folder}/public";
        }

        $majisti->url     = "{$majisti->public}/majisti";
        $majisti->styles  = "{$majisti->url}/styles";
        $majisti->scripts = "{$majisti->url}/scripts";
        $majisti->images  = "{$majisti->url}/images";

        define('MAJISTI_PUBLIC',      $majisti->public);
        define('MAJISTI_URL_STYLES',  $majisti->styles);
        define('MAJISTI_URL_SCRIPTS', $majisti->scripts);
        define('MAJISTI_URL_IMAGES',  $majisti->images);

        $majistix = $settings->majistix;

        $majistix->url = "{$majisti->public}/majistix";

        /*
         * the majisti[x]'s public folders according
         * to the static url previously setup
         */
        define('MAJISTI_URL',  MAJISTI_PUBLIC . '/majisti');
        define('MAJISTIX_URL', MAJISTI_PUBLIC . '/majistix');

        define('MAJISTIX_URL_STYLES',  MAJISTIX_URL . '/styles');
        define('MAJISTIX_URL_SCRIPTS', MAJISTIX_URL . '/scripts');
        define('MAJISTIX_URL_IMAGES',  MAJISTIX_URL . '/images/common');

        /* jQuery urls */
        define('MAJISTI_JQUERY',          MAJISTI_PUBLIC . '/jquery');
        define('MAJISTI_JQUERY_UI',       MAJISTI_JQUERY         . '/ui');
        define('MAJISTIX_JQUERY_PLUGINS', MAJISTIX_URL   . '/jquery/plugins');
        define('MAJISTIX_JQUERY_STYLES',  MAJISTIX_URL   . '/jquery/styles');
        define('MAJISTIX_JQUERY_THEMES',  MAJISTIX_URL   . '/jquery/themes');

        /* locales */
        $locales        = \Majisti\Application\Locales::getInstance();
        $currentLocale  = strtolower($locales->getCurrentLocale()->toString());
        $defaultLocale  = strtolower($locales->getDefaultLocale()->toString());

        define('MAJISTI_LOCALE_CURRENT', $currentLocale);
        define('MAJISTI_LOCALE_DEFAULT', $defaultLocale);
    }

    public function defineSettingsAliases(\Zend_Config $settings)
    {
        $app = $settings->application;

        define('MAJ_APP', $app->path);
        define('MAJ_APP_PUB_PATH', $app->public);
        define('MAJ_APP_LIB',  $app->library);
        define('MAJ_APP_ENV',  $app->environment);

        define('MAJ_APP_PREFIX', $app->urlPrefix);
//        define('MAJ_APP_URL', );
//        define('MAJ_APP_SCRIPTS', APPLICATION_URL_SCRIPTS);
//        define('MAJ_APP_STYLES', APPLICATION_URL_STYLES);
//        define('MAJ_APP_IMG', APPLICATION_URL_IMAGES);
//        define('MAJ_APP_IMG_LOC', APPLICATION_URL_IMAGES_LOCALE);
//
//        define('MAJ_APP_LANG', APPLICATION_LOCALE_CURRENT);
//        define('MAJ_APP_LANG_DEF', APPLICATION_LOCALE_DEFAULT);
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
    }

    /**
     * @desc Returns a merged Majisti's default configuration with
     * the application's configuration, the later overwriting the former.
     *
     * @return \Zend_Config
     */
    protected function loadConfiguration(\Zend_Config $options)
    {
        $app = $options->application;

        $defaultConfig = new \Zend_Config_Ini(dirname(__FILE__) .
            '/Application/Configs/core.ini', $app->environment, true);

        $concreteConfigPath = $app->path . '/configs/core.ini';
        if( !file_exists($concreteConfigPath) ) {
            throw new Exception("The core.ini under " . 
                dirname($concreteConfigPath) . "/ is mandatory!");
        }

        return $defaultConfig->merge(new \Zend_Config_Ini(
            $concreteConfigPath, $app->environment, true));
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
