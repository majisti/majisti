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
    /**
     * @desc Inits a null translator.
     *
     * @return \Zend_Translate_Adapter_Abstract
     */
    public function init()
    {
        return $this->getTranslate();
    }
    
    /**
     * @desc Returns a null translator for this application if nothing
     * was setup in the configuration. Any string will be returned with it.
     * Note that it also sets it under the key Zend_Translate in the registry.
     *
     * @return \Zend_Translate_Adapter
     */
    public function getTranslate()
    {
        if( !array_key_exists('data', $this->getOptions()) ) {
            \Zend_Registry::set('Zend_Translate', new \Zend_Translate_Adapter_Array(
                array(), null, array('disableNotices' => true)), array());

            return \Zend_Registry::get('Zend_Translate');
        } else {
            return parent::getTranslate();
        }
    }
}
