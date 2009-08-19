<?php

abstract class Majisti_Excel_Writer_Adapter_Abstract
{
	protected $_headers;
	
	public function __construct(array $headers)
	{
		$this->_headers = $headers;	
	}
	
	/**
	 *
	 * @return Array
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}
	
	/**
	 *
	 * @return Array
	 */
	public abstract function getData();
}