<?php

namespace MajistiX\Editing\View\Editor;

use \MajistiX\Editing\Model\Content;

class PanelFactory
{
    protected $_key;

    public function __construct($key)
    {
        $this->_key = $key;
    }

    public function getKey()
    {
        return $this->_key;
    }

    /**
     *
     * @return \Majisti\Model\Form The panel
     */
    public function createEditPanel()
    {
        $form = new \Majisti\Model\Form();
        $key  = $this->getKey();

        $submit = new \Zend_Form_Element_Submit(
            'maj_editing_edit_' . $key, 'Edit');
        $submit->setAttrib('class', 'maj-editing-editor-panel-edit'
            . ' maj-editing-buttons');
        $form->addElement($submit);

        $form->setLayout(new \Majisti\Model\Form\Layout\Table());
        $form->setAttrib('class', 'maj-editing-editor-panel');
        $form->setName('maj_editing_editor_panel_edit_' . $key);

        return $form;
    }
}
