<?php

namespace MajistiX\Extension\InPlaceEditing\View\Editor;

/**
 * @desc Editor implementation of the very popular CkEditor.
 * Configuration of the editor is issued through of list of params
 * available in the documentation.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
//        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper(
//            'viewRenderer')->view;
//        $view->headScript()->appendFile(
//            MAJISTIX_URL . '/editing/scripts/editors/ckeditor/ckeditor.js');
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
}
