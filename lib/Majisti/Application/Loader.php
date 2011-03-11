<?php

namespace Majisti\Application;

use Doctrine\Common\ClassLoader;

/**
 * @desc Deploy anywhere Majisti's concrete loader
 *
 * @author Steven Rosato
 */
final class Loader
{
    /**
     * @var Array
     */
    private $_options;

    const MAX_DEPTH = 100;

    /**
     * @desc Constructs the loader.
     * @param array $options The options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
        $this->init();
    }

    /**
     * @desc Returns the default options.
     * @return array The default options
     */
    static public function getDefaultOptions()
    {
        $conf = array('majisti' => array(
            'path'    => 'majisti-0.4/lib',
            'app' => array(
                'namespace' => 'Cochimbec',
                'path'      => dirname(__DIR__),
                'env'       => 'development',
            ),
            'ext'     => array(),
        ));

        if( 'cli' === php_sapi_name() ) {
            $baseUrl = dirname(__DIR__) . '/public';
            $conf['majisti']['app']['baseUrl'] = $baseUrl;
            $conf['majisti']['app']['url']     = $baseUrl;
        }

        return $conf;
    }

    /**
     * @desc Returns the options
     *
     * @return array The options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @desc Sets the options
     * @param array $options The options
     */
    public function setOptions(array $options = array())
    {
        $this->_options = array_replace_recursive(
            self::getDefaultOptions(),
            $options
        );
    }

    /**
     * @desc Inits the loader
     */
    private function init()
    {
        $this->findMajisti();
        $this->updateSymlinks();

        $majPath = $this->_options['majisti']['path'];

        require_once $majPath .
            '/vendor/doctrine2-common/lib/Doctrine/Common/ClassLoader.php';
        $loader = new ClassLoader('Majisti', $majPath);
        $loader->register();
    }

    /**
     * @desc Update application's symlinks according to newly found library.
     */
    private function updateSymlinks()
    {
        $lib      = $this->_options['majisti']['path'];
        $appDir   = dirname(__DIR__) . '/public';
        $majDir   = realpath($lib . '/../public');
        $symlink  = $appDir . '/majisti';

        $updateSymlink = function($majDir, $symlink) {
            @unlink($symlink);
            symlink($majDir, $symlink);
        };

        if( file_exists($symlink) ) {
            if( readlink($symlink) !== $majDir ) {
                $updateSymlink($majDir, $symlink);
            }
        } else {
            $updateSymlink($majDir, $symlink);
        }
    }

    /**
     * @desc Creates an application manager.
     * @return Manager The manager
     */
    public function createApplicationManager()
    {
        return new Manager($this->getOptions());
    }

    /**
     * @desc Launches the application, bootstrapping it and running it altogether.
     */
    public function launchApplication()
    {
        $this->createApplicationManager()
             ->getApplication()
             ->bootstrap()
             ->run();
    }

    /**
     * @desc Finds the majisti library and resolves its real path.
     */
    public function findMajisti()
    {
        $paths   = array();
        $lib     = $this->_options['majisti']['path'];

        if( !($path = realpath($lib) ) ) {
            $this->_options['majisti']['path'] = $this->findLibrary($lib);
        }
    }

    /**
     * @desc Finds a library updir.
     *
     * @param string $partialPath The partial path
     * @return string The found lib path
     *
     * @throws \Exception If MAX_DEPTH is reached. This is to prevent
     * infinite loops when the library is not found.
     */
    public function findLibrary($partialPath)
    {
        $depth = self::MAX_DEPTH;
        $dir   = __DIR__;
        $found = false;

        $partialPath = trim($partialPath, '/');

        while( !$found && $depth > 0 ) {
            if( !realpath($dir . '/' . $partialPath) ) {
                $dir = dirname($dir);
            } else {
                $found = true;
            }

            $depth--;
        }

        if( !$found ) {
            throw new \Exception("Max depth level of " . self::MAX_DEPTH . " reached
                    while searching for library with path {$partialPath}");
        }

        return $dir . '/' . $partialPath;
    }
}
