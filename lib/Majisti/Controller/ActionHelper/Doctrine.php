<?php

namespace Majisti\Controller\ActionHelper;

/**
 * @desc Controller Helper that returns the Doctrine EntityManager
 *
 * @author Majisti
 */
class Doctrine extends \Zend_Controller_Action_Helper_Abstract
{
    /**
     * @desc Returns the registered Majisti ModelContainer
     *
     * @return \Majisti\ModelContainer
     */
    public function direct()
    {
        //FIXME: wrong coupling
        return \Zend_Registry::get('Doctrine_EntityManager');
    }
}

