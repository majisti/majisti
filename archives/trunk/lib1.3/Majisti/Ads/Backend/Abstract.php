<?php

/**
 * An abstract banner ad provider for the Majisti_Ads class 
 *
 * @author Yanick Rochon
 */
abstract class Majisti_Ads_Backend_Abstract
{
	
	/**
	 * Tranform a Majisti_Ads_Banner_Abstract object to an array.
	 * The array keys will be the banner properties and the values
	 * to the property values
	 *
	 * @param Majisti_Ads_Banner_Abstract $banner
	 * @return Majisti_Object
	 */
	protected function _bannerToObject($banner) {
		if ( ! (($banner instanceof Majisti_Ads_Banner_Abstract) 
			   || ($banner instanceof Majisti_Object) 
			   || is_array($banner)) ) {
			throw new Majisti_Ads_Backend_Exception('cannot convert this banner to array');
		}
		
		if ( !is_array($banner) || !($banner instanceof Iterator) ) {
			$banner = array(
				'id'      => $banner->getId(),
				'type'    => $banner->getType(),
				'name'    => $banner->getName(),
				'width'   => $banner->getWidth(),
				'height'  => $banner->getHeight(),
				'weight'  => $banner->getWeight(),
				'url'     => $banner->getUrl(),
				'attribs' => $banner->getAttribs(),
				'params'  => $banner->getParams(),
				'content' => $banner->getContent()
			);
		}
		
		return new Majisti_Object($banner);
	}
	
	/**
	 * Return a new unused id for a newly created banner
	 *
	 * @return mixed
	 */
	abstract public function getNewId();
	
	/**
	 * Check whether a given id is valid (true) or not (false)
	 *
	 * @param mixed $id
	 * @return bool
	 */
	abstract public function isValidId($id);
	
	/**
	 * This function should load all the ads and return an array 
	 * of abstract banners.
	 * 
	 * @return array 
	 */
	abstract public function load();
	
	/**
	 * This function should save all the ads. The $data is an array 
	 * of abstract banners
	 * 
	 * (1) if the banner ad type is text, the url is the target url of the
	 *     banner text
	 * 
	 * @return array 
	 */
	abstract public function save($data);
	
}