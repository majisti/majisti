<?php

namespace MajistiX\Extension\InPlaceEditing;

require_once 'TestHelper.php';

class BootstrapTest extends \Majisti\Test\TestCase
{
    /**
     * @var Bootstrap
     */
    public $bootstrap;

    public function setUp()
    {
        $bootstrap = $this->getHelper()->createBootstrapInstance(array(
            'resources' => array('db' => array(
                'params' => array(
                    'dbname' => 'majisti_development_koala',
                    'username' => 'root',
                    'password' => '',
                    'host'    => 'localhost'
                ),
                'adapter' => 'mysqli',
                'isDefaultTableAdapter' => true,
            ))
        ));
        $bootstrap->registerPluginResource('Doctrine');
        $bootstrap->bootstrap('frontController');
        $bootstrap->bootstrap('Doctrine');
        $this->em = $bootstrap->getPluginResource('Doctrine')->getEntityManager();

        $this->bootstrap = new Bootstrap();
    }

    public function testLoad()
    {
        $this->bootstrap->load();
        $em = $this->em;

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $schemaTool->updateSchema($this->em->getMetadataFactory()->getAllMetadata());

        $model = new Model\InPlaceEditing(new View\Editor\CkEditor(), 'en');
        $em->persist($model);
        $model->setContent('foo');
        $em->flush();
    }
}

BootstrapTest::runAlone();