<?php

/**
 * This is a concrete implementation of a bootstrap
 * which gather all the common operations that could be
 * reflected on multiple projects. A more concrete bootstrap
 * should extend this class to implement project specific
 * operations
 *
 * @author Steven Rosato
 * @version 1.0
 *
 * Changelog:
 *
 * 1.0
 * 		- First implementation
 */
abstract class Majisti_Bootstrap
{
	/** @var Majisti_Config_Xml */
	protected $_defaultXmlConfig;

	/** @var Zend_Controller_Front */
	private $_frontController;


	protected function __construct()
	{
		/* load default configuration */
		$this->_defaultXmlConfig = new Majisti_Config_Xml( dirname(__FILE__) . '/Config/default.xml', 'development', true);

		$this->_frontController = Zend_Controller_Front::getInstance();;

		$this->_registerMiscellaneous();

		$this->postConstruct(); //give back control to child class
	}

	/**
	 * @desc Attempt to apply the default configuration setted by the configuration.
	 * If autoMergeWithDefaultConfig is false, it assumes that the configuration passed
	 * as parameter is either the original copy of the defaultXmlConfiguration from the public
	 * library under Majisti/Config/default.xml or that this copy has overridden part or all the fields
	 * of that library XML config. Otherwiese, it will automatically merge the default configuration
	 * with the configuration passed as parameter.
	 *
	 * When everything is done, the configuration is saved under the registry with the key given as parameter
	 * and is set to readOnly.
	 *
	 * The application of the configuration currently follows:
	 *
	 * @param Majisti_Config_Xml $config The configuration to apply
	 * @param boolean autoMergeWithDefaultConfig (optionnal) Will merge default configuration with this config. Default is true.
	 */
	protected function _applyConfiguration(Majisti_Config_Xml $config, $autoMergeWithDefaultConfig = true)
	{
		/* auto merge */
		if( $autoMergeWithDefaultConfig ) {
			$config = $this->_mergeDefaultXmlConfig($config);
		}

		$this->_registerLayout($config);
		$this->_registerHelpers($config);

		/* Database settings */
		if( $config->database->use ) {
			Zend_Db_Table::setDefaultAdapter(
				Zend_Db::factory($config->database->adapter, array(
				    'host'     => $config->database->params->host,
				    'username' => $config->database->params->username,
				    'password' => $config->database->params->password,
				    'dbname'   => $config->database->params->dbname,
					'profiler' => $config->debug->logical,
					'charset'  => $config->charset
				))
			);
		}

		//Zend_Locale::setDefault($config->i18n->default->abbreviate);

		/* Timezone */
		date_default_timezone_set($config->timezone);

		/* error reporting */
		error_reporting($config->debug->error_reporting);

		/* Register DocType */
		$doctype = new Zend_View_Helper_Doctype();
		$doctype->setDoctype($config->doctype);

		/* Load plugin via the plugin loader */
		Majisti_Controller_Plugin_Loader::load($config);
		
		/* Apply the server_host and port to these */
		$host = '';
		$port = '';

		if( $config->links->autoDetectHost ) {
			$port = $_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '';
			$host = 'http://' . $_SERVER['HTTP_HOST'];
		}

		$config->links->library = $host . $port . $config->links->library;

		/* set front controller base Url and exceptions throwing */
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->throwExceptions($config->debug->logical);
		$frontController->setBaseUrl(BASE_URL);

		/* modules directory is optionnal, if none is existant, default controllers directory is setup */
		if( file_exists('../application/modules') ) {
			$frontController->addModuleDirectory('../application/modules/');
		} else {
			$frontController->addControllerDirectory('../application/controllers');
		}

		define('LIB_URL', $config->links->library);
		define('APPLICATION_URL', rtrim('http://' . $_SERVER['HTTP_HOST'] . BASE_URL, '/'));
		
		/* registry */
		Zend_Registry::set('config', $config);
		
		/* Handler for internationalisation */
		Zend_Registry::set('Majisti_I18n', new Majisti_I18n());
		
		/* javascript handling */
		$this->_registerJavascript();

		$config->setReadOnly();
	}

	private function _registerLayout(Zend_Config $config)
	{
		if( $config->layout->use ) {
			Zend_Layout::startMvc(array( /* start the MVC for layout usage */
				'layoutPath' 	=> $config->layout->layoutPath,
				'layout' 		=> $config->layout->defaultLayoutName
			));
		}
	}
	
	private function _registerJavascript()
	{
		$baseUrl 			= BASE_URL;
		$applicationUrl 	= APPLICATION_URL;
		$libUrl 			= LIB_URL;
		
		$js = "
			var Application = {
				url: '{$applicationUrl}',	
				baseUrl: '{$baseUrl}',
				libUrl: '{$libUrl}'
			};
		";
		
		$view = @Zend_Controller_Action_HelperBroker::getHelper('viewRenderer')->view;
		$view->headScript()->prependScript($js);
	}

	/**
	 * Registers some miscellaneous details which can't be categorised in specific functions
	 */
	private function _registerMiscellaneous()
	{
		/* support UTF chars with Zend_JSON::encode/decode() methods */
		Zend_Json::$useBuiltinEncoderDecoder = true;
	}

	private function _registerHelpers($config)
	{
		/* set Majisti's custom view containing a function named '_' that does the same thing as $translate->_ */
		$view = new Majisti_View();
		$view->addBasePath(dirname(__FILE__) . '/Layouts/');
		$view->addScriptPath($config->layout->layoutPath);
		$view->addHelperPath('Majisti/View/Helper/', 'Majisti_View_Helper');
		$view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
		Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setView($view);
		
		Zend_Registry::set('view', $view);
		
		/**
		 * @deprecated DO NOT USE THIS VALUE,
		 * @see 'view'
		 */
		Zend_Registry::set('Majisti_View', $view);
		
		Zend_Controller_Action_HelperBroker::addPath('Majisti/Controller/Action/Helper', 'Majisti_Controller_Action_Helper');
	}

	protected function _mergeDefaultXmlConfig(Majisti_Config_Xml $config)
	{
		$this->_defaultXmlConfig->mergeProperties($config);

		return $this->_defaultXmlConfig->merge($config)->reparse();
	}

	public function dispatch()
	{
		$this->_frontController->dispatch();
	}

	protected abstract function postConstruct();

	public static abstract function getInstance(); //concrete instance should be a singleton
}