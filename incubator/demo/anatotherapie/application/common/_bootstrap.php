<?php

require_once '_remote.php';

/**
 * @desc The project's bootstrap file
 *
 * Anatothérapie is a clinic company that offers many treatments to its customers.
 * They have a lot of services and treaments for them. This project is mainly
 * content's output and the only dynamism is the therapists, where they need to
 * be managed by the website's admin
 *
 * @author Steven Rosato and Jean-François Hamelin
 */
class Anato_Bootstrap extends Majisti_Bootstrap
{
	private static $_instance;

	protected function __construct() { parent::__construct(); }

	/**
	 * @desc Apply the custom configuration for this project
	 */
	public function postConstruct()
	{
		$this->_applyConfiguration(
			new Majisti_Config_Xml( dirname(__FILE__) . '/config.xml',
			ENVIRONMENT_MODE,
			true)
		);

		/* models include path */
		set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/models/'));
		set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/layouts/'));

		/* images constant for shorter urls in <img /> tags */
		define('DIR_IMAGES', BASE_URL . '/images');
		define('DIR_IMAGES_LOCALE', BASE_URL . '/images/fr'); //TODO: instead of fr: currentLocale
		define('DIR_IMAGES_COMMON', BASE_URL . '/images/common');

		/* add custom view helpers path */
		$view = Zend_Registry::get('view');
		$view->addHelperPath('../application/common/helpers', 'Anatotherapie_View_Helper');
		$view->setUseStreamWrapper(true);

		/* No translator given since this project is not internationalised yet */
		Zend_Registry::set('Zend_Translate', new Majisti_Translate_Adapter_Null());
	}

	/**
	 * @return Anato_Bootstrap
	 */
	public static function getInstance()
	{
		if( self::$_instance == null ) {
			self::$_instance = new Anato_Bootstrap();
		}

		return self::$_instance;
	}
}