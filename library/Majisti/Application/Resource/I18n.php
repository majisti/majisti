<?php

namespace Majisti\Application\Resource;

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
     * @return \Zend_Translate_Adapter_Abstract
     */
    public function getTranslator()
    {
        \Zend_Registry::set('Zend_Translate', new \Zend_Translate_Adapter_Array(
            array(), null, array('disableNotices' => true)), array());

        return \Zend_Registry::get('Zend_Translate');
    }
}
