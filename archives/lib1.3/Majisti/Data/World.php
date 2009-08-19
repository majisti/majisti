<?php

class Majisti_Data_World extends Majisti_Data_Locale
{
	private $_countries;
	
	private function _loadCountries()
	{
		$countries = new Zend_Config_Ini(dirname(__FILE__) . '/World/Countries.ini');
		
		$language = $this->getLocale()->getLanguage();
		
		if( $countries->$language == NULL ) {
			throw new Majisti_Data_Exception("Language {$language} is not supported for this data");
		}
		
		$this->_countries = array();
		foreach ($countries->$language as $key => $value) {
			$this->_countries[] = new Majisti_Data_World_Country($key, $value, $this->getLocale());
		}
	}
	
	public function getCountries()
	{
		if( $this->_countries == null ) {
			$this->_loadCountries();	
		}
		
		return $this->_countries;
	}
	
	public function getCountry($isoCode)
	{
		if( $this->_countries == null ) {
			$this->_loadCountries();	
		}
		
		foreach ($this->_countries as $country) {
			if( $country->getIsoCode() == $isoCode ) {
				return $country;
			}
		}
		
		return null;
	}
	
	public function toArray()
	{
		if( $this->_countries == null ) {
			try {
				$this->_loadCountries();	
			} catch(Majisti_Data_Exception $e) {
				return array();	
			}
		}
		
		$countries = array();
		foreach ($this->_countries as $country) {
			$countries[$country->getIsoCode()] = $country->getName();
		}
		
		return $countries;
	}
}