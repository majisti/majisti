<?php
/**
 * @desc Validates that a given value is a valid postal code according to the countries
 * passed as parameter in constructor. If a value validates on at least one country syntax
 * the element will be valid
 * 
 * @author Steven Rosato
 */
class Majisti_Validate_PostalCode extends Majisti_Validate_ByCountry_Abstract
{
    const INVALID_POSTALCODE 			= 'invalidPostalCode';
    const INVALID_POSTALCODE_COUNTRY 	= 'invalidPostalCodeCountry';
    
    protected $_defaultPattern = 'Canada';
    
    /* All the patterns for each countries */
    protected $_countriesPatterns = array(
    	'Canada' => '/^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/'
    );

    /* Messages */
    protected $_messageTemplates = array(
        self::INVALID_POSTALCODE 			=> "Postal Code is invalid",
        self::INVALID_POSTALCODE_COUNTRY 	=> "Postal Code for %country% is invalid"
    );
    
    /**
     * @desc Returns true if a postal code matches one of the syntax of a country
     * among the accepted countries previously given in constructor
     *
     * @param string $value The value to validate
     * @return boolean true if the postal code is valid
     */
    public function isValid($value)
    {
    	$value = strtoupper($value);
    	
    	/* validate that syntax matches at least one country */
    	if( count($this->_acceptedCountries) ) {
    		foreach ($this->_acceptedCountries as $acceptedCountry) {
    			if( preg_match($this->_countriesPatterns[$acceptedCountry], $value) ) {
    				return true;
    			} else {
    				$this->_country = ucfirst($acceptedCountry);
    			}
    		}
 			$this->_error(self::INVALID_POSTALCODE_COUNTRY);
 			return false; 
 		/* nothing was given, default pattern is applied */
    	} else if( !preg_match($this->_countriesPatterns[$this->_defaultPattern], $value)  ) {
    		$this->_error(self::INVALID_POSTALCODE);
    		return false; 
    	}
		return true;
    }
}

