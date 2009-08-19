<?php

/**
 * TODO: doc
 *
 * @author Steven Rosato and Yanick Rochon
 */
class Majisti_Controller_Plugin
{

	const DEFAULT_NAMESPACE = 'Majisti_Controller_Plugin';

	/**
	 * Create a plugin given it's name. The function
	 * will attempt to locate the class and load it
	 * before it returns an instance of it
	 *
	 * Options :
	 *
	 *    'config' => Zend_Config (optional)  the config to pass to the plugin
	 *    'namespace' => string (optional)    the class name namespace (default DEFAULT_NAMESPACE)
	 *    'include' => string (optional)      the class source file to include
	 *
	 * @param string $name
	 * @param array $options Zend_Config $config
	 * @param (optional) string $namespace  the plugin class' namespace
	 * @return Zend_Controller_Plugin_Abstract
	 */
	public static function factory($name, $options = array())
	{

		if ( isset($options['namespace']) ) {
			if ( empty($options['namespace']) ) {
				$pluginClass = $name;
			} else {
				$pluginClass = $options['namespace'] . '_' . $name;
			}
		} else {
			$pluginClass = self::DEFAULT_NAMESPACE . '_' . $name;
		}

		if ( isset($options['script']) ) {
			include_once $options['script'];
		}

		if ( isset($options['config']) ) {
			return new $pluginClass($options['config']);
		} else {
			return new $pluginClass();
		}

	}
}