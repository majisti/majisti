<?php

/**
 * Represents a cart-like array using different saving method to keep
 * data across requests. This class is iterable, meaning that it can
 * be used directly inside a foreach loop. Also, this class can
 * be used inside a paginator directly as it provides a toArray
 * function to represent the cart directly as an array.
 *
 * @author Yanick Rochon and Steven Rosato
 */
final class Majisti_Shopping_Cart implements Iterator
{
	/**
	 * @var Majisti_Shopping_Cart_Factory
	 **/
	private static $_factory;

	/**
	 * @var Majisti_Shopping_Cart
	 */
	private static $_instance;

	/**
	 * Returns the singleton instance of this class. If it was not previously
	 * created, it is created now. When this function is called, the function
	 * setFactory can no longuer be called.
	 *
	 * @return Majisti_Shopping_Cart
	 */
	public static function getInstance()
	{
		if( is_null(self::$_instance) ) {
			if( is_null(self::$_factory) ) {
				throw new Majisti_Shopping_Cart_Exception('Cart factory was never setup');
			}

			self::$_instance = new self(self::$_factory);
		}

		return self::$_instance;
	}

	/**
	 * Defines the cart factory of the class instance. Unless the class instance
	 * was created, this function may be called as many times as needed.
	 *
	 * @param Majisti_Shopping_Cart_Factory $factory
	 */
	public static function setFactory(Majisti_Shopping_Cart_Factory $factory)
	{
		if( is_null($factory) ) {
			throw new Majisti_Shopping_Cart_Exception("Factory can't be null");
		}

		// check whether a factory can actually be set
		if( self::hasFactory() ) {
			throw new Majisti_Shopping_Cart_Exception('Factory instance was already set');
		}

		self::$_factory = $factory;
	}

	/**
	 * This function tells whether a factory has already been set or not.
	 * A factory has been set if and only if a factory has been previously
	 * set and an instance was created.
	 *
	 * @return boolean
	 */
	public static function hasFactory()
	{
		return !is_null(self::$_instance) && !is_null(self::$_factory);
	}


	/**
	 * The data factory
	 *
	 * @var Majisti_Shopping_Cart_Factory
	 */
	private $_data;

	private function __construct()
	{
	 	$this->_data = self::$_factory->load();
	}

	/**
	 * Magic function needs to be public. When this method is called, the
	 * cart items are saved via the factory and the instance is destroyed.
	 */
	public function __destruct()
	{
	 	self::$_factory->save($this->_data);
	 	self::$_instance = null;

	 	$this->_data = null;
	}

	/**
	 * This function assures that the specified $index is within the
	 * bounds of the cart. If $strict is TRUE, and the $index is not
	 * valid or outside the range of the items in the cart, the returned
	 * value will be FALSE. If $strict is FALSE, and if the $index is not
	 * valid, it will be set to the size of the cart, meaning that if an
	 * item is set at the returned value, a new element will be added to
	 * the cart.
	 */
	private function _validateIndex($index, $strict = TRUE)
	{
		if (($index !== 0 && empty($index)) || ($index < 0) || ($index > $this->count()) ) {
			if ( $strict )
				$index = FALSE;
			else
				$index = $this->count();
		}
		return $index;
	}

	/**
	 * Validate that $item is an instance of Gestion_Cart_Item_Abstract
	 */
	private function _validateItem($item)
	{
		if (!($item instanceof Majisti_Shopping_Cart_Item)) {
			throw new Majisti_Shopping_Cart_Exception('Item is not a cart item', 0);
		}
	}

	/**
	 * Get an item in the cart. If the item at the
	 * specified index does not exist, the function
	 * returns null
	 *
	 * @param int $index
	 * @return mixed|null
	 */
	public function get($index)
	{
		if ( $this->_validateIndex($index, TRUE) === FALSE ) {
			return NULL;
		} else {
			return $this->_data[$index];
		}
	}

	/**
	 * Return the size of the cart
	 */
	public function count()
	{
		return count($this->_data);
	}

	/**
	 * Set an item in the cart. If $index is specified,
	 * the item will replace the item at the specified
	 * location. If $index is beyond the size of the cart
	 * or is not specified, the item is added to the end
	 * of the cart's list.
	 *
	 * @param Gestion_Cart_Item $item   the item to add/set
	 * @param int $index (optional)   the index to set
	 */
	public function put($item, $index = NULL)
	{
		$this->_validateItem($item);

		$this->_data[$this->_validateIndex($index, FALSE)] = $item;

		return $this;
	}

	/**
	 * Clear all items from the cart. The function
	 * returns this object
	 */
	public function clear()
	{
		$this->_data = array();

		return $this;
	}


	/** Iterator implementation **/

	/**
	 * Returns the keys of the cart. This function returns
	 * all the available indexes for all items
	 *
	 * @return array
	 */
	public function key()
	{
		return key($this->_data);
	}

	/**
	 * Returns the current item at the iterator's position.
	 * The function returns false if current index is beyond
	 * the array
	 *
	 * @return Gestion_Cart_Item|false
	 */
	public function current()
	{
		return current($this->_data);
	}

	/**
	 * Returns the next item in the cart or false if there
	 * is no other item to return
	 *
	 * @return Gestion_Cart_Item|false
	 */
	public function next()
	{
		return next($this->_data);
	}

	/**
	 * Returns if the there is an item at the current
	 * location.
	 *
	 * @return bool
	 */
	public function valid()
	{
		return (bool) $this->current();
	}

	/**
	 * Rewind the cart pointer to the first element and
	 * return it. The function returns null if the cart
	 * has no item
	 *
	 * @return Gestion_Cart_Item|null
	 */
	public function rewind()
	{
		return reset($this->_data);
	}
}