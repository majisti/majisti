<?php

/**
 * Provide a way for namespacing any kind of variable. By default,
 * the Namespace won't be saved to the session like Zend_Session_Namespace
 * does. If after namespacing the user wants to commit it to the session
 * the instance will then aggregate a Zend_Session_Namespace to remember
 * its items.
 * 
 * TODO: doc, once again
 * 
 * @author Steven Rosato
 */
class Majisti_Namespace
{
	private $_chunks;
	private $_sessionKey;
	
	public function __construct($sessionKey = null)
	{
		if( $sessionKey != null ) {
			$this->_sessionKey = $sessionKey;
			
			$session = new Zend_Session_Namespace($this->_sessionKey);
			
			if( isset( $session->chunks ) ) {
				$this->_chunks = $session->chunks;
			} else {
				$this->_chunks = array();
			}
		} else {
			$this->_chunks = array();
		}
	}
	
	public function & __get($chunk)
	{
		if( $chunk === '' ) {
			throw new Majisti_Namespace_Exception("The '{$chunk}' key must be a non-empty string");
		}
		
		if( $this->chunkExists($chunk) ) {
			return $this->_chunks[$chunk];
		} else {
			throw new Majisti_Namespace_Exception("The '{$chunk}' key is inexistant");
		}
	}
	
	public function chunkExists($chunk)
	{
		return array_key_exists($chunk, $this->_chunks);
	}
	
	public function __set($chunk, $value)
	{
		if( $chunk === '' ) {
			throw new Majisti_Namespace_Exception("The '{$chunk}' key must be a non-empty string");
		}
		
		$this->_chunks[$chunk] = $value;
	}
	
	public function storeAsSessionNamespace($sessionKey)
	{
		$this->_save($sessionKey);
	}
	
	public function saveToSession()
	{
		if( $this->_sessionKey == null ) {
			throw new Majisti_Namespace_Exception('The namespace was never stored as a session namespace');
		}
		
		$this->_save($this->_sessionKey);
	}
	
	private function _save($sessionKey)
	{
		$session = new Zend_Session_Namespace($sessionKey);
		$session->chunks = $this->_chunks;
	}
}