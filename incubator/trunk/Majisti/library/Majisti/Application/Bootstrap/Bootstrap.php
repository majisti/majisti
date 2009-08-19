<?php

namespace Majisti\Application\Bootstrap;

/**
 * @desc Majisti's application boostrap.
 * 
 * @author Steven Rosato
 * @version 1.0
 */
class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap 
{
    /**
     * @desc Constructs the boostrap class and calls the postConstruct()
     * method after instanciation.
     * 
     * @param $application The Majisti's application
     */
    public function __construct($application)
    {
        parent::__construct($application);
        $this->_postConstruct();
    }
    
    /**
     * @desc Anything related after the construction of the bootstrap class
     */
    protected function _postConstruct() {}
}
