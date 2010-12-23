<?php

/**
 * @desc The index controller.
 *
 * @author Majisti
 */
class Editing_IndexController extends Zend_Controller_Action
{
    /**
     * @desc The index action
     */
    public function indexAction()
    {
    }

    /**
     * @desc Shows an editing method called "In Place Editing"
     */
    public function staticInPlaceAction()
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
