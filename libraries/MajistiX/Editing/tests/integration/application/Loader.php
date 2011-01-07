<?php

namespace Majisti\Application;

/**
 * TODO: documentation
 *
 * @desc Deploy anywhere Majisti's concrete loader
 *
 * @author Majisti
 * @version 1.1.0
 */
final class Loader
{
    /**
     * @var Array
     */
    private $_options;

    private $_majistiLibraryPath;

    const MAX_DEPTH = 100;

    public function __construct($options = array())
    {
        $this->setOptions($options);
        $this->init();
    }

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

    public function getOptions()
    {
        return $this->_options;
    }

    public function setOptions(array $options = array())
    {
        $this->_options = array_replace_recursive(
            self::getDefaultOptions(),
            $options
        );
    }

    private function init()
    {
        $libraries = $this->getLibrariesPaths();

        set_include_path(implode(PATH_SEPARATOR,
                $libraries + array(get_include_path())));

        require_once 'Zend/Loader/Autoloader.php';
        $autoloader = \Zend_Loader_Autoloader::getInstance();

        $autoloader->setFallbackAutoloader(true);
        $autoloader->suppressNotFoundWarnings(true);

        require_once 'Majisti/Loader/Autoloader.php';
        $autoloader->pushAutoloader(new \Majisti\Loader\Autoloader());
    }

    public function createApplicationManager()
    {
        return new Manager($this->getOptions());
    }

    public function launchApplication()
    {
        $this->createApplicationManager()->getApplication()->bootstrap()->run();
    }

    public function getLibrariesPaths()
    {
        $paths   = array();
        $options = $this->getOptions();

        $autoFind = isset($options['majisti']['autoFindLibraries']) && $options['majisti']['autoFindLibraries'];

        foreach($options['majisti']['lib'] as $lib) {
            if( !($path = realpath($lib) ) ) {
                if( $autoFind ) {
                    $path = $this->findLibrary($lib);
                } else {
                    throw new \Exception("Path ${lib} does not map anywhere!");
                }
            }

            $paths[] = $path;
        }

        return $paths;
    }

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

    /**
     * @desc Includes Majisti's library and its external libraries
     * such as Zend and ZendX.
     */
    private function setIncludePaths()
    {
        set_include_path(implode(PATH_SEPARATOR, array(
            realpath($this->getMajistiPath(self::MAX_DEPTH) . '/../majistip/library'),
            realpath($this->getMajistiPath(self::MAX_DEPTH) . '/library'),
            dirname(__DIR__) . '/library',
            realpath($this->getMajistiPath(self::MAX_DEPTH) . '/externals'),
            get_include_path(),
        )));
    }

    /**
     * @desc Registers Zend's loader for Zend's default class searching and
     * Majisti's default AutoLoader which supports PHP namespaces.
     */
    private function registerLoaders()
    {
        require_once 'Zend/Loader/Autoloader.php';
        $autoloader = \Zend_Loader_Autoloader::getInstance();

        require_once 'Majisti/Loader/Autoloader.php';
        $autoloader->pushAutoloader(new \Majisti\Loader\Autoloader());
    }

    /**
     * @desc Will start a upright directory search for a folder entitled by
     * the MAJISTI_FOLDER_NAME constant unless a MAJISTI_LIBRARY_PATH absolute
     * path is setup. If the folder is not found after the depth param
     * counter has reached 0 an exception will be thrown.
     *
     * Note that this function is lazy and note that calling this function
     * more than once after the initial call will always return the same
     * path, even if the library is no longer on the filesystem.
     *
     * @param int $maxDepth [optionnal] The maximum depth the search should go.
     *
     * @throws Exception if MAJISTI_FOLDER_NAME constant is not
     * defined when MAJISTI_LIBRARY_PATH is omitted.
     *
     * @return String Returns Majisti's library top level library path.
     * Top level means the absolute path found according to
     * MAJISTI_FOLDER_NAME | MAJIST_LIBRARY_PATH constant defined
     * in public/index.php
     */
    private function getMajistiPath($maxDepth = 100)
    {
        if( null !== $this->_majistiLibraryPath ) {
            return $this->_majistiLibraryPath;
        }

        if( defined('MAJISTI_LIBRARY_PATH') ) {
            $this->_majistiLibraryPath = MAJISTI_LIBRARY_PATH;
        } else {
            /* constant must be defined */
            if( !defined('MAJISTI_FOLDER_NAME') ) {
                throw new \Exception('MAJISTI_FOLDER_NAME not defined');
            }

            $this->_majistiLibraryPath = $this->searchMajistiFolderName($maxDepth);
        }

        return $this->_majistiLibraryPath;
    }

    /**
     * @desc Starts the lazy searching, will stop on $depth max depth to
     * ensure no infinite loops.
     *
     * @throws Exception If the library directory was never found
     * @return String Majisti's library absolute path
     */
    private function searchMajistiFolderName($maxDepth)
    {
        $upDir = dirname(__DIR__);

        $foundDir = false;
        while( !$foundDir && $maxDepth > 0 ) {
            $foundDir = file_exists($upDir . '/' . MAJISTI_FOLDER_NAME);

            if( !$foundDir ) {
                $upDir = dirname($upDir);
            }
            $maxDepth--;
        }

        if( $maxDepth === 0 ) {
            throw new \Exception("Majisti's library
                 folder not found under the name " . MAJISTI_FOLDER_NAME);
        }

        return realpath($upDir . '/' . MAJISTI_FOLDER_NAME);
    }
}
