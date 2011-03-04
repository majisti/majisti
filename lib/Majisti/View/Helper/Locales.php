<?php

namespace Majisti\View\Helper;

/**
 * @desc Locale view helper to retrieve the singleton instance of
 * \Majisti\Application\Locales to help control language switching.
 *
 * @author Majisti
 */
class Locales extends AbstractHelper
{
    /**
     * @var \Majisti\Application\Locales
     */
    protected $_locales;

    /**
     * @desc Returns the locale session instance for locale handling.
     * @return \Majisti\Application\Locales
     */
    public function helper()
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
        if( null === $this->_locales ) {
            $this->_locales = \Zend_Controller_Front::getInstance()
                ->getParam('bootstrap')
                ->getResource('Locales');
        }

        return $this->_locales;
    }
}
