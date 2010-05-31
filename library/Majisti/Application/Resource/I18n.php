<?php

namespace Majisti\Application\Resource;

/**
 * @desc Initializes all kind of internationnalisation for an application
 *
 * @author Majisti
 */
class I18n extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @desc Inits the resource
     */
    public function init()
    {
        $this->initForm();
    }

    /**
     * @desc Inits translation for forms
     */
    protected function initForm()
    {
        $locales = \Majisti\Application\Locales::getInstance();

        $translator = new \Zend_Translate(
            'array',
            MAJISTI_ROOT . '/resources/languages',
            $locales->getCurrentLocale(),
            array('scan' => \Zend_Translate_Adapter::LOCALE_DIRECTORY)
        );

        \Zend_Form::setDefaultTranslator($translator);
    }
}
