<?php

class Majisti_View_Helper_Locale extends Majisti_View_Helper_Abstract
{
    /**
     * @var \Majisti\I18n\LocaleSession
     */
    protected $_locale;

    public function locale()
    {
        return $this->getLocale();
    }

    /**
     * @desc Returns the locale object.
     *
     * @return \Majisti\I18n\LocaleSession
     */
    public function getLocale()
    {
        if( null === $this->_locale ) {
            $this->_locale =  \Majisti\I18n\LocaleSession::getInstance();
        }

        return $this->_locale;
    }
}
