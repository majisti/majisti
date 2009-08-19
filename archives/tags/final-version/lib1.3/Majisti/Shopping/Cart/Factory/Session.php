<?php

/**
 * Implements a cart-like array using session cookies to keep data
 * across requests.
 * 
 * @author Yanick Rochon
 */
class Majisti_Shopping_Cart_Factory_Session extends Majisti_Shopping_Cart_Factory
{		
	
	/** @var Zend_Session_Namespace */
	private $_session;
	
	private $_sessionKey = 'Majisti_Shopping_Cart_Factory_Session'; 
	
	public function __construct($options = array())
	{
		if( isset($options['namespace_key']) ) {
			$this->_sessionKey = $options['namespace_key'];
		}
	}
	
	public function load()
	{
		$items = $this->_getSession()->items;
		
		if( empty($items) ) {
			return array();
		}
		
		return $items;
	}
	
	public function save( array $data )
	{
		$this->_getSession()->items = $data;
	}
	
	private function _getSession()
	{
		if( $this->_session == null ) {
			$this->_session = new Zend_Session_Namespace($this->_sessionKey, true);
		}
		
		return $this->_session;
	}
}