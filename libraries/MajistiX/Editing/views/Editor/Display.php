<?php

namespace MajistiX\Editing\View\Editor;

use MajistiX\Editing\Model\Content;

class Display
{
    const MODE_DISPLAY_THEN_EDIT = 1;
    const MODE_DISPLAY           = 2;
    const MODE_EDIT              = 3;

    const SCREEN_DEFAULT         = 1;

    protected $_mode = self::MODE_DISPLAY_THEN_EDIT;

    protected $_ajaxEnabled = true;

    protected $_acl; //TODO: acl

    protected $_auth; //TODO: auth

    /**
     * @var IEditor 
     */
    protected $_editor;

    protected $_content;

    protected $_view;

    public function __construct(Content $content, IEditor $editor,
        \Zend_View $view)
    {
        $this->_content = $content;
        $this->_editor  = $editor;
        $this->_view    = $view;
    }

    public function setAjaxEnabled($flag)
    {
        $this->_ajaxEnabled = (bool) $flag;
    }

    public function isAjaxEnabled()
    {
        return $this->_ajaxEnabled;
    }

    public function getMode()
    {
        return $this->_mode;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function getView()
    {
        return $this->_view;
    }

    public function getEditor()
    {
        return $this->_editor;
    }

    public function getEditForm($key)
    {
        $form   = new \Majisti\Model\Form();
        $form->setAttrib('class', 'majistix-editing-form');
        $form->setName('majistix_editing_form_edit_' . $key);

        $submit = new \Zend_Form_Element_Submit('majistix_editing_submit_edit_' . $key, 'Edit');
        $submit->setAttrib('class', 'majistix-editing-submit-edit');
        $form->addElement($submit);

        $form->setLayout(new \Majisti\Model\Form\Layout\Table());

        return $form;
    }

    public function render()
    {
        $editor  = $this->getEditor();
        $view    = $this->getView();
        $content = $this->getContent();

        $key = $content->getName();

        $editForm = $this->getEditForm($key);
        $activationJs = $editor->getActivationJavascript($key);

        $editorForm = $editor->getForm($content);
        $editorForm->setAttrib('style', 'display: none;');

        $js = <<<EOT
$(function() {
    $("#majistix_editing_form_edit_{$key}").submit(function() {
        $("#{$editForm->getName()}").hide();
        {$activationJs}
        $("#{$editorForm->getName()}").show();
        return false;
    });
});
EOT;
        $view->inlineScript()->appendScript($js);

        return '<div class="majistix-editing-content-container">' .
                $editForm->render() .
                   '<div class="majisti-editing-content-text">' .
                       $content->getContent() .
                   '</div>' .
               '</div>' . $editorForm->render();

//        $view->inlineScript()->appendScript($editor->getActivationJavascript(
//            $content->getName()));

//        return $editor->getForm($content)->render();
    }
}