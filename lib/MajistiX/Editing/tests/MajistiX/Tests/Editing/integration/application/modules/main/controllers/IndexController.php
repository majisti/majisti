<?php

use Doctrine\Common\DataFixtures as DataFixtures;
/**
 * @desc The index controller.
 *
 * @author Majisti
 */
class IndexController extends Zend_Controller_Action
{
    /**
     * @desc The index action
     */
    public function indexAction()
    {
    }

    /**
     * @desc Drops the entire schema, recreates it and regenerate fixtures
     */
    public function resetAction()
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em      = \Zend_Registry::get('Doctrine_EntityManager');
        $schema  = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();

        $schema->dropSchema($classes);
        $schema->createSchema($classes);

        $loader = new DataFixtures\Loader();
        $purger = new DataFixtures\Purger\ORMPurger($em);

        $app = $this->getInvokeArg('bootstrap')->getApplication();
        $maj = $app->getOption('majisti');
        $path = $maj['app']['path'] .  '/library/models/doctrine/fixtures';

        if( file_exists($path) ) {
            $loader->loadFromDirectory($path);
        }

        $cont = $app->getBootstrap()->getResource('frontController');
        /* load modules fixtures */
        $modules = $cont->getControllerDirectory();
        foreach( $modules as $module ) {
            if( $path = realpath($module . '/../models/doctrine/fixtures') ) {
                $loader->loadFromDirectory($path);
            }
        }
        $executor = new DataFixtures\Executor\ORMExecutor($em, $purger);

        $fixtures = $loader->getFixtures();
        $executor->execute($fixtures);

        $this->_redirect('index');
    }
}
