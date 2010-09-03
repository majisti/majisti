<?php

namespace Majisti\View\Helper;

/**
 * @desc Locale view helper to retrieve the singleton instance of
 * \Majisti\Application\Locales to help control language switching.
 *
 * @author Majisti
 */
class Locale extends AbstractHelper
{
    /**
     * @var \Majisti\Application\Locales
     */
    protected $_locale;

    /**
     * @desc Returns the locale session instance for locale handling.
     * @return \Majisti\Application\Locales
     */
    public function locale()
    {
        return $this->getLocale();
    }

    /**
     * @desc Returns the locale object.
     *
     * @return \Majisti\Application\Locales
     */
    public function getLocale()
    {
        if( null === $this->_locale ) {
            $this->_locale =  \Majisti\Application\Locales::getInstance();
        }

        return $this->_locale;
    }
}
