<?php

namespace Majisti\I18n;

/**
 * @desc This controller handles internationalisation of an application
 * automatically with the aid of a \Zend_Session_Namespace by populating
 * the class with the application's supported languages and default
 * language which were all defined in a configuration. It is then possible
 * to switch amongst the locales at any time.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class LocaleSession implements ILocale
{
    /**
     * @var \Zend_Session_Namespace
     */
    protected $_session;

    /**
     * @var LocaleSession
     */
    static protected $_instance;

    /**
     * @desc Constructs a new Internationalisation object which will handle
     * the locale through \Zend_Locale automatically using the session.
     *
     * Based on the application's configuration,
     * it will populate the supported locales.
     */
    protected function __construct()
    {
    	$this->_session = new \Zend_Session_Namespace('Majisti_I18n', true);
    	$this->reset();
    }

    /**
     * @desc Returns the single instance of this class
     *
     * @return \Majisti\I18n\LocaleSession
     */
    static public function getInstance()
    {
        if( null === static::$_instance ) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

	/**
     * @desc Flushes all I18n persistence and puts back defaults.
     * @return \Majisti\I18n\LocaleSession this
     */
    public function reset()
    {
    	$session 	= $this->_session;
    	$config 	= \Zend_Registry::get('Majisti_Config');

    	if( !(isset($session->locales) && isset($session->defaultLocale)) ) {
    	    $selector = new \Majisti\Config\Selector($config);

    	    $defaultLocale     = $selector->find('plugins.i18n.defaultLocale',
                'en');
            $currentLocale     = $selector->find('plugins.i18n.currentLocale',
                $defaultLocale);
            $supportedLocales  = $selector->find('plugins.i18n.supportedLocales',
                new \Zend_Config(array()))->toArray();

    	    $session->unlock();

    	    $session->defaultLocale = $session->currentLocale = $defaultLocale;
            $session->locales       = $supportedLocales;

            $this->_registerLocales($config);
            $this->_registerLocaleObject();

            $session->lock();
    	}

        return $this;
    }

    /**
     * @desc Registers the current locale within a Zend_Locale object.
     *
     * In any case, a \Zend_Locale will be available is the registry
     * under the Zend_Locale key
     */
    protected function _registerLocaleObject()
    {
    	$currentLocale = $this->getCurrentLocale();
        \Zend_Locale::setDefault($currentLocale);
        \Zend_Registry::set('Zend_Locale', new \Zend_Locale($currentLocale));
    }

    /**
     * @desc Registers the default locale among with all the supported locales
     * defined by the application's configuration. The default locale will
     * always be the first element in the array returned by getLocales().
     */
    protected function _registerLocales(\Zend_Config $config)
    {
        $selector = new \Majisti\Config\Selector($config);

        $defaultLocale = $selector->find('defaultLocale', 'en');

    	$this->_session->defaultLocale = $defaultLocale;

    	if( $supportedLocales = $selector->find('supportedLocales', false) ) {
    		$this->_session->locales = $supportedLocales->toArray();
    	}

    	array_unshift($this->_session->locales, $defaultLocale);
    }

    /**
     * @desc Returns the current locale.
     * The current locale persists through the session.
     *
     * @return String The current locale
     *
     * @see switchLocale() To switch the current locale
     */
    public function getCurrentLocale()
    {
        return $this->_session->currentLocale;
    }

    /**
     * @desc Returns the default locale
     * @return string The default locale
     */
    public function getDefaultLocale()
    {
    	return $this->_session->defaultLocale;
    }

    /**
     * @desc Returns all the supported locales including the default locale
     * defined by the application's configuration. The default locale
     * is always the first element of the array.
     *
     * @return Array All the supported locales, exclusing the default locale
     */
    public function getLocales()
    {
    	return $this->_session->locales;
    }

    /**
     * @desc Returns only the supported locale as an array, omitting
     * the default locale
     * @return Array All the supported locales, excluding the default locale
     */
    public function getSupportedLocales()
    {
    	$locales = $this->getLocales();
    	array_shift($locales);
    	return $locales;
    }

    /**
     * @desc Returns whether the current application's locale is also the
     * default locale defined by the application's configuration.
     *
     * @return bool True is the current locale is also the default locale.
     */
    public function isCurrentLocaleDefault()
    {
        return $this->getCurrentLocale() === $this->getDefaultLocale();
    }

    /**
     * @desc Toggles between the registered locales. Switching is circular,
     * meaning that switching between the languages will never come to an end.
     *
     * @param String $locale (optionnal def=null) Directly switch to that locale
     * and sets the pointer to that locale. An abbreviation must be passed
     * (ex: fr, fr_CA) depending on what was setup in the configuration.
     *
     * @throws Exception If the locale given is not supported by
     * this application.
     *
     * @return The next locale or the given locale if it was passed
     * as parameter.
     */
    public function switchLocale($locale = null)
    {
    	$session = $this->_session;
    	$locales = $this->getLocales();

    	$localeToSearch = null === $locale
    		? $this->getCurrentLocale()
    		: $locale;

    	$key = array_search($localeToSearch, $locales);

		if( false === $key ) {
			throw new Exception("Locale {$localeToSearch} is
				not supported by this application");
		}

		if( null === $locale ) {
			$key = $key + 1 >= count($locales)
				? 0
				: $key + 1;
		}

		$session->unlock();
		$session->currentLocale = $locales[$key];
		$session->lock();

		return $session->currentLocale;
    }

    /**
     * @desc Returns whether the given local is supported by this application.
     *
     * @param String $locale The locale abbreviation following the
     * configuration's syntax
     * @return bool True if this locale is supported.
     */
    public function isLocaleSupported($locale)
    {
    	return false !== array_search($locale, $this->getLocales());
    }
}
