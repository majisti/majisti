<?php

abstract class Majisti_Crypto_Abstract
{

	/**
	 * @desc
	 * Encode a specified value. The function should
	 * return the hash computed from the given value
	 * on success, or false upon failure.
	 *
	 * @param mixed $value
	 * @return string|false
	 */
	public abstract function encode($value);

	/**
	 * @desc
	 * Decode a specified hash value and return the
	 * result. This function should return a value
	 * compatible with the value given to the encode()
	 * function.
	 *
	 * Ex: $value ~= decode(encode($value))
	 *
	 * Note : some implementation may always return
	 *        an array such as "decode(encode(1)) = array(1)"
	 *
	 * If the specified hash cannot be decoded, the
	 * function should return false.
	 *
	 * If the encoding method does not allow decoding
	 * of the hash, the function should always return false.
	 *
	 * @param string $hash
	 * @return mixed|false
	 */
	public abstract function decode($hash);

}