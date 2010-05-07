<?php

namespace Majisti\I18n;

/**
 * @desc The Locales class aggregates a set of \Zend_Locale objects, called
 * 'available locales' and provides a way to persist a chosen current locale
 * from the ones available. It is also possible to chose a default locale that
 * will set the current locale to it when the object is first instanciated.
 * This singleton class persists the state of the current locale through
 * the use of a \Majisti\Model\StorageModel. If no storage model
 * is given, it will use the \Majisti\Model\StorageModelSession.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Locales
{
    /**
     * @var \Zend_Session_Namespace
     * @param string current The current locale
     */
    private $_session;

    /**
     * @var \Zend_Locale
     */
    protected $_defaultLocale;

    /**
     * @var array of \Zend_Locale
     */
    protected $_locales = array();

    /**
     * @var Locales
     */
    static protected $_instance;

    /**
     * @desc Constructs a new Internationalisation object which will handle
     * the locale through \Zend_Locale automatically using the session.
     */
    protected function __construct()
    {
    	$this->_session = new \Zend_Session_Namespace('Majisti_Locale', true);
    }

    /**
     * @desc Returns the single instance of this class
     *
     * @return \Majisti\I18n\Locales
     */
    static public function getInstance()
    {
        if( null === static::$_instance ) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * @desc Flushes the current locale persistence and reset
     * the current locale to the default one.
     */
    public function reset()
    {
        $this->switchLocale($this->getDefaultLocale());
    }

    /**
     * @desc Returns true if the session is active
     *
     * @return bool True is the session is active
     */
    private function isSessionActive()
    {
        return isset($this->_session->current);
    }

    /**
     * @desc Returns the number of available locales supported by
     * this application.
     */
    public function count()
    {
        return count($this->getLocales());
    }

    /**
     * @desc Returns the current locale. If there is no longer any
     * available locales, it will return null, but will still keep
     * the current locale until there are available locales added.
     *
     * If the current locale was never set or if the current locale is
     * no longer amongst the available locale, it will reset itself to the
     * default locale.
     *
     * @return \Zend_Locale|null The current locale
     */
    public function getCurrentLocale()
    {
        if( $this->isEmpty() ) {
            return null;
        }

        if( !isset($this->_session->current) ) {
            $this->_session->current = serialize($this->getDefaultLocale());
        }

        $current = unserialize($this->_session->current);

        if( !$this->hasLocale($current) ) {
            return null;
        }

        return $current;
    }

    /**
     * @desc Returns the default locale. If there is no longer any
     * available locales, it will return null, but will still keep
     * the default locale until there are available locales added.
     *
     * If the default locale was never set or if the default locale is
     * no longer amongst the available locale, it will reset itself to the
     * first added locale.
     *
     * @return \Zend_Locale|null The default locale
     */
    public function getDefaultLocale()
    {
        if( $this->isEmpty() ) {
            return null;
        }

        if( null == $this->_defaultLocale ||
                !$this->hasLocale($this->_defaultLocale) ) {
            $this->_defaultLocale = $this->_locales[0];
        }

        return $this->_defaultLocale;
    }

    public function isEmpty()
    {
        return 0 === $this->count();
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
        return $this->_locales;
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
     * @return Locales this
     */
    public function switchLocale(\Zend_Locale $locale)
    {
        if( !$this->hasLocale($locale) ) {
            throw new Exception("Locale $locale is not available.");
        }

//        $this->getStorageModel()->current = serialize($locale);
        $this->_session->current = serialize($locale);
        \Zend_Registry::set('Zend_Locale', $locale);

        return $this;
    }

    /**
     * @desc Returns whether the given locale is supported by this application.
     *
     * @param Sring $locale The locale abbreviation following the
     * configuration's syntax
     * @return bool True if this locale is supported.
     */
    public function hasLocale(\Zend_Locale $locale)
    {
        return false !== $this->findLocale($locale);
    }

    /**
     * @desc Returns whether the given locales are all available.
     *
     * @param array $locales Array of \Zend_Locale
     * @return bool True is and only if all the locales are available
     */
    public function hasLocales(array $locales)
    {
        foreach ($locales as $locale) {
            if( !$this->hasLocale($locale) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @desc Clears all the available locales and adds the ones provided.
     *
     * @param array $locales An array of \Zend_Locale
     * @param \Zend_Locale $default [optionnal] Directly switch
     * the default locale to the one provided
     */
    public function setLocales(array $locales, $default = null)
    {
        $this->clearLocales();
        $this->addLocales($locales);

        if( null !== $default ) {
            $this->setDefaultLocale($default);
        }
    }

    /**
     * @desc Sets the default locale
     *
     * @param Zend_Locale $locale The locale
     * @throws Exeption if the default locale is not an available locale
     */
    public function setDefaultLocale(\Zend_Locale $locale)
    {
        if( !$this->hasLocale($locale) ) {
            throw new Exception("Locale $locale is not available.");
        }

        $this->_defaultLocale = $locale;
    }

    /**
     * @desc Add multiple locales at once to the list of available locales.
     *
     * @param array $locales An array of \Zend_Locales
     * @return Locales this
     */
    public function addLocales(array $locales)
    {
        foreach ($locales as $locale) {
           $this->addLocale($locale);
        }

        return $this;
    }

    /**
     * @desc Clears all the locales
     */
    protected function clearLocales()
    {
        $this->_locales = array();
    }

    /**
     * @desc Adds a locale to this list of available locales.
     *
     * @param \Zend_Locale $locale The locale
     * @return Locales this
     */
    public function addLocale(\Zend_Locale $locale)
    {
        if( !$this->hasLocale($locale) ) {
            $this->_locales[] = $locale;
        }

        return $this;
    }

    /**
     * @desc Finds a locale in the list of available locales, retuning the key.
     *
     * @param \Zend_Locale $locale The locale
     * @return int|false The key or false if the locale was not found.
     */
    protected function findLocale(\Zend_Locale $locale)
    {
        foreach ($this->getLocales() as $key => $loc) {
            if( $locale->equals($loc) ) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @desc Removes a locale to the list of available locales
     *
     * @param \Zend_Locale $locale The locale
     * @return Locales|false This or false if the locale was not found
     * and therefore not removed.
     */
    public function removeLocale(\Zend_Locale $locale)
    {
        if( $key = $this->findLocale($locale) !== false) {
            unset($this->_locales[$key]);
            return $this;
        }

        return false;
    }

    /**
     * @desc Removes multiple locales at once from the list of available locales
     *
     * @param array $locales An array of locales
     * @return Locales this
     */
    public function removeLocales(array $locales)
    {
        foreach ($locales as $locale) {
           $this->removeLocale($locale);
        }

        return $this;
    }

    /**
     * @desc Returns the current locale as string.
     *
     * @see \Zend_Locale::toString();
     * @return string Returns the current locale as a string
     */
    public function toString()
    {
        return $this->getCurrentLocale()->toString();
    }

    /**
     * @desc Returns the current locale as string.
     *
     * @see \Zend_Locale::toString();
     * @return string Returns the current locale as a string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
