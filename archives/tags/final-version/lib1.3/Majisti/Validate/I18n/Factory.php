<?php

class Majisti_Validate_I18n_Factory
{
	public static function getI18n($className)
	{
		$namespace = split('_', $className);
		
		if( count($namespace) == 2 ) {
			switch($namespace[0]) {
				case 'Default':
					switch($namespace[1]) {
						case 'Fr': return new Majisti_Validate_I18n_Default_Fr();
							break;
					}
					break;
				case 'Original':
					switch($namespace[1]) {
						case 'Fr': return new Majisti_Validate_I18n_Original_Fr();
							break;
					}
					break;
			}
		}
		
		return null;
	}
}