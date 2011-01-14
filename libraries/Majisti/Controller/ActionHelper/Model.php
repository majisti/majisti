<?php

namespace Majisti\Controller\ActionHelper;

/**
 * @desc Controller Helper that returns Majisti's ModelContainer
 *
 * @author Majisti
 */
class Model extends \Zend_Controller_Action_Helper_Abstract
{
    public function __call($methodName, $args)
    {
        return call_user_func_array(array($this->direct(), $methodName), $args);
    }

    /**
     * @desc Returns the registered Majisti ModelContainer
     *
     * @return \Majisti\ModelContainer
     */
    public function direct()
    {
        //FIXME: wrong coupling
        return \Zend_Registry::get('Majisti_ModelContainer');
    }
}
