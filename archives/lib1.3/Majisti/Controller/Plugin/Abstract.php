<?php

/**
 * Abstract controller plugin.
 *
 */
class Majisti_Controller_Plugin_Abstract extends Zend_Controller_Plugin_Abstract
{

	/**
	 * The config object given to the constructor or null
	 *
	 * @var Zend_Config
	 */
	private $_config;

	/**
	 * Create a new instance of the plugin optionnally using some config data
	 *
	 * @param Zend_Config $config
	 */
	public function __construct($config = null) {
		if ( !is_null($config) && !($config instanceof Zend_Config) ) {
			throw new Majisti_Controller_Plugin_Exception('invalid config data');
		}

		$this->_config = $config;
	}

	/**
	 * Returns the config data passed to the constructor or null of no
	 * config was specified.
	 *
	 * @return Zend_Config
	 */
	protected function getConfig()
	{
		return $this->_config;
	}

	/**
	 * A convenient method to return a view that can be used to render
	 * some script and push them into the response object.
	 *
	 * @see http://framework.zend.com/manual/en/zend.controller.response.html
	 *
	 * The returned view is a retrieve from Zend_Registry::get('view'). If no
	 * such view is registered, a new one is created and inserted into the
	 * registry for sharing
	 *
	 * @return Majisti_View
	 */
	protected function getView()
	{
		if ( !Zend_Registry::isRegistered('view') ) {
			Zend_Registry::set('view', new Majisti_View() );
		}

		return Zend_Registry::get('view');
	}


}
