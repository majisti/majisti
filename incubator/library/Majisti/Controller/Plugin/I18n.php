<?php

namespace Majisti\Controller\Plugin;

/**
 *
 * @author Steven Rosato
 */
class I18n extends AbstractPlugin
{
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
//         print 'I18n controller plugin enabled<br>';
//         $config = \Zend_Registry::get('Majisti_Config')->controllerPlugin->I18n;
//         
//         \Zend_Debug::dump($config->supportedLanguages);
         
//        if( $lang = $request->getParam('lang', false) ) {
//            $i18n = Zend_Registry::get('Majisti_I18n');
//            
//            if( $i18n->isLocaleSupported($lang) /* && $i18n->getCurrentLocale() !== $lang*/ ) {
//                $i18n->switchLocale($lang);
//                
//                if( $forward = $request->getParam('forward', false) ) {
//                    header("Location: {$forward}");
//                } else {
//                    header('Location: ' . $this->_reconstructUrl($request));
//                }
//                exit;
//            }
//        }
    }
    
    
    private function _reconstructUrl(\Zend_Controller_Request_Abstract $request) {
        // reconstruct query, remove the lang param only
//        $query = APPLICATION_URL;
//        if ('default' != strtolower($request->getModuleName())) {
//            $query .= '/' . $request->getModuleName();   
//        }
//        $query .= '/' . $request->getControllerName()
//                . '/' . $request->getActionName();
//        $params = $request->getParams();
//        if (!empty($params)) {
//            unset($params['lang'], $params['module'], $params['controller'], $params['action']);
//            foreach ($params as $paramName => $paramValue) {
//                $query .= '/' . $paramName . '/' . $paramValue;
//            }
//        }
//        return $query;
    }
}