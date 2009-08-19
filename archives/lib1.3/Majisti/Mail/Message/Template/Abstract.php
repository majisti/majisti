<?php

class Majisti_Mail_Message_Template_Abstract extends Majisti_Mail
{
	/** @var Majisti_Mail_Message_Template_Interface */
	private $_messageTemplate;
	
	/** @var Majisti_View */
	private $_view;
	
	public function __construct(Majisti_Mail_Message_Template_Interface $messageTemplate)
	{
		$this->_messageTemplate = $messageTemplate;	
	}
	
	public function getSubject()
	{
		if( parent::getSubject() == '' ) {
			return $this->_messageTemplate->getSubject();	
		}
		return parent::getSubject();
	}
	
	/**
	 * @return The message template 
	 */
	public function getMessageTemplate()
	{
		return $this->_messageTemplate;
	}
	
	public function setView(Majisti_View $view)
	{
		
	}
	
	/**
	 * 
	 * @return Majisti_View 
	 */
	public function getView()
	{
		
	}
	
	/**
	 * Automatically sets the subject and the body html and send the message template
	 * 
	 * @link {Majisti_Mail::send()}
	 */
	public function send()
	{
		$this->setSubject($this->getSubject());
		$this->setBodyHtml($this->getMessage() . $this->_getFooter());
		
		parent::send();
	}
}