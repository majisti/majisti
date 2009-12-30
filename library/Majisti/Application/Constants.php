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
class Constants
{
	private function __construct()
	{}

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
        define('MAJISTIX_MODULES_PATH', MAJISTIX_PATH . '/Modules');

        /*
         * TODO: support overriding with custom virtual host such as
         * static.local or static.majisti.com and etc...
         */

        define('MAJISTI_URL_PREFIX', $request->getScheme()
            . '://' . $request->getHttpHost());

        define('MAJISTI_PUBLIC', MAJISTI_URL_PREFIX
            . '/' . MAJISTI_FOLDER_NAME
            . '/public');

        define('MAJISTI_URL', MAJISTI_PUBLIC . '/majisti');
        define('MAJISTIC_URL', MAJISTI_PUBLIC . '/majistic');
        define('MAJISTIX_URL', MAJISTI_PUBLIC . '/majistix');

        self::_defineAliases();
    }

	/**
     * @desc Define aliases if it is supported in the configuration
     */
    static protected function _defineAliases()
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
