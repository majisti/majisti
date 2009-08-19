<?php

/**
 * TODO: doc
 * 
 * @author Yanick Rochon and Steven Rosato
 */
abstract class Majisti_Shopping_Cart_Factory
{
	protected function __construct() 
	{}
	
	/**
	 *
	 * @param unknown_type $type
	 * @param unknown_type $options
	 * 
	 * @return Majisti_Shopping_Cart_Abstract
	 */
	public final static function create( $type, $options = array() )
	{
		switch ( $type ) {
			case 'session':
				return new Majisti_Shopping_Cart_Factory_Session($options);
			case 'db':
				//return new Majisti_Shopping_Cart_Factory_Db($options);
		}
		return null;
	}
	
	abstract public function load();
	
	abstract public function save(array $data);
}