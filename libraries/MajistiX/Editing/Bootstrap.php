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

        $view = $this->getView();

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
     * @desc Returns a configured view with needed helper paths.
     *
     * @return \Majisti\View\View The configured view
     */
    protected function getView()
    {
        //FIXME: MA-67: View should be retrievable via the master bootstrap
        if( null === $this->_view ) {
            /* view helper paths */
            $view = new \Majisti\View\View();
            $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
            $view->addHelperPath('Majisti/View/Helper', 'Majisti\View\Helper\\');
            $view->addHelperPath('MajistiX/Editing/views/helpers',
                'MajistiX\Editing\View\Helper\\');

            $view->addBasePath(__DIR__ . '/views');

            $this->_view = $view;
        }

        return $this->_view;
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
                    'default' => $pubUrl . '/styles/editing.css',
                ),
                'scripts' => array(
                    'default'  => $pubUrl . '/scripts/editing.js',
                )
            )
        ));
    }
}
