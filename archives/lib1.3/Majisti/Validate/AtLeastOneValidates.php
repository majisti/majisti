<?php

class Majisti_Validate_AtLeastOneValidates extends Zend_Validate_Abstract
{
	private $_nextElement;
	
	public function __construct($nextElement = null)
	{
		$this->_nextElement = $nextElement;		
	}
	
	public function isValid($value, $context = null)
	{
		
	}
}