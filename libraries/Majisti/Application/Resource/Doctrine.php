<?php

namespace Majisti\Application\Resource;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

class Doctrine extends \Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $maj = $bootstrap->getApplication()->getOption('majisti');

        $db = $bootstrap->bootstrap('Db')->getResource('Db');

        if( null === $db ) {
            throw new Exception("Db settings must be set in order to
                bootstrap the doctrine resource");
        }

        $config = new Configuration();

        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $config->setMetadataCacheImpl($cache);

        //FIXME: change MA_APP
        $driverImpl = $config->newDefaultAnnotationDriver();

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

        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(MA_APP . '/application/doctrine/proxies');
        $config->setProxyNamespace($maj['app']['namespace'] . '\Doctrine\Proxies');
        $config->setAutoGenerateProxyClasses(true);

        $dbConfig = $db->getConfig();
        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user'   => $dbConfig['username'],
        ) + $dbConfig;

        $em = EntityManager::create($connectionOptions, $config);

        \Zend_Registry::set('Doctrine_EntityManager', $em);

        return $em;
    }
}
