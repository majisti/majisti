<?php

namespace MajistiX\Extension\Editing\View\Editor\CkEditor;

use MajistiX\Extension\Editing\Model\Content,
    MajistiX\Extension\Editing\View\Editor\IEditor;

/**
 * @desc Editor implementation of the very popular CkEditor.
 * Configuration of the editor is issued through of list of params
 * available in the documentation.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Renderer implements IEditor
{
    protected $_view;

    protected $_options = array();

    static protected $_preloaded = false;

    /**
     * @desc Constructs the CkEditor by applying the javascript file needed
     * for it to work.
     */
    public function __construct(\Zend_View $view = null)
    {
        if( null !== $view ) {
            $this->setView($view);
        }
    }

    static public function preload(array $options)
    {
        if( !static::$_preloaded ) {
            $view = $options['view'];
            $url  = $options['url'];

            $view->headScript()->appendFile($url . '/ckeditor/ckeditor.js');
            $view->headScript()->appendFile($url . '/ckeditor/adapters/jquery.js');

            static::$_preloaded = true;
        }
    }

    public function getView()
    {
        return $this->_view;
    }

    public function setView(\Zend_View $view)
    {
        $this->_view = $view;
    }

    public function preset($preset)
    {
        $preset = __NAMESPACE__ . '\Preset\\' . ucfirst((string)$preset);

        if( !class_exists($preset) ) {
            throw new Exception("Preset {$preset} does not exist.");
        }

        $preset = new $preset();
        $this->setOptions($preset->toArray());

        return $this;
    }

    public function setOptions(array $options)
    {
        $this->_options = $options;

        return $this;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @desc Renders a CkEditor.
     *
     * @param $content The content to render in the CkEditor
     */
    public function render(Content $content)
    {
        $form = new \Majisti\Model\Form();
        $key  = $content->getName();
        $form->setName('majistix_editing_form_' . $key);

        /* insert textarea that will be changed to CkEditor */
        $textArea = new \Zend_Form_Element_Textarea($key);
        $textArea
            ->setValue($content->getContent())
        ;
        $form->addElement($textArea);

        //FIXME: jquery wrapper will make the save button fail! (ckeditor bug)
        if( $this->_view->jQuery()->isEnabled() ) {
            $this->applyJs($key);
        } else {
            $textArea->setAttrib('class', 'ckeditor');
        }

        /* hidden field for post recognition of the controller plugin */
        $hiddenField = new \Zend_Form_Element_Hidden(
            'majistix_editing_' . $key);
        $hiddenField->setValue('##MAJISTIX_EDITING##');
        $form->addElement($hiddenField);

        /* insert submit button */
        $btn_submit = new \Zend_Form_Element_Submit(
            'majistix_editing_submit_' . $key, 'Save'); //TODO: PO translator?
        $form->addElement($btn_submit);

        $form->setLayout(new \Majisti\Model\Form\Layout\Table());

        return $form->render();
    }

    protected function applyJs($key)
    {
        $options = \Zend_Json::encode($this->getOptions());
        $js = <<<EOT
$(function() {
    $("#{$key}").ckeditor($options);
});
EOT;
        $this->_view->headScript()->appendScript($js);
    }
}
