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
     * @param string defaultLocale The default locale
     * @param string currentLocale The current locale
     * @param array  availableLocales The supported locales
     */
    protected $_locales;

    /**
     * @var LocaleSession
     */
    static protected $_instance;

    /**
     * @desc Constructs a new Internationalisation object which will handle
     * the locale through \Zend_Locale automatically using the session.
     */
    protected function __construct()
    {
    	$this->_locales = new \Zend_Session_Namespace('Majisti_Locale', true);
        $this->init();
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
     * @desc Flushes the I18n persistence.
     */
    public function reset()
    {
        $this->setCurrentLocale($this->getDefaultLocale());
    }

    public function setCurrentLocale($locale)
    {
        $this->_locales->current = $locale;
    }

    protected function isSessionActive()
    {
        return isset($this->_locales->current);
    }

    /**
     * @desc
     */
    protected function init()
    {
        if( $this->isSessionActive() ) {
            if( !array_search($this->getCurrentLocale(), $this->getLocales()) ) {
                $this->switchLocale($this->getDefaultLocale());
            }
        }
    }

    /**
     * @desc Returns the current locale.
     * The current locale persists through the session.
     *
     * @return \Zend_Locale The current locale
     *
     * @see switchLocale() To switch the current locale
     */
    public function getCurrentLocale()
    {
        return $this->_locales->current;
    }

    /**
     * @desc Returns the default locale
     * @return string The default locale
     */
    public function getDefaultLocale()
    {
    	return $this->_locales->default;
    }

    /**
     * @desc Returns all the available locales including the default locale
     * supported by this application.
     *
     * TODO: support exclude default
     *
     * @return Array All the supported locales, including the default locale
     */
    public function getLocales($excludeDefault = false)
    {
        return $this->_locales->available;
    }

    /**
     * @desc Returns whether the current application's locale is also the
     * default locale defined by the application's configuration.
     *
     * @return bool True is the current locale is also the default locale.
     */
    public function isCurrentLocaleDefault()
    {
        return $this->getCurrentLocale()->equals($this->getDefaultLocale());
    }

    /**
     * @desc Toggles between the registered locales. Switching is circular,
     * meaning that switching between the languages will never come to an end.
     *
     * @param \Zend_Locale $locale Directly switch to that locale
     * and sets the pointer to that locale. 
     *
     * @throws Exception If the locale given is not supported by
     * this application.
     *
     * @return LocaleSession this
     */
    public function switchLocale(\Zend_Locale $locale)
    {
        if( !$this->isLocaleSupported($locale) ) {
			throw new Exception("Locale $locale is
				not supported by this application");
        }

        $this->setCurrentLocale($locale);
        return $this;
    }

    /**
     * @desc Returns whether the given local is supported by this application.
     *t
     * @param Sring $locale The locale abbreviation following the
     * configuration's syntax
     * @return bool True if this locale is supported.
     */
    public function isLocaleSupported(\Zend_Locale $locale)
    {
        foreach ($this->getLocales() as $loc) {
            if( $locale->equals($loc) ) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     */
    public function addSupportedLocale($locale)
    {
        if( !$this->isLocaleSupported($locale) ) {
            $this->_locales->locales[] = $locale;
        }
    }

    /**
     *
     */
    public function removeSupportedLocale($locale)
    {
        if( $this->isLocaleSupported($locale) ) {
            if( $locale === $this->getCurrentLocale() ) {
                $this->switchLocale($this->getDefaultLocale());
            }

            if( !($key = array_search($locale, $this->getLocales())) ) {
               unset($this->_locales->locales[$key]);
            }
        }
    }

    public function toString()
    {
        return $this->getCurrentLocale()->toString();
    }

    public function __toString()
    {
        return $this->toString();
    }
}
