<?php

namespace MajistiX\Editing\View\Editor;

use MajistiX\Editing\Model\Content;

class Provider
{
    static protected $_instance;

    protected $_view;

    protected function __construct()
    {}

    /**
     *
     * @return Provider
     */
    static public function getInstance()
    {
        if( null === static::$_instance ) {
            static::$_instance = new self();
        }

        return static::$_instance;
    }

    public function getView()
    {
        return $this->_view;
    }

    /**
     *
     * @param \Zend_View $view
     * @return Provider
     */
    public function setView(\Zend_View $view)
    {
        $this->_view = $view;

        return $this;
    }

    public function setAjaxEnabled($flag)
    {

    }

    public function isAjaxEnabled()
    {

    }

    /**
     *
     * @param <type> $editor
     * @return Provider
     */
    public function setEditor($editor, $options = null)
    {
        if( !($editor instanceof IEditor) ) {
            $editor = __NAMESPACE__ . '\\' . (string) $editor . '\Renderer';
        }

        $editor = new $editor();

        $this->loadPublicFiles($editor, $options);

        $this->_editor = $editor;

        return $this;
    }

    protected function loadPublicFiles($editor, $options = null)
    {
        $view = $this->getView();
        $files = $editor->getPublicFiles($options);

        if( $files->has('scripts') ) {
            foreach( $files->find('scripts') as $jsFile ) {
                $view->headScript()->appendFile($jsFile);
            }
        }
    }

    /**
     *
     * @return IEditor The editor
     */
    public function getEditor($options = array())
    {
        return $this->_editor;
    }

    public function createEditorDisplay(Content $model, $options = array())
    {
        $editor = $this->getEditor();

        if( is_string($options) ) {
            $editor->preset($options);
        } else {
            $editor->setOptions($options);
        }

        return new Display($model, $editor, $this->getView());
    }
}