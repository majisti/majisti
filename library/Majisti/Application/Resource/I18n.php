<?php

namespace Majisti\Application\Resource;

/**
 * @desc The I18n resource will basically just initiate a null translator.
 * Even if an application is supported in one language it should always make
 * use of a translator for extensibility. That way, at any given time, a real
 * translator can be put in the registry and the application therefore translated
 * without having to modify the code.
 *
 * @author Majisti
 */
class I18n extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @desc Inits a null translator.
     *
     * @return \Zend_Translate_Adapter_Abstract
     */
    public function init()
    {
        return $this->getTranslator();
    }
    
    /**
     * @desc Returns a null translator for this application. Any string
     * will be returned with it. Note that it also sets it under the key
     * Zend_Translate in the registry.
     *
     * @return \Zend_Translate_Adapter
     */
    public function getTranslator()
    {
        \Zend_Registry::set('Zend_Translate', new \Zend_Translate_Adapter_Array(
            array(), null, array('disableNotices' => true)), array());

        return \Zend_Registry::get('Zend_Translate');
    }
}
