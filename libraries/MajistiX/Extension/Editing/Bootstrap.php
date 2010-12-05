<?php

namespace MajistiX\Extension\Editing;

use \Doctrine\ORM;

/**
 * @desc Editing extension bootstrap.
 *
 * @author Majisti
 */
class Bootstrap extends \Majisti\Application\Addons\AbstractBootstrap
{
    /**
     * @var ORM\EntityManager 
     */
    protected $_em;

    /**
     * @desc Inits the entity manager
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
        $maj     = $this->getApplication()->getBootstrap()->getOption('majisti');
        $options = new \Majisti\Config\Selector(new \Zend_Config($this->getOptions()));

        $view = new \Zend_View();
        $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
        $view->addHelperPath('Majisti/View/Helper', 'Majisti\View\Helper\\');

        $provider = View\Editor\Provider::getInstance()
            ->setView($view)
            ->setEditorsUrl($maj['url'] . '/majistix/ext/editing/editors')
            ->setEditorType($options->find('editor', 'CkEditor'))
            ->preloadEditor();
    }

    /**
     * @desc Inits the table name.
     */
    protected function _initTableName()
    {
        //TODO: configurable table name
        $maj = $this->getApplication()->getBootstrap()->getOption('majisti');

        Model\Content::setTableName(
            strtolower($maj['app']['namespace']) . '_content');
    }
}
