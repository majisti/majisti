<?php

/**
 * Factory class to create concrete instances of hashing
 * objects. This class simply provides an abstraction layer
 * to return abstract crypto objects.
 * 
 * @author Yanick Rochon
 */
class Majisti_Crypto {

	/**
	 * The prefix of standard Majisti's crypto classes
	 * @var string
	 */
	const STANDARD_CLASS_PREFIX = 'Majisti_Crypto_';

	/**
	 * Factory function to create and return a encoder/decoder
	 * concrete crypto object. The type of the concrete implementation
	 * may be specified as a standard Majisti crypto class or any
	 * custom one.
	 *
	 * @param string $type
	 * @param array $options
	 */
	public static function factory($type, $options = array())
	{

		if ( !is_string($type) ) {
			$type = (string) $type;
		}

		// try to see if the type is a standard Majisti crypto type
		// TODO : change this... it's freakin' ugly!!!
		$filename = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, self::STANDARD_CLASS_PREFIX . $type . '.php');

		if ( is_readable($filename) ) {
			$type = self::STANDARD_CLASS_PREFIX . ucfirst($type);

			require_once $filename;
		}

		// try to create an instance of this class. The file should
		// be auto-loadable or already been included

		$typeObj = new $type($options);

		if ( !($typeObj instanceOf Majisti_Crypto_Abstract) ) {
			throw new Majisti_Exception('invalid crypto object');
		}

		return $typeObj;
	}


}