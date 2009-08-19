<?php

class Majisti_Mail_Body_View_Partial extends Majisti_Mail_Body_View_Abstract
{
	/** @var String */
	protected $_partialName;
	
	public function __construct($partialName, $model = null, $view = null, $bodyType = self::TYPE_HTML, Zend_Translate_Adapter $translator = null)
	{
		parent::__construct($model, $view, $bodyType, $translator);
		
		$this->_registerPartialName($partialName);
	}
	
	protected function _registerPartialName($partialName)
	{
		if( $partialName == null ) {
			throw new Majisti_Mail_Body_Exception("Partial name can't be null");
		} else if ( empty($partialName) ) {
			throw new Majisti_Mail_Body_Exception("Partial name can't be empty");
		} else if( !is_string($partialName) ) {
			throw new Majisti_Mail_Body_Exception("Partial name must be a string");
		}
		
		$this->_partialName = $partialName;
	}
	
	
	public function getPartialName()
	{
		return $this->_partialName;	
	}
	
	public function getBody()
	{
		return $this->_view->partial($this->getPartialName(), array('model' => $this->getModel()));
	}
}
