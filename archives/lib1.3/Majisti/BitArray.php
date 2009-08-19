<?php

/**
 * This class provides an easy way to manipulate bits in
 * an abstract way.
 *
 * TODO : add test units
 *
 */
class Majisti_BitArray extends ArrayObject
{

	/**
	 * Create a new BitArray. If $value is specified, it may
	 * be an integer value, or any string representing the values
	 * of the bit array. If it is a string, each character will be
	 * evaluated as true (1) or false (0) based on PHP boolean
	 * evaluation scheme.
	 *
	 * @param int|string $value (optional)
	 */
	public function __construct($value = null)
	{
		if ( !is_null( $value ) ) {
			$this->set(0, $value);
		}
	}

	/**
	 * Make sure that the bit array is large enough to contain
	 * $length number of bits
	 *
	 * @param int $length
	 */
	private function _ensureCapacity($length)
	{
		$count = $this->count();
		while ( $count < $length ) {
			$this[$count++] = '0';
		}
	}

	public function append($newval)
	{
		$this->offsetSet($this->count(), $newval);
	}
	public function offsetGet($index)
	{
		if (!is_numeric($index)) {
			throw new Majisti_Exception('index must be numeric');
		}

		if ( $index >= $this->count() ) return 0;

		return parent::offsetGet($index) ? '1' : '0';
	}
	public function offsetSet($index, $newval)
	{
		if (!is_numeric($index)) {
			throw new Majisti_Exception('index must be numeric');
		}

		if ( $index > $this->count() ) $this->_ensureCapacity($index);

		parent::offsetSet($index, $newval ? '1' : '0');
	}
	public function offsetUnset($index)
	{
		if (!is_numeric($index)) {
			throw new Majisti_Exception('index must be numeric');
		}

		if ( $index < $this->count() - 1 )
			parent::offsetSet($index, 0);
		else
			parent::offsetUnset($index);
	}

	/**
	 * Return a string representation of the given value. If $value
	 * is numeric, it is converted to a base 2 number (0 and 1)
	 *
	 * @param mixed $value
	 * @return string
	 */
	private function _getBitString($value)
	{
		if ( is_string( $value ) ) {
			return $value;
		} else if ( is_numeric( $value ) ) {
			return decbin($value);
		} else {
			return (string) $value;
		}
	}

	/**
	 * Set the bits at a specified offset. Each bit from
	 * $offset to the bit length of value will be set or reset
	 * regardless, and replaced by the bits composing $value.
	 * The specified value should be a numeric value, or be
	 * a string. If not, the function will attempt to convert it
	 * to string.
	 *
	 * @param int $offset              the start offset
	 * @param mixed $value             the value
	 * @param int $length (optional)   the length of the value
	 * @param mixed $value   a value
	 */
	public function set($offset, $value, $length = NULL)
	{
		$value = strrev($this->_getBitString($value));
		$strLen = strlen($value);

		if ( is_null( $length ) ) {
			$length = $strLen;
		}

		for ($i=0; $i<$length; $i++) {
			if ( $i < $strLen && substr($value, $i, 1) )
				$this[$offset + $i] = '1';
			else
				$this[$offset + $i] = '0';
		}
	}

	/**
	 * Return a string representation of the bits to get. The value
	 * returned is a string of length $length composed of 0 and 1's.
	 * By default, the function return a string corresponding all the
	 * bits set. To return an int value, call the function with a length
	 * of 32.
	 *
	 * Note : first bit (the bit at offset $offset is the last bit of the
	 * return string! Whereas the first bit of the string is the bit at offset
	 * $offset + $length - 1 in the BitArray
	 *
	 * @param int $offset (optional)
	 * @param int $length (optional)
	 */
	public function get($offset = 0, $length = NULL)
	{
		if ( is_null( $length ) ) $length = $this->count();

		$str = '';
		for ($i=0; $i<$length; $i++) {
			$str .= $this[$offset + $i];
		}
		return strrev($str);
	}

	/**
	 * Utility function to return an int instead of the bit string
	 *
	 * @param int $offset
	 * @param int $length
	 */
	public function getInt($offset = 0, $length = NULL)
	{
		return (int) bindec($this->get($offset, $length));
	}

}