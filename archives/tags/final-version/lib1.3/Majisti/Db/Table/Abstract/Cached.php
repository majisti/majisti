<?php

/**
 * This class will let the abstract table the option to cache it's queries
 * by automatically adding the Class cache adapter when calling the constructor.
 * Other cache adapter can be passed as parameter to the constructor but the user
 * must give it's own implementation for caching (frontend options and backend options).
 * 
 * @deprecated
 * 
 * @author Steven Rosato
 */
class Majisti_Db_Table_Abstract_Cached extends Zend_Db_Table_Abstract 
{
	protected $_cache; //adapter
	protected $_cache_dir;
	protected $_cache_lifetime;
	protected $_cache_prefix;
	
	/**
	 * The class constructor, will enable caching for this abstract table queries.
	 *
	 * @param String $cacheAdapter The adapter to use for caching.
	 */
	public function __construct($options = array())
	{
		if( isset($options['adapter']) ) {
			$this->_cache = $options['adapter'] == 'class' ? $this->createCacheClass() : $options['adapter'];
		} else {
			$this->_cache = $this->createCacheClass();
		}
		
		if( isset($options['cache_dir']) ) {
			$this->_cache_dir = $options['cache_dir'];
		} else {
			throw new Majisti_Db_Exception("cache_dir was not specified in the options.");
		}
		
		if( isset($options['lifetime']) ) {
			$this->_cache_lifetime = $options['lifetime'];
		} else {
			throw new Majisti_Db_Exception("lifetime was not specified in the options.");
		}
		
		if( isset($options['cache_id_prefix']) ) {
			$this->_cache_prefix = $options['cache_id_prefix'];
		} else {
			$this->_cache_prefix = 'majisti_cache';
		}
		
		parent::__construct($options);
	}
	
	/**
	 * Retrieve the cache adapter
	 *
	 * @return Mixed, a Zend_Cache adapter of any sort. Default is class adapter.
	 */
	public function getCache()
	{
		return $this->_cache();
	}
	
	/**
	 * Set a new cache adapter.
	 * 
	 * Post-condition: must implement own frontend/backend options
	 *
	 * @param Zend_Cache $cache The cache to append
	 */
	public function setCache($cache)
	{
		$this->_cache = $cache;
	}
	
	/**
	 * Creates the cache class frontend/backend options
	 *
	 * @return Zend_Cache The created cache.
	 */
	protected function _createCacheClass()
	{
		$frontendOptions = array(
			'lifetime' 					=> $this->_cache_lifetime, 
			'cache_id_prefix' 			=> $this->_cache_prefix . '_',
			'automatic_serialization' 	=> true,
			'cached_entity'				=> get_class(),
		);
		
		$backendOptions = array(
			'cache_dir' => $this->_cache_dir
		);
	
		return Zend_Cache::factory('Class', 'File', $frontendOptions, $backendOptions);
	}
	
	protected function _cacheLoad($className, $functionName)
	{
//		Zend_Debug::dump(get_class($this), '<strong>: </strong>', Zend_Registry::get('config')->debug->output);
		return $this->_cache->load($className . '_' . $functionName);
		
	}
	
	/* FIXME: pas d'lair de cacher */
	protected function _cacheSave($results, $className, $functionName)
	{
		$this->_cache->save($results, $className . '_' . $functionName);
	}
}