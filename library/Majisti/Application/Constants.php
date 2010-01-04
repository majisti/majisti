<?php

namespace Majisti\Application;

/**
 * @desc Class that declares constants needed in standard applications.
 *
 * TODO: this should be more flexible according to configuration
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
use Majisti\Config;

class Constants
{
	private function __construct()
	{}

	/**
     * @desc Defines all the needed constants for an application.
     *
     * TODO: update comment documentation
     *
     * @param $applicationPath The application path
     */
    static public function defineConstants($applicationPath)
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

        define('MAJISTI_ROOT', dirname(dirname(dirname(__FILE__))));
        define('MAJISTI_PATH', MAJISTI_ROOT . '/Majisti');

        define('MAJISTIC_PATH', MAJISTI_ROOT . '/MajistiC');

        define('MAJISTIX_PATH', MAJISTI_ROOT . '/MajistiX');
        define('MAJISTIX_EXTENSIONS_PATH', MAJISTIX_PATH . '/Extensions');
        define('MAJISTIX_MODULES_PATH',    MAJISTIX_PATH . '/Modules');

        /*
         * TODO: support overriding with custom virtual host such as
         * static.local or static.majisti.com and etc...
         */

        define('APPLICATION_URL_PREFIX', $request->getScheme()
            . '://' . $request->getHttpHost());

        define('APPLICATION_URL', APPLICATION_URL_PREFIX . BASE_URL);
        define('APPLICATION_STYLES',  APPLICATION_URL . '/styles');
        define('APPLICATION_SCRIPTS', APPLICATION_URL . '/scripts');
        define('APPLICATION_IMAGES',  APPLICATION_URL . '/images');

        self::_defineAliases();
    }

    /**
     * @desc Defines all volatile constants
     *
     * TODO: update comment documentation
     */
    static public function defineVolatileConstants()
    {
        $selector = new \Majisti\Config\Selector(
            \Zend_Registry::get('Majisti_Config'));

        if( $staticUrl = $selector->find('urls.static', false) ) {
            define('MAJISTI_PUBLIC', $staticUrl);
        }

        if( !defined('MAJISTI_PUBLIC') ) {
            $request = new \Zend_Controller_Request_Http();
            define('MAJISTI_PUBLIC',
                APPLICATION_URL_PREFIX . '/' . MAJISTI_FOLDER_NAME . '/public');
        }

        if( $urls = $selector->find('urls', false) ) {
            foreach ($urls as $key => $url) {
            	define(
            	   strtoupper(APPLICATION_NAME) . '_URL_' .
            	   strtoupper($key), $url);
            }
        }

        define('MAJISTI_URL',  MAJISTI_PUBLIC . '/majisti');
        define('MAJISTIC_URL', MAJISTI_PUBLIC . '/majistic');
        define('MAJISTIX_URL', MAJISTI_PUBLIC . '/majistix');

        define('JQUERY', MAJISTI_PUBLIC . '/externals/jquery');
        define('JQUERY_PLUGINS', JQUERY . '/plugins');
        define('JQUERY_STYLES',  JQUERY . '/styles');
        define('JQUERY_THEMES',  JQUERY . '/themes');
        define('JQUERY_UI',      JQUERY . '/ui');
    }

	/**
     * @desc Define aliases if it is supported in the configuration
     */
    static protected function _defineAliases()
    {
        /* TODO: support disable aliases */
        define('APP_PATH', APPLICATION_PATH);
        define('APP_LIB',  APPLICATION_LIBRARY);
        define('APP_ENV',  APPLICATION_ENVIRONMENT);

        define('MA_ROOT', MAJISTI_ROOT);
        define('MA_PATH', MAJISTI_PATH);

        /* those constants are way too confusing, something better must be thought of */
        define('MAC_PATH', MAJISTIC_PATH);

        define('MAX_PATH', MAJISTIX_PATH);
        define('MAX_MODULES_PATH', MAJISTIX_MODULES_PATH);
    }
}
