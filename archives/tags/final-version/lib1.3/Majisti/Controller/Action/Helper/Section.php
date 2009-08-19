<?php

/**
 * @desc This helper checks if the current passed section is the same as the one stored
 * in session. If they differ, an automatic redirection will be triggered to the
 * correct section.
 * 
 * TODO: implement class
 * 
 * @author Steven Rosato
 * 
 * @param string $currentSection
 */
class Majisti_Controller_Action_Helper_Section extends Zend_Controller_Action_Helper_Abstract
{
	public function direct($currentSection, $defaultSection = 'default', $sessionNamespace = 'section', $sessionKey = 'current', $enabled = 'true')
	{
		throw new Majisti_Controller_Action_Helper_Exception('Helper not implemented yet!');
		
//		$this->checkSection($currentSection, $defaultSection, $sessionNamespace, $sessionKey, $enabled);
	}
	
	private function _checkSection($currentSection, $defaultSection = 'default', $sessionNamespace = 'section', $sessionKey = 'current', $enabled = 'true')
	{
		if( $enabled ) {
			$section = new Zend_Session_Namespace($sessionNamespace);
			
			$redirectionLink = 'Location: ' . Zend_Registry::get('config')->link->site . '/' . 
						Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . $defaultSection;
			
			if( !isset($section->$sessionKey) ) {
				$section->$sessionKey = $defaultSection;
				
				if( $currentSection != $section->$sessionKey ) {
					header($redirectionLink);
					exit;
				}
			} else if( $section->sessionKey != $currentSection ) {
				header($redirectionLink);
				exit;
			}
		}
	}
	
	/**
	 * Sets the current passed session namespace's Section and redirect if given parameter is true.
	 *
	 */
	private function _setSection($sectionName, $redirect = false, $sessionNamespace = 'section', $sessionKey = 'current')
	{
		$section = new Zend_Session_Namespace($sessionNamespace);
		$section->$sessionKey = $sectionName;
		
		if( $redirect ) {
			header('Location: ' . Zend_Registry::get('config')->link->site . '/' . 
						Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . $sectionName); 
			exit;
		}
	}
}
