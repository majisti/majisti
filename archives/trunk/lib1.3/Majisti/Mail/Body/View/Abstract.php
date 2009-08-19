<?php

abstract class Majisti_Mail_Body_View_Abstract extends Majisti_Mail_Body_Abstract
{
	/** @var Majisti_View */
	protected $_view;
	
	public function __construct($model = null, $view = null, $bodyType = self::TYPE_HTML, Zend_Translate_Adapter $translator = null)
	{
		$this->_registerView($view);
		
		if( $translator == null && $view instanceof Majisti_View ) {
			parent::__construct($model, $bodyType, $view->getTranslator());
		} else {
			parent::__construct($model, $bodyType, $translator);	
		}	
	}
	
	private function _registerView($view)
	{
		if( $view == null ) {
			if ( ( $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view) !== null ) {
				$this->_view = $view;
			} else if( Zend_Registry::isRegistered('Zend_View') ) {
				$this->_view = Zend_Registry::get('Zend_View');
			} else if ( Zend_Registry::isRegistered('Majisti_View') ) {
				$this->_view = Zend_Registry::get('Majisti_View');
			} else {
				throw new Majisti_Mail_Body_Exception("No instance of Zend_View or Majisti_View was
					found in registry and therefore the view passed as parameter can't be null");	
			}
		} else if ( !($view instanceof Zend_View) ) {
			throw new Majisti_Mail_Body_Exception("View must be an instance of Zend_View");	
		} else {
			$this->_view = $view;
		}
	}
	
	
	public function getView()
	{
		return $this->_view;
	}
}
