<?php

require_once 'Majisti/Crypto/Abstract.php';


class Majisti_Crypto_Serial extends Majisti_Crypto_Abstract
{

	/**
	 * @var Majisti_Crypto_Serial_Encoder
	 */
	private $encoder;

	/**
	 *
	 * @var Majisti_Crypto_Serial_Decoder
	 */
	private $decoder;

	/**
	 * @desc
	 * Create a new serial encoder/decoder
	 *
	 * @param array $options
	 */
	public function __construct($options = array())
	{
		$this->encoder = new Majisti_Crypto_Serial_Encoder($options);
		$this->decoder = new Majisti_Crypto_Serial_Decoder($options);
	}


	/**
	 * @desc
	 * Encode a specified value. The function should
	 * return the hash computed from the given value
	 * on success, or false upon failure.
	 *
	 * @param mixed $value
	 * @return string|false
	 */
	public function encode($value)
	{
		return $this->encoder->encode($value);
	}

	/**
	 * @desc
	 * Decode a specified hash value and return the
	 * result. This function should return a value
	 * compatible with the value given to the encode()
	 * function.
	 *
	 * Ex: $value ~= decode(encode($value))
	 *
	 * Note : "decode(encode(1)) = array(1)" as the method
	 *        decode always returns an array
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
	public function decode($hash)
	{
		return $this->decoder->decode($hash);
	}


}