<?php

/**
 * @desc Controller Helper that returns Majisti's ModelContainer
 *
 * @author Majisti
 */
class Majisti_Controller_ActionHelper_Model extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @desc Returns the registered Majisti ModelContainer
     *
     * @return \Majisti\ModelContainer
     */
    public function direct()
    {
        return Zend_Registry::get('Majisti_ModelContainer');
    }
}
