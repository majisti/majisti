<?php

class Majisti_Data_Locale
{
	protected $_locale;
	
	public function __construct(Zend_Locale $locale = null)
	{
		/* detect locale, assume locale En if nothing was given */
		if( $locale == null ) {
			if( Zend_Registry::isRegistered('Zend_Locale') && Zend_Registry::get('Zend_Locale') instanceof Zend_Locale ) {
				$this->_locale = Zend_Registry::get('Zend_Locale');
			} else {
				$this->_locale = new Zend_locale('en');
			}
		} else {
			$this->_locale = $locale;	
		}
	}
	
	public function getLocale()
	{
		return $this->_locale;	
	}
}
