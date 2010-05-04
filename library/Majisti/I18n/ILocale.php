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
     * @desc Flushes all I18n persistence and puts back defaults.
     * @return \Majisti\I18n\ILocale this
     */
    public function reset();

    /**
     * @desc Returns the current locale.
     * The current locale persists through the session.
     *
     * @return String The current locale
     *
     * @see switchLocale() To switch the current locale
     */
    public function getCurrentLocale();

    /**
     * @desc Returns the default locale
     * @return string The default locale
     */
    public function getDefaultLocale();

    /**
     * @desc Returns all the supported locales including the default locale
     * defined by the application's configuration. The default locale
     * is always the first element of the array.
     *
     * @return Array All the supported locales, exclusing the default locale
     */
    public function getLocales();

    /**
     * @desc Returns only the supported locale as an array, omitting
     * the default locale
     * @return Array All the supported locales, excluding the default locale
     */
    public function getSupportedLocales();

    /**
     * @desc Returns whether the current application's locale is also the
     * default locale defined by the application's configuration.
     *
     * @return bool True is the current locale is also the default locale.
     */
    public function isCurrentLocaleDefault();

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
    public function switchLocale($locale = null);

    /**
     * @desc Returns whether the given local is supported by this application.
     *
     * @param String $locale The locale abbreviation following the
     * configuration's syntax
     * @return bool True if this locale is supported.
     */
    public function isLocaleSupported($locale);
}