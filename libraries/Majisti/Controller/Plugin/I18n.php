<?php

namespace Majisti\Controller\Plugin;

/**
 * @desc Listens for a specific URL param (that was setup in the
 * I18n plugin configuration) and switches locale according to that parameter
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class I18n extends AbstractPlugin
{
	/**
	 * @desc Switches to a specific locale according to a request parameter
	 * that was setup under plugins.i18n.requestParam.
	 *
	 * @throws Exception if the plugins.i18n.requestParam was never
	 * setup in the configuration
	 */
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
         $selector = new \Majisti\Config\Selector($this->getConfig());

         if( $config = $selector->find('plugins.i18n', false) ) {
             /* requestParam must be set */
             if( !$selector->find('plugins.i18n.requestParam', false) ) {
                throw new Exception("Request parameter is mandatory
                    in the configuration");
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
             throw new Exception("Request parameter is mandatory
             in the configuration");
         }
    }
}
