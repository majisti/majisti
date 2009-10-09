<?php

/**
 * @desc Admin index
 * 
 * @author J-F Hamelin and Steven Rosato
 */
class Admin_IndexController extends Zend_Controller_Action
{
	/**
	 * @desc The Admin index page
	 */
	public function indexAction()
	{
		$this->_redirect('admin/therapists');
	}
}
