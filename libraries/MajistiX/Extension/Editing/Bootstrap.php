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

    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    protected function _bootstrap($resource = null)
    {
        /* prepare table prefix according to app namespace */
        //TODO: configurable table name
        $maj = $this->getApplication()->getBootstrap()->getOption('majisti');
        Model\Content::setTableName(
            strtolower($maj['app']['namespace']) . '_content');

        return parent::_bootstrap($resource);
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function run()
    {
        /* doctrine is mendatory for this extension to work */
        if( !$this->hasPluginResource('Doctrine') ) {
            $this->registerPluginResource('Doctrine');
        }

        /* @var $em ORM\EntityManager */
        $this->_em = $this->getApplication()
                          ->getBootstrap()
                          ->bootstrap('Doctrine')
                          ->getPluginResource('Doctrine')
                          ->getEntityManager();

        /* add metadata driver for this extension's persistent models */
        $this->addMetadataDriver();

        /* content monitoring */
        $this->registerPlugin();
    }

    /**
     * @desc Adds the metadata driver to the driver chain.
     */
    protected function addMetadataDriver()
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
    protected function registerPlugin()
    {
        $front = $this->getResource('Frontcontroller');
        $front->registerPlugin(new Plugin\ContentMonitor());
    }
}
