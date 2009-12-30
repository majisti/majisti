<?php

//namespace MajistiX\View\Helper;
//TODO: this will move in its actual namespace when plugin bootstraping will work

/**
 * @desc InPlaceEditing view helper. Renders the default in place content editor
 * setup in the configuration. Check documentation for more information on how
 * to configure a in place editor.
 *
 * @author Steven Rosato
 */
class MajistiX_View_Helper_InPlaceEditing extends \Majisti\View\Helper\HelperAbstract
{
    protected $_inPlaceEditingModel;

    /**
     * @desc Renders content based on storage key.
     *
     * @param $key The storage key
     * @param $options the options
     */
    public function inPlaceEditing($key, $options = array())
    {
        return $this->getModel()->render($key);
    }

    /**
     * @return \MajistiX\Model\Editing\InPlace
     */
    protected function getModel()
    {
        if( null === $this->_inPlaceEditingModel ) {
            $this->_inPlaceEditingModel = \MajistiX\Extensions\InPlaceEditing\
                Model\Factory::createInPlaceEditingModel($this->getConfig());

            $container = Zend_Registry::get('Majisti_ModelContainer');
            $container->addModel('model', $this->_inPlaceEditingModel, 'MajistiX_InPlaceEditing');
        }

        return $this->_inPlaceEditingModel;
    }
}