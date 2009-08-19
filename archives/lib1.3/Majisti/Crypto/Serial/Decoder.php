<?php

/**
 * This class implements a serial number decoder. The
 * generator supports abstract value part sequences to be
 * passed as options. This class is not a serial number validator,
 * the validation must be made from the parts decoded by this class.
 *
 * The specified serial value may be split into chunks, The option
 * 'chunk_separator' => string specifies the character(s) each chunk are
 * be separated with (default is '-')
 *
 * Usage allow implementation public or private serials. A public serial may
 * be decoded without specifying any key, but private serial must have
 * a key specified to decode it. If a key is set (see setPrivateKey()) then
 * the serial will be decodable only providing the same key. If the key
 * provided is NULL (default) then the serial will be decodable, even if
 * a key is provided (any) or not. The specify a private key in the
 * constructor options, specify it using 'private_key' option
 *
 *
 * @author Yanick Rochon
 */
class Majisti_Crypto_Serial_Decoder
{

	/**
	 * The default chunk separator char
	 * @var string
	 */
	const DEFAULT_CHUNK_SEPARATOR = '-';

	/**
	 * @var string
	 */
	private $chunkSeparator;
	/**
	 * @var string
	 */
	private $key;

	/**
	 * @desc
	 * Construct a new serial encoder with the specified options
	 *
	 * @param array $options
	 */
	public function __construct($options = array())
	{
		if ( isset( $options['chunk_separator'] ) ) {
			$this->chunkSeparator = (string) $options['chunk_separator'];
		} else {
			$this->chunkSeparator = self::DEFAULT_CHUNK_SEPARATOR;
		}
		if ( isset( $options['private_key'] ) ) {
			$this->setPrivateKey($options['private_key']);
		}

	}

	/**
	 * @desc
	 * Prepare the seed for random numbers in ths serial
	 * The random seed is extracted from the signature of the hash
	 * if the serial contains one, or created from the private key
	 * provided to the decoder. If neither is found, an excetion
	 * is thrown
	 */
	private function _prepareSeed($hash)
	{
		$signature = new Majisti_BitArray( base_convert(substr($hash,0,1),36,2));

		// if the serial is not private, a key is provided
		if ( !$signature[0] ) {
			$seedSigned = $signature[1];
			$seedSize = $signature->getInt(2, 4);
			$seed = (int) base_convert(substr($hash,1,$seedSize),36,10);
			if ( $seedSigned ) $seed = -$seed;

			$offset = 1 + $seedSize;
		} else {
			if ( empty( $this->key ) ) {
				throw new Majisti_Exception('private serial has no key provided');
			}

			$xorSeed = $signature->getInt(4, 2);

			$seed = (int) crc32($this->key) ^ $xorSeed;

			//echo "Key = "; var_dump( $seed ); echo " (xored " . $xorSeed . ") - ";
			$offset = 1;
		}

		return array($seed, $offset);
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
		// if necessary... (auto-detect)
		$hash = str_replace($this->chunkSeparator, '', $hash);

		$hash = $this->_unsealHash($hash);
		list($seed, $offset) = $this->_prepareSeed($hash);

		//echo "\nDecode : " . $signature->count() . ' = ' . $signature->get(0, 6) . ' = ';
		//echo "Seed = (" . substr($hash,1,$seedSize) . ') ' . $seed;

		$hashLen = strlen($hash);
		$values = array();
		$index = 0;

		while ( $offset < $hashLen ) {
			$value = $this->_getNextSequence($hash, $offset, $seed);
			if ( null !== $value ) {
				$values[$index] = $value;
			}
			$index++;
		}

		if ( $offset < $hashLen ) {
			throw new Majisti_Exception('corrupted serial found');
		}

		return $values;
	}

	/**
	 * @desc
	 * Try to unseal the given hash by removing the random factor
	 * contain within. The function should return the hash stripped
	 * from a random layer.
	 *
	 * NOTE : This function may be called repeatedly to remove
	 *        more random factor layers.  -- UNTESTED --
	 *
	 * @param string $hash
	 * @return string
	 */
	private function _unsealHash($hash)
	{
		// try to unseal it...
		$seal = base_convert(substr($hash, -1, 1), 36, 10);
		$len = strlen($hash);

		$lastSeal = $seal;
		$unsealedHash = '';
		// shift everything
		for ($i=0; $i<$len - 1; $i++) {
			//echo "\nlastSeal = " . utf8_encode($lastSeal);
			$byte = base_convert(substr($hash,$i,1),36,10);
			//$nextSeal = ($byte ^ $lastSeal) % 36;
			$byte = (72 + ($byte - $lastSeal)) % 36;
			//$lastSeal = $nextSeal;
			$unsealedHash .= base_convert($byte, 10, 36);
		}

		//echo "\n(unsealed with " . $seal . " : " . $hash . ' = ' . $unsealedHash . ")  ";

		return $unsealedHash;
	}

	/**
	 * @desc
	 * This function returns the next sequence from the hash. It will also
	 * check for data corruption, if it can. The $offset will be advanced
	 * to the next byte to read upon function return. If the next sequence
	 * is a random number, return null
	 *
	 * @param string $hash
	 * @param int $offset
	 * @param int $seed
	 * @return int|null
	 */
	private function _getNextSequence($hash, &$offset, $seed)
	{
		$signature = new Majisti_BitArray( base_convert(substr($hash, $offset, 1), 36, 2) );
		$valueSign = $signature[1];
		$valueSize = $signature->getInt(2, 4);

		if ( $offset + $valueSize >= strlen($hash) ) {
			echo 'DEBUG[offset:' . $offset . ',value size:' . $valueSize . ',hash length: ' . strlen($hash) . ']';
			throw new Majisti_Exception('data corruption found in serial');
		}

		// a random value
		if ( $signature[0] ) {
			$value = null;
		} else {
			$value = base_convert( substr($hash, $offset+1, $valueSize), 36, 10);

			if ( $valueSign ) $value = -$value;

			$value ^= $seed;
		}

		// adjust offset
		$offset += 1 + $valueSize;

		return $value;
	}

	/**
	 * @desc
	 * Return the private key previously set. If no key was set,
	 * the function returns null
	 *
	 * @return string|null
	 */
	public function getPrivateKey()
	{
		return $this->key;
	}

	/**
	 * @desc
	 * Prepare the private key for the next serial number.
	 *
	 * @param string $key
	 */
	public function setPrivateKey($key)
	{
		$this->key = (string) $key;
	}


}