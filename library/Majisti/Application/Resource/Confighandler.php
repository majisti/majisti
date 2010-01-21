<?php

namespace Majisti\Application\Resource;

/**
 * @desc ConfigHandler that basically reads up from configuration
 * a list of IHandler to apply on the global Majisti_Config that
 * is used by the entire application.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Confighandler extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var \Majisti\Config\Handler\CompositeHandler
     */
    protected $_compositeHandler;

    /**
     * @desc Handles a configuration by applying instances of
     * \Majisti\Config\Handler\IHandler on it using the
     * \Majisti\Config\Handler\Composite
     *
     * The handled configuration in put back in the Majisti_Config registry key
     *
     * @throws Exception if an exception occurs while handling the confuration
     * it will be thrown wrapped in this namespace's Exception.
     *
     * @return \Majisti\Config\Handler\Composite the composite handler.
     */
    public function init()
    {
        try {
            $compositeHandler = $this->_prepareComposite();
            
            \Zend_Registry::set('Majisti_Config',
                $compositeHandler->handle(\Zend_Registry::get('Majisti_Config')));
        } catch( \Exception $e ) {
            throw new Exception("An exception occured in ConfigHandler resource
            while trying to load configuration with exception message:
            {$e->getMessage()},
            thrown from {$e->getFile()},
            with stack trace:
            {$e->getTraceAsString()}");
        }

        return $compositeHandler;
    }

    /**
     * @desc Prepares the composite handler by pushing instances of
     * \Majisti\Config\Handler\IHandler to it.
     *
     * @return \Majisti\Config\Handler\CompositeHandler
     */
    protected function _prepareComposite()
    {
        if( null !== $this->_compositeHandler ) {
            return $this->_compositeHandler;
        }

        $compositeHandler = $this->getCompositeHandler();

        foreach ($this->getOptions() as $className => $value) {
            if( !empty($value) ) {
                /* denotes that the handler should be enabled */
                if( 1 == $value ) {
                    $class = 'Majisti\Config\Handler\\' . ucfirst($className);
                    $compositeHandler->push(new $class());
                /* the value is a class name */
                } else if( is_string($value) ) {
                    if( !class_exists($value) ) {
                        throw new Exception("Class {$value} does not exists");
                    }
                    $compositeHandler->push(new $value());
                /* handler parameters provided */
                } else if( is_array($value) ) {
                    /* class key for finding the propert class */
                    $className = isset($value['class'])
                        ? $value['class']
                        : 'Majisti\Config\Handler\\' . ucfirst($className);

                    unset($value['class']);
                    $compositeHandler->push(new $className($value));
                }
            }
        }

        $this->_compositeHandler = $compositeHandler;

        return $this->getCompositeHandler();
    }

    /**
     * @desc Lazily instanciates the Composite Handler
     *
     * @return \Majisti\Config\Handler\Composite
     */
    public function getCompositeHandler()
    {
        if( null === $this->_compositeHandler ) {
            $this->_compositeHandler = new \Majisti\Config\Handler\Composite();
        }

        return $this->_compositeHandler;
    }
}
