<?php

namespace Majisti\Test\Database;

use Doctrine\ORM\EntityManager,
    Doctrine\Common\DataFixtures,
    Doctrine\ORM\Tools\SchemaTool
;

/**
 * @desc Doctrine Database Helper implementation.
 *
 * @author Steven Rosato
 */
class DoctrineHelper implements Helper
{
    /**
     * @var EntityManager
     */
    protected $_em;

    /**
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    protected $_schemaTool;

    /**
     * @var \Majisti\Test\Helper 
     */
    protected $_helper;

    /**
     * Constructs the database helper
     * 
     * @param \Majisti\Test\Helper $helper The test helper
     */
    public function __construct(\Majisti\Test\Helper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Returns the test helper.
     * 
     * @return \Majisti\Test\Helper 
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->_em = $em;

        return $this;
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
        $this->doSchemaOperation('create');

        $em = $this->getEntityManager();
        $em->getProxyFactory()->generateProxyClasses(
            $em->getMetadataFactory()->getAllMetadata(),
            $em->getConfiguration()->getProxyDir()
        );

        return $this;
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
    public function recreateSchema()
    {
        $this->dropSchema();
        $this->createSchema();

        return $this;
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
     * @desc Returns Doctrine's entity manager.
     * 
     * @return \Doctrine\ORM\EntityManager The entity manager
     */
    public function getEntityManager()
    {
        if( null === $this->_em ) {
            $this->_em = $this->getApplication()
                ->getBootstrap()
                ->bootstrap('Doctrine')
                ->getResource('Doctrine');
        }

        return $this->_em;
    }

    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    public function getApplication()
    {
        return $this->getHelper()->getApplication();
    }

    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    public function reloadFixtures()
    {
        $em = $this->getEntityManager();
        $conf = $em->getConfiguration();
        $classes = $em->getMetadataFactory()->getAllMetadata();

        $em->clear();
        $caches = array(
            $conf->getResultCacheImpl(),
            $conf->getQueryCacheImpl(),
            $conf->getMetadataCacheImpl(),
        );

        foreach( $caches as $cache ) {
            if( null !== $cache ) {
                $cache->deleteAll();
            }
        }

        $loader = new DataFixtures\Loader();
        $purger = new DataFixtures\Purger\ORMPurger($em);

        $app = $this->getApplication();
        $maj  = $app->getOption('majisti');
        $path = $maj['app']['path'] . '/lib/models/doctrine/fixtures';

        if( file_exists($path) ) {
            $loader->loadFromDirectory($path);
        }

        $executor = new DataFixtures\Executor\ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        return $this;
    }
}
