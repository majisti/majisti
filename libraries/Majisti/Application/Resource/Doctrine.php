<?php

namespace Majisti\Application\Resource;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

/**
 * @desc Doctrine Resource. This resource will bootstrap a Doctrine EntityManager
 * according to the database configuration provided by the Db Resource. The resource
 * will make sure to load any entities located under every module and under
 * the application's library, use proxies, cache and data fixtures.
 *
 * @author Majisti
 */
class Doctrine extends \Zend_Application_Resource_ResourceAbstract
{
    protected $_em;

    public function init()
    {
        return $this->getEntityManager();
    }

    public function getEntityManager()
    {
        if( null !== $this->_em ) {
            return $this->_em;
        }

        $bootstrap = $this->getBootstrap();
        $maj = $bootstrap->getApplication()->getOption('majisti');

        $bootstrap->bootstrap('frontController');
        $db = $bootstrap->bootstrap('Db')->getResource('Db');

        if( null === $db ) {
            throw new Exception("Db settings must be set in order to
                bootstrap the doctrine resource");
        }

        $config = new Configuration();

        /* FIXME: which cache to use? */
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $config->setMetadataCacheImpl($cache);

        $chain = new \Doctrine\ORM\Mapping\Driver\DriverChain();
        $driverImpl = $config->newDefaultAnnotationDriver();
        $chain->addDriver($driverImpl, $maj['app']['namespace'] . '\Model');

        $paths = array(
            $maj['app']['path'] . '/library/models',
        );

        $cont = $bootstrap->getResource('frontController');
        foreach( $cont->getControllerDirectory() as $dir ) {
            if( $path = realpath($dir . '/../models/doctrine/fixtures') ) {
                $paths[] = $path;
            }
        }

        $driverImpl->addPaths($paths);

        $config->setMetadataDriverImpl($chain);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir($maj['app']['path'] . '/application/doctrine/proxies');
        $config->setProxyNamespace($maj['app']['namespace'] . '\Doctrine\Proxies');
        $config->setAutoGenerateProxyClasses(true);

        $adapterClass = get_class($db);
        $dbConfig = $db->getConfig();
        $dbConfig['user'] = $dbConfig['username'];
        $dbConfig['driver'] = strtolower(substr(
            $adapterClass, 16, strlen($adapterClass)));

        $evm = new \Doctrine\Common\EventManager();

        $em = EntityManager::create($dbConfig, $config);

        \Zend_Registry::set('Doctrine_EntityManager', $em, $evm);

        $this->_em = $em;

        return $em;
    }
}
