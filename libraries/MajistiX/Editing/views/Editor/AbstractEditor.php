<?php

namespace MajistiX\Editing\View\Editor;

use MajistiX\Editing\Model\Content;

abstract class AbstractEditor implements IEditor
{
    /**
     * @var \Zend_Form 
     */
    protected $_form;

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function preset($preset)
    {
        $rc = new \ReflectionClass($this);
        $preset = $rc->getNamespaceName() . '\Preset\\' . ucfirst((string)$preset);

        if( !class_exists($preset) ) {
            throw new Exception("Preset {$preset} does not exist.");
        }

        $preset = new $preset();
        $this->setOptions($preset->toArray());

        return $this;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function createForm(Content $content)
    {
        $form = new \Majisti\Model\Form();
        $key  = $content->getName();
        $form->setName('maj_editing_editor_' . $key);
        $form->setAttrib('class', 'editor');

        /* content */
        $textArea = new \Zend_Form_Element_Textarea($key);
        $textArea
            ->setValue($content->getContent())
        ;
        $form->addElement($textArea);

        /* hidden field for post recognition of the controller plugin */
        $hiddenField = new \Zend_Form_Element_Hidden(
            'maj_editing_editor_hidden_' . $key);
        $hiddenField->setValue('##MAJISTIX_EDITING##');
        $form->addElement($hiddenField);

        //TODO: render all buttons in a display group
        //http://zend-framework-community.634137.n4.nabble.com/Zend-Form-submit-and-reset-decorators-td647799.html

        /* submit button */
        $btn_submit = new \Zend_Form_Element_Submit(
            'maj_editing_editor_save_' . $key, 'Save'); //TODO: PO translator?
        $btn_submit->setAttrib('class', 'save');
        $form->addElement($btn_submit);

        /* cancel button */
        $btn_cancel = new \Zend_Form_Element_Reset(
            'maj_editing_editor_cancel_' . $key); //TODO: PO translator?
        $btn_cancel
            ->removeDecorator('label')
            ->setAttrib('class', 'cancel');
        $form->addElement($btn_cancel);

        $form->addDisplayGroup(array(
            'maj_editing_editor_hidden_' . $key,
            'maj_editing_editor_save_'   . $key,
            'maj_editing_editor_cancel_' . $key,
        ), 'buttons');
        $dg = $form->getDisplayGroup('buttons');
        $dg->removeDecorator('Fieldset');
        $dg->removeDecorator('HtmlTag');

        $form->setLayout(new \Majisti\Model\Form\Layout\Table());

        return $form;
    }
}
