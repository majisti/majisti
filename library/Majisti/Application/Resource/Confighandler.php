<?php

namespace Majisti\Application\Resource;

/**
 * @desc ConfigHandler that basically reads up from configuration
 * a list of IHandler to apply on the global Majisti_Config that
 * is used by the entire application.
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ConfigHandler extends \Zend_Application_Resource_ResourceAbstract
{
    protected $_compositeHandler;

    /**
     * @desc Handles a configuration by applying instances of
     * Config\Handler\IHandler on it using the Config\Handler\Composite
     * 
     * To enable config handlers here are some examples for an ini configuration:
     * 
     * configHandler.property = 1 //Enables Majisti Property Handler
     * configHander.markup = "MyProject\Config\Handler\Markup" //specific handler
     * 
     * Note that setting 0 as value will skip the instanciation of that config handler
     * 
     * The handled configuration in put back in the Majisti_Config registry key
     * 
     * @return \Majisti\Config\Handler\Composite the composite handler.
     */
    public function init()
    {
        $compositeHandler = $this->_prepareComposite();
        
        \Zend_Registry::set('Majisti_Config', 
            $compositeHandler->handle(\Zend_Registry::get('Majisti_Config'))); 
            
        return $compositeHandler;
    }
    
    /**
     * @desc Prepares the composite handler by pushing instances of
     * \Majisti\Config\Handler\IHandler to it
     * @return \Majisti\Config\Handler\CompositeHandler
     * TODO: cleanup this before release, hard to understand for other programmers
     */
    protected function _prepareComposite()
    {
        if( null === $this->_compositeHandler ) {
            $compositeHandler = $this->getCompositeHandler();
            
            foreach ($this->getOptions() as $className => $enabled) {
                if( $enabled ) {
                    if( 1 == $enabled ) {
                        $class = 'Majisti\Config\Handler\\' . ucfirst($className);
                        $compositeHandler->push(new $class());
                    } else if( is_string($enabled) ) {
                        $compositeHandler->push(new $enabled());
                    } else if( is_array($enabled) ) {
                        $className = isset($enabled['class'])
                            ? $enabled['class']
                            : 'Majisti\Config\Handler\\' . ucfirst($className);
                        $values = array();
                        foreach ($enabled as $k => $v) {
                            $values[] = 1 == $v
                                ? $k
                                : $v;
                        }
                        $compositeHandler->push(new $className($values));
                    }
                }
            }
            
            $this->_compositeHandler = $compositeHandler;
        }
        
        return $this->getCompositeHandler();
    }
    
    /**
     * @desc Lazily instanciates the Composite Handler
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
