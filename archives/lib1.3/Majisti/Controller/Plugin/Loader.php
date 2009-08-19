<?php

/**
 * TODO: doc
 *
 * @author Yanick Rochon
 */
class Majisti_Controller_Plugin_Loader
{

	/**
	 * Plugin loader instance
	 *
	 * @var Majisti_Plugin_loader
	 */
	private static $instance;

	/**
	 * Return the singleton instance
	 *
	 * @return Majisti_Plugin_Loader
	 */
	private static function _getInstance()
	{
		if ( !self::$instance ) {
			self::$instance = new Majisti_Controller_Plugin_Loader();
		}

		return self::$instance;
	}


	/**
	 * Load the specified plugins from the given config.
	 * The value should contain a value $config->plugins such as :
	 *
	 * plugins
	 * ..plugin name [options]
	 * .....[constraints]
	 * ..[plugin name [options]]
	 * .....
	 * [plugins
	 * ...[plugin name [options]]
	 * ......]
	 *
	 * @param unknown_type $config
	 */
	public static function load(Zend_Config $config)
	{
		self::_getInstance()->_registerPlugins($config);
	}


	/**
	 * The config to use
	 *
	 * @var Zend_Config
	 */
	private $config;

	/**
	 * The front controller instance
	 *
	 * @var Zend_Controller_Front
	 */
	private $front;

	/**
	 * Called by getInstance to create the singleton instance
	 */
	private function __construct()
	{
		$this->front = Zend_Controller_Front::getInstance();
	}

	/**
	 * Register all the plugins in $plugins
	 *
	 * @param Zend_Config $plugins
	 */
	private function _registerPlugins(Zend_Config $config)
	{
		$this->config = $config;  // save config in use

		//header('Content-type: text/plain');

		if ( $config->plugins->count() ) {
			//echo "First in = " . count($config->plugins) .", " . $config->plugins->namespace . "\n";
			// only one plugins tag (with or without namespace)
			if ( $config->plugins->namespace || $config->plugins->count() == 1 ) {
				$this->_registerPluginsImpl($config->plugins->plugin, $config->plugins->namespace);
			} else {  // if more <plugins> than more tags exists in all $config
				// NOTE : $config->plugins->plugin == default.xml
				//        $config->plugins->n      == extended config

				$defaultPlugins = $config->plugins->plugin;  // get default plugins
				foreach ($config->plugins as $key => $plugins) {
					// NOTE : if string, then it's an attribute or an empty tag -> skip it
					if ( is_string($key) || is_string($plugins) ) continue; // skip container
					$namespace = $plugins->namespace;

					//echo "Namespace = " . $namespace . "\n";

					if ( $plugins->plugin ) {
						$plugin = $plugins->plugin;
					} else {
						$plugin = $plugins;
					}

					$this->_registerPluginsImpl($plugin, $namespace);
				}

				// if we have some default plugins, register them now
				if ( $defaultPlugins ) {
					$this->_registerPluginsImpl($defaultPlugins, null);
				}
			}
		}

		//exit();
	}

	/**
	 * Function called by _registerPlugins
	 *
	 * @param Zend_Config $plugins
	 * @param string $namespace
	 */
	private function _registerPluginsImpl(Zend_Config $plugins, $namespace) {

		if ( $plugins->name ) {
			$this->_registerSinglePluginImpl($plugins, $namespace);
		} else {
			foreach ($plugins as $plugin) {
				// if string, then it is an attribute, skip it
				if ( is_string($plugin) ) continue;

				$this->_registerSinglePluginImpl($plugin, $namespace);
			}
		}
	}

	/**
	 * Function called by _registerPluginsImpl
	 *
	 * @param Zend_Config $plugin
	 * @param string $namespace
	 */
	private function _registerSinglePluginImpl(Zend_Config $plugin, $namespace) {
		static $_registeredPlugins = array();

		$config = $this->config;

		if( $plugin->enabled && !in_array("{$namespace}_{$plugin->name}", $_registeredPlugins) ) {
			$options = array();

			if ( !is_null($namespace) ) {
					$options['namespace'] = $namespace;
			}

			// if a config is specified, try to fetch it
			if ( $plugin->config ) {
				$options['config'] = $config->get($plugin->config);
			}

			if ( $plugin->script ) {
				$options['script'] = $plugin->script;
			}

			// (1) if the plugin cannot be created, this throws an exception now
			$createdPlugin = Majisti_Controller_Plugin::factory($plugin->name, $options);

			//echo get_class($createdPlugin) . "\n";

			if( !$this->front->hasPlugin(get_class($createdPlugin)) ) {
			    // if plugin has constraints
				if ( $plugin->allow || $plugin->deny ) {
					$this->_registerPluginConstraintsImpl($createdPlugin, $plugin);
				} else {
					$this->front->registerPlugin($createdPlugin);
				}
			}

			// put the plugin name to the array so we don't register it twice (internal check)
			$_registeredPlugins[] = "{$namespace}_{$plugin->name}";
		}

	}

	/**
	 * Function called by _registerSinglePluginImpl
	 *
	 * @param Zend_Controller_Plugin_Abstract $pluginInstance
	 * @param Zend_Config $plugin
	 */
	private function _registerPluginConstraintsImpl($pluginInstance, $plugin) {
		static $pluginContainer;

		if ( empty( $pluginContainer ) ) {
			$pluginContainer = new Majisti_Controller_Plugin_Container();

			$this->front->registerPlugin($pluginContainer);
		}

		// it make sense to allow THEN deny (if some deny would counteract an allow statement...)
		foreach ($plugin as $mode => $constraint) {
		    switch (strtolower($mode)) {
		        case 'allow':
		        case 'deny':
		            foreach ($constraint as $rule) {
		                if (is_string($rule)) {
		                    $subject = $rule;
		                } else {
		                    $subject = $rule->subject;
		                }
   			            $pluginContainer->addConstraint($pluginInstance, $subject, $mode == 'allow');
		            }
		            break;
		        default:
		            break;
		    }

		}

	}




}