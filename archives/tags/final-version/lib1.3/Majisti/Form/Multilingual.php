<?php

/**
 * TODO: doc
 *
 * @author Steven Rosato
 *
 * @deprecated see {@link Majisti_Form}
 */
class Majisti_Form_Multilingual extends Majisti_Form
{
	//const DEFAULT_MESSAGES_SCOPE = 'Default';

	public function __construct($options = null)
	{
		//$this->_loadFile($options);
		parent::__construct($options);
	}

//	protected function _loadFile(&$options = null)
//	{
//		/* retrieve locale */
//		Zend_Locale::$compatibilityMode = false;
//		$locales = Zend_Locale::getDefault();
//		end($locales);
//		$locale = key($locales);
//
//		$chunks = split('_', $locale);
//
//		if( count($chunks) ) {
//			$locale = $chunks[0];
//		}
//
//		/* retrieve messages scope */
//		$scope = self::DEFAULT_MESSAGES_SCOPE;
//		if( $options != null ) {
//			if( isset($options['messages_scope']) ) {
//				$scope = ucfirst(strtolower($options['messages_scope']));
//				unset($options['messages_scope']);
//
//				if( !file_exists(dirname(__FILE__) . "/../Validate/I18n/{$scope}") ) {
//					throw new Majisti_Form_Multilingual_Exception('Messages scope is invalid');
//				}
//			}
//		}
//
//		/* load file from current locale, if existant, otherwise, default Zend Messages will be outputed */
//		$file = dirname(__FILE__) . "/../Validate/I18n/{$scope}/{$locale}.php";
//		if( file_exists($file) ) {
//
//			// load the translation class
//			require_once $file;
//			$className = ;
//			$translationData = new $className();
//
//			$adapter = new Zend_Translate_Adapter_Array($translationData->getMessagesTemplates(), $locale);
//			$adapter->setLocale($locale);
//			$this->setTranslator($adapter);
//		}
//	}

}