<?php

namespace Majisti\Application\Resource;

/**
 * @desc The translator resource will basically just initiate a null translator.
 * Even if an application is supported in one language it should always make
 * use of a translator for extensibility. That way, at any given time, a real
 * translator can be put in the registry and the application therefore translated
 * without having to modify the code.
 *
 * @author Majisti
 */
class Translate extends \Zend_Application_Resource_Translate
{
    protected $_adapter;

    /**
     * @desc Inits a gettext translator when language is different from default.
     *
     * @return \Zend_Translate_Adapter_Abstract
     */
    public function getTranslate()
    {
        if( null !== $this->_adapter ) {
            return $this->_adapter;
        }

        $locales = $this->getBootstrap()
            ->bootstrap('Locales')
            ->getResource('Locales');
        $options = new \Zend_Config(
            $this->getBootstrap()->getApplication()->getOptions());
        $options = $options->majisti;

        $currentLocale = $locales->getCurrentLocale();
        $moPath = $options->app->path . "/library/models/i18n/{$currentLocale}.mo";

        if( !file_exists($moPath) ) {
            return $this->getNullTranslate();
        }

        /* add application's translation file */
        $adapter = new \Zend_Translate_Adapter_Gettext(
            $moPath,
            $locales->getCurrentLocale()
        );

        /* add majp mo file, provided the application is not already en */
        $majpMo = $options->path . "/resources/i18n/{$currentLocale}.mo";
        if( !$locales->getCurrentLocale()->equals(new \Zend_Locale('en'))
            && file_exists($majpMo) )
        {
            $adapter->addTranslation(array(
                'content' => $majpMo,
                'locale'  => $currentLocale,
            ));
        }

        \Zend_Registry::set('Zend_Translate', $adapter);

        $this->_adapter = $adapter;

        return $adapter;
    }

    /**
     * @desc Returns a empty translator for this application if nothing
     * was setup in the configuration. Any string will be returned with it.
     * Note that it also sets it under the key Zend_Translate in the registry.
     *
     * @return \Zend_Translate_Adapter
     */
    protected function getNullTranslate()
    {
        if( !array_key_exists('data', $this->getOptions()) ) {
            \Zend_Registry::set('Zend_Translate',
                new \Zend_Translate_Adapter_Array(
                    array(), null, array('disableNotices' => true)), array());

            return \Zend_Registry::get('Zend_Translate');
        } else {
            return parent::getTranslate();
        }
    }
}
