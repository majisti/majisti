<?php

namespace Majisti\Model;

use \Doctrine\ORM\EntityManager;

/**
 * @desc Container for holding models by providing case insensitive
 * namespace access, lazy instanciation, aliases (todo) and automatic
 * persistence (todo).
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Container
{
    /**
     * @var \ArrayObject
     */
    protected $_registry;

    protected $_entityManager;

    protected $_automaticPersistence = false;

    /**
     * @desc Constructs the model container
     */
    public function __construct()
    {
        $this->_registry = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @desc Adds a model to the container, inside a namespace. Either an object
     * or classname can be provided, autoloaders and pluginloaders should be
     * able to load that class when it is instanciated, in other words,
     * when getModel() is called for the first time on the model key.
     * When no namespace is given, it is stored under the 'default' namespace
     * key.
     *
     * @param $key The key for accessing the model
     * @param $model The model, either an object or classname
     * @param $namespace [opt; def=default] The namespace key
     * @param $args [optionnal] The object arguments needed for instanciation
     */
    public function addModel($key, $model, $namespace = 'default', array $args = array())
    {
        /* prepare params */
        if( is_array($namespace) ) {
            $args       = $namespace;
            $namespace  = 'default';
        }

        if( null === $namespace ) {
            $namespace = 'default';
        }

        $registry   = $this->_registry;
        $namespace  = strtolower((string) $namespace);
        $key        = strtolower((string)$key);

        /* create namespace */
        if( !$registry->offsetExists($namespace) ) {
            $registry->$namespace = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
        }

        /* add model */
        $registry->$namespace->$key = new \ArrayObject(array(
            'model' => $model,
            'args'  => $args
        ), \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @desc Removes a model from the provided key and namespace.
     *
     * @param $key The key the model was stored in
     * @param $namespace [opt; def=default] The namespace key
     *
     * @return True if the model was successfully removed, false otherwise
     */
    public function removeModel($key, $namespace = 'default')
    {
        $registry   = $this->_registry;
        $namespace  = strtolower((string) $namespace);
        $key        = strtolower((string)$key);

        /* wrong namespace */
        if( !$registry->offsetExists($namespace) ) {
            return false;
        }

        /* wrong key */
        if( !$registry->$namespace->offsetExists($key) ) {
            return false;
        }

        /* remove model */
        unset($registry->$namespace->$key);

        return true;
    }

    /**
     * @desc Retrieves a model from the container. If the model was a classname,
     * instanciation will occur with the args provided with addModel(),
     * it will be instanciated only once and the object must be accessible through
     * autoloaders and pluginsloaders and errors associated are not handled. Once
     * the instanciation has occured, further calls on getModel() will return
     * that same model.
     *
     * @param $key The model key stored in this container
     * @param $namespace [opt] The container's namespace
     * @param $returnModel [opt] If model does not exist with the provided
     * key and namespace, it will attempt to instanciate the classname given
     * in this parameter while adding it to this container. If an object or null
     * is given, the model returned by this function will likewise, adding it
     * meanwhile if it was an object
     * @param $args [opt] provide args for addModel fallback, when a returnModel
     * classname is specified
     *
     * @return A laziliy loaded model if it was never instanciated, the contained
     * model if it was already loaded
     */
    public function getModel($key, $namespace = 'default',
        $returnModel = null, array $args = array())
    {
        if( class_exists($key, true) && null === $returnModel) {
            $returnModel = $key;
        }

        $registry   = $this->_registry;
        $namespace  = strtolower((string) $namespace);
        $key        = strtolower((string) $key);

        /*
         * retrieve model, if it is a class name, attempt to
         * instanciate it with provided args. Once loaded,
         * the container will always return that model.
         */
        if( $registry->offsetExists($namespace) && $registry->$namespace->offsetExists($key) ) {
            $returnModel    = $registry->$namespace->$key->model;
            $args           = $registry->$namespace->$key->args;
        }

        return $this->loadModel($key, $returnModel, $namespace, $args);
    }

    /**
     * @desc Loads a model with with the provided arguments
     *
     * @param string|object $model The classname or object
     * @param array $args The args
     */
    protected function loadModel($key, $model, $namespace, array $args)
    {
        if( !(is_object($model) || null === $model) ) {
            $model = new \ReflectionClass($model);
            $model = $model->hasMethod('__construct')
                ? $model->newInstanceArgs($args)
                : $model->newInstance();
        }

        if( null !== $model ) {
            $this->addModel($key, $model, $namespace);

            if( $this->isAutomaticPersistenceEnabled() ) {
                $this->persistModel($model);
            }
        }

        return $model;
    }

    protected function detachModel($model)
    {
        $manager = $this->getEntityManager();

        if( null === $manager ) {
            throw new Exception(
                "A entity manager must be set prior to unpersisting models."
            );
        }

        if( $manager->contains($model) ) {
            $manager->detach($model);
        }
    }

    protected function persistModel($model)
    {
        $manager = $this->getEntityManager();

        if( null === $manager ) {
            throw new Exception(
                "A entity manager must be set prior to persisting models."
            );
        }

        if( $manager->contains($model) ) {
            return;
        }

        try {
            $manager->persist($model);
        } catch( \Doctrine\ORM\Mapping\Exception $e ) {}
    }

    /**
     * @return bool
     */
    public function isAutomaticPersistenceEnabled()
    {
        return $this->_automaticPersistence;
    }

    public function setAutomaticPersistenceEnabled($flag)
    {
        $this->_automaticPersistence = (bool) $flag;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    public function setEntityManager(EntityManager $manager)
    {
        $this->_entityManager = $manager;
    }

    public function flush()
    {
        $manager = $this->getEntityManager();

        if( null === $manager ) {
            throw new Exception(
                "An entity manager must be set in order to save models"
            );
        }

        $manager->flush();
    }
}
