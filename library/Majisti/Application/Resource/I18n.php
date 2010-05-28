<?php

namespace Majisti\Application\Resource;

class I18n extends \Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $this->initFormValidation();
    }

    protected function initFormValidation()
    {
        $locales = \Majisti\Application\Locales::getInstance();

        $translator = new \Zend_Translate(
            'array',
            MAJISTI_ROOT . '/resources/languages',
            $locales->getCurrentLocale(),
            array('scan' => \Zend_Translate_Adapter::LOCALE_DIRECTORY)
        );

        \Zend_Validate_Abstract::setDefaultTranslator($translator);
    }
}
