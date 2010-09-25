<?php

namespace Majisti\Test;

/**
 * @desc This test helper is the singleton helper for every test case.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Helper
{
    /**
     * @var TestHelper 
     */
    static protected $_instance;

    /**
     * @var Array
     */
    static protected $_defaultOptions;

    /**
     * @var \Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var string
     */
    protected $_testPath;

    /**
     * @var string
     */
    protected $_majistiPath;

    /**
     * @var string
     */
    protected $_majistiUrl;

    /**
     * @var string
     */
    protected $_majistiBaseUrl;

    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @desc Returns the helper's singleton instance.
     *
     * @return Helper
     */
    static public function getInstance()
    {
        if ( null === static::$_instance ) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * @desc Factory method to create a Majisti application instance.
     *
     * @return \Zend_Application The application instance
     */
    public function createApplicationInstance()
    {
        $options = $this->getOptions();
        $app = new \Zend_Application($this->getOption('majisti.app.path'));
        $app->setOptions($options);

        return $app;
    }

    /**
     * @desc Factory method to create a Majisti boostrap instance.
     *
     * @return \Majisti\Application\Bootstrap The bootstrap instance
     */
    public function createBootstrapInstance()
    {
        return new \Majisti\Application\Bootstrap(
            $this->createApplicationInstance()
        );
    }

    /**
     * @desc Returns an option according to a CSS like selection.
     *
     * @param string $selection The selection
     *
     * @throws \Majisti\Config\Exception If the option cannot be found.
     *
     * @return string The option
     */
    public function getOption($selection)
    {
        $selector = new \Majisti\Config\Selector(
            new \Zend_Config($this->getOptions()));

        return $selector->find($selection);
    }

    /**
     * @desc Returns all the helper's options as an array.
     *
     * @return array The options
     */
    public function getOptions()
    {
        return array_replace_recursive(static::getDefaultOptions(),
            $this->_options);
    }

    /**
     * @desc Returns the default options
     *
     * @return array The default options
     */
    static public function getDefaultOptions()
    {
        if( null === static::$_defaultOptions ) {
            $helper = static::getInstance();
            static::$_defaultOptions = array('majisti' => array(
                'path' => $helper->getMajistiPath()
            ));
        }

        return static::$_defaultOptions;
    }

    /**
     * @desc Sets the default options
     *
     * @param array $options The default options
     */
    static public function setDefaultOptions(array $options)
    {
        static::$_defaultOptions = $options;
    }

    /**
     * @desc Sets the helper's options.
     *
     * @param array $options The options
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
    }

    /**
     * @desc Inits the helper, calling every init methods contained
     * in the class.
     */
    public function init()
    {
        $methods = get_class_methods($this);

        foreach ( $methods as $method ) {
            if (4 < strlen($method) && 'init' === substr($method, 0, 4)) {
                $this->$method();
            }
        }
    }

    /**
     * @desc Sets the required include paths
     */
    public function initIncludePaths()
    {
        $includePaths = array(
            $this->getTestsPath(),
            $this->getMajistiPath() . '/libraries',
            get_include_path()
        );

        set_include_path(implode(PATH_SEPARATOR, $includePaths));
    }

    /**
     * @desc Inits the php settings
     */
    public function initPhpSettings()
    {
        ini_set('memory_limit', '256M');
    }

    /**
     * @desc set error reporting to the level to which the code must comply.
     */
    public function initErrorReporting()
    {
        error_reporting( E_ALL | E_STRICT );
    }

    /**
     * @desc Init autoloaders
     */
    public function initAutoloaders()
    {
        /* Zend */
        require_once 'Zend/Loader/Autoloader.php';
        \Zend_Loader_Autoloader::resetInstance();
        $loader = \Zend_Loader_Autoloader::getInstance();

        /* Majisti */
        require_once 'Majisti/Loader/Autoloader.php';
        $loader->pushAutoloader(new \Majisti\Loader\Autoloader());
    }

    /**
     * @desc Include all required dependencies
     */
    public function initDependencies()
    {
        /* PHPUnit */
        if( !class_exists('PHPUnit_TextUI_Command') ) {
            $phpunit = array('Framework', 'Framework/IncompleteTestError',
                             'Framework/TestCase', 'Framework/TestSuite',
                             'Runner/Version', 'TextUI/TestRunner', 'Util/Filter');

            foreach ($phpunit as $file) {
                require_once 'PHPUnit/' . $file . '.php';
            }
        }
    }

    /**
     * @desc Inits xdebug.
     */
    public function initXdebug()
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
                    'xdebug.var_display_max_depth'      => 3,
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
    public function initCodeCoverage()
    {
        $majistiPath = $this->getMajistiPath();

        if( 'cli' === PHP_SAPI ) {
            /* exclude those directories */
            $dirs = array(
                'libraries/Zend',
                'libraries/ZendX',
                'resources',
                'tests',
            );

            foreach( $dirs as $dir ) {
                \PHPUnit_Util_Filter::addDirectoryToFilter(
                        $majistiPath . '/' . $dir);
            }

            /* exclude files with provided suffixes */
            $suffixes = array(
                'php',
                'phtml',
                'inc',
            );

            foreach ( $suffixes as $suffix) {
                \PHPUnit_Util_Filter::addDirectoryToFilter($majistiPath .
                        '/tests', ".$suffix");
            }

            /* exclude specific files */
            $files = array();

            foreach( $files as $file ) {
                \PHPUnit_Util_Filter::addFileToFilter($file);
            }
        }
    }

    /**
     * @desc Inits uncategorized
     */
    public function initMiscelleneous()
    {
        $request     = $this->getRequest();

        /* be a little bit more verbose according to request param */
        if( $request->has('v') ) {
            \Majisti\Test\Runner::setDefaultArguments(array(
                'printer' => new \Majisti\Test\Listener\Simple\Html(null, true)
            ));
        }

        \Zend_Session::start();
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
     * @desc Returns Majisti's directory path.
     *
     * @return string Majisti's dir path
     */
    public function getMajistiPath()
    {
        if( null === $this->_majistiPath ) {
            $this->_majistiPath = realpath(__DIR__ . '/../../..');
        }

        return $this->_majistiPath;
    }

    /**
     * @desc Returns the Majisti's url.
     *
     * @return string The url
     */
    public function getMajistiUrl()
    {
        return $this->_majistiUrl;
    }

    /**
     * @desc Sets teh Majisti's url.
     *
     * @param string $majistiUrl The url
     */
    public function setMajistiUrl($majistiUrl)
    {
        $this->_majistiUrl = $majistiUrl;
    }

    /**
     * @desc Returns the Majisti's url.
     *
     * @return string The url
     */
    public function getMajistiBaseUrl()
    {
        return str_replace(
            realpath($_SERVER['DOCUMENT_ROOT']),
            '', 
            $this->getMajistiPath()
        );
    }

    /**
     * @desc Sets teh Majisti's url.
     *
     * @param string $majistiBaseUrl The url
     */
    public function setMajistiBaseUrl($majistiBaseUrl)
    {
        $this->_majistiBaseUrl = $majistiBaseUrl;
    }


    /**
     * @desc Sets Majisti's dir path.
     *
     * @param string $majistiPath The dir path
     */
    public function setMajistiPath($majistiPath)
    {
        $this->_majistiPath = $majistiPath;
    }

    /**
     * @desc Returns Majisti's test directory path.
     *
     * @return string The test dir path.
     */
    public function getTestsPath()
    {
        if( null === $this->_testPath ) {
            $this->_testPath = $this->getMajistiPath() . '/tests';
        }

        return $this->_testPath;
    }

    /**
     * @desc Sets Majisti's directory path.
     *
     * @param string $testPath The test path
     */
    public function setTestPath($testPath)
    {
        $this->_testPath = $testPath;
    }
}
