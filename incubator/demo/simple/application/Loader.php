<?php

namespace MyProject;

/**
 * TODO: documentation
 * 
 * @desc Deploy anywhere Majisti's concrete loader
 * 
 * @author Steven Rosato
 */
final class Loader
{
	/** @var Loader */
	static private $_instance;
	
	private $_majistiLibraryPath;
	
	/**
	 * @desc Starts the loading
	 * 
	 * @throws Exception If this function is called more than one time
	 */
	static public function load($autoRunApplication = true)
	{
		if( null !== self::$_instance ) {
			throw new \Exception('Instance already loaded');
		}
		
		self::$_instance = new self($autoRunApplication);
	}
	
	/**
	 * @desc Constructs this application's Loader, initializing the include paths, the required Loaders
	 * and the application's constants.
	 */
	private function __construct($autoRun = true)
	{
		$this->_setIncludePaths();
		$this->_registerLoaders();
		
		if( $autoRun ) {
			self::run();
		}
	}
	
	/**
	 * @desc Includes Majisti's library and its external libraries such as Zend and ZendX.
	 */
	private function _setIncludePaths()
	{
		set_include_path(implode(PATH_SEPARATOR, array( 
			realpath($this->_getMajistiTopLevelLibraryPath() . '/laboratory/library'), /* laboratory */ 
			$this->_getMajistiTopLevelLibraryPath() . '/incubator/library', /* incubator */
			$this->_getMajistiTopLevelLibraryPath() . '/standard/library', /* standard */
			$this->_getMajistiTopLevelLibraryPath() . '/standard/externals',
			get_include_path(),
		)));
	}
	
	/**
	 * @desc Registers Zend's loader for Zend's default class searching and Majisti's default AutoLoader
	 * which supports PHP namespaces.
	 */
	private function _registerLoaders()
	{
		require_once 'Zend/Loader/Autoloader.php';
		$autoloader = \Zend_Loader_Autoloader::getInstance();
		
		require_once 'Majisti/Loader/Autoloader.php';
		$autoloader->pushAutoloader(new \Majisti\Loader\Autoloader());
	}
	
	/**
	 * @desc Will start a upright directory search for a folder entitled by the MAJISTI_FOLDER_NAME constant
	 * unless a MAJISTI_LIBRARY_PATH absolute path is setup.
	 * If the folder is not found after the depth param counter has reached 0 an exception will be thrown.
	 * 
	 * Note that this function is lazy and note that calling this function more than once after the
	 * initial call will always return the same path, even if the library is no longer on the filesystem.
	 * 
	 * @param int $maxDepth [optionnal] The maximum depth the search should go.
	 * 
	 * @throws Exception if MAJISTI_FOLDER_NAME constant is not defined when MAJISTI_LIBRARY_PATH
	 * is omitted.
	 * 
	 * @return String Returns Majisti's library top level library path. Top level means
	 * the absolute path found according to MAJISTI_FOLDER_NAME | MAJIST_LIBRARY_PATH 
	 * constant defined in public/index.php
	 */
	private function _getMajistiTopLevelLibraryPath($maxDepth = 100)
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
			
			$this->_majistiLibraryPath = $this->_searchMajistiFolderName($maxDepth);
		}
		
		return $this->_majistiLibraryPath;
	}
	
	/**
	 * @desc Starts the lazy searching, will stop on $depth max depth to ensure no infinite loops.
	 * 
	 * @throws Exception If the library directory was never found
	 * @return String Majisti's library absolute path
	 */
	private function _searchMajistiFolderName($maxDepth)
	{
		$upDir = dirname(dirname(__FILE__));
		
		$foundDir = false;
		while( !$foundDir && $maxDepth > 0 ) {
			$foundDir = file_exists($upDir . '/' . MAJISTI_FOLDER_NAME);
			
			if( !$foundDir ) {
				$upDir = dirname($upDir);
			}
			$maxDepth--;
		}
		
		if( $maxDepth === 0 ) {
			throw new \Exception("Majisti's library folder not found under the name " . MAJISTI_FOLDER_NAME);
		}
		
		return realpath($upDir . '/' . MAJISTI_FOLDER_NAME);
	}
	
	/**
	 * @desc Runs the application
	 */
	static public function run()
	{
		/* Create application, bootstrap, and run */
		$application = new \Majisti\Application(dirname(__FILE__));
		$application->bootstrap()
		            ->run();
	}
}
