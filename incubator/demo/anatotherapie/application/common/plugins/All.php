<?php

/**
 * @desc Code applied on each controller
 *
 * @author Steven Rosato
 */
class Anato_Controller_Plugin_All extends Majisti_Controller_Plugin_Abstract
{
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		$controller = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->getActionController();
		
		/* stylesheets */
		$controller->view->headLink()->appendStylesheet(LIB_URL . '/styles/forms/default.css');
		$controller->view->headLink()->appendStylesheet(LIB_URL . '/styles/tables/cusco-sky/cusco-sky.css');
		$controller->view->headLink()->appendStylesheet(LIB_URL . '/styles/common.css');
		$controller->view->headLink()->appendStylesheet(BASE_URL . '/styles/core.css');
		
		/* jQuery and theme */
		$jQueryTheme = 'redmond';
		$controller->view->jQuery()->setLocalPath(LIB_URL . '/libraries/jquery/jquery.js');
		$controller->view->jQuery()->setUiLocalPath(LIB_URL . '/libraries/jquery/ui.js');
		$controller->view->headLink()->appendStylesheet(LIB_URL . "/libraries/jquery/themes/ui-{$jQueryTheme}/ui.all.css");
		
		/* png fix for ie6 */
		$controller->view->headScript()->appendFile(LIB_URL . '/scripts/ie/DD_belatedPNG.js', 'text/javascript', array('conditional' => 'lt IE 7'));
		$controller->view->headScript()->appendScript("DD_belatedPNG.fix('*');", 'text/javascript', array('conditional' => 'lt IE 7'));
		
		/* jQuery plugins */
		$controller->view->headScript()->appendFile(LIB_URL . '/libraries/jquery/plugins/idle.js');
		$controller->view->headScript()->appendFile(LIB_URL . '/libraries/jquery/plugins/tablesorter.js');
		$controller->view->headScript()->appendFile(LIB_URL . '/libraries/jquery/plugins/tablesorter.pager.js');
		$controller->view->headScript()->appendFile(LIB_URL . '/libraries/jquery/plugins/quicksearch.js');
		$controller->view->headScript()->appendFile(LIB_URL . '/libraries/jquery/plugins/autocomplete/autocomplete.js');
		$controller->view->headLink()->appendStylesheet(LIB_URL . '/libraries/jquery/plugins/autocomplete/default.css');
		
		/* thickbox for secret admin login */
		$controller->view->headLink()->appendStylesheet(LIB_URL . '/libraries/jquery/plugins/thickbox/default.css');
		$controller->view->jQuery()->addJavascriptFile(LIB_URL . '/libraries/jquery/plugins/thickbox/thickbox.js');
	}
}
