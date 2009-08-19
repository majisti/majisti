<?php

class Majisti_Validate_NotOnlyCharacters extends Zend_Validate_Abstract
{
	const IS_ONLY_CHARACTERS = 'isOnlyCharacters';
	
	protected $_messageTemplates = array(
        self::IS_ONLY_CHARACTERS => "Field can't contain only characters"
    );
	
	public function isValid($value)
	{
		if( !preg_match('/\d/', $value) ) {
			$this->_error(self::IS_ONLY_CHARACTERS);
			return false;
		}
		return true;
	}
}