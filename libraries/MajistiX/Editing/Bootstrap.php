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
     * @var \Zend_View
     */
    protected $_view;

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

        $this->_em = $bootstrap->bootstrap('Doctrine')
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

        /* @var $front \Zend_Controller_Front */
        $front = $this->getResource('FrontController');

        if( !$front->hasPlugin('MajistiX\Editing\Plugin\ContentMonitor') ) {
            $front->registerPlugin(new Plugin\ContentMonitor());
        }
    }

    /**
     * @desc Inits the configurable public files needed for this extension.
     */
    protected function _initPublicFiles()
    {
        $view   = $this->getView();
        $config = $this->getConfiguration();

        $view->publicFiles(new Configuration($config->find('publicFiles')));
    }

    /**
     * @desc Inits the editor provider singleton.
     */
    protected function _initProvider()
    {
        $config = $this->getConfiguration();
        $view   = $this->getView();

        /* setup provider singleton */
        $provider = View\Editor\Provider::getInstance()
            ->setView($view)
            ->setEditor($config->find('editor'), 
                new Configuration($config->find('majisti')));
    }

    /**
     * @desc Inits the content filters for both encryption and decryption
     */
    protected function _initContentFilters()
    {
        $chain = new \Zend_Filter();
        $chain->addFilter(new Util\Filter\DynamicUrl($this->getConfiguration()));

        Model\Content::setEncryptFilters($chain);

        $chain = new \Zend_Filter();
        $chain->addFilter(new Util\Filter\StaticUrl($this->getConfiguration()));

        Model\Content::setDecryptFilters($chain);
    }

    /**
     * @desc Returns The view
     *
     * @return \Majisti\View\View The view
     */
    protected function getView()
    {
        return $this->getApplication()
                    ->getBootstrap()
                    ->getResource('view');
    }

    /**
     * @desc Inits the table name. Assumes majisti.app.namespace . _content
     * if no table name given in the configuration.
     */
    protected function _initTableName()
    {
        $config = $this->getConfiguration();

        Model\Content::setTableName(
            $config->find('table', 'majistix_editing_content')
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

            $config->extend($this->getDefaultConfiguration($config))
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
    protected function getDefaultConfiguration($maj)
    {
        $pubUrl = $maj->find('majisti.url') . '/majistix/editing';

        return new Configuration(array(
            'editor' => 'CkEditor',
            'publicFiles' => array(
                'styles' => array(
                    'majistix-editing-1' => $pubUrl . '/styles/editing.css',
                ),
                'scripts' => array(
                    'majistix-editing-1' => $pubUrl . '/scripts/editing.js',
                )
            )
        ));
    }
}
