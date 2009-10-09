<?php

/**
 * This Auth controller handles everything related to members/admin loggin access.
 * It will process both login and logout actions.
 * 
 * @author Steven Rosato
 */
class AuthController extends Majisti_Controller_Auth_DbTable
{
	/**
	 * Redirects to the login view if the user isn't connected
	 */
	public function indexAction()
	{
		$this->_forward('login');
	}
	
	protected function _doLogin()
	{
		$this->_login(array(
			'login' 			=> $this->_getParam('login'),
			'pass' 				=> $this->_getParam('pass'),
			
			'table' 			=> Zend_Registry::get('config')->database->tables->users,
			'identity' 			=> 'login',
			'credential' 		=> 'password',
			'columns' 			=> array('id', 'login'),
			'omittedColumns' 	=> 'pass'
		));
		$this->_failed();
	}
	
	public function loginAction()
	{
		if( $this->_request->isPost() ) {
			$this->_doLogin();
		}
	}
	
	public function successed()
	{
		/* TODO: use Majisti_User::getInstance to persist addtionnal user data */
		parent::successed();
	}
	
	protected function _failed()
	{
		$this->view->message = $this->view->_('Either the login or password are wrong.');
	}

	public function logoutAction()
	{
		$this->_logout();
	}
}