<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
abstract class Majisti_Validate_ByCountry_Abstract extends Zend_Validate_Abstract
{
	protected $_acceptedCountries;
	
	 protected $_messageVariables = array(
        'country' => '_country',
    );
    
	protected $_country;
	
	protected $_countriesPatterns = array();
    
    public function __construct(array $acceptedCountries = array('Canada'))
    {
    	$this->_validateCountries($acceptedCountries);
    	$this->_acceptedCountries = $acceptedCountries;	
    }
    
    private function _validateCountries($countries)
    {
    	if( !count($countries) ) {
    		throw new Majisti_Validate_Exception("Array can't be empty");
    	}
    	
    	foreach ($countries as $country) {
    		if( empty($country) ) {
    			throw new Majisti_Validate_Exception("Country can't be empty");
    		}
    		
    		if( !array_key_exists(ucfirst($country), $this->_countriesPatterns) ) {
    			throw new Majisti_Validate_Exception("The country $country is not present in the list");
    		}
    	}
    }
}