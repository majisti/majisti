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

    protected $_panelFactory;

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

    public function setPanelFactory(PanelFactory $panelFactory)
    {
        $this->_panelFactory = $panelFactory;
    }

    public function getPanelFactory()
    {
        return $this->_panelFactory;
    }

    public function render()
    {
        $editor  = $this->getEditor();
        $view    = $this->getView();
        $content = $this->getContent();

        if( null === ($panelFactory = $this->getPanelFactory()) ) {
            $panelFactory = new PanelFactory($content->getName());
            $this->setPanelFactory($panelFactory);
        }

        $view->inlineScript()->appendScript(
            $this->getJavascript($content, $editor));

        return $view->partial('majistix/editing/editor.phtml', array(
            'panel'   => $panelFactory->createEditPanel(),
            'editor'  => $editor,
            'content' => $content,
        ));
    }
}