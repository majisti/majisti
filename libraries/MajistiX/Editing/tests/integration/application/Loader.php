<?php

namespace Majisti\Application;

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
        return array('majisti' => array(
            'app' => array(
                'namespace'   => 'MyApplication',
                'path'        => dirname(__DIR__),
                'env' => 'development',
            ),
            'ext' => array(),
            'lib' => array(
               'app'        => dirname(__DIR__) . '/library',
               'majisti'    => 'majisti/libraries',
            ),
            'autoFindLibraries' => true,
        ));
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
        $libraries = $this->getLibrariesPaths();

        set_include_path(implode(PATH_SEPARATOR,
                $libraries + array(get_include_path())));

        $this->updateSymlinks($libraries['majisti']);

        require_once 'Zend/Loader/Autoloader.php';
        $autoloader = \Zend_Loader_Autoloader::getInstance();

        $autoloader->setFallbackAutoloader(true);

        require_once 'Majisti/Loader/Autoloader.php';
        $autoloader->pushAutoloader(new \Majisti\Loader\Autoloader());
    }

    /**
     * @desc Update application's symlinks according to newly found library.
     *
     * @param string $lib Majisti's lib path
     */
    private function updateSymlinks($lib)
    {
        $appDir   = dirname(__DIR__);
        $majDir   = realpath($lib . '/..');
        $symlink  = $appDir . '/majisti';

        $updateSymlink = function($majPublicDir, $symlink) {
            @unlink($symlink);
            symlink($majPublicDir, $symlink);
        };

        if( file_exists($symlink) ) {
            if( readlink($symlink) !== $majDir ) {
                $updateSymlink($majDir, $symlink);
            }
        } else {
            $updateSymlink($majDir, $symlink);
        }

        if( !file_exists($appDir . '/public/majisti') ) {
            symlink('../majisti/public', $appDir . '/public/majisti');
        }

        if( !file_exists($appDir . '/public/images/majisti') ) {
            symlink('../majisti/images', $appDir . '/public/images/majisti');
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
        $this->createApplicationManager()->getApplication()->bootstrap()->run();
    }

    /**
     * @desc Returns every libraries specified in the options, autofinding them
     * if required.
     * @return string The libraries paths
     */
    public function getLibrariesPaths()
    {
        $paths   = array();
        $options = $this->getOptions();

        $autoFind = isset($options['majisti']['autoFindLibraries']) && $options['majisti']['autoFindLibraries'];

        foreach($options['majisti']['lib'] as $key => $lib) {
            if( !($path = realpath($lib) ) ) {
                if( $autoFind ) {
                    $path = $this->findLibrary($lib);
                } else {
                    throw new \Exception("Path ${lib} does not map anywhere!");
                }
            }

            $paths[$key] = $path;
        }

        return $paths;
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
