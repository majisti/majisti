<?php

/**
 * @desc This class represent an Anato service. Basically, a service
 * has a name, description, product list and a fee.
 * 
 * Note: The product list may have sub products, which should be wrapped in another array.
 * 
 * @deprecated Fee, it was not possible to abstract a fee since an Anatobalayage
 * has two different prices for exemple. The fees will be a service itself.
 * 
 * @author Steven Rosato
 */
class Anato_Service
{
	/** @var String */
	private $_key;
	
	/** @var String */
	private $_name;
	
	/** @var Array */
	private $_productList;
	
	/** @var String */
	private $_description;
	
	/** @var double */
	private $_fee = 0.00;
	
	/**
	 * @desc Constructs a service.
	 *
	 * @param String $key [opt] The service's key
	 * @param String $name [opt] The service's name
	 * @param String $description [opt] The service's description
	 * @param Array $productList [opt; def=array] The service's product list, may contain recursively arrays of sub products
	 * @param double $fee [opt] The service's fee
	 */
	public function __construct($key, $name, $description = null, $productList = array(), $fee = null)
	{
		$this->_key 		= $key;
		$this->_name 		= $name;
		$this->_productList = $productList;
		
		if( null !== $description ) {
			$this->_description = $description;
		}
		
		if( null !== $fee ) {
			$this->_fee = $fee;
		}
	}
	
	/**
	 * @return String the service's key
	 */
	public function getKey()
	{
		return $this->_key;
	}
	
	/**
	 * @return String the description
	 */
	public function getDescription()
	{
		return $this->_description;
	}
	
	/**
	 * @return Array The product list
	 */
	public function getProductList()
	{
		return $this->_productList;	
	}
	
	/**
	 * @return String the service's name
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * @return double The service's fee
	 */
	public function getFee()
	{
		return $this->_fee;
	}
	
	/**
	 * @see getName()
	 *
	 * @return String
	 */
	public function toString()
	{
		return $this->getName();
	}
	
	/**
	 * @see toString()
	 *
	 * @return String
	 */
	public function __toString()
	{
		return $this->toString();
	}
}
