<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_Validate_NoDigits extends Zend_Validate_Abstract
{
    const IS_DIGITS = 'isDigits';

    protected $_messageTemplates = array(
        self::IS_DIGITS => "Field can't contain digits"
    );

    public function isValid($value)
    {
    	if( preg_match('/\d/', $value) ) {
    		$this->_error(self::IS_DIGITS);
			return false; 
    	}
		return true;
    }
}

