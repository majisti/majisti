<?php

namespace MajistiX\View\Helper;

/**
 * @desc InPlaceEditing view helper. Renders the default in place content editor
 * setup in the configuration. Check documentation for more information on how
 * to configure a in place editor.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class InPlaceEditing extends \Majisti\View\Helper\AbstractHelper
{
    protected $_inPlaceEditingModel;

    /**
     * @desc Renders content based on storage key.
     *
     * @param $key The storage key
     * @param $options the options
     */
    public function helper($key, $options = array())
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