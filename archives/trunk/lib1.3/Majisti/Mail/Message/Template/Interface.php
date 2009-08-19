<?php

interface Majisti_Mail_Message_Template_Interface
{
	public function getSubject();
	public function setSubject();
	
	public function getMessage();
	public function setMessage();
}
