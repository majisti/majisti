<?php

/**
 * @desc Code applied on each of the admin's module controllers
 *
 * @author Steven Rosato
 */
class Anato_Controller_Plugin_Admin extends Majisti_Controller_Plugin_Abstract
{
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		$controller = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->getActionController();

		$controller->view->headLink()->appendStylesheet(BASE_URL . '/styles/admin.css');
		
		$session = new Zend_Session_Namespace(Zend_Registry::get('config')->session . "Admin");
		
		if( !($session->allowed || 'auth' == $request->getControllerName()) ) {
			header('Location:' . APPLICATION_URL . '/admin/auth/login');
			exit;
		}
		
		/* multiselect plugin */
		$controller->view->headLink()->appendStylesheet(LIB_URL . '/libraries/jquery/plugins/multiselect/css/ui.multiselect.css');
		$controller->view->jQuery()->addJavascriptFile(LIB_URL . '/libraries/jquery/plugins/multiselect/js/ui.multiselect.js');
		$controller->view->jQuery()->addJavascriptFile(LIB_URL . "/libraries/jquery/plugins/multiselect/js/locale/ui.multiselect-fr.js");
	}
}