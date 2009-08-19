<?php

abstract class Majisti_Mail_Body_Abstract implements Majisti_Mail_Body_Interface
{
	/** @var Zend_Translate */
	protected $_translator;
	
	protected $_bodyType;
	
	protected $_model;
	
	public function __construct($model = null, $bodyType = self::TYPE_HTML, Zend_Translate_Adapter $translator = null)
	{
		$this->_model = $model;
		
		$this->_registerBodyType($bodyType);
		
		$this->_registerTranslator($translator);
	}
	
	public function getModel()
	{
		return $this->_model;
	}
	
	public function getBodyType()
	{
		return $this->_bodyType;
	}
	
	public function getTranslator()
	{
		return $this->_translator;	
	}
	
	protected function _registerBodyType($bodyType)
	{
		$this->_bodyType = $bodyType;	
	}
	
	protected function _registerTranslator(Zend_Translate_Adapter $translator = null)
	{
		if( $translator != null ) {
			$this->_translator = $translator;
		} else if( Zend_Registry::isRegistered('Zend_Translate') ) {
			if( ($translator = Zend_Registry::get('Zend_Translate')) instanceof Zend_Translate_Adapter ) {
				$this->_translator = $translator;	
			} else {
				$this->_translator = new Majisti_Translate_Adapter_Null();
			}
		} else {
			$this->_translator = new Majisti_Translate_Adapter_Null();
		}
	}
}
