<?php

//namespace MajistiX\View\Helper;

class MajistiX_View_Helper_InPlaceEditing extends \Majisti\View\Helper\HelperAbstract
{
    protected $_formCounter = 0;
    
    public function inPlaceEditing($key, $options = array())
    {
        //TODO: retrieve from model container
        $editor     = $this->_createEditor();
        $content    = $editor->getContent($key);
        
        if( null === $content ) {
            $content = 'Place content here'; //TODO: PO translator?
        }
        
        //TODO: place elsewhere in a CKEditor config scope
        $i18n = \Majisti\I18n\LocaleSession::getInstance();
        $this->view->inlineScript()->appendScript("CKEDITOR.replace('{$key}', " .
            \Zend_Json::encode(array(
                'toolbar'           => 'Full',
                'language'          => $i18n->getCurrentLocale(),
                //'width'             => '400',
                //'resize_minWidth'   => '400',
                //'resize_maxWidth'   => '800'
            )) . ');'
        );
        
        return $this->_createForm($key, $content)->render();
    }
    
    protected function _createEditor()
    {
        $configSelector = new \Majisti\Config\Selector($this->getConfig());
        
        $storageAdapter = $configSelector->find('plugins.inPlaceEditing.storage.adapter', 'db');
        $storageParams  = $configSelector->find('plugins.inPlaceEditing.storage.params', 
            new Zend_Config(array()));
        
        $adapter = \MajistiX\Model\Editing\InPlaceStorageFactory::createStorage(
            (string)$storageAdapter, $storageParams->toArray());
            
        //TODO: use future model container
        $editor = new \MajistiX\Model\Editing\InPlace($adapter);
        \Zend_Registry::set('Majisti_InPlaceEditing_Model', $editor);
        return $editor;
    }
    
    /**
     * @return \Zend_Form 
     */
    protected function _createForm($key, $content)
    {
        $form = new \Zend_Form();
        $form->setName('majisti_inPlaceEditing_form' . $this->_formCounter);
        
        $textArea = new \Zend_Form_Element_Textarea($key);
        $textArea
            ->setOptions(array(
                'class' => 'ckeditor' //TODO: support multiple WYSIWYG editors 
            ))
            ->setValue($content)
        ;
        $form->addElement($textArea);
        
        $hiddenField = new \Zend_Form_Element_Hidden('majisti_inPlaceEditing' . $this->_formCounter);
        $hiddenField->setValue('##MAJISTI_INPLACE_EDITING##');
        $form->addElement($hiddenField);
        
        $btn_submit = new \Zend_Form_Element_Submit('majisti_inPlaceEditing_submit' . $this->_formCounter);
        $btn_submit->setLabel('Save'); //TODO: PO translator?
        $form->addElement($btn_submit);
        
        $this->_formCounter++;
        
        return $form;
    }
}