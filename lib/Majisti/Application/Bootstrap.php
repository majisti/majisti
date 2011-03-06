<?php

namespace Majisti\Application;

use \Zend_Controller_Action_HelperBroker as HelperBroker;

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
        $this->initActionHelper();
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
            $app['path']      . '/lib/resources/'
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
                'basePath'  => realpath(dirname($path) . '/../lib'),
            )));
        }
        return $this->_resourceLoader;
    }

    /**
     * @desc Inits the action helper broker with initial paths.
     * FIXME: this seems to load on every bootstrap class, even modules, should it?
     */
    protected function initActionHelper()
    {
        HelperBroker::addPath(
            'Majisti/Controller/ActionHelper',
            'Majisti\Controller\ActionHelper\\'
        );

        /* add application's library action helpers */
        $r    = new \ReflectionClass($this);
        $path = realpath(dirname($r->getFileName()) . '/../lib/actionHelpers');

        if( $path ) {
            HelperBroker::addPath(
                $path,
                $this->getAppNamespace() . '\Controller\ActionHelper\\'
            );
        }
    }
}
