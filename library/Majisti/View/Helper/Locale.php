<?php

/**
 * @desc Locale view helper to retrieve the singleton instance of
 * \Majisti\I18n\Locales to help control language switching.
 *
 * @author Majisti
 */
class Majisti_View_Helper_Locale extends Majisti_View_Helper_Abstract
{
    /**
     * @var \Majisti\I18n\Locales
     */
    protected $_locale;

    /**
     * @desc Returns the locale session instance for locale handling.
     * @return \Majisti\I18n\Locales
     */
    public function locale()
    {
        return $this->getLocale();
    }

    /**
     * @desc Returns the locale object.
     *
     * @return \Majisti\I18n\Locales
     */
    public function getLocale()
    {
        if( null === $this->_locale ) {
            $this->_locale =  \Majisti\I18n\Locales::getInstance();
        }

        return $this->_locale;
    }
}
