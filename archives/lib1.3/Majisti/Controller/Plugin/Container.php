<?php

class Majisti_Controller_Plugin_Container extends Zend_Controller_Plugin_Abstract
{

    private static $_DEBUG = false;


    /**
     * An array where the indexes are the names of the plugins and values are the plugin objects
     * @var array<string => Zend_Controller_Plugin_Abstract>
     */
    private $_pluginRegistry;

    /**
     * A Majisti object to hold the constraints
     *
     *   $constraints->$pluginName->modules->$module->controllers->$controller->actions->$action
     *                                           ...->allow
     *                                                                     ...->allow
     *                                                                                       ...->allow
     */
    private $constraints;

    /**
     * Construct a new plugin container
     */
    public function __construct()
    {
        $this->_pluginRegistry = array();
        $this->constraints = new Majisti_Object();
    }

    /**
     * Test and call the plugins function specified by $function.
     *
     * @param Zend_Controller_Request_Abstract $request the request object
     * @param strin $function   the function name
     * @param bool $useparam (optional)   if true, invoke function with $request
     */
    private function _invokePlugins($function, Zend_Controller_Request_Abstract $request = null, $useParam = true, $validate = true )
    {
        // TODO : cache _canInvoke for the given request

        // optimization
        if ($useParam) {
            $this->setRequest($request); // update $request object

            if ( self::$_DEBUG ) echo $function . ' : ';
            foreach ($this->_pluginRegistry as $pluginName => $plugin) {
                if ( self::$_DEBUG ) echo $pluginName . ' ...';
                if (!$validate || $this->_canInvoke($pluginName)) {
                    if ( self::$_DEBUG ) echo "ok\n";
                    $plugin->$function($request);
                } else {
                    if ( self::$_DEBUG ) echo "Denied!\n";
                }
            }
        } else {
            foreach ($this->_pluginRegistry as $pluginName => $plugin) {
                if ($this->_canInvoke($pluginName)) {
                    $plugin->$function();
                }
            }
        }
    }

    /**
     * Add a new constraint to this container. The $subjects can be a string
     * or an array of strings. The strings must be formed as the following :
     *
     *   "module" or "module/controller" or "module/controller/action"
     *
     * Any other format may throw an error or produce unexpected results.
     *
     * @param Zned_Controller_Plugin_Abstract  $plugin    the plugin to set constraints to
     * @param string|array $subjects  the constraint subjects
     * @param boolean allow (default=true)
     *
     * @return Majisti_Controller_Pluginc_Container
     */
    public function addConstraint( $plugin, $subjects, $allow = TRUE )
    {
        if (! is_array($subjects)) {
            $subjects = array($subjects);
        }
        $pluginName = get_class($plugin);

        foreach ($subjects as $subject) {
            // get module / controller / action
            $subject = split('/', $subject);
            if (empty($subject) || count($subject) > 3) {
                throw new Majisti_Controller_Plugin_Exception('invalid subject constraint format');
            }
            array_push($subject, null, null, null); // make sure we have at least three elements in the array
            list ($module, $controller, $action) = $subject;

            if (empty($module)) {
                throw new Majisti_Controller_Plugin_Exception('invalid module constraint');
            } else if (! empty($action) && empty($controller)) {
                throw new Majisti_Controller_Plugin_Exception('action without conttroller defined');
            }

            if (! empty($action)) {
                $this->_setPluginForAction($pluginName, $module, $controller, $action, $allow);
            } else if (! empty($controller)) {
                $this->_setPluginForController($pluginName, $module, $allow);
            } else {
                $this->_setPluginForModule($pluginName, $module, $allow);
            }
        }

        // add plugin to the local registry
        $this->_pluginRegistry[$pluginName] = $plugin;

        return $this;
    }

    /**
     * Determine if the given $plugin is allowed for the given $module
     *
     * @param string $plugin
     * @param string $module
     * @return bool
     */
    private function _getPluginForModule($plugin, $module)
    {
        $allowed = false; // default

        // Only if the specified module is valid...
        if ( isset($this->constraints->$plugin) ) {
            $pluginConstraint = $this->constraints->$plugin;

            if ( isset($pluginConstraint->modules->$module) ) {
                $allowed = $pluginConstraint->modules->$module->allowed;
            }
        }

        if ( self::$_DEBUG ) echo " (module is " . ($allowed?'allowed':'denied') .')';

        return $allowed;
    }

    /**
     * Determine if the given $plugin is allowed for the given $module.
     * This method calls _getPluginForModule
     *
     * @param string $plugin
     * @param string $module
     * @param string $controller
     * @return bool
     */
    private function _getPluginForController($plugin, $module, $controller)
    {
        $allowed = false; // default

        // Only if the specified module is valid...
        if ( $this->_getPluginForModule($plugin, $module) ) {
            $moduleConstraint = $this->constraints->$plugin
                ->modules->$module;

            if ( isset($moduleConstraint->controllers->$controller) ) {
                $allowed = $moduleConstraint->controllers->$controller->allowed;
            } else {
                $allowed = true; // at this point, we suppose the module allows for all controllers
            }
        }

        if ( self::$_DEBUG ) echo " (controller is " . ($allowed?'allowed':'denied') .')';

        return $allowed;
    }

    /**
     * Determine if the given $plugin is allowed for the given $action.
     * This method calls _getPluginForController
     *
     * @param string $plugin
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return bool
     */
    private function _getPluginForAction($plugin, $module, $controller, $action)
    {
        $allowed = false; // default

        // Only if the specified controller is valid...
        if ( $this->_getPluginForController($plugin, $module, $controller) ) {
            $controllerConstraint = $this->constraints->$plugin
                ->modules->$module
                ->controllers->$controller;

            if ( isset($controller->actions->$action) ) {
                $allowed = $controller->actions->$action->allowed;
            } else {
                $allowed = true; // at this point, we suppose the controller allows for all actions
            }
        }

        if ( self::$_DEBUG ) echo " (action is " . ($allowed?'allowed':'denied') .')';

        return $allowed;
    }

    /**
     * Sets if a plugin is allowed for a specific module
     *
     * @param string $plugin
     * @param string $module
     * @param bool $allow
     *
     * @return Majisti_Object   the $module constraint object
     */
    private function _setPluginForModule( $plugin, $module, $allow )
    {
        if (! $this->constraints->$plugin) {
            $pluginConstraints = $this->constraints->$plugin = new Majisti_Object(array('modules' => new Majisti_Object()));
        } else {
            $pluginConstraints = $this->constraints->$plugin;
        }
        if (! $pluginConstraints->modules->$module) {
            $pluginConstraints->modules->$module = new Majisti_Object(array('controllers' => new Majisti_Object()));
        }
        $pluginConstraints->modules->$module->allowed = $allow;
        return $pluginConstraints->modules->$module;
    }

    /**
     * Set if a plugin is allowed for a specific controller. This method also
     * invokes _setPluginForModule
     *
     * @param string $plugin
     * @param string $module
     * @param string $controller
     * @param bool $allow
     * @return Majisti_Object   the $controller constraint object
     */
    private function _setPluginForController( $plugin, $module, $controller, $allow )
    {
        $moduleConstraints = $this->_setPluginForModule($plugin, $module, $allow);
        if (! $moduleConstraints->controllers->$controller) {
            $moduleConstraints->controllers->$controller = new Majisti_Object(array('actions' => new Majisti_Object()));
        }
        $moduleConstraints->controllers->$controller->allowed = $allow;
        return $moduleConstraints->controllers->$controller;
    }

    /**
     * Set if a plugin is allowed for a specific action. This method also
     * invokes _setPluginForController
     *
     * @param string $plugin
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param string $allow
     * @return Majisti_Object    the $action constraint object
     */
    private function _setPluginForAction( $plugin, $module, $controller, $action, $allow )
    {
        $controllerConstraints = $this->_setPluginForController($plugin, $module, $controller, $allow);
        if (! $controllerConstraints->actions->$action) {
            $controllerConstraints->actions->$action = new Majisti_Object();
        }
        $controllerConstraints->actions->$action->allowed = $allow;
        return $controllerConstraints->actions->$action;
    }

    /**
     * Returns a boolean whether a plugin can be called at the current stage
     *
     * @param string $pluginName
     */
    private function _canInvoke( $pluginName )
    {
        $request = $this->getRequest();

        return $this->_getPluginForAction(
            $pluginName,
            $request->getModuleName(),
            $request->getControllerName(),
            $request->getActionName()
        );
    }

    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup( Zend_Controller_Request_Abstract $request )
    {
        if ( self::$_DEBUG ) header('Content-type: text/plain');
        $this->_invokePlugins(__FUNCTION__, $request, true, false);
    }

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown( Zend_Controller_Request_Abstract $request )
    {
        $this->_invokePlugins(__FUNCTION__, $request);
    }

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup( Zend_Controller_Request_Abstract $request )
    {
        $this->_invokePlugins(__FUNCTION__, $request);
    }

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch( Zend_Controller_Request_Abstract $request )
    {
        $this->_invokePlugins(__FUNCTION__, $request);
    }

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch( Zend_Controller_Request_Abstract $request )
    {
        $this->_invokePlugins(__FUNCTION__, $request);
    }

    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $this->_invokePlugins(__FUNCTION__, null, false);
        if ( self::$_DEBUG ) exit();
    }
}

