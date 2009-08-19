<?php

/**
 * TODO: support default locale loading in case the current locale is not supported
 * 
 * @author Steven Rosato
 */
class Majisti_Data_World_Country extends Majisti_Data_Locale
{
	protected $_isoCode;
	
	protected $_name;
	
	protected $_regions = array();
	
	public function __construct($isoCode, $name, $locale = null)
	{
		parent::__construct($locale);
		
		$this->_isoCode = strtoupper($isoCode);
		$this->_name	= $name;
	}
	
	public function getIsoCode()
	{
		return $this->_isoCode;	
	}
	
	public function getName()
	{
		return $this->_name;	
	}
	
	public function getRegion($isoCode)
	{
		if( $this->_regions == null ) {
			$this->_loadRegions();	
		}
		
		foreach ($this->_regions as $region) {
			if( $region->getIsoCode() == $isoCode ) {
				return $region;
			}
		}
		
		return null;
	}
	
	public function getRegions()
	{
		if( $this->_regions == null ) {
			$this->_loadRegions();	
		}
		
		return $this->_regions;
	}
	
	private function _loadRegions()
	{
		$file = dirname(__FILE__) . '/Country/Regions/' . $this->_isoCode . '.ini';
		
		if( file_exists($file) ) {
			try {
				$regions = new Zend_Config_Ini($file, $this->getLocale()->getLanguage());
			} catch( Zend_Config_Exception $e ) {
				throw new Majisti_Data_Exception("No regions is supported for this country or locale");	
			}
			
			foreach ($regions as $key => $value) {
				$this->_regions[] = new Majisti_Data_World_Country_Region($key, $value, $this->getLocale());
			}
		}
	}
	
	public function toArray()
	{
		if( $this->_regions == null ) {
			try {
				$this->_loadRegions();
			} catch(Majisti_Data_Exception $e) {
				return array();	
			}
		}
		
		$regions = array();
		foreach ($this->_regions as $region) {
			$regions[$region->getIsoCode()] = $region->getName();
		}
		
		return $regions;
	}
	
	public function toString()
	{
		return $this->_name;	
	}
	
	public function __toString()
	{
		return $this->toString();
	}
}
