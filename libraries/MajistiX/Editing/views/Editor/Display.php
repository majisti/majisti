<?php

namespace MajistiX\Editing\View\Editor;

use MajistiX\Editing\Model;

class Display
{
    protected $_acl; //TODO: acl

    protected $_auth; //TODO: auth

    /**
     * @var IEditor 
     */
    protected $_editor;

    /**
     * @var Content
     */
    protected $_content;

    /**
     * @var \Zend_View
     */
    protected $_view;

    /**
     * @var string
     */
    protected $_partial = 'majistix/editing/editor.phtml';

    public function __construct(Model\Content $content, IEditor $editor,
        \Zend_View $view)
    {
        $this->_content = $content;
        $this->_editor  = $editor;
        $this->_view    = $view;
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

    protected function getJavascript(Model\Content $content, IEditor $editor)
    {
        $options = \Zend_Json::encode($editor->getOptions());

        $js = <<<EOT
$(function() {
   majisti.ext.editing.createEditor('{$content->getName()}', {$options});
});
EOT;
        return $js;
    }

    public function setPartial($partial)
    {
        $this->_partial = $partial;
    }

    public function getPartial()
    {
        return $this->_partial;
    }

    public function render()
    {
        $editor  = $this->getEditor();
        $view    = $this->getView();
        $content = $this->getContent();

        if( !\Zend_Auth::getInstance()->hasIdentity() ) {
            return $content->getContent();
        }

        $view->inlineScript()->appendScript(
            $this->getJavascript($content, $editor));

        return $view->partial($this->getPartial(), array(
            'editor'  => $editor,
            'content' => $content,
        ));
    }
}