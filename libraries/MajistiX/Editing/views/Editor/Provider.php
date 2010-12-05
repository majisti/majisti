<?php

namespace MajistiX\Extension\Editing\View\Editor;

class Provider
{
    static protected $_instance;

    protected $_editorType;

    protected $_editorsUrl;

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

    public function getEditorsUrl()
    {
        return $this->_editorsUrl;
    }

    /**
     *
     * @param <type> $url
     * @return Provider
     */
    public function setEditorsUrl($url)
    {
        $this->_editorsUrl = $url;

        return $this;
    }

    public function getEditorType()
    {
        return $this->_editorType;
    }

    /**
     *
     * @param <type> $editor
     * @return Provider
     */
    public function setEditorType($editor)
    {
        $this->_editorType = $editor;

        return $this;
    }

    /**
     *
     * @param <type> $options
     * @return Provider
     */
    public function preloadEditor($options = array())
    {
        $editor = __NAMESPACE__ . '\\' . $this->_editorType . '\Renderer';

        $options['view'] = $this->getView();
        $options['url']  = $this->getEditorsUrl();

        $editor::preload($options);

        return $this;
    }

    /**
     *
     * @return IEditor The editor
     */
    public function provideEditor($options = array())
    {
        $editor = __NAMESPACE__ . '\\' . $this->_editorType . '\Renderer';
        $editor = new $editor();
        $editor->setOptions($options);
        $editor->setView($this->getView());

        return $editor;
    }
}