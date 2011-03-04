<?php
namespace Majisti\Application\Resource;

/**
 * @desc Supports adding 'available' locales to an application. Available locales
 * constraints the application to the locales specified. 
 *
 * @see \Majisti\Application\Locales
 * @author Majisti
 */
class Locales extends \Zend_Application_Resource_Locale
{
    /**
     * @var \Majisti\Application\Locales 
     */
    protected $_locales;
    /**
     * @desc Prepares the locales according to the options
     */
    public function getLocale()
    {
        if( null !== $this->_locales ) {
            return $this->_locales;
        }

        $maj        = $this->getBootstrap()
                            ->getApplication()
                            ->getOption('majisti');
        $locales    = new \Majisti\Application\Locales($maj['app']['namespace']);
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

            /* set default if given */
            if( $defaultLocale = $selector->find('default', false) ) {
                $locales->setDefaultLocale(new \Zend_Locale($defaultLocale));
            }

            $defaultLocale = $locales->getDefaultLocale();
            \Zend_Locale::setDefault($defaultLocale);
            \Zend_Registry::set(static::DEFAULT_REGISTRY_KEY, $defaultLocale);
        } else {
            $locale = parent::getLocale();
            $locales->addLocale($locale); //will become default
        }

        $this->_locales = $locales;

        return $locales;
    }
}
