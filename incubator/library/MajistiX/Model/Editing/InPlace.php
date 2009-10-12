<?php

namespace MajistiX\Model\Editing;

class Blocks extends \Zend_Db_Table_Abstract
{
    protected $_name = "majisti_demo_simple_blocks";
}

class InPlace extends \Majisti\Model\Storage\StorableModel
{
    protected $_genericStorage = 'MajistiX\Model\Editing\IInPlaceStorage';
    
    public function __construct()
    {
        //TODO: replace this with configuration params
        $this->setStorageModel(new InPlaceDbStorage(new Blocks()));
    }
    
    public function getContent($key, $locale = null)
    {
        return $this->getStorageModel()->getContent($key, $this->_getLocale($locale));
    }
    
    public function editContent($key, $content, $locale = null)
    {
        $this->getStorageModel()->editContent($key, $this->_getLocale($locale), $content);
    }
    
    protected function _getLocale($locale)
    {
        if( null === $locale ) {
            $locale = \Zend_Registry::get('Majisti_I18n')->getCurrentLocale();
        }
        
        return $locale;
    }
}
