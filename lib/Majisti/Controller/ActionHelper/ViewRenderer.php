<?php

namespace Majisti\Controller\ActionHelper;

/**
 * @desc The ViewRenderer makes sure that it integrates with namespaec
 * action helpers.
 *
 * @author Majisti
 */
class ViewRenderer extends \Zend_Controller_Action_Helper_ViewRenderer
{
    protected $_appNamespace = '';

    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    protected function _setOptions(array $options)
    {
        if( isset($options['appnamespace']) ) {
            $this->_appNamespace = $options['appnamespace'];
        }

        return parent::_setOptions($options);
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    protected function _generateDefaultPrefix()
    {
        return $this->_appNamespace . '\\' . parent::_generateDefaultPrefix();
    }
}
