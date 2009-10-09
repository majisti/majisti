<?php

class Admin_AuthController extends Zend_Controller_Action 
{
	/** @var Zend_Config_Xml */
	private $_config;
	
	/** @var Zend_Session_Namespace */
	private $_session;
	
	public function init()
	{
		$this->_config = Zend_Registry::get('config');
		$this->_session = new Zend_Session_Namespace($this->_config->session . "Admin");
	}
	
	/**
	 * @desc Login the administrator
	 */
	public function loginAction()
	{
		if( $this->_request->isPost() ) {
			$data = $this->_request->getPost();
			
			if( $data['login'] 	 == $this->_config->admin->login &&
				$data['pass'] 	 == $this->_config->admin->password) {
				$this->_session->allowed = true;	
				$this->_redirect('admin/therapists');		
			} else {
				$this->view->message = $this->view->_("Mauvais login ou mot de passe");
			}
		}
	}
	
	/**
	 * @desc Logouts the administrator
	 */
	public function logoutAction()
	{
		$this->_session->unsetAll();
		$this->_redirect('index');
	}
}