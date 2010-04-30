<?php

namespace Majisti\Application;

/**
 * @desc Class that declares constants needed in standard applications.
 * It supports short names aliases as well by default.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Constants
{
    /**
     * @var bool
     */
    static protected $_aliasesUsed;

    /**
     * @desc Private constructor for no instanciation
     */
    private function __construct()
    {}

    /**
     * @desc Defines all the needed constants for an application.
     *
     * @param $applicationPath The application path
     */
    static public function defineConstants($applicationPath)
    {
        /* application's constants */
        define('APPLICATION_PATH', $applicationPath);
        define('APPLICATION_LIBRARY', realpath(APPLICATION_PATH . '/../library'));

        /*
         * retrieve environment variable that should have been defined in
         * a .htaccess. If APPLICATION_ENVIRONMENT can't be retrieved,
         * it will try the fastcgi REDIRECT_APPLICATION_ENVIRONMENT and if
         * there is still no defined constants, it will use production as default
         */
        defined('APPLICATION_ENVIRONMENT')  /* should have been defined in .htaccess */
            || define('APPLICATION_ENVIRONMENT', (getenv('APPLICATION_ENVIRONMENT')
                ? getenv('APPLICATION_ENVIRONMENT')
                : (getenv('REDIRECT_APPLICATION_ENVIRONMENT') /* fastcgi */
                    ? getenv('REDIRECT_APPLICATION_ENVIRONMENT')
                    : 'production')));

        $request = new \Zend_Controller_Request_Http();

        /* retrieve base url based on request object */
        if( !defined('BASE_URL') ) {
            define('BASE_URL', $request->getBaseUrl());
        }

        /* majisti's library path constants */
        define('MAJISTI_ROOT', dirname(dirname(dirname(__FILE__))));
        define('MAJISTI_PATH', MAJISTI_ROOT . '/Majisti');

        define('MAJISTIX_PATH', MAJISTI_ROOT . '/MajistiX');
        define('MAJISTIX_MODULES',    MAJISTIX_PATH . '/Modules');
        define('MAJISTIX_EXTENSIONS', MAJISTIX_PATH . '/Extensions');

        /* majisti's library url constants */
        define('APPLICATION_URL_PREFIX', $request->getScheme() . '://' .
            $request->getHttpHost());
        define('APPLICATION_URL', APPLICATION_URL_PREFIX . BASE_URL);
        define('APPLICATION_URL_STYLES',  BASE_URL . '/styles');
        define('APPLICATION_URL_SCRIPTS', BASE_URL . '/scripts');
        define('APPLICATION_URL_IMAGES',  BASE_URL . '/images/common');
    }

    /**
     * @desc Defines all dynamic constants that will be built
     * according to configuration.
     */
    static public function defineConfigurableConstants()
    {
        $selector = new \Majisti\Config\Selector(
            \Zend_Registry::get('Majisti_Config'));

        /*
         * Majisti's library public directory, a static url could had been
         * provided in the configuration.
         * exemple: http://static.mydomain.com
         *
         * otherwise it is mapped to public/development
         * for development and testing environments
         * and public/production for staging and production ones.
         */
        if( $staticUrl = $selector->find('urls.static', false) ) {
            define('MAJISTI_PUBLIC', $staticUrl);
        } else {
            define('MAJISTI_PUBLIC',
                APPLICATION_URL_PREFIX . MAJISTI_FOLDER_NAME . '/public');
        }

        /*
         * other custom urls for the website. One could define say
         * urls[blog] = "http://blog.mydomain.com" in the production config
         * so that it can be easily retrieved via ${APPLICATION_NAME}_URL_BLOG
         */
        if( $urls = $selector->find('urls', false) ) {
            foreach ($urls as $key => $url) {
                define(
                   strtoupper(APPLICATION_NAME) . '_URL_' .
                   strtoupper($key), $url);
            }
        }

        /*
         * the majisti[x]'s public folders according
         * to the static url previously setup
         */
        define('MAJISTI_URL',  MAJISTI_PUBLIC . '/majisti');
        define('MAJISTIX_URL', MAJISTI_PUBLIC . '/majistix');

        define('MAJISTI_URL_STYLES',  MAJISTI_URL . '/styles');
        define('MAJISTI_URL_SCRIPTS', MAJISTI_URL . '/scripts');
        define('MAJISTI_URL_IMAGES',  MAJISTI_URL . '/images/common');

        $locale = \Majisti\I18n\LocaleSession::getInstance();
        define('MAJISTI_URL_IMAGES_LOCALE', MAJISTI_URL .
            "/images/{$locale->getCurrentLocale()}");
        define('APPLICATION_URL_IMAGES_LOCALE', BASE_URL .
            "/images/{$locale->getCurrentLocale()}");

        define('APPLICATION_LOCALE_CURRENT', $locale->getCurrentLocale());
        define('APPLICATION_LOCALE_DEFAULT', $locale->getDefaultLocale());

        define('MAJISTIX_URL_STYLES',  MAJISTIX_URL . '/styles');
        define('MAJISTIX_URL_SCRIPTS', MAJISTIX_URL . '/scripts');
        define('MAJISTIX_URL_IMAGES',  MAJISTIX_URL . '/images/common');

        /* JQuery public directories */
        define('JQUERY',         MAJISTI_PUBLIC . '/jquery');
        define('JQUERY_UI',      JQUERY         . '/ui');
        define('JQUERY_PLUGINS', MAJISTIX_URL   . '/jquery/plugins');
        define('JQUERY_STYLES',  MAJISTIX_URL   . '/jquery/styles');
        define('JQUERY_THEMES',  MAJISTIX_URL   . '/jquery/themes');
    }

    /**
     * @desc Whether aliases should be defined or not. Will
     * lazily checks the configuration for majisti.app.useConstantsAliases
     * to determine if constants aliases should be defined. If no such
     * configuration is found, aliases will be defined by default.
     *
     * @return True if aliases should be used
     */
    static public function isAliasesUsed()
    {
        if( null === static::$_aliasesUsed ) {
            $selector = new \Majisti\Config\Selector(
                \Zend_Registry::get('Majisti_Config'));
            static::$_aliasesUsed = (bool)$selector->find(
                'majisti.app.useConstantsAliases', true);
        }

        return static::$_aliasesUsed;
    }

    /**
     * @desc Whether this application should define aliases.
     *
     * @param bool $useAliases
     */
    static public function setUseAliases($useAliases)
    {
        static::$_aliasesUsed = (bool)$useAliases;
    }

    /**
     * @desc Defines aliases for the previous defined constants.
     */
    static public function defineAliases()
    {
        if( static::isAliasesUsed() ) {
            define('APP_PATH', APPLICATION_PATH);
            define('APP_LIB',  APPLICATION_LIBRARY);
            define('APP_ENV',  APPLICATION_ENVIRONMENT);

            define('APP_PREFIX', APPLICATION_URL_PREFIX);
            define('APP_URL', APPLICATION_URL);
            define('APP_SCRIPTS', APPLICATION_URL_SCRIPTS);
            define('APP_STYLES', APPLICATION_URL_STYLES);
            define('APP_IMG', APPLICATION_URL_IMAGES);
            define('APP_IMG_LOC', APPLICATION_URL_IMAGES_LOCALE);

            define('APP_LANG', APPLICATION_LOCALE_CURRENT);
            define('APP_LANG_DEF', APPLICATION_LOCALE_DEFAULT);

            define('MAJ_ROOT', MAJISTI_ROOT);
            define('MAJ_PATH', MAJISTI_PATH);
            define('MAJ_PUB',  MAJISTI_PUBLIC);

            define('MAJX_PATH', MAJISTIX_PATH);
            define('MAJX_EXT',  MAJISTIX_EXTENSIONS);
            define('MAJX_MOD',  MAJISTIX_MODULES);

            define('MAJ_URL',  MAJISTI_URL);
            define('MAJX_URL', MAJISTIX_URL);

            define('MAJ_STYLES' , MAJISTI_URL_STYLES);
            define('MAJ_SCRIPTS', MAJISTI_URL_SCRIPTS);
            define('MAJ_IMG'    , MAJISTI_URL_IMAGES);
            define('MAJ_IMG_LOC', MAJISTI_URL_IMAGES_LOCALE);

            define('MAJX_STYLES', MAJISTIX_URL_STYLES);
            define('MAJX_SCRIPTS', MAJISTIX_URL_SCRIPTS);
            define('MAJX_IMG', MAJISTIX_URL_IMAGES);

            define('JQ', JQUERY);
            define('JQ_STYLES', JQUERY_STYLES);
            define('JQ_PLUGINS', JQUERY_PLUGINS);
            define('JQ_THEMES', JQUERY_THEMES);
        }
    }
}
