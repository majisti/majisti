<?php

/**
 * @desc This class handles automatically with the aid of a Zend_Session_Namespace
 * the internationalisation of an application by populating the class with the
 * application's supported languages and default language which were all defined
 * in the configuration.
 * 
 * It is then possible to switch among the supported languages at any time.
 * 
 * Currently, two view helpers make use of this class:
 * 
 * - GetCurLang which returns the currentLocale (@see getCurrentLocale())
 * - EnumerateLanguagesAsLinks which will print i18n links for switching languages (ex: French | English)
 * 
 * There is also the i18n plugin which listens forr language changes through the request that makes
 * use of this class to switch the current Locale.
 * 
 * This class is dependent of Majisti_Bootstrap.
 *
 * @author Steven Rosato
 */
class Majisti_I18n
{
	/** @var Zend_Session_Namespace */
	protected $_session;
	
	/** @var Array */
	protected $_supportedLocales = array();
	
	/**
	 * @desc Construct a new Internationalisation object which will handle
	 * the locale through Zend_Locale automatically using the session.
	 * 
	 * Based on the application's configuration, it will populate the supported locales.
	 */
	public function __construct()
	{
		$this->_session = new Zend_Session_Namespace(Zend_Registry::get('config')->session);
		
		$this->_registerSupportedLocales();
		
		$this->_registerLocaleObject();
	}
	
	/**
	 * @desc Registers the current locale. If the current locale was already
	 * registered in the session, it will take this current locale, otherwise
	 * it will take the current locale in the configuration and then set it 
	 * in the session.
	 * 
	 * In any case, a Zend_Locale will be available is the registry
	 */
	private function _registerLocaleObject()
	{
		Zend_Locale::setDefault($this->getCurrentLocale());
		Zend_Registry::set('Zend_Locale', new Zend_Locale($this->getCurrentLocale()));
	}
	
	/**
	 * @desc Registers the default locale among with all the supported locales defined by
	 * the application's configuration into the $_supportedLocales array. The default
	 * language defined by the configuration will always be the first element on the array.
	 * 
	 * The default language will be stored into the session if it never were stored before.
	 * 
	 * @throws Majisti_I18n_Exception if the session was altered and the string is not a supported locale
	 */
	private function _registerSupportedLocales()
	{
		/* configuration */
		$config = Zend_Registry::get('config');
		
		/* default locale */
		$this->_supportedLocales[$config->i18n->default->abbreviate] = $config->i18n->default->full;
		
		/* supported locales */
		if( isset($config->i18n->supported) ) {
			/* multiple supported locales detected */
			if( is_array(reset($config->i18n->supported->toArray())) ) {
				foreach ($config->i18n->supported as $supported) {
					if( !in_array($supported->abbreviate, $this->_supportedLocales) ) {
						$this->_supportedLocales[$supported->abbreviate] = $supported->full;
					}
				}
			/* only one supported locale detected */
			} else {
				$this->_supportedLocales[$config->i18n->supported->abbreviate] = $config->i18n->supported->full;
			}
		}
		
		reset($this->_supportedLocales);
		/* store into the session */
		if( !isset($this->_session->i18n) ) {
			$this->_session->i18n = key($this->_supportedLocales);
		/* set the internal pointer to the session's locale, if it is existant */
		} else if( isset($this->_supportedLocales[$this->_session->i18n]) ) {
			while( key($this->_supportedLocales) !== $this->_session->i18n ) {
				next($this->_supportedLocales);
			}
		} else { /* unsupported locale defined in the session */
			throw new Majisti_I18n_Exception("The session key i18n is not supported by this application.");
		}
	}
	
	/**
	 * @desc Returns the current locale. The current locale persists through the session. 
	 * 
	 * @param bool $as_array (optionnal def=false) Pass true to get a key/value paired array which
	 * contains the 'abbreviate' => 'full language description'
	 * 
	 * @return String The current locale defined by the 'abbreviate' key from the i18n configuration.
	 * 
	 * @see switchLocale() To switch the current locale
	 */
	public function getCurrentLocale($as_array = false)
	{
		if( $as_array ) {
			return array(key($this->_supportedLocales) => current($this->_supportedLocales));
		}
		return key($this->_supportedLocales);
	}
	
	/**
	 * @desc Returns all the supported locales including the default locale defined by the application's
	 * configuration. The default language is always the first element of the array.
	 *
	 * @param bool $excludeCurrentLocale (optionnal; def=false) Exclude the current locale from the returned array?
	 * @return Array An associative array containing the languages abbreviations/language description as key => value
	 */
	public function getSupportedLocales($excludeCurrentLocale = false)
	{
		/* save the current pointer */
		$pointer = key($this->_supportedLocales);
		
		/* current locale */
		$currentLocale = $this->getCurrentLocale();
		
		/* make a copy of the current supported locales */
		$supportedLocales = array();
		foreach ($this->_supportedLocales as $key => $value){
			$supportedLocales[$key] = $value;
		}
		
		/* exlude the current locale */
		if( $excludeCurrentLocale ) {
			if( isset($supportedLocales[$currentLocale]) ) {
				unset($supportedLocales[$currentLocale]);
			}
		}
		
		/* replace the pointer */
		reset($this->_supportedLocales);
		while( key($this->_supportedLocales) !== $pointer ) {
			next($this->_supportedLocales);
		}
		
		return $supportedLocales;
	}
	
	/**
	 * @desc Returns whether the current application's locale saved in the session
	 * is also the default locale defined by the application's configuration.
	 *
	 * @return bool True is the current locale is also the default locale defined by the application's config
	 */
	public function isCurrentLocaleDefault()
	{
		$supportedLocales = $this->_supportedLocales;
		reset($supportedLocales);
		return $this->getCurrentLocale() == key($supportedLocales);
	}
	
	/**
	 * @desc Toggles between the registered supported locales moving the internal
	 * pointer of the array forward.
	 * 
	 * @param String $locale (optionnal def=null) Directly switch to that locale and sets the pointer
	 * to that locale. An abbreviate must be passed (ex: fr, fr_CA) depending on what was setup
	 * in the configuration.
	 * 
	 * @throws Majisti_I18n_Exception If the locale given is not supported by this application.
	 * 
	 * @return The next locale
	 */
	public function switchLocale($locale = null)
	{
		if( count($this->_supportedLocales) > 1 ) {
			/* switch using the next php function */
			if( $locale == null ) {
				if( next($this->_supportedLocales) ) {
					$this->_session->i18n = key($this->_supportedLocales);
				} else {
					reset($this->_supportedLocales);
					$this->_session->i18n = key($this->_supportedLocales);
				}
			/* a locale was given, check first if it exists, throw exception if not */
			} elseif ( !isset($this->_supportedLocales[$locale]) ) {
				throw new Majisti_I18n_Exception("Locale {$locale} is not supported by this application.");
			/* locale is supported, directly switch to it and set internal pointer to new current locale */
			} else {
				$this->_session->i18n = $locale;
				
				reset($this->_supportedLocales);
				while( key($this->_supportedLocales) !== $locale) {
					next($this->_supportedLocales);
				}
			}
			
			return $this->_session->i18n;
		}
		return $this->getCurrentLocale();
	}
	
	/**
	 * @desc Returns whether the given local is supported by this application.
	 *
	 * @param String $locale The locale abbreviation following the configuration's syntax
	 * @return bool True if this locale is supported.
	 */
	public function isLocaleSupported($locale)
	{
		return isset($this->_supportedLocales[$locale]);
	}
}