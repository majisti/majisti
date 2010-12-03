<?php

namespace MajistiX\Extension\Editing;

require_once 'TestHelper.php';

/**
 * @desc Tests the InPlaceEditing extension bootstrap.
 *
 * @author Majisti
 */
class BootstrapTest extends \Majisti\Test\TestCase
{
    /**
     * @var MajistiX\Extension\InPlaceEditing\Bootstrap
     */
    public $bootstrap;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * @desc Setups the test case.
     */
    public function setUp()
    {
        /* ensure doctrine is loaded as a resource */
        $bootstrap = $this->getHelper()->createBootstrapInstance();
        $bootstrap->registerPluginResource('Doctrine');
        $bootstrap->bootstrap('Doctrine');

        /* retrieve entity manager */
        $this->em = $bootstrap->getPluginResource('Doctrine')->getEntityManager();
        $this->bootstrap = new Bootstrap($bootstrap->getApplication());
    }

    /**
     * @desc Tests that loading the bootstrap will ensure
     * that a static PHP driver for the extension's models is used
     */
    public function testLoadEnsuresStaticDriverUsage()
    {
        $em = $this->em;
        $this->bootstrap->load();

        /* @var $driverChain \Doctrine\ORM\Mapping\Driver\DriverChain */
        $driverChain = $em->getConfiguration()->getMetadataDriverImpl();
        $drivers = $driverChain->getDrivers();
        $namespace = 'MajistiX\Extension\InPlaceEditing\Model';

        /* ensure driver is contained in the driver chain */
        $this->assertArrayHasKey($namespace, $drivers);
        $this->assertThat($drivers[$namespace], $this->isInstanceOf(
            'Doctrine\ORM\Mapping\Driver\StaticPHPDriver'));
    }
}

BootstrapTest::runAlone();
