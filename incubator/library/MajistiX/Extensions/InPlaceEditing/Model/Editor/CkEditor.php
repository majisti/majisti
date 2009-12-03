<?php

namespace MajistiX\Extensions\InPlaceEditing\Model\Editor;

/**
 * @desc Editor implementation of the very popular CkEditor.
 * Configuration of the editor is issued through of list of params
 * available in the documentation.
 * 
 * @author Steven Rosato
 */
class CkEditor implements IEditor
{
    protected $_formCounter = 0;
    
    /**
     * @desc Constructs the CkEditor by applying the javascript file needed
     * for it to work.
     */
    public function __construct()
    {
        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $view->headScript()->appendFile(MAJISTI_URL . '/majistix/scripts/ckeditor/ckeditor.js');
    }
    
    /**
     * @desc Renders an CkEditor with different options according
     * to the params provided.
     * 
     * @param $content The content to render in the CkEditor
     * @param array $params [optionnal] Optionnal params for the editor 
     */
    public function render($content, array $params = array())
    {
        $form = new \Zend_Form();
        $form->setName('majisti_inPlaceEditing_form' . $this->_formCounter);
        
        //TODO: verify $params['key']
        /* insert textarea that will be changed to CkEditor */
        $textArea = new \Zend_Form_Element_Textarea($params['key']);
        $textArea
            ->setOptions(array('class' => 'ckeditor'))
            ->setValue($content)
        ;
        $form->addElement($textArea);
        
        /* hidden field for post recognition of the controller plugin */
        $hiddenField = new \Zend_Form_Element_Hidden('majisti_inPlaceEditing' . $this->_formCounter);
        $hiddenField->setValue('##MAJISTI_INPLACE_EDITING##');
        $form->addElement($hiddenField);
        
        /* insert submit button */
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
