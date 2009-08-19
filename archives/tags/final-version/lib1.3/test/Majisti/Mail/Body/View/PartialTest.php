<?php

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

class Majisti_Mail_Body_View_PartialTest extends Majisti_Test_PHPUnit_TestCase
{
	private $_partialTest;
	
	public function setUp()
	{
		$view = new Zend_View();
		$view->addScriptPath(dirname(__FILE__) . '/scripts');
		Zend_Registry::set('Zend_View', $view);
		
		$model = new Majisti_Object();
		$model->content = 'This is text outputed from model';
		$model->partial = 'sub-message.phtml';
		
		$this->_partialTest = new Majisti_Mail_Body_View_Partial('message.phtml', $model);
	}
	
	public function test__construct()
	{
		
	}
	
	public function testGetPartialName()
	{
		$this->assertEquals('message.phtml', $this->_partialTest->getPartialName());	
	}
	
	public function testGetBody()
	{
		$this->assertEquals('This is a test|This is text outputed from model|sub-message-text', $this->_partialTest->getBody());
	}
}

Majisti_Test_Runner::run('Majisti_Mail_Body_View_PartialTest');
