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
        $this->initModel();
    }

    /**
     * @desc Inits translation for forms
     */
    protected function initForm()
    {
        $locales = $this->getLocales();
        $appSettings = $this->getBootstrap()->getOptions();

        $translator = new \Zend_Translate(
            'array',
            $appSettings['majisti']['path'] . '/resources/languages',
            $locales->getCurrentLocale(),
            array('scan' => \Zend_Translate_Adapter::LOCALE_DIRECTORY)
        );

        \Zend_Form::setDefaultTranslator($translator);
    }

    /**
     * @desc Returns the locales
     * 
     * @return \Majisti\Application\Locales
     */
    protected function getLocales()
    {
        return $this->getBootstrap()
            ->bootstrap('Locales')
            ->getResource('Locales');
    }

    /**
     * @desc Init the models.
     */
    protected function initModel()
    {
        \Majisti\Model\Data\Xml::setLocales($this->getLocales());
    }
}
