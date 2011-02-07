<?php

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
     * @desc Resets the schema
     */
    public function resetAction()
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em      = \Zend_Registry::get('Doctrine_EntityManager');
        $schema  = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();

        $schema->dropSchema($classes);
        $schema->createSchema($classes);
    }
}
