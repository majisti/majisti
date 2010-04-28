<?php
namespace Majisti\Config\Handler;
/**
 * @desc Mock class used for testing purposes.
 * The handle function only triggers a boolean flag to indicate that
 * it truely was called.
 *
 * @author Majisti
 */
class Mock implements IHandler
{
    /**
     * @desc Boolean flag
     * @var boolean
     */
    private $_wasHandled = false;

    /**
     * @desc This handle method is good for nothing else than setting the
     * boolean flag to true once it has been called. Used in unit tests.
     *
     * @return empty \Zend_Config object
     */
    public function handle(\Zend_Config $config)
    {
        $this->_wasHandled = true;
        return new \Zend_Config(array());
    }

    /**
     * @desc If the mock object has been handled, returns true. False otherwise.
     *
     * @return whether the handler was handled.
     */
    public function hasBeenHandled()
    {
        return $this->_wasHandled;
    }
}
?>
