<?php

namespace Majisti\Application;

/**
 * @desc Majisti's application boostrap.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap
{
    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function __construct($application)
    {
        parent::__construct($application);

        $this->initResourcePaths();
    }

    /**
     * @desc Adds the application's resource path to the plugin loader stack.
     */
    protected function initResourcePaths()
    {
        /* add resources path */
        $options = $this->getOptions();
        $app     = $options['majisti']['app'];

        $this->getPluginLoader()->addPrefixPath(
            $app['namespace'] . '\Application\Resource\\',
            $app['path']      . '/library/resources/'
        );
    }

    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    public function getResourceLoader()
    {
        if ((null === $this->_resourceLoader)
            && (false !== ($namespace = $this->getAppNamespace()))
        ) {
            $r    = new \ReflectionClass($this);
            $path = $r->getFileName();
            $this->setResourceLoader(new ModuleAutoloader(array(
                'namespace' => $namespace,
                'basePath'  => realpath(dirname($path) . '/../library'),
            )));
        }
        return $this->_resourceLoader;
    }

    /**
     * @desc Inits the action helper broker
     */
    protected function _initActionHelper()
    {
        \Zend_Controller_Action_HelperBroker::addPath(
            'Majisti/Controller/ActionHelper',
            'Majisti_Controller_ActionHelper');
    }
}
