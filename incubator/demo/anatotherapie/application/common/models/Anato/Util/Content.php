<?php

/**
 * @desc Content loader utility class
 * 
 * @author Steven Rosato
 */
class Anato_Util_Content
{
	/**
	 * @param String $contentName The xml file, without any path, without any extension
	 * @param String $specificSection [opt] Load a specific section instead of the entire xml file
	 * 
	 * @return Zend_Config_Xml The loaded xml file
	 */
	public static function getXmlContent($contentName, $specificSection = null)
	{
		$path = realpath(dirname(__FILE__) . "/../../content/{$contentName}.xml");
		
		return null === $specificSection
			? new Anato_Util_Config_Xml($path)
			: new Anato_Util_Config_Xml($path, $specificSection);
	}
}