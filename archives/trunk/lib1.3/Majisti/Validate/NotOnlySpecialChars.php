<?php

/**
 * @desc Validates that a value does not contain only special characters.
 * At least a digit or an alphanumeric character must be present in the value.
 * 
 * @author Steven Rosato
 */
class Majisti_Validate_NotOnlySpecialChars extends Zend_Validate_Abstract
{
	const NOT_ONLY_SPECIAL_CHARS = 'notOnlySpecialChars';
	
	protected $_messageTemplates = array(
		self::NOT_ONLY_SPECIAL_CHARS => "The field must not be consisted with only special chars"
	);
	
	public function isValid($value)
	{
		preg_match('/[^a-zA-Z0-9]+/', $value, $matches);
		
		if( count($matches) > 0 ) {
			if( strlen($matches[0]) == strlen($value) ) {
				$this->_error(self::NOT_ONLY_SPECIAL_CHARS);
				return false;
			}
		}
		return true;
	}
}
