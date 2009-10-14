<?php

namespace MajistiX\Model\Editing;

class CkEditor implements IEditor
{
    protected $_formCounter = 0;
    
    public function render($content, array $params = array())
    {
        $form = new \Zend_Form();
        $form->setName('majisti_inPlaceEditing_form' . $this->_formCounter);
        
        $textArea = new \Zend_Form_Element_Textarea($params['key']);//TODO: verify $params['key']
        $textArea
            ->setOptions(array('class' => 'ckeditor'))
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
        
        return $form->render();
    }
    
    
    //TODO: place elsewhere in a CKEditor config scope
//        $i18n = \Majisti\I18n\LocaleSession::getInstance();
//        $this->view->inlineScript()->appendScript("CKEDITOR.replace('{$key}', " .
//            \Zend_Json::encode(array(
//                'toolbar'           => 'Full',
//                'language'          => $i18n->getCurrentLocale(),
//                //'width'             => '400',
//                //'resize_minWidth'   => '400',
//                //'resize_maxWidth'   => '800'
//            )) . ');'
//        );
}