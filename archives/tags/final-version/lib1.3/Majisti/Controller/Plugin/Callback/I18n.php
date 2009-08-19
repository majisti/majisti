<?php

/**
 * TODO: implement this class
 * 
 * @author Steven Rosato
 */
class Majisti_Controller_Plugin_Callback_I18n extends Zend_Controller_Plugin_Abstract 
{
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
		/* register the gettext translation file assigned to this action. If file doesn't exists, exception is ignored */
//		Majisti_Functions::registerLanguageTranslation($request->getModuleName() . '_' . $request->getControllerName(),
//			'default', Majisti_Functions::getCurLang('project_name'), Zend_Registry::get('translate'));
		throw new Majisti_Controller_Plugin_Exception('Plugin i18n not implemented yet');
    }
}
