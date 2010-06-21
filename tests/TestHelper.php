<?php

namespace Majisti;

TestHelper::getInstance()->init();

/**
 * @desc This file should be required once with every TestCase.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class TestHelper
{
    static protected $_instance;

    /**
     * @var \Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * @return TestHelper
     */
    static public function getInstance()
    {
        if ( null === static::$_instance ) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    public function init()
    {
        $this->initErrorReporting();
        $this->initIncludePaths();
        $this->initPhpSettings();
        $this->initDependencies();
        $this->initAutoloaders();
        $this->initXdebug();
        $this->initCodeCoverage();
        $this->initMiscelleneous();
    }

    /**
     * @desc set error reporting to the level to which the code must comply.
     */
    protected function initErrorReporting()
    {
        error_reporting( E_ALL | E_STRICT );
    }

    /**
     * @desc Sets the required include paths
     */
    protected function initIncludePaths()
    {
        $majistiRoot = $this->getLibraryRoot();
        $includePaths = array(
            $majistiRoot,
            "$majistiRoot/externals",
            "$majistiRoot/library",
            "$majistiRoot/tests",
            "$majistiRoot/tests/externals",
            "$majistiRoot/tests/library",
            get_include_path()
        );

        set_include_path(implode(PATH_SEPARATOR, $includePaths));
    }

    protected function initPhpSettings()
    {
        ini_set('memory_limit', '256M');

        $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../../');
    }

    /**
     * @desc Include all required dependencies
     */
    protected function initDependencies()
    {
        /* PHPUnit */
        $phpunit = array('Framework', 'Framework/IncompleteTestError',
                         'Framework/TestCase', 'Framework/TestSuite',
                         'Runner/Version', 'TextUI/TestRunner', 'Util/Filter');

        foreach ($phpunit as $file) {
            require_once 'PHPUnit/' . $file . '.php';
        }
    }

    /**
     * @desc Init autoloaders
     */
    protected function initAutoloaders()
    {
        /* Zend */
        require_once 'Zend/Loader/Autoloader.php';
        $loader = $toUnset[] = \Zend_Loader_Autoloader::getInstance();

        /* Majisti */
        require_once 'Majisti/Loader/Autoloader.php';
        $loader->pushAutoloader(new \Majisti\Loader\Autoloader());
    }

    protected function initXdebug()
    {
        /* configure xdebug for performance, if the module is enabled */
        $request = $this->getRequest();
        if( extension_loaded('xdebug') ) {
            if( !$request->has('d') ) {
                xdebug_disable();
            } else {
                $params = array(
                    'xdebug.collect_params'             => 300,
                    'xdebug.var_display_max_data'       => 300,
                    'xdebug.var_display_max_children'   => 300,
                    'xdebug.var_display_max_depth'      => 300,
                );

                foreach ($params as $key => $value) {
                   ini_set($key, $value);
                }
            }
        }
    }

    /**
     * @desc Code coverage filtering. Works only using
     * the command line.
     */
    protected function initCodeCoverage()
    {
        $majistiRoot = $this->getLibraryRoot();

        if( 'cli' === PHP_SAPI ) {
            /* exclude those directories */
            $dirs = array(
                'externals',
                'resources',
                'tests',
            );

            foreach( $dirs as $dir ) {
                \PHPUnit_Util_Filter::addDirectoryToFilter(
                        $majistiRoot . '/' . $dir);
            }

            /* exclude files with provided suffixes */
            $suffixes = array(
                'php',
                'phtml',
                'inc',
            );

            foreach ( $suffixes as $suffix) {
                \PHPUnit_Util_Filter::addDirectoryToFilter($majistiRoot . 
                        '/tests', ".$suffix");
            }

            /* exclude specific files */
            $files = array(
                $majistiRoot . '/library/Majisti/Application/Constants.php',
            );

            foreach( $files as $file ) {
                \PHPUnit_Util_Filter::addFileToFilter($file);
            }
        }
    }

    protected function initMiscelleneous()
    {
        $majistiRoot = $this->getLibraryRoot();
        $request     = $this->getRequest();

        /* instanciate a mock application */
        define('MAJISTI_FOLDER_NAME', dirname($majistiRoot));
        define('APPLICATION_NAME', 'Majisti_Test');
        define('APPLICATION_ENVIRONMENT', 'development');

        \Majisti\Application::setApplicationPath(
            $majistiRoot . '/tests/library/Majisti/Application/_project/application');
        \Majisti\Application::getInstance()->bootstrap();

        /* be a little bit more verbose according to request param */
        if( $request->has('v') ) {
            \Majisti\Test\Runner::setDefaultArguments(array(
                'printer' => new \Majisti\Test\Listener\Simple\Html(null, true)
            ));
        }

        \Zend_Session::$_unitTestEnabled = true;
    }

    /**
     * @return \Zend_Controller_Request_Http
     */
    public function getRequest()
    {
        if( null == $this->_request ) {
            $this->_request = new \Zend_Controller_Request_Http();
        }

        return $this->_request;
    }

    /**
     * @return string The library's root
     */
    public function getLibraryRoot()
    {
        return realpath(dirname(__FILE__) . '/../');
    }
}
