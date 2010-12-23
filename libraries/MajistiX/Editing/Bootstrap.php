<?php

namespace MajistiX\Editing;

use \Doctrine\ORM,
    \Majisti\Config\Configuration;

/**
 * @desc Editing extension bootstrap.
 *
 * @author Steven Rosato
 */
class Bootstrap extends \Majisti\Application\Extension\AbstractBootstrap
{
    /**
     * @var ORM\EntityManager 
     */
    protected $_em;

    /**
     * @var Configuration
     */
    protected $_configuration;

    /**
     * @desc Inits the entity manager.
     */
    protected function _initEntityManager()
    {
        $bootstrap = $this->getApplication()->getBootstrap();

        /* doctrine is mendatory for this extension to work */
        if( !$bootstrap->hasPluginResource('Doctrine') ) {
            $bootstrap->registerPluginResource('Doctrine');
        }

        /* @var $em ORM\EntityManager */
        $this->_em = $this->getApplication()
                          ->getBootstrap()
                          ->bootstrap('Doctrine')
                          ->getPluginResource('Doctrine')
                          ->getEntityManager();
    }

    /**
     * @desc Adds the metadata driver to the driver chain.
     */
    protected function _initMetadataDriver()
    {
        /* @var $driverChain ORM\Mapping\Driver\DriverChain */
        $driverChain = $this->_em->getConfiguration()->getMetadataDriverImpl();

        $driverChain->addDriver(
            $this->createMetadataDriver(),
            __NAMESPACE__ . '\Model'
        );
    }

    /**
     * @desc Creates the metadata driver.
     *
     * @return ORM\Mapping\Driver\StaticPHPDriver
     */
    protected function createMetadataDriver()
    {
        return new ORM\Mapping\Driver\StaticPHPDriver(array(
            __DIR__ . '/models'
        ));
    }

    /**
     * @desc Registers the needed plugin for content monitoring
     */
    protected function _initPlugin()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $front->registerPlugin(new Plugin\ContentMonitor());
    }

    /**
     * @desc Inits the editor provider singleton.
     */
    protected function _initProvider()
    {
        $config = $this->getConfiguration();

        /* view helper paths */
        $view = new \Zend_View();
        $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
        $view->addHelperPath('Majisti/View/Helper', 'Majisti\View\Helper\\');

        /* setup provider singleton */
        $provider = View\Editor\Provider::getInstance()
            ->setView($view)
            ->setEditorsUrl($config->find('majisti.url') . '/majistix/editing/editors')
            ->setEditorType($config->find('editor'))
            ->preloadEditor();
    }

    /**
     * @desc Inits the table name.
     */
    protected function _initTableName()
    {
        $config = $this->getConfiguration();

        Model\Content::setTableName(
            $config->find(
                'table',
                strtolower($config->find('majisti.app.namespace')) . '_content'
            )
        );
    }

    /**
     * @desc Returns the configuration.
     *
     * @return Configuration The configuration
     */
    protected function getConfiguration()
    {
        if( null === $this->_configuration ) {
            $maj     = $this->getApplication()->getBootstrap()->getOptions();
            $config  = new Configuration($maj);

            $config->extend($this->getDefaultConfiguration())
                   ->extend($this->getOptions());

            $this->_configuration = $config;
        }

        return $this->_configuration;
    }

    /**
     * @desc Returns the default configuration.
     *
     * @return Configuration The default configuration
     */
    protected function getDefaultConfiguration()
    {
        return new Configuration(array(
            'editor' => 'CkEditor'
        ));
    }
}
