<?php

namespace Majisti\Test\Database;

/**
 * @desc Doctrine Database Helper implementation.
 *
 * @author Steven Rosato
 */
class DoctrineHelper implements Helper
{
    /**
     * @var \Majisti\Test\Helper
     */
    protected $_helper;

    /**
     * @var \Zend_Application_Bootstrap_BootstrapAbstract
     */
    protected $_bootstrap;

    /**
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    protected $_schemaTool;

    /**
     * @desc Constructs the database helper with the provided Majisti Test
     * Helper
     *
     * @param \Majisti\Test\Helper $helper The test helper
     */
    public function __construct(\Majisti\Test\Helper $helper)
    {
        $this->_helper = $helper;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @desc Returns a lazy loaded schema tool.
     *
     * @return \Doctrine\ORM\Tools\SchemaTool The schema tool
     */
    public function getSchemaTool()
    {
        if( null === $this->_schemaTool ) {
            $this->_schemaTool = new \Doctrine\ORM\Tools\SchemaTool(
                $this->getEntityManager()
            );
        }

        return $this->_schemaTool;
    }

    /**
     * @desc Performs create|update|drop operation with the schema tool.
     *
     * @param string $operation The operation
     *
     * @return DoctrineHelper this
     */
    private function doSchemaOperation($operation)
    {
        $operation .= 'Schema';

        $em = $this->getEntityManager();

        $this->getSchemaTool()
            ->$operation($em->getMetadataFactory()->getAllMetadata());

        return $this;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function createSchema()
    {
        return $this->doSchemaOperation('create');
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function updateSchema()
    {
        return $this->doSchemaOperation('update');
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function dropSchema()
    {
        return $this->doSchemaOperation('drop');
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function truncateTables(array $entities)
    {
        $em = $this->getEntityManager();

        foreach( $entities as $repository ) {
            if( !($repository instanceof \Doctrine\ORM\EntityRepository) ) {
                $repository = $em->getRepository((string) $repository);
            }

            foreach( $repository->findAll() as $entity ) {
                $em->remove($entity);
            }
        }

        $em->flush();

        return $this;
    }

    /**
     * @desc Returns Doctrine's entity manager
     * @return \Doctrine\ORM\EntityManager The entity manager
     */
    public function getEntityManager()
    {
        return $this->getBootstrap()->getPluginResource('Doctrine')
                    ->getEntityManager();
    }

    /**
     * @desc Returns a bootstrapped bootstrap from the test helper.
     *
     * @return \Zend_Application_Bootstrap_BootstrapAbstract
     */
    protected function getBootstrap()
    {
        if( null === $this->_bootstrap ) {
            $this->_bootstrap = $this->getHelper()->createBootstrapInstance()
                ->bootstrap();
        }

        return $this->_bootstrap;
    }
}
