<?php

namespace Majisti;

/**
 * @desc The core view class adding more behaviour to the traditional
 * {@link \Zend_View} by providing the underscore function for traduction and
 * the support for multiple fallback directories for view scripts (according
 * to the standard dispatcher).
 *
 * @author Steven Rosato
 */
class View extends \Zend_View 
{
    /**
     * @desc Traduction function that proxies to the translate view helper.
     * 
     * @param string $messageId The message to translate
     * @param string|Zend_Locale $locale (Optional)
     * 
     * @return string The translated message, or this messageId if no translation was found
     */
    public function _($messageId, $locale = null)
    {
        if (null === $locale) {
            return $this->getHelper('Translate')->translate($messageId);
        } else {
            return $this->getHelper('Translate')->translate($messageId, $locale);
        }
    }
    
    /**
     * @desc Returns whether if a translator is registered with this view
     * @return boolean True if a translator is registered.
     */
    public function hasTranslator()
    {
        return \Zend_Registry::isRegistered('Zend_Translate');
    }
    
    /**
     * @desc Sets the translator
     * @param \Zend_Translate $translate The translator
     * @return View this
     */
    public function setTranslator(\Zend_Translate $translate)
    {
        \Zend_Registry::set('Zend_Translate', $translate);
        return $this;
    }
    
    /**
     * @desc Returns the translator
     * @return \Zend_Translate
     */
    public function getTranslator()
    {
        if( $this->hasTranslator() ) {
            return \Zend_Registry::get('Zend_Translate');
        }
        return null;
    }
    
    /**
     * @desc Fallbacks to the Dispatcher's controller directories whenever
     * a script is not found in the default controller directory. Provided that
     * the front controller's dispatcher is an instance of 
     * Majisti\Dispatcher\IDispatcher
     * 
     * @param $name The script name to find
     * @return string The script name
     */
    protected function _script($name)
    {
        try {
            return parent::_script($name);
        } catch( \Zend_View_Exception $e ) {
            $front      = \Zend_Controller_Front::getInstance();
            $dispatcher = $front->getDispatcher();
            $request    = $front->getRequest();

            /* fallback to the dispatcher's controller directories */
            if( null !== $request &&
                $dispatcher instanceof \Majisti\Controller\Dispatcher\IDispatcher ) {
                $fallbacks = $dispatcher->getFallbackControllerDirectory($request->getModuleName());
                
                if( null !== $fallbacks ) {
                    foreach ($fallbacks as $fallback) {
                        //FIXME: check if views/scripts should be the way to go
                        $script = realpath($fallback . '/../views/scripts/' . $name);
                        if( is_readable($script) ) {
                            return $script;
                        }
                    }
                }
            }
            
            throw $e;
        }
    }
}