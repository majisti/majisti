<?php

/**
 * This class implements the necessary methods to add, remove or
 * retrieve ads from a given adapter
 *
 */
class Majisti_Ads implements Iterator, Countable {
	
	/**
	 * Get the ad provider
	 * 
	 * @var Majisti_Ads_Backend_Abstract
	 */
	private $_backend;
	/**
	 * The ads manager session namespace
	 *
	 * @var Zend_Session_Namespace
	 */
	private $_session;
	/**
	 * Banner cache
	 *
	 * @var array
	 */
	private $_cache;
	
	/**
	 * @var int
	 */
	private $_index;
	/**
	 * @var int
	 */
	private $_count;
	
	/**
	 * Construct a new ads manager. The ads manager should have a unique
	 * name as it uses the session space to store it's usage stats.
	 *
	 * @param string $name                              the session namespace to use
	 * @param Majisti_Ads_Backend_Abstract $backend
	 */
	public function __construct($name, $backend) {
		
		if ( !($backend instanceof Majisti_Ads_Backend_Abstract) ) {
			throw new Majisti_Ads_Exception( 'invalid ads provider' );
		}
		
		// init session
		$this->_session = new Zend_Session_Namespace( $name, true );
		if (! $this->_session->stats) {
			$this->_session->stats = array();
		}
		
		$this->_backend = $backend;
		$this->_cache = $this->_backend->load();
		
		// iterator
		$this->_index = 0;
		$this->_count = count($this->_cache);
	}
	
	/**
	 * Save adapter upon destroying the object
	 *
	 */
	public function __destruct() {
		$this->_backend->save( $this->_cache );
	}
	
	/** Iterator implementation **/
	
	public function current() {
		return current( $this->_cache );
	}
	
	public function key() {
		return key( $this->_cache );
	}
	
	public function next() {
		$this->_index++;
		return next( $this->_cache );
	}
	
	public function rewind() {
		$this->_index = 0;
		return reset( $this->_cache );
	}
	
	public function valid() {
		return $this->_index < $this->_count;
	}
	
	/**
	 * Returns the number of available ads in this provider
	 *
	 * @return int
	 */
	public function count() {
		return $this->_count;
	}
	
	/**
	 * Return a new, unused, valid id from the adapter
	 *
	 * @return mixed
	 */
	public function getNewId() {
		return $this->_backend->getNewId();
	}
	
	/**
	 * Utility function to check if a given id has been set with a banner ad.
	 * The function should return true is the id has a corresponding banner ad,
	 * or false if not.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function hasId($id) {
		return isset( $this->_cache[$id] );
	}
	
	/**
	 * Get a banner ad by id. The function should return a concrete implementation
	 * of a Majisti_Ads_Banner_Abstract object.
	 *
	 * @param mixed $id
	 * @return Majisti_Ads_Banner_Abstract
	 */
	public function get($id) {
		return $this->_cache[$id];
	}
	
	/**
	 * Sets a banner ad by id. The function should return the success state of the operation
	 * as a boolean value; true if the banner has been set successfully,
	 * false if not.
	 *
	 * @param mixed $id
	 * @param Majisti_Ads_Banner_Abstract $banner
	 * @param bool
	 */
	public function set($id, $banner) {
		
		if ( !$this->_backend->isValidId($id) ) {
			throw new Majisti_Ads_Exception('id is not valid : ' . $id);
		}
		if ( !($banner instanceof Majisti_Ads_Banner_Abstract) ) {
			throw new Majisti_Ads_Exception('invalid banner instance');
		}
		
		if ( isset($this->_cache[$id]) && $this->_cache[$id] != $banner ) {
			$banner->setChanged(true);
		}
		$this->_cache[$id] = $banner;
		
		$this->_count = count($this->_cache);
		return $this;
	}
	
	/**
	 * Reset the specific id off (delete). Returns true if the id
	 * was successfully deleted, false otherwise
	 *
	 * @param mixed $id
	 * @return bool
	 */
	public function reset($id) {
		if ( $this->_backend->isValidId($id) && isset($this->_cache[$id]) ) {
			unset($this->_cache[$id]); // will be deleted later...
			
			return true;
		}
		return false;
	}
	
	/**
	 * Return a random banner. This function will distribute the banner in
	 * a manner that many calls will produce a normal distribution population
	 * of banners. The function will save the statistics of the returned
	 * banners within the session namespace. This function ensures that two
	 * banners will never be returned consecutively.
	 * 
	 * @return Majisti_Ads_Banner_Abstract
	 */
	public function getRandomBanner() {
		$nextBannerId = $lastBannerId = $this->_session->lastBannerId;
		
		if ( !empty($this->_cache) ) {
		
			// while the last banner is exactly the same as the next banner...
			while ($nextBannerId === $lastBannerId) {
				list( $banners, $lookup, $total_weight ) = $this->_buildlookups();
				$r = mt_rand( 0, $total_weight );
				$nextBanner = $banners[$this->_searchNextId( $r, $lookup )];
				// find the banner in the original cache
				$nextBannerId = array_search( $nextBanner, $this->_cache, true );
			}
			
			$this->_session->lastBannerId = $nextBannerId;
			return $nextBanner;
		} else {
			return null;
		}
	}
	
	/**
	 * Build the lookup table for the banners.
	 * 
	 * @see http://w-shadow.com/blog/2008/12/10/fast-weighted-random-choice-in-php/
	 *
	 * @return array The lookup array and the sum of all weights
	 */
	private function _buildlookups() {
		$lookup = array();
		$total_weight = 0;
		
		$banners = $this->_sortCache();
		foreach ( $banners as $id => $banner ) {
			$total_weight += $banner->getWeight();
			$lookup [$id] = $total_weight;
		}
		return array( $banners, $lookup, $total_weight );
	}
	
	/**
	 * Utility function called by _buildLookups() to sort the banner cache by weight
	 * and return the result array. The original object's cache is unchanged.
	 *
	 * @return array       the object's banner cache sorted
	 */
	private function _sortCache() {
		$_cache = $this->_cache;
		$cmp = create_function( '$a,$b', 'return $a->getWeight() - $b->getWeight();' );
		usort( $_cache, $cmp );
		return $_cache;
	}
	
	/**
	 * _searchNextId()
	 * Search the sorted cache for a weight. Returns the weight's id if found. Otherwise 
	 * returns the best id, or count($haystack)-1 if the $randomWeight is higher than every 
	 * weight in the array. They $haystack array should be a sorted array of all the weights 
	 * of the ads manager cache. Each weights should be associated with the corresponding banner 
	 * in the same id.
	 *
	 * @param int $randomWeight   a random weight from 0 to the sum of all weights in $haystack
	 * @param array $haystack     all the weights of the banners
	 * @return int                the id of the banner to return
	 */
	private function _searchNextId($randomWeight, $weights) {
		$high = count( $weights ) - 1;
		$low = 0;
		
		while ( $low < $high ) {
			$probe = (int) (($high + $low) / 2);
			if ($weights [$probe] < $randomWeight) {
				$low = $probe + 1;
			} else if ($weights [$probe] > $randomWeight) {
				$high = $probe - 1;
			} else {
				return $probe;
			}
		}
		
		if ($low != $high) {
			return $probe;
		} else {
			if ($weights [$low] >= $randomWeight) {
				return $low;
			} else {
				return $low + 1;
			}
		}
	}

}