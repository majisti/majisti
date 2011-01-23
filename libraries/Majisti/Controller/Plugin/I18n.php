<?php

namespace Majisti\Controller\Plugin;

use Zend_Controller_Request_Abstract as Request;

/**
 * @desc I18n plugin that listens to a specific URL parameter to switch locales
 * for this application.
 *
 * @author Majisti
 */
class I18n extends AbstractPlugin
{
    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function preDispatch(Request $request)
    {
        $this->initI18n($request);
    }

    /**
     * @desc Listens for a specific URL param (that was setup in the
     * I18n plugin configuration) and switches locale according to that parameter.
     *
     * @throws Exception if the plugins.i18n.requestParam was never
     * setup in the configuration
     */
    public function initI18n(Request $request)
    {
         $selector = new \Majisti\Config\Selector($this->getConfig());

         $exception = new Exception("resources.frontController.plugins.i18n.requestParam" .
                 " is mendatory in the configuration");

         if( $config = $selector->find('resources.frontController.plugins.i18n', false) ) {
             /* requestParam must be set */
             if( !$selector->find('resources.frontController.plugins.i18n.requestParam', false) ) {
                throw $exception;
             }

             $locales = \Majisti\Application\Locales::getInstance();

             /* retrieve locale and switch if it is supported and not current */
             if( $locale = $request->getParam($config->requestParam, false) ) {

                $locale = new \Zend_Locale($locale);

                if( $locales->hasLocale($locale)
                    && !$locale->equals($locales->getCurrentLocale())) {

                    $locales->switchLocale($locale);

                    /* remove requestParam parameter */
                    $params = $request->getParams();
                    unset($params[$config->requestParam]);

                    /* send result as json encoded response */
                    if( $request->isXmlHttpRequest() ) {
                        $helper = \Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                        /* @var $helper \Zend_Controller_Action_Helper_Json */
                        $helper->sendJson(array('switched' => true));
                    } else { /* redirect */
                        \Zend_Controller_Action_HelperBroker ::getStaticHelper('redirector')
                            ->gotoSimpleAndExit(
                                $request->getActionName(),
                                $request->getControllerName(),
                                $request->getModuleName(),
                                $params
                            );
                    }
                }
             }
         } else {
             throw $exception;
         }
    }
}