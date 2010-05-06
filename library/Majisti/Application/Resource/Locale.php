<?php
namespace Majisti\Application\Resource;

use \Majisti\I18n\LocaleSession as LocaleSession;

/**
 * @desc This resource will register a LayoutSwitcher plugin
 * that will enable modules with their own layout views.
 *
 * @author Majisti
 */
class Locale extends \Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {

    }

    protected function prepareLocaleSession()
    {
        $localeSession = LocaleSession::getInstance();
        $selector      = new \Majisti\Config\Selector($this->getOptions());

        if( $availLocales = $selector->find(
                'majisti.app.locale.available', false) )
        {
//            $localeSession->setLocales($availLocales);

            if( $defaultLocale = $selector->find(
                'majisti.app.locale.default', false) )
            {
//                $localeSession->setDefaultLocale($defaultLocale);
            }
        }
    }
}
