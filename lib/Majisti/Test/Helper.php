<?php

namespace Majisti\Test;

use Majisti\Test\Util\ServerInfo,
    \Doctrine\Common\ClassLoader
;

require_once dirname(__DIR__) . '/../vendor/doctrine2-common/lib/Doctrine/Common/ClassLoader.php';

/**
 * @desc This test helper is the singleton helper for every test case.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Helper
{
    /**
     * @var Array
     */
    static protected $_defaultOptions;

    /**
     * @var Helper
     */
    static protected $_instance;

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
     * @var DatabaseHelper
     */
    protected $_dbHelper;

    /**
     * @desc Constructs the helper.
     *
     * @param array $options The options
     */
    protected function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * @desc Returns the single instance. If it was never instanciated,
     * it will instanciate a new Helper with the provided options.
     *
     * @param array $options The options
     * @return Helper
     */
    static public function getInstance(array $options = array())
    {
        if( null === static::$_instance ) {
            static::$_instance = new self($options);
        }

        return static::$_instance;
    }

    /**
     * @desc Adds a test module directory(ies) that will register a module
     * autoloader. The directory must follow the module file structure.
     *
     * @param string $namespace The namespace
     * @param string|array $basePath The base path(s)
     */
    public function addTestModuleDirectory($namespace, $basePath)
    {
        if( !is_array($basePath) ) {
            $basePath = array($basePath);
        }

        foreach( $basePath as $path) {
            new \Majisti\Application\ModuleAutoloader(array(
                'namespace' => $namespace,
                'basePath'  => $path,
            ));
        }

        return $this;
    }

    /**
     * @desc Adds an extension by adding required module autoloaders
     * for the extension test namespace and test directory.
     *
     * @param string $vendor The vendor name
     * @param string $testsNs The full extension tests namespace, such as
     * MajistiX\Tests\ExtensionName
     * @param string $testsDir The extension's test root directory
     *
     * @return Helper
     */
    public function addExtension($vendor, $testsNs, $testsDir)
    {
        $ns     = str_replace('\Tests', '', $testsNs);
        $srcDir = realpath($testsDir . '/../lib');

        $nsToDir = function($ns) {
            return '/' . str_replace('\\', '/', $ns);
        };

        $this->addTestModuleDirectory($ns, $srcDir . $nsToDir($ns));
        $this->addTestModuleDirectory($testsNs, $testsDir . $nsToDir($testsNs));

        $loader = new ClassLoader($ns, $srcDir);
        $loader->register();

        $loader = new ClassLoader($testsNs, $testsDir);
        $loader->register();

        /* last part of namespace (extension name) */
        $this->addApplicationExtension($vendor, 
            substr(strrchr($testsNs, '\\'), 1));

        return $this;
    }

    /**
     * @desc Adds an extension to bootstrap.
     *
     * @param string $vendor The vendor name
     * @param string $extension The extension name
     *
     * @return Helper
     */
    public function addApplicationExtension($vendor, $extension)
    {
        $newOptions = array_merge_recursive(
            $this->getOptions(),
            array('resources' => array(
                'extensions' => array($vendor => array($extension))
            )
        ));

        $this->setOptions($newOptions);

        return $this;
    }

    /**
     * @desc Factory method to create a Zend application instance.
     *
     * @return \Zend_Application The application instance
     */
    public function createApplicationInstance($options = array())
    {
        $options = array_merge_recursive($this->getOptions(), $options);
        $manager = new \Majisti\Application\Manager($options);

        return $manager->getApplication();
    }

    /**
     * @desc Factory method to create a Majisti bootstrap instance.
     *
     * @return \Majisti\Application\Bootstrap The bootstrap instance
     */
    public function createBootstrapInstance($options = array())
    {
        return $this->createApplicationInstance($options)->getBootstrap();
    }

    /**
     * @desc Returns the database helper.
     *
     * @return Database\Helper The database helper
     */
    public function getDatabaseHelper()
    {
        if( null === $this->_dbHelper ) {
            $this->_dbHelper = new Database\DoctrineHelper($this);
        }

        return $this->_dbHelper;
    }

    /**
     * @desc Sets the database helper.
     *
     * @param Database\Helper $helper The database helper
     */
    public function setDatabaseHelper(Database\Helper $helper)
    {
        $this->_dbHelper = $helper;
    }

    /**
     * @desc Creates the database schema.
     *
     * @return Helper this
     */
    public function createDatabaseSchema()
    {
        $this->getDatabaseHelper()->createSchema();
        return $this;
    }

    /**
     * @desc Drops the database schema.
     *
     * @return Helper this
     */
    public function dropDatabaseSchema()
    {
        $this->getDatabaseHelper()->dropSchema();
        return $this;
    }

    /**
     * @desc Updates the database schema.
     *
     * @return Helper this
     */
    public function updateDatabaseSchema()
    {
        $this->getDatabaseHelper()->updateSchema();
        return $this;
    }

    /**
     * @desc Truncates the provided database tables.
     *
     * @param array $tables Array of mixed tables, could be either
     * table name, repositories, depending on the database helper used.
     *
     * @return Helper this
     */
    public function truncateDatabaseTables(array $tables)
    {
        $this->getDatabaseHelper()->truncateTables($tables);
        return $this;
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
            static::$_defaultOptions = array(
                'majisti' => array(
                    'path' => realpath(__DIR__ . '/../../..'),
                ),
                'mvc' => false,
            );
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
        $options = $this->getOptions();

        $includePaths = array_unique(array(
            get_include_path(),
            $options['majisti']['app']['path'] . '/tests',
            $this->getMajistiPath() . '/tests',
            $this->getMajistiPath() . '/lib',
            $this->getMajistiPath() . '/lib/vendor/zend/tests'
        ));

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

        $loader->setFallbackAutoloader(true);
        $loader->suppressNotFoundWarnings(true);

        /* Majisti */
        require_once 'Majisti/Loader/Autoloader.php';
        $loader->pushAutoloader(new \Majisti\Loader\Autoloader());

        $maj = $this->getOption('majisti');

        /* library autoloader */
        if( file_exists($libPath = $maj->app->path . '/tests/lib') ) {
            new \Majisti\Application\ModuleAutoloader(
                array(
                    'namespace' => $maj->app->namespace,
                    'basePath'  => $libPath,
                )
            );
        }

        /* application's modules autoloaders */
        $modulesPath = $maj->app->path . '/tests/application/modules';

        if( file_exists($modulesPath) ) {
            foreach( new \DirectoryIterator($modulesPath) as $file ) {
                if( $file->isDot() ) continue;

                if( $file->isDir() ) {
                    $filename  = $file->getFilename();

                    new \Majisti\Application\ModuleAutoloader(
                        array(
                            'namespace' => $maj->app->namespace .
                                '\\' . ucfirst($filename),
                            'basePath'  => "{$modulesPath}/{$filename}",
                        )
                    );
                }
            }
        }
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

        if( ServerInfo::isCliRunning() ) {
            /* exclude those directories */
            $dirs = array(
                'lib/Zend',
                'lib/ZendX',
                'lib/vendor',
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

            $testDirs = array(
                'tests',
                'lib/vendor/zend/tests'
            );

            foreach( $testDirs as $dir ) {
                foreach ( $suffixes as $suffix ) {
                    \PHPUnit_Util_Filter::addDirectoryToFilter($majistiPath .
                            '/' . $dir, $suffix);
                }
            }
        }
    }

    /**
     * @desc Inits uncategorized
     */
    public function initMiscelleneous()
    {
        $request = $this->getRequest();
        $options = $this->getOptions();

        /*
         * The idea here is to make it appear as the unit tests are
         * running from the application's perspective as though
         * the request was handled on the index file.
         */
        $app = $options['majisti']['app'];
        $scriptFile = realpath($app['path']) . '/public/index.php';
        $baseUrl = str_replace(
            realpath($_SERVER['DOCUMENT_ROOT']),
            '',
            realpath($app['path'])
        ) . '/public';

        $_SERVER['SCRIPT_FILENAME'] = $scriptFile;
        $_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'] = "{$baseUrl}/index.php";
        $_SERVER['REQUEST_URI'] = $baseUrl;

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
        if( null === $this->_majistiBaseUrl ) {
            $this->_majistiBaseUrl =  str_replace(
                realpath($_SERVER['DOCUMENT_ROOT']),
                '',
                $this->getMajistiPath()
            );
        }

        return $this->_majistiBaseUrl;
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
     * @desc Sets Majisti's dir path.
     *
     * @param string $majistiPath The dir path
     */
    public function setMajistiPath($majistiPath)
    {
        $this->_majistiPath = $majistiPath;
    }
}
