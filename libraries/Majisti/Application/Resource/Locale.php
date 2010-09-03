<?php
namespace Majisti\Application\Resource;

/**
 * @desc Supports adding 'available' locales to an application. Available locales
 * constraints the application to the locales specified. The current locale
 * is remembered through a storage model.
 *
 * @see \Majisti\Application\Locales
 * @author Majisti
 */
class Locale extends \Zend_Application_Resource_Locale
{
    /**
     * @desc Prepares the locales according to the options
     */
    public function getLocale()
    {
        $locales    = \Majisti\Application\Locales::getInstance();
        $selector   = new \Majisti\Config\Selector(
            new \Zend_Config($this->getOptions()));

        /* add all available locales */
        if( $availLocales = $selector->find('available', false) ) {
            if( $availLocales instanceof \Zend_Config ) {
                $availLocales = $availLocales->toArray();
            } else if( !is_array($availLocales) ) {
                $availLocales = array($availLocales);
            }

            foreach ($availLocales as $availLocale) {
               $locales->addLocale(new \Zend_Locale($availLocale));
            }

            $defaultLocale = $locales->getDefaultLocale();
            \Zend_Locale::setDefault($defaultLocale);
            \Zend_Registry::set(static::DEFAULT_REGISTRY_KEY, $defaultLocale);
        } else {
            $locale = parent::getLocale();
            $locales->addLocale($locale); //will become default
        }

        return $locales->getDefaultLocale();
    }
}
