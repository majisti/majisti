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
     * @var \Majisti\Config\Handler\Chain
     */
    protected $_chainHandler;

    /**
     * @desc Handles a configuration by applying instances of
     * \Majisti\Config\Handler\IHandler on it using the
     * \Majisti\Config\Handler\Chain
     *
     * @throws Exception if an exception occurs while handling the confuration
     * it will be thrown wrapped in this namespace's Exception.
     *
     * @return \Majisti\Config\Handler\Chain the composite handler.
     */
    public function init()
    {
        try {
            $chainHandler = $this->prepareChain();
            $bootstrap    = $this->getBootstrap();

            $bootstrap->setOptions($chainHandler->handle(
                new \Zend_Config(
                    $bootstrap->getOptions(),
                    true
                ))->toArray()
            );
        } catch( \Exception $e ) {
            throw new Exception("An exception occured in ConfigHandler resource
            while trying to load configuration with exception message:
            {$e->getMessage()},
            thrown from {$e->getFile()},
            with stack trace:
            {$e->getTraceAsString()}");
        }

        return $chainHandler;
    }

    /**
     * @desc Prepares the composite handler by pushing instances of
     * \Majisti\Config\Handler\IHandler to it.
     *
     * @return \Majisti\Config\Handler\Chain
     */
    protected function prepareChain()
    {
        if( null !== $this->_chainHandler ) {
            return $this->_chainHandler;
        }

        $chainHandler = $this->getChainHandler();

        foreach (array_reverse($this->getOptions()) as $className => $value) {
            if( !empty($value) ) {
                /* denotes that the handler should be enabled */
                if( 1 == $value ) {
                    $class = 'Majisti\Config\Handler\\' . ucfirst($className);
                    $chainHandler->push(new $class());
                /* the value is a class name */
                } else if( is_string($value) ) {
                    if( !class_exists($value) ) {
                        throw new Exception("Class {$value} does not exists");
                    }
                    $chainHandler->push(new $value());
                /* handler parameters provided */
                } else if( is_array($value) ) {
                    /* class key for finding the property class */
                    $className = isset($value['class'])
                        ? $value['class']
                        : 'Majisti\Config\Handler\\' . ucfirst($className);

                    unset($value['class']);
                    $chainHandler->push(new $className($value));
                }
            }
        }

        $this->_chainHandler = $chainHandler;

        return $this->getChainHandler();
    }

    /**
     * @desc Lazily instanciates the Chain Handler
     *
     * @return \Majisti\Config\Handler\Chain
     */
    public function getChainHandler()
    {
        if( null === $this->_chainHandler ) {
            $this->_chainHandler = new \Majisti\Config\Handler\Chain();
        }

        return $this->_chainHandler;
    }
}
