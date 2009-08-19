<?php

/**
 * @desc Validates that a given value is a valid phone number according to the countries
 * passed as parameter in constructor. If a value validates on at least one country syntax
 * the element will be valid. The phone number can have dashes in it, they will be ignored.
 * 
 * @author Jean-Fran�ois Hamelin, Refactored by Steven Rosato
 */
class Majisti_Validate_Phone extends Majisti_Validate_ByCountry_Abstract
{
    const IS_NOT_PHONE 			= 'isNotPhone';
    
    const IS_NOT_PHONE_COUNTRY 	= 'isNotPhoneCountry';
    
    protected $_defaultPattern = 'Canada';
    
    /* All the patterns for each countries */
    protected $_countriesPatterns = array(
    	'Canada' => '^([0-9]{10})$'
    );

    /* Messages */
    protected $_messageTemplates = array(
        self::IS_NOT_PHONE 			=> "Invalid phone number",
        self::IS_NOT_PHONE_COUNTRY 	=> "The field doesn't appear to be a valid %country% phone number"
    );

    /**
     * @desc Returns true if a phone number matches one of the syntax of a country
     * among the accepted countries previously given in constructor
     *
     * @param string $value The value to validate
     * @return boolean true if the phone number is valid
     */
    public function isValid($value)
    {
        $value = str_replace(array('-', ' ', '(', ')'), '', $value); // Strip out dashes, white spaces and parenthesis

        if( count($this->_acceptedCountries) ) {
        	foreach ($this->_acceptedCountries as $acceptedCountry) {
        		if( ereg($this->_countriesPatterns[$acceptedCountry], $value) ) {
        			return true;
        		} else {
        			$this->_country = ucfirst($acceptedCountry);
        		}
        		$this->_error(self::IS_NOT_PHONE_COUNTRY);
        		return false;
        	}
        } else if( !ereg($this->_countriesPatterns[$this->_defaultPattern], $value) ) {
        	$this->_error(self::IS_NOT_PHONE); 
            return false; 
        }
        
        return true;
    }
}

