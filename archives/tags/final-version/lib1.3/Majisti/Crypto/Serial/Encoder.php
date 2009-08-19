<?php

/**
 * This class implements a serial number generator. The
 * generator supports abstract value part sequences to be
 * passed as options.
 *
 * For a complete random serial number (this can obviously
 * not be validated precisely), simply set an option value
 * called 'sequence_count' => int to specify how many sequences
 * the serial should have and set NULL (or an empty string) to
 * be encoded.
 *
 * The value to be encode should be an array. if it is not one,
 * it will be converted as an array of one index. If the value
 * to encode is specified, the key of the array should be numeric
 * (or sequences will be treated in appearing order) will tell where
 * the values will be sotred. If the 'sequences' array contains more
 * element indexes than any specified 'sequence_count' the value will
 * be adjusted to make sure that every sequence elements are in the
 * generated hash.
 *
 * The returned value may be split into chunks, the chunks
 * may be configured with the option 'chunk_size' => int which
 * will tell the size of each chunk (the last chunk will be the
 * remaining size of the last chunk) and 'chunk_separator' => string
 * which tells with what character(s) each chunk should be
 * separated with (default is '-') The chunk separator is ignored
 * if no chunk size is specified. A chunk size of 0 or less means no chunk
 * size.
 *
 * If the generated hash's last chunk is not long enough, it may be padded
 * with random values. To ensure that each chunk are 'chunk_size', use the
 * option 'chunk_pad' => true.
 *
 * For unspecified sequences, a random number will be generated. This
 * number will be in the range of 'random_min' and 'random_max'
 * inclusively. The default values for these options are 100 and 999
 * respectively.
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
class Majisti_Crypto_Serial_Encoder
{

	/**
	 * The default chunk separator char
	 * @var string
	 */
	const DEFAULT_CHUNK_SEPARATOR = '-';
	/**
	 * The default minimum random value. This value
	 * should not be less than 0
	 * @var int
	 */
	const DEFAULT_RANDOM_MIN = -999;
	/**
	 * The default maximum random value. This value should not
	 * be greater than 1'000'000'000
	 * @var int
	 */
	const DEFAULT_RANDOM_MAX = 999;


	/**
	 * @var int
	 */
	private $chunkSize;
	/**
	 * @var string
	 */
	private $chunkSeparator;
	/**
	 * @var bool
	 */
	private $chunkPad;
	/**
	 * @var int
	 */
	private $sequenceCount;
	/**
	 * @var int
	 */
	private $randomMin;
	/**
	 * @var int
	 */
	private $randomMax;
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

		if ( isset( $options['chunk_size'] ) ) {
			$this->chunkSize = $options['chunk_size'];
		} else {
			$this->chunkSize = 0;  // no chunk
		}
		if ( isset( $options['chunk_pad'] ) ) {
			$this->chunkPad = $options['chunk_pad'];
		} else {
			$this->chunkPad = false;  // no chunk padding
		}

		if ( isset( $options['random_min'] ) ) {
			$this->randomMin = (int) $options['random_min'];
			if ( $this->randomMin < 0 ) {
				throw new Majisti_Exception('min random value less than 0');
			}
		} else {
			$this->randomMin = self::DEFAULT_RANDOM_MIN;
			if ( $this->randomMin > 1000000000 ) {
				throw new Majisti_Exception('max random value too big');
			}
		}
		if ( isset( $options['random_max'] ) ) {
			$this->randomMax = (int) $options['random_max'];
		} else {
			$this->randomMax = self::DEFAULT_RANDOM_MAX;
		}

		if ( isset( $options['sequence_count'] ) ) {
			$this->sequenceCount = $options['sequence_count'];
		} else {
			$this->sequenceCount = -1;  // unknown sequence count
		}

		if ( isset( $options['private_key'] ) ) {
			$this->setPrivateKey($options['private_key']);
		}

	}

	/**
	 * @desc
	 * Make sure the sequences are arranged in order. The algorithm
	 * tries to put the element in a best effort basis. Any non-numeric
	 * key index in the sequence array will be converted into a
	 * numeric key. Note that numeric indexes in the sequence array will
	 * have priority over non-numeric keys, meaning that any non-numeric
	 * key will be put at the end of the array in a natural sort order.
	 * Non-numeric keys will non be preserved.
	 *
	 * This function will also correct the sequence count if previously
	 * set too low.
	 *
	 * @param mixed $value   the input sequence
	 * @return array   the sequences to encode
	 */
	private function _prepareSequences($value)
	{
		if ( !is_array( $value ) ) {
			if ( empty( $value ) ) {
				$value = array();
			} else {
				$value = array($value);
			}
		}

		$sequenceKeys = array_keys($value);
		natsort($sequenceKeys);

		$sequences = array();
		$index = 0;
		foreach ($sequenceKeys as $key) {
			if ( is_numeric($key) ) {
				$index = $key;
			} else {
				$index++;
			}

		  $sequences[$index] = $value[$key];
		}

		$sequenceCount = max($this->sequenceCount, $index, 1);

		for ($i=0; $i<$sequenceCount; $i++) {
			if ( !isset( $sequences[$i] ) ) {
				$sequences[$i] = null;
			}
		}

		return $sequences;
	}

	/**
	 * @desc
	 * Prepare the seed for random numbers in ths serial
	 * The random seed is generated from a private key if provided,
	 * or randomly if not. The function returns an array containing
	 * two indexes ; 'value' => int and 'public' => bool
	 */
	private function _prepareSeed()
	{
		$public = empty($this->key);

		if ( $public ) {
			$seed = (int) mt_rand($this->randomMin, $this->randomMax);
		} else {
			$seed = (int) crc32($this->key);
		}

		return array($seed, $public);
	}

	/**
	 * @desc
	 * Encode a specified value. The function should
	 * return the hash computed from the given value
	 * on success. The function will throw an exception
	 * if any sequence value is a float or if negative.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function encode($value)
	{
		list($seed, $publicSeed) = $this->_prepareSeed();

		$values = $this->_prepareSequences($value);

		// sequence values
		$sequences = array();

		// note : this bitarray should NOT be greater than 5 bits...
		//        'z' in base 32 is '10011' in base 2
		//        Fact : base 36 values need 5.5 bits
		$signature = new Majisti_BitArray();

		if ( $publicSeed ) {
			// the signature should not be greater than 5 bits long
			//    bit  public            private
			//    0    0                 1
			//    1    seedSign          0
			//    2    seedSize          0
			//    3    seedSize          0
			//    4    seedSize          xorseed
			//    5    seedSize          xorseed
			$signature[0] = 0;   // first signature bit to 0, public serial
			$signature[1] = ($seed < 0 ? 1 : 0); // seed sign...

			$seed36 = base_convert($seed, 10, 36);

			$seedSize = strlen($seed36);  // should not be breater than 15 bytes
			$signature->set(2, $seedSize, 4);

		} else {
			$signature[0] = 1;  // first signature bit to 1 (private key required)

			$xorSeed = mt_rand(0, 2);
			$seed = (int) ($seed ^ $xorSeed);
			$signature->set(4, $xorSeed, 2);

			//echo "Key = "; var_dump( $seed ); echo " (xored " . $xorSeed . ") - ";

			$seed36 = '';
		}

		$hash = base_convert($signature->get(), 2, 36) . $seed36;

		foreach ($values as $value) {
			$hash .= $this->_hashNextSequence($value, $seed);
		}

		//echo "Encode : " . $signature->count() . ' = ' . $signature->get(0, 6) . ' = ';
		//echo "Seed = (" . $seed36 . ') ' . $seed['value'];
		//echo ' :: ';

		// TODO : implement this
		$hash = $this->_addPadding($hash);

		// seal the serial number
		$hash = $this->_sealHash($hash);

		// chunks separator
		if ( $this->chunkSize > 0 ) {
			$hash = rtrim(chunk_split($hash, $this->chunkSize, $this->chunkSeparator), $this->chunkSeparator);
		}

		return $hash;
	}

	/**
	 * @desc
	 * This function will generate the next hash sequence from the given
	 * value
	 *
	 * @param int $value
	 */
	private function _hashNextSequence($value, $seed)
	{
		$signature = new Majisti_BitArray();

		if ( null === $value ) {
			$signature[0] = 1;

			$value = (int) mt_rand($this->randomMin, $this->randomMax);
		} else {
			$signature[0] = 0;
		}

		// encode value
		$value ^= $seed;

		if ( $value < 0 ) {
			$signature[1] = 1;
			$value = abs($value);
		}

		$value36 = base_convert($value, 10, 36);
		$seedSize = strlen($value36);  // should not be breater than 15 bytes
		$signature->set(2, $seedSize, 4);

		return base_convert($signature->get(), 2, 36) . $value36;
	}

	/**
	 * @desc
	 * Pad the hash value with random values until the length of hash
	 * has filled the remaining of the last chunk, then return the
	 * newly padded hash string. The function does nothing if the hash
	 * string fills the entire last chunk.
	 *
	 * TODO : implement this
	 *
	 * @param string $hash
	 * @return string
	 */
	private function _addPadding($hash)
	{
		if ( $this->chunkSize > 0 ) {
			// if the last chunk is not long enough...
			//$lastChunkMod = strlen($hash) % $this->chunkSize;
			//if ( $this->chunkPad && ($lastChunkMod != 0) ) {
	 		//	$hash .= ...;
			//}
		}

		return $hash;
	}

	/**
	 * @desc
	 * Ensure that the hash does not match another previously
	 * generated hash with the same value / seed. The random
	 * factor of a sealed hash does not guarantee that two
	 * generated hash will not be the same. Two generated
	 * hash will be equal at a proportion of 1/34.
	 *
	 * NOTE : This function may be called repeatedly to add
	 *        more proportion factors.  -- UNTESTED --
	 *
	 * @param string $hash
	 * @return string
	 */
	private function _sealHash($hash)
	{
		// generate a random value of base 36
		$seal = mt_rand(1, 35);
		$len = strlen($hash);

		$lastSeal = $seal;
		$sealedHash = '';
		// shift everything
		for ($i=0; $i<$len; $i++) {
			//echo "\nlastSeal = " . $lastSeal;
			$byte = base_convert(substr($hash,$i,1),36,10);
			$byte = ($byte + $lastSeal) % 36;
			//$lastSeal = ($byte ^ $lastSeal) % 36;
			$sealedHash .= base_convert($byte, 10, 36);
		}

		//echo "(sealed with " . $seal . " : " . $sealedHash . " = " . $hash . ", ";
		$sealedHash .= base_convert($seal, 10, 36);

		return $sealedHash;
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