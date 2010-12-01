<?php

namespace MajistiX\Extension\InPlaceEditing;

use \Doctrine\ORM;

class Bootstrap implements \Majisti\Application\Addons\IAddonsBootstrapper
{
    public function load()
    {
        /* @var $em ORM\EntityManager */
        $em = \Zend_Registry::get('Doctrine_EntityManager');

        /* add metadata driver for this extension's persistent models */
        $this->addMetadataDriver($em);
    }


    protected function addMetadataDriver(ORM\EntityManager $em)
    {
        /* @var $driverChain ORM\Mapping\Driver\DriverChain */
        $driverChain = $em->getConfiguration()->getMetadataDriverImpl();

        $driverChain->addDriver(
            $this->createMetadataDriver(),
            __NAMESPACE__ . '\Model'
        );
    }

    protected function createMetadataDriver()
    {
        return new ORM\Mapping\Driver\StaticPHPDriver(array(
            __DIR__ . '/Model'
        ));
    }
}
