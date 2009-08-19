<?php

/**
 * TODO: retrieve abstraction from Majist_Mail_Smtp_Bcc
 * TOOD: Complete class. NOT STABLE YET!
 * 
 * @author Steven Rosato
 */
class Majisti_Mail extends Zend_Mail 
{
	protected $_mailPrepared = false;
	protected $_maxMailsAtOnce;
	
	protected $_bodyObject;
	
	public function __construct($charset = 'iso-8859-1', array $options = array())
	{
		parent::__construct();
		
		$this->_maxMailsAtOnce = isset($options['max_send']) ? $options['max_send'] : 99;
	}
	
	public function setMaxMailsAtOnce($maxMailsAtOnce)
	{
		$this->_maxMailsAtOnce = $maxMailsAtOnce;	
	}
	
	public function getMaxMailsAtOnce()
	{
		return $this->_maxMailsAtOnce;	
	}
	
	public function addBccRecipients($recipients)
	{	
		foreach ($recipients as $recipient) {
			$this->addBcc((string)$recipient);
		}
	}
	
	protected function _clearBccRecipients()
	{
		$this->_recipients = array();
	}
	
	public function prepareMail($subject, $body, $html = false)
	{
		if( !$this->_mailPrepared ) {
			$this->setSubject($subject);
			if( $html ) {
				$this->setBodyHtml($body);
			} else {
				$this->setBodyText($body);
			}
		}
		$this->_mailPrepared = true;
		
		return $this;
	}
	
	public function sendTo($recipients = null, $splitRecipients = true)
	{
		if( !$this->_mailPrepared ) {
			throw new Zend_Mail_Exception('Mail was not prepared.');
		}
		
		$this->addTo($this->getFrom());
		
		if( $recipients != null ) {
			if( $splitRecipients ) {
				$recipients = $this->splitRecipients($recipients);
				
				if( count($recipients) == 0 ) {
					throw new Zend_Mail_Exception('No recipients found in the array.');
				}
				
				for ($i = 0 ; $i < count($recipients) ; $i++) {
					$this->clearBccRecipients();
					$this->addBccRecipients($recipients[$i]);
					$this->send();
				}
			} else {
				$this->addBccRecipients($recipients);
				if( count($recipients) == 0 ) {
					throw new Zend_Mail_Exception('No recipients found in the array.');
				}
				$this->send();
			}
		}
	}
	
	protected function _splitRecipients($recipients)
	{
		$splittedRecipients = array();
		$currentRecipientIndex = 0;
		for ($i = 0 ; $i < count($recipients) / $this->_maxMailsAtOnce ; $i++) {
			$splittedRecipients[$i] = array_slice($recipients, $currentRecipientIndex, $this->_maxMailsAtOnce, false);
			$currentRecipientIndex += $this->_maxMailsAtOnce;
		}
		return $splittedRecipients;
	}
	
	public function setBodyObject(Majisti_Mail_Body_Interface $body)
	{
		$this->_bodyObject = $body;
	}
	
	public function getBodyObject()
	{
		return $this->_bodyObject;
	}
	
	public function send($transport = null)
	{
		if( ($bodyObject = $this->getBodyObject()) !== null ) {
			if( $bodyObject->getBodyType() == Majisti_Mail_Body_Interface::TYPE_HTML ) {
				$this->setBodyHtml($bodyObject->getBody());
			} else if( $bodyObject->getBodyType() == Majisti_Mail_Body_Interface::TYPE_TEXT ) {
				$this->setBodyText($bodyObject->getBody());
			}
		}
		parent::send($transport);
	}
}