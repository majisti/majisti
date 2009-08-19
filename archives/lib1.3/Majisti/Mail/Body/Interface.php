<?php

interface Majisti_Mail_Body_Interface
{
	const TYPE_HTML = 0;
	const TYPE_TEXT = 1;
	
	public function getBody();
	public function getBodyType();
}