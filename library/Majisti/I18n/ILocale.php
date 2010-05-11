<?php

namespace Majisti\I18n;

/**
 * @desc Provides an interface for locale controllers that control the
 * languages support for an entire application.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface ILocale
{
    /**
     * @desc Flushes the current locale persistence and reset
     * the current locale to the default one.
     */
    public function reset();

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
    public function getCurrentLocale();

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
    public function getDefaultLocale();

    /**
     * @desc Returns all the available locales including the default locale
     * supported by this application.
     *
     * @param bool $excludeDefault Excludes the default locale from the
     * returned locales
     *
     * @return Array All the supported locales, including the default locale
     */
    public function getLocales($excludeDefault = false);

    /**
     * @desc Returns whether the current application's locale is also the
     * default locale defined by the application's configuration.
     *
     * @return bool True is the current locale is also the default locale.
     */
    public function isCurrentLocaleDefault();

    /**
     * @desc Returns whether there is any available locales.
     *
     * @return True is there is no available locales
     */
    public function isEmpty();

    /**
     * @desc Returns the number of available locales supported by
     * this application.
     */
    public function count();

    /**
     * @desc Returns whether the given locale is supported by this application.
     *
     * @param Sring $locale The locale abbreviation following the
     * configuration's syntax
     * @return bool True if this locale is supported.
     */
    public function hasLocale(\Zend_Locale $locale);

    /**
     * @desc Returns whether the given locales are all available.
     *
     * @param array $locales Array of \Zend_Locale
     * @return bool True is and only if all the locales are available
     */
    public function hasLocales(array $locales);

    /**
     * @desc Clears all the available locales and adds the ones provided.
     *
     * @param array $locales An array of \Zend_Locale
     * @param \Zend_Locale $default [optionnal] Directly switch
     * the default locale to the one provided
     */
    public function setLocales(array $locales, $default = null);

    /**
     * @desc Toggles between the available locales, storing the current locale
     * to the one provided.
     *
     * Everytime the locale is switched, the Zend_Registry key Zend_Locale
     * is updated with the new locale accordingly.
     *
     * @param \Zend_Locale $locale Directly switch to that locale.
     *
     * @throws Exception If the locale given is not available.
     *
     * @return Locales this
     */
    public function switchLocale(\Zend_Locale $locale);

    /**
     * @desc Sets the default locale
     *
     * @param Zend_Locale $locale The locale
     * @throws Exeption if the default locale is not an available locale
     */
    public function setDefaultLocale(\Zend_Locale $locale);

    /**
     * @desc Adds a locale to this list of available locales.
     *
     * @param \Zend_Locale $locale The locale
     * @return Locales this
     */
    public function addLocale(\Zend_Locale $locale);

    /**
     * @desc Add multiple locales at once to the list of available locales.
     *
     * @param array $locales An array of \Zend_Locales
     * @return Locales this
     */
    public function addLocales(array $locales);

    /**
     * @desc Clears all the available locales
     */
    public function clearLocales();

    /**
     * @desc Removes a locale to the list of available locales
     *
     * @param \Zend_Locale $locale The locale
     * @return Locales|false This or false if the locale was not found
     * and therefore not removed.
     */
    public function removeLocale(\Zend_Locale $locale);

    /**
     * @desc Removes multiple locales at once from the list of available locales
     *
     * @param array $locales An array of locales
     * @return Locales this
     */
    public function removeLocales(array $locales);
}