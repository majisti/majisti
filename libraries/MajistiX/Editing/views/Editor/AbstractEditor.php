<?php

namespace MajistiX\Editing\View\Editor;

use MajistiX\Editing\Model\Content;

abstract class AbstractEditor implements IEditor
{
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
    public function getForm(Content $content)
    {
        $form = new \Majisti\Model\Form();
        $key  = $content->getName();
        $form->setName('majistix_editing_form_' . $key);
        $form->setAttrib('class', 'majistix-editing-form');

        /* insert textarea that will be changed to CkEditor */
        $textArea = new \Zend_Form_Element_Textarea($key);
        $textArea
            ->setValue($content->getContent())
        ;
        $form->addElement($textArea);

        /* hidden field for post recognition of the controller plugin */
        $hiddenField = new \Zend_Form_Element_Hidden(
            'majistix_editing_' . $key);
        $hiddenField->setValue('##MAJISTIX_EDITING##');
        $form->addElement($hiddenField);

        /* insert submit button */
        $btn_submit = new \Zend_Form_Element_Submit(
            'majistix_editing_submit_' . $key, 'Save'); //TODO: PO translator?
        $btn_submit->setAttrib('class', 'majistix-editing-submit-save');
        $form->addElement($btn_submit);

        /* cancel button */
        $btn_cancel = new \Zend_Form_Element_Submit(
            'majistix_editing_submit_cancel_' . $key, 'Cancel'); //TODO: PO translator?
        $btn_cancel->setAttrib('class', 'majistix-editing-submit-cancel');
        $form->addElement($btn_cancel);

        $form->setLayout(new \Majisti\Model\Form\Layout\Table());

        return $form;
    }
}
