<?php

namespace Majisti\View\Helper;

/**
 * @desc Retrieves a model from the model container.
 *
 * @author Majisti
 */
class Model extends AbstractHelper
{
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
    public function helper($key, $namespace = 'default',
        $returnModel = null, array $args = array())
    {
        $modelContainer = \Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('ModelContainer');

        if( 0 === func_num_args() ) { //MA-28
            return $modelContainer;
        }

        return $modelContainer->getModel($key, $namespace, $returnModel, $args);
    }
}
