<?php

namespace MajistiX\Model\Editing;

class InPlace extends \Majisti\Model\Storage\StorableModel
{
    protected $_genericStorage = 'MajistiX\Model\Editing\IInPlaceStorage';
    
    /**
     * @var IEditor
     */
    protected $_editor;
    
    public function __construct($storageModel, IEditor $editor)
    {
        parent::__construct($storageModel);
        $this->setEditor($editor);
    }
    
    public function getEditor()
    {
        return $this->_editor;
    }
    
    public function setEditor(IEditor $editor)
    {
        $this->_editor = $editor;
    }
    
    public function getContent($key, $locale = null)
    {
        return $this->getStorageModel()->getContent($key, $this->_getLocale($locale));
    }
    
    public function editContent($key, $content, $locale = null)
    {
        $this->getStorageModel()->editContent($key, $content, $this->_getLocale($locale));
    }
    
    public function render($key, $locale = null)
    {
        return $this->getEditor()->render($this->getContent($key, $locale), 
            array('key' => $key));
    }
    
    protected function _getLocale($locale)
    {
        if( null === $locale ) {
            $locale = \Majisti\I18n\LocaleSession::getInstance()->getCurrentLocale();
        }
        
        return $locale;
    }
}
