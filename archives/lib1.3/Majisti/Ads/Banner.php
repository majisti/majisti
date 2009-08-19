<?php

/**
 * Banner factory
 *
 * @author Yanick Rochon
 */
abstract class Majisti_Ads_Banner {
	

	/**
	 * Utility method to create a banner for a given type. The banner
	 * will be created using the class Majisti_Ads_Banner_{$type}
	 *
	 * @param string $type
	 * @param array $options   the banner option
	 */
	public static function factory($type, $options) {
		
		// TODO : allow custom types
		
		$class = 'Majisti_Ads_Banner_' . ucfirst(strtolower($type));
		
		return new $class($options);		
	}
	

}