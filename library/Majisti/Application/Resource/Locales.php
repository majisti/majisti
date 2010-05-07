<?php
namespace Majisti\Application\Resource;

/**
 * @desc This resource will register a LayoutSwitcher plugin
 * that will enable modules with their own layout views.
 *
 * @author Majisti
 */
class Locales extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @desc Inits the resource.
     *
     * @return \Majisti\I18n\Locales the locales
     */
    public function init()
    {
        $this->prepareLocales();
        return \Majisti\I18n\Locales::getInstance();
    }

    /**
     * @desc Prepares the locales according to the options
     */
    protected function prepareLocales()
    {
        $locales    = \Majisti\I18n\Locales::getInstance();
        $selector   = new \Majisti\Config\Selector(
            new \Zend_Config($this->getOptions()));

        /* add all available locales */
        if( $availLocales = $selector->find('available', false) ) {
            if( !is_array($availLocales) ) {
                $availLocales = array($availLocales);
            }
            $localeSession->setLocales($availLocales);
        } else { /* library's default language */
            $locales->addLocale(new \Zend_Locale('en'));
        }
    }
}
