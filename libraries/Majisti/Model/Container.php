<?php

namespace Majisti\Model;

/**
 * @desc Container for holding models by providing case insensitive
 * namespace access, and lazy instanciation.
 *
 * @deprecated Usage of this class is not recommanded if you need to maintain
 * applications overtime because this class will be refactored in future revisions
 * to include Symfony's Depedency Injection.
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

    /**
     * @var EntityManager
     */
    protected $_persistenceManager;

    /**
     * @var bool
     */
    protected $_automaticPersistence = false;

    /**
     * @desc Constructs the model container
     */
    public function __construct()
    {
        $this->_registry = new \ArrayObject(
            array(),
            \ArrayObject::ARRAY_AS_PROPS
        );
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
    public function addModel($key, $model, $namespace = 'default',
        array $args = array())
    {
        /* prepare params */
        if( is_array($namespace) ) { /* args provided after model */
            $args       = $namespace;
            $namespace  = null;
        } else if( is_bool($namespace) ) { /* persistence provided after model */
            $persist   = $namespace;
            $namespace = null;
        }

        if( null === $namespace ) {
            $namespace = 'default';
        }

        $registry   = $this->_registry;
        $namespace  = strtolower((string) $namespace);
        $key        = strtolower((string) $key);

        /* create namespace */
        if( !$registry->offsetExists($namespace) ) {
            $registry->$namespace = new \ArrayObject(
                array(),
                \ArrayObject::ARRAY_AS_PROPS
            );
        }

        /* add model */
        $registry->$namespace->$key = new \ArrayObject(array(
            'model'   => $model,
            'args'    => $args,
        ), \ArrayObject::ARRAY_AS_PROPS);

        return $this;
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
        //MA-50 suppress warning before searching for key
        $autoloader = \Zend_Loader_Autoloader::getInstance();
        $warnings   = $autoloader->suppressNotFoundWarnings();

        $autoloader->suppressNotFoundWarnings(true);
        if( class_exists($key, true) && null === $returnModel) {
            $returnModel = $key;
        }
        $autoloader->suppressNotFoundWarnings($warnings);

        $registry   = $this->_registry;
        $namespace  = strtolower((string) $namespace);
        $key        = strtolower((string) $key);

        /*
         * retrieve model, if it is a class name, attempt to
         * instanciate it with provided args. Once loaded,
         * the container will always return that model.
         */
        if( $this->hasModel($key, $namespace) ) {
            $returnModel = $registry->$namespace->$key->model;
            $args        = $registry->$namespace->$key->args;
        }

        return $this->loadModel($key, $returnModel, $namespace, $args);
    }

    /**
     * @desc Returns whether the given key maps to a model within the container.
     *
     * @param string $key The model key
     * @param string $namespace The namespace
     *
     * @return bool True if the model exists, instanciated or not
     */
    public function hasModel($key, $namespace = 'default')
    {
        $registry  =  $this->_registry;
        $namespace = strtolower((string) $namespace);
        $key       = strtolower((string) $key);

        return $registry->offsetExists($namespace) &&
            $registry->$namespace->offsetExists($key);
    }

    /**
     * @desc Loads a model with the provided arguments
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
        }

        return $model;
    }
}
