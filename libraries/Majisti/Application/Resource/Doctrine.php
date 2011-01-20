<?php

namespace Majisti\Application\Resource;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

class Doctrine extends \Zend_Application_Resource_ResourceAbstract
{
    protected $_em;

    public function init()
    {
        $this->getBootstrap()->bootstrap('frontController');
        return $this->getEntityManager();
    }

    public function getEntityManager()
    {
        if( null !== $this->_em ) {
            return $this->_em;
        }

        $bootstrap = $this->getBootstrap();
        $maj = $bootstrap->getApplication()->getOption('majisti');

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

    protected function adapterToDoctrineDriver(\Zend_Db_Adapter_Abstract $db)
    {
        /* get Zend adapter name */
        $adapter = strtolower(str_replace(
            'Zend_Db_Adapter_',
            '' ,
            get_class($db)
        ));

    \Zend_Debug::dump($adapter);

        return $adapter;

        /* transform to corresponding Doctrine driver */
        $driver = str_replace(array(
            'mysqli',
        ), array(
            'pdo_mysql',
        ), $adapter);

        return $driver;
    }
}
