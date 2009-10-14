<?php

//namespace MajistiX\View\Helper;

//TODO: this will move in its actual package when plugin bootstraping will work

class MajistiX_View_Helper_InPlaceEditing extends \Majisti\View\Helper\HelperAbstract
{
    protected $_inPlaceEditingModel;
    
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
            $this->_inPlaceEditingModel = \MajistiX\Model\Editing\Factory::
                createInPlaceEditingModel($this->getConfig());
            
            //FIXME: temporary
            \Zend_Registry::set('Majisti_InPlaceEditing_Model', $this->_inPlaceEditingModel);
        }
        
        return $this->_inPlaceEditingModel;
    }
}