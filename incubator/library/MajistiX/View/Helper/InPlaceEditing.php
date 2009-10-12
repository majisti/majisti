<?php

//namespace MajistiX\View\Helper;

class MajistiX_View_Helper_InPlaceEditing extends \Zend_View_Helper_Abstract
{
    protected $_formCounter = 0;
    
    public function inPlaceEditing($key, $options = array())
    {
        $form = new \Zend_Form();
        $form->setName('majisti_inPlaceEditing_form' . $this->_formCounter);
        
        //TODO: retrieve from model container
        $editor     = new \MajistiX\Model\Editing\InPlace();
        $content    = $editor->getContent($key);
        
        if( null === $content ) {
            $content = 'Place content here'; //FIXME: PO translator?
        }
        
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
        
        $this->_formCounter++;
        
        return $form->render();
    }
}