<?php

namespace MajistiX\Extension\Editing\View\Editor;

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

    protected $_view;

    /**
     * @desc Constructs the CkEditor by applying the javascript file needed
     * for it to work.
     */
    public function __construct(\Zend_View $view, $editorsUrl)
    {
        $this->_view = $view;
        $view->headScript()->appendFile($editorsUrl . '/ckeditor/ckeditor.js');
    }

    public function getView()
    {
        return $this->_view;
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
        $form = new \Majisti\Model\Form();
        $form->setName('majistix_editing_form' . $this->_formCounter);

        //TODO: verify $params['key']
        /* insert textarea that will be changed to CkEditor */
        $textArea = new \Zend_Form_Element_Textarea($params['key']);
        $textArea
            ->setOptions(array('class' => 'ckeditor'))
            ->setValue($content)
        ;
        $form->addElement($textArea);

        /* hidden field for post recognition of the controller plugin */
        $hiddenField = new \Zend_Form_Element_Hidden(
            'majistix_editing' . $this->_formCounter);
        $hiddenField->setValue('##MAJISTIX_EDITING##');
        $form->addElement($hiddenField);

        /* insert submit button */
        $btn_submit = new \Zend_Form_Element_Submit(
            'majistix_editing_submit' . $this->_formCounter, 'Save'); //TODO: PO translator?
        $form->addElement($btn_submit);

        $form->setLayout(new \Majisti\Model\Form\Layout\Table());

        $this->_formCounter++;

        return $form->render();
    }
}
