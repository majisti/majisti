<?php

namespace Majisti\Controller\Plugin;

class Majisti extends AbstractPlugin
{
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $methods = get_class_methods($this);

        foreach ( $methods as $method ) {
            if (4 < strlen($method) && 'init' === substr($method, 0, 4)) {
                $this->$method($request);
            }
        }
    }

	/**
	 * @desc Listens for a specific URL param (that was setup in the
     * I18n plugin configuration) and switches locale according to that parameter.
     *
	 * @throws Exception if the plugins.i18n.requestParam was never
	 * setup in the configuration
	 */
    public function initI18n(\Zend_Controller_Request_Abstract $request)
    {
         $selector = new \Majisti\Config\Selector($this->getConfig());

         $exception = new Exception("resources.frontController.plugins.majisti.i18nRequestParam" .
                 " is mendatory in the configuration");

         if( $config = $selector->find('resources.frontController.plugins.majisti', false) ) {
             /* requestParam must be set */
             if( !$selector->find('resources.frontController.plugins.majisti.i18nRequestParam', false) ) {
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

    protected function initJavascript(\Zend_Controller_Request_Abstract $request)
    {
        $view   = $this->getView();
        $config = $this->getConfig()->majisti;

        $conf = \Zend_Json::encode(array(
            'app'  => $config->app->toArray()
                      + array('currentUrl' => $view->url()),
            'url'  => $config->url,
            'path' => $config->path,
            'ext'  => new \ArrayObject(),
        ));

        if( !('production' === $config->app->env && 'staging' === $config->app->env) ) {
            $conf = \Zend_Json::prettyPrint($conf, array('indent' => '  '));
        }

        $view->headScript()->prependScript("var majisti = {$conf};");

    }

    public function initViewHelpers(\Zend_Controller_Request_Abstract $request)
    {
        $view   = $this->getView();
        $module = $request->getModuleName();
        $app    = $this->getConfig()->majisti->app;

        $path = $app->path . "/application/modules/{$module}/views/helpers";
        $ns   = $app->namespace . '\\' . ucfirst($module) . '\View\Helper\\';
        $view->addHelperPath($path, $ns);
    }

}