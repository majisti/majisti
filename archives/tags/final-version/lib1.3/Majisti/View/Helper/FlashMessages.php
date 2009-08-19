<?php

/**
 * @version 1.2
 * @author Steven Rosato, Yanick Rochon
 */
class Majisti_View_Helper_FlashMessages extends Zend_View_Helper_Abstract 
{
	/**
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	private $_session;
	
	public function __construct() {
		$this->_session = new Zend_Session_Namespace('FlashMessages', true);
		
		if ( !isset($this->_session->messages) ) {
			$this->_session->messages = array();			
		}
	}

	/**
	 * 
	 * @return Majisti_View_Helper_FlashMessages|string
	 * @param string $message[optional]
	 * @param array $options[optional]
	 */
	public function flashMessages($message = null, $options = array())
	{
		if ( null !== $message ) {
			if (is_string($options)) {
				$options = array(
					'state' => $options
				);	
			}
			
			$this->_session->messages[] = array('message' => $message, 'options' => $options);
		}
		
		return $this;
	}
	
	
	public function __toString() {
		return $this->toString();
	}
	
	public function toString() 
	{
		$html = '';
		
		if( count($this->_session->messages) ) {
			$html .= '<div class="flash-messages"><ul class="flash-messages-wrapper">';
			
			foreach ($this->_session->messages as $message) {
				$class = array('flash-messages-item');
				if ( isset($message['options']['state']) ) {
					$class[] = 'item-state-' . $message['options']['state'];	
				} else {
					$class[] = 'item-state-green';	
				}
				
				$html .= '<li class="' . implode(" ", $class) . '">' . $message['message'] . '</li>';
			}
			
			$html .= '</ul></div>';	
		}
		
		$this->_session->messages = array();
		
		return $html;
	}
	
}