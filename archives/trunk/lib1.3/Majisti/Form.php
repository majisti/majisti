<?php

/**
 * @desc Majisti Form handles internationalization automatically by loading the prefered
 * language from the registry key Zend_Locale (if existant) by translating it's own error messages
 * through the supported languages.
 * 
 * Current automatic error messages languages supported are:
 * 	-Fr, En
 * 
 * By default, the 'default' scope will be taken, and therefore the error messages displayed
 * will be the ones from Majisti's. If the errors Messages from Zend's should be employed, one can
 * set the default scope to 'Original' through $options['messages_scope_namespace'].
 * 
 * Moreover, one can extend the Majisti_Validate_I18n_Abstract to define it's own messages scope
 * by giving the instance via $options['messages_scope'].
 * 
 * Additionally, an error message containing the number of errors after validation will be added
 * prepended to the $view->messages variable.
 * 
 * @author Steven Rosato
 */
class Majisti_Form extends ZendX_JQuery_Form 
{
	const DEFAULT_SCOPE = 'Default';
	
	/** @var Majisti_Validate_I18n_Abstract Scope instance */
	protected $_scope;

	/** @var bool */
	protected $_autoDetectScopeNamespace = true;

	/** @var string */
	protected $_messagesScopeNamespace;
	
	/**
	 * Constructs the form and load the translation according to the passed options if any or by
	 * automatically applying the proper options if nothing was setup.
	 *
	 * @param Array $options
	 */
	public function __construct($options = null)
	{
		$this->_loadOptions($options);

		$this->_loadTranslation();
		
		$this->addPrefixPath('Majisti_Form_Decorator', 'Majisti/Form/Decorator', 'decorator');
		$this->addElementPrefixPath('Majisti_Form_Decorator', 'Majisti/Form/Decorator', 'decorator');
		$this->addElementPrefixPath('Majisti_Validate', 'Majisti/Validate', 'validate');
		$this->addDisplayGroupPrefixPath('Majisti_Form_Decorator', 'Majisti/Form/Decorator');
		
		parent::__construct($options);
	}
    
	/**
	 * Load the options
	 *
	 * @param Array $options
	 */
	private function _loadOptions(&$options)
	{
		/* load messages scope instance */
		if( isset($options['messages_scope']) ) {
			$scope = $options['messages_scope'];

			if( !($scope instanceof Majisti_Validate_I18n_Abstract) ) {
				throw new Majisti_Form_Exception('The scope must be an instance of Majisti_Validate_I18n_Abstract');
			}

			$this->_autoDetectScopeNamespace = false;
			$this->_scope = $scope;
			unset($options['messages_scope']);
		/* load message scope name */
		} else if( isset($options['messages_scope_namespace']) ) {
			$this->_messagesScopeNamespace = $options['messages_scope_namespace'];
			unset($options['messages_scope_namespace']);
		} else { /* nothing was given, load default scope */
			$this->_scope = self::DEFAULT_SCOPE;
		}
	}
	
	/**
	 * Load the translation
	 */
	protected function _loadTranslation()
	{
		if( $this->_autoDetectScopeNamespace ) {
			$locale = ucfirst($this->_getLocale());
			if( ($obj = Majisti_Validate_I18n_Factory::getI18n('Default_' . $locale)) != null ) {
				$this->_setTranslation($obj, strtolower($locale));
			}
		} else if( $this->_scope != null ) {
			$this->_setTranslation($this->_scope, $this->_getLocale());
		} else if( $this->_messagesScopeNamespace != null ) {
			if( ($obj = Majisti_Validate_I18n_Factory::getI18n($this->_messagesScopeNamespace)) != null ) {
				$this->_setTranslation($obj, $this->_getLocale());
			}
		}
	}

	/**
	 * Sets the default translator and the intance's translator.
	 *
	 * @param Majisti_Validate_I18n_Abstract $scope
	 * @param String $locale
	 */
	private function _setTranslation(Majisti_Validate_I18n_Abstract $scope, $locale)
	{
		$adapter = new Zend_Translate_Adapter_Array($scope->getMessagesTemplates(), $locale);
		$adapter->setLocale($locale);
		$this->setTranslator($adapter);
		self::setDefaultTranslator($adapter);
	}

	/**
	 * @return String The current Locale
	 */
	private function _getLocale()
	{
		return Zend_Registry::get('Zend_Locale')->getLanguage();
	}
	
	public function isValid($data)
	{
		$_errorExist = $this->_errorsExist;
		$valid = parent::isValid($data);
		
		// override if it was set to false by Zend_Form...
		if ($_errorExist) {
			$this->_errorsExist = true;
			$valid = false;
		}
		
		/* Prepend errors count to the view messages */
		if( $this->_errorsExist ) {
			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$errorMessage = 'You have %errorsCount% error(s) to correct.';
			if( $this->getTranslator()->isTranslated('form_errors_count') ) {
				$errorMessage = $this->getTranslator()->translate('form_errors_count');
			}
			$count = max( count($this->getMessages()), 1 );
			$errorMessage = str_replace(
				'%errorsCount%', 
				'<span class="form-errors-count">' . $count . '</span>', 
				$errorMessage
			);
			
			$view->flashMessages($errorMessage, 'red');
		}
		
		return $valid;
	}
}