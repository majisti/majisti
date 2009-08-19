<?php
/**
 * Adds directly the 'underscore' method to the view
 * adding the possibility to translate with the helper.
 * 
 * TODO: doc
 *
 * @author Steven Rosato
 */
class Majisti_View extends Zend_View 
{
    /**
     * Traduction function
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
    
    public function hasTranslator()
    {
    	return Zend_Registry::isRegistered('Zend_Translate');
    }
    
    public function setTranslator($translate)
    {
    	Zend_Registry::set('Zend_Translate', $translate);
    }
    
    public function getTranslator()
    {
    	if( $this->hasTranslator() ) {
    		return Zend_Registry::get('Zend_Translate');
    	}
    	return null;
    }
}