<?php

/**
 * Concreat class to enable caching queries automatically. The class
 * allows activating or deactivating cache so queries may be prevented
 * from being cached (default behavior)
 * 
 * Usage :
 * 
 *   $cache_table = new Majisti_Db_Table_Cached($options);
 * 
 * where $options is an array of options as Zend_Db_Table_Abstract
 * options, with a 'cache_options' index (optional) to specify the
 * cache's frontend/backend options. Example :
 * 
 *   $options = array(
 *      // Zend_Db_Table_Abstract options
 *      'name' => 'db_table_name',
 *      'id' => 'table_column_id',
 * 
 *      // Zend_Cache options (frontend and backend only)
 *      'cache_options' => array(
 *        'frontendOptions' => array( [...] ),
 *        'backendOptions' => array( [...] )
 *   );
 * 
 * Note : the 'cache_options' key/value will not be passed to the
 *        Zend_Db_Table_Abstract constructor
 * 
 * 
 * @author Yanick Rochon
 */
class Majisti_Db_Table_Cached extends Zend_Db_Table_Abstract 
{
	
	/**
	 * @var string
	 */
	static private $CACHE_FRONTEND = 'Class';
	/**
	 * @var string
	 */
	static private $CACHE_BACKEND = 'File';
	
	
	/**
	 * @var Zend_Cache
	 */
	private $_cache;
	
	/**
	 * @var array
	 */
	private $_frontendOptions;
	/**
	 * @var array
	 */
	private $_backendOptions;
	
	/**
	 * @var bool
	 */
	private $_cacheEnabled;
	
	
	/**
	 * Defines the default front end cach options
	 *
	 * @param mixed  the entity object. Must be a class instance, or a class name
	 * @return array
	 */
	static protected function _getCacheDefaultFrontendOptions(&$obj)
	{
		return array(
			'lifetime' => 7200, // cache lifetime of 2 hours
			'automatic_serialization' => true,
			'cached_entity' => $obj // The class name or an instance
		); 
	}

	/**
	 * Defines the default back end cache options
	 *
	 * @return array
	 */
	static protected function _getCacheDefaultBackendOptions()
	{
		return array(
			'cache_dir' => './tmp/'
		); 
	}
	
	
	/**
	 * The class constructor, will enable caching for this abstract table queries.
	 *
	 * @param String $cacheAdapter The adapter to use for caching.
	 */
	public function __construct($options = array())
	{
		if ( isset($options['cache_options']) ) {
			$this->_frontendOptions = $options['cache_options']['frontendOptions'];
			$this->_backendOptions = $options['cache_options']['backendOptions'];
			
			// prevent overwriting cached_entity
			unset($this->_frontendOptions['cached_entity']);
			
			// remove cache options
			unset($options['cache_options']);
		} else {
			$this->_frontendOptions = 
			$this->_backendOptions = array();
		}
			
		// setting cache options...
		$this->_frontendOptions = array_merge(self::getCacheDefaultFrontendOptions($this), $this->_frontendOptions);
		$this->_backendOptions = array_merge(self::getCacheDefaultBackendOptions(), $this->_backendOptions);

		// TODO : add default option for this
		$this->cacheEnabled(true);
		
		// init parent
		parent::__construct($options);
	}
	
	/**
	 * Determine if the cache is enabled for this instance. If the cache
	 * is not enabled, this class behaves the same was as Zend_Db_Table_Abstract
	 *
	 * @param bool $state   TRUE the cache system is enabled, FALSE otherwise
	 */
	public function cacheEnabled($state)
	{
		$this->_cacheEnabled = (bool) $state;
	}
	
	/**
	 * Returns if the cache is enabled for this instance
	 *
	 * @return bool
	 */
	public function isCacheEnabled()
	{
		return $this->_cacheEnabled;
	}
	
	
	/**
	 * Retrieve the cache instance
	 *
	 * @return Zend_Cache  Zend_Cache class adapter.
	 */
	private function _getCache()
	{
		if ( $this->_cache == NULL ) {
			$this->_cache = Zend_Cache::factory(
				self::$CACHE_FRONTEND, self::$CACHE_BACKEND, 
				$this->_frontendOptions, $this->_backendOptions
			);
		}
		
		return $this->_cache;
	}

 /**
   * Fetches all rows.
   *
   * Honors the Zend_Db_Adapter fetch mode.
   *
   * @param string|array|Zend_Db_Table_Select $where    OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
   * @param string|array                      $order    OPTIONAL An SQL ORDER clause.
   * @param int                               $count    OPTIONAL An SQL LIMIT count.
   * @param int                               $offset   OPTIONAL An SQL LIMIT offset.
   * @param bool                              $cachable OPTIONAL is the query cachable? (ignored if cache is disabled)
   * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
   */
  public function fetchAll($where = null, $order = null, $count = null, $offset = null, $cachable = TRUE)
  {
		if ( $cachable && $this->isCacheEnabled() ) {
			return $this->_getCache()->fetchAll($where, $order, $count, $offset, FALSE);			
		} else {
			return parent::fetchAll($where, $order, $count, $offset);			
		}
  }
	
  /**
   * Fetches one row in an object of type Zend_Db_Table_Row_Abstract,
   * or returns Boolean false if no row matches the specified criteria.
   *
   * @param string|array|Zend_Db_Table_Select $where    OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
   * @param string|array                      $order    OPTIONAL An SQL ORDER clause.
   * @param bool                              $cachable OPTIONAL is the query cachable? (ignored if cache is disabled)
   * 
   * @return Zend_Db_Table_Row_Abstract The row results per the
   *     Zend_Db_Adapter fetch mode, or null if no row found.
   */
	public function fetchRow($where = null, $order = null, $cachable = TRUE)
	{
		if ( $cachable && $this->isCacheEnabled() ) {
			return $this->_getCache()->fetchRow($where, $order, FALSE);
		} else {
			return parent::fetchRow($where, $order);
		}
	}
	
}