<?php

/**
 * TODO: doc
 * TODO: gets the abtraction of this class out for an Majisti_Controller_Auth_Abstract so we will
 * be able to support more than just DbTable
 * 
 * @author Steven Rosato
 */
abstract class Majisti_Controller_Auth_DbTable extends Zend_Controller_Action
{
	private $_useBanningMecanism = false;
	
	private $_authCode;
	
	private function _cleanExpiredBannedUsers()
	{
		//TODO:
	}
	
	private function _cleanLoginAttemps()
	{
		//TODO:
	}
	
	private function _applyLoginPenalty()
	{
		//TODO:
	}
	
	private function _isBanned()
	{
		//TODO:
		return false;
	}
	
	/**
	 * @desc This is the login view.
	 * It uses Zend_Auth to check the users table for login.
	 * If validation is successfull it will store the user's information
	 * in Zend_Auth singleton class. Access can be retrieved by the view helpers
	 * isUserLogged() and getUser()
	 * 
	 * @see $this->view->getUser(), $this->view->isUserLogged()
	 */
	protected function _login(array $options)
	{
		$this->_cleanExpiredBannedUsers();
		
		$treatment = isset($options['treatment']) ? $options['treatment'] : null;
		
		$username = $options['login'];
		$password = $options['pass'];
		
		if( !empty($username) && !empty($password) ) { //both fields must be filled
			/*
			 * retrieve default adapter to set the users table with its user and password
			 * field for identity and credential
			 */
			$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
			$authAdapter->setTableName($options['table']);
			$authAdapter->setIdentityColumn($options['identity']);
			$authAdapter->setCredentialColumn($options['credential']);

			$authAdapter->setIdentity($username);
			$authAdapter->setCredential($password);
			
			if( $treatment != null ) {
				$authAdapter->setCredentialTreatment($treatment);
			}

			$auth = Zend_Auth::getInstance();
			
			$result = $auth->authenticate($authAdapter);
			
			$this->_authCode = $result->getCode();
			
			/* Failure */
			if ( $this->_authCode <= 0 ) {
				$this->_applyLoginPenalty();
				$this->_failed();
			/* Login successfull only if the user wasn't banned by the Majisti_User_Manager_Banner class */
			} elseif( $this->_authCode == Zend_Auth_Result::SUCCESS && !$this->_isBanned() ) {
				$data = $authAdapter->getResultRowObject($options['columns'] , $options['omittedColumns']);
				if( $data ) {
					$userData = Majisti_User::getInstance();
					$userData->setDefaultData($data);
					$this->_cleanLoginAttemps();
					$auth->getStorage()->write($userData);
					$this->_successed();
				}
				$this->_failed();
			} else { /* user is banned */
				$auth->clearIdentity();
				$this->banned();
			}
		}
		$this->_failed();
	}
	
	public function getAuthentificationCode()
	{
		return $this->_authCode;
	}
	
	public function ajaxLoginAction()
	{
		$this->view->layout()->disableLayout();
		
		if( !$this->view->isUserLogged() && $this->_request->isXmlHttpRequest() ) {
			$this->_doLogin();
			$response = $this->getResponse();
			
			$response->setHeader('Content-Type', 'plain/HTML; charset=UTF-8')->sendHeaders();
			
			$response->appendBody(
				Zend_Json::encode(array('authentificationCode' => $this->getAuthentificationCode()))
			)->outputBody();
			exit;
		}
		$this->_redirect('index');
	}
	
	/**
	 * @desc This will logout the user.
	 * 
	 * Pre-conditions (optionnal) :
	 * some ZEND GET params must be setted
	 * if and only if you want a redirection to that desired page:
	 * 
	 * module_redir: The module name (optionnal)
	 * controller_redir: The controller name (needed)
	 * view_redir: The action/view name (optionnal)
	 */
	protected function _logout()
	{
		Zend_Auth::getInstance()->clearIdentity();
		Majisti_User::getInstance()->clear();
		$this->_redirection();
	}
	
	protected function _redirection()
	{
		if( $uri = $this->_request->getQuery('forward', false) ) {
			header('Location: ' . urldecode($uri) );
			exit();
		} else {
			$this->_redirect('index');
		}
	}
	
	protected abstract function _failed();
	
	protected abstract function _doLogin();
	
	protected function _successed()
	{
		$this->_redirection();
	}
	
	protected function _banned() {}
	
	protected function _useBanningMecanism($boolean)
	{
		$this->_useBanningMecanism = $boolean;
	}
	
	public function isBanningMecanismEnabled()
	{
		return $this->_useBanningMecanism;
	}
}