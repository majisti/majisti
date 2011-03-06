<?php

namespace Majisti\Application\Resource;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration,
    Doctrine\DBAL\Event\Listeners\MysqlSessionInit
;

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
    /**
     * @var EntityManager
     */
    protected $_em;

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function init()
    {
        return $this->getEntityManager();
    }

    /**
     * @desc Inits the Entity Manager
     * @return EntityManager The manager
     */
    public function getEntityManager()
    {
        if( null !== $this->_em ) {
            return $this->_em;
        }

        $bootstrap = $this->getBootstrap();
        $maj = $bootstrap->getApplication()->getOption('majisti');

        $bootstrap->bootstrap('frontController');
        $db = $bootstrap->bootstrap('Db')->getResource('Db');

        /* ensure db settings */
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
        $driverImpl->addPaths(array(
            $maj['app']['path'] . '/lib/models',
        ));

        /* add more annotation drivers based on modules */
        $cont = $bootstrap->getResource('frontController');
        foreach( $cont->getControllerDirectory() as $module => $dir ) {
            $driver = $config->newDefaultAnnotationDriver();
            if( $path = realpath($dir . '/../models/doctrine/fixtures') ) {
                $driver->addPaths(array($path));
            }

            if( count($driver->getPaths()) ) {
                $chain->addDriver(
                    $driver,
                    $maj['app']['namespace'] . '\\' . ucfirst($module) . '\Model'
                );
            }
        }

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
        $dbConfig['driverOptions'] = array(
            'charset' => 'UTF8'
        );

        $evm = new \Doctrine\Common\EventManager();

        $evm->addEventSubscriber(new
            MysqlSessionInit('utf8', 'utf8_unicode_ci'));

        $em = EntityManager::create($dbConfig, $config, $evm);

        \Zend_Registry::set('Doctrine_EntityManager', $em);

        $this->_em = $em;

        return $em;
    }
}
