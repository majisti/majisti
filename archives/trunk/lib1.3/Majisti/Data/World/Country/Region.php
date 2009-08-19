<?php

class Majisti_Data_World_Country_Region extends Majisti_Data_Locale
{
	protected $_isoCode;
	
	protected $_name;
	
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
	
	public function toString()
	{
		return $this->_name;	
	}
	
	public function __toString()
	{
		return $this->toString();	
	}
}