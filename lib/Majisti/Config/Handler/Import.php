<?php

namespace Majisti\Config\Handler;

/**
 * @desc Import handler enabling a configuration file to import another
 * configuration file and merging the files into the current \Zend_Config
 * object. The ImportHandler will override the parent configuration values, if
 * necessary, with the children configuration files imported.
 *
 * Exemple:  A core module calling upon the Users module, thus importing it's
 *           configuration file, will cause the core configuration values to be
 *           overriden by the Users' configuration values if duplicate keys are
 *           found.
 *
 * The Import Handler digs recursively into the configuration files, meaning
 * that a configuration file may import one or several other files, which can
 * themselves import and so on.
 *
 * Note: Circular importing is not blocked. A parent-child endless call is
 * prevented but, if a child imports a parent, the parent will override any
 * duplicate keys of the child. Then, the 2 files will not be resolved
 * afterward.
 *
 * Exemple: A imports config file B. B imports config file A. B had overriden
 * A's common keys, but since A is imported, A's keys' values will be replaced
 * by the originial ones. Then, both A and B have been resolved and the import
 * handler will not get back to them. Use round importing with care.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Import implements IHandler
{
    /**
     * @desc Array containing all resolved import paths
     * @var array
     */
    protected $_importPaths = array();
    
    /**
     * @desc Type of the Config objects we will attempt to instanciate
     * @var \Zend_Config
     */
    protected $_configType;
    
    /**
     * @desc Optional Chain object
     * @var Chain
     */
    protected $_chainHandler;
    
    protected $_configSectionName;
    
    /**
     * @desc The final \Zend_Config object returned after all imports have been
     * resolved.
     * @var \Zend_Config
     */
    protected $_finalConfig;

    /**
     * @var array
     */
    protected $_options;

    /**
     * @desc Constructs the Import Handler.
     *
     * @param array $options The options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }
    
    /**
     * @desc Handles the configuration by finding the import paths and then
     * merging everything.
     * @param \Zend_Config $config
     * @param Chain $chainHandler (optional)
     * @param array $params (optional)
     * @see _loadOptions() for $params
     *
     * @return \Zend_Config
     */
    public function handle(\Zend_Config $config,
                    Chain $chainHandler = null, $params = array())
    {
        $this->clear();
        $this->_chainHandler = $chainHandler;
        $options             = $this->getOptions();

        $this->_configSectionName = null === $config->getSectionName()
            ? $options['section']
            : $config->getSectionName();

        $this->_finalConfig = new \Zend_Config($config->toArray(), true);
        
        if( !empty($params) ) {
            $this->loadParams($params);
        }

        $this->setConfigType($config);

        if( null !== ($chainHandler = $this->getChainHandler()) ) {
            $this->_finalConfig = $chainHandler->handle($this->_finalConfig);
        }

        $selector = new \Majisti\Config\Selector($this->_finalConfig);
        if( $imports = $selector->find('majisti.import', false) ) {
            $this->resolveImports($imports);
            $majistiNamespace = $this->_finalConfig->majisti;
            unset($majistiNamespace->import);
        }

        return $this->_finalConfig;
    }
    
    /**
     * @desc Clears the import paths array
     * @return Import this
     */
    public function clear()
    {
        $this->_importPaths = array();
        return $this;
    }
    
    /**
     * @desc Resolves the imports by checking that the every requested path is
     * valid and unresolved. Then, \Zend_Config objects are instanciated and
     * merged into the final configuration object.
     * @param \Zend_Config $config
     */
    protected function resolveImports(\Zend_Config $config)
    {
        foreach( $config as $key => $path ) {
            
            if($this->isUnresolvedPath($path)) {
                
                $this->_importPaths[] = $path;
                $resolvedConfig = $this->getConfigFileByPath($path);
                
                if( null !== ($chainHandler =
                              $this->getChainHandler()) ) {
                    $resolvedConfig = $chainHandler->handle($resolvedConfig);
                }
                
                $this->mergeImports($resolvedConfig);
                $selector = new \Majisti\Config\Selector($resolvedConfig);
                $imports  = $selector->find('majisti.import', false);
                if( false !== $imports ) {
                    $this->resolveImports($imports);
                }
            }
        }
    }
    
    /**
     * @desc Checks weither the requested path has already been resolved.
     * @param string $path
     * @return bool true if the path is unresolved
     */
    protected function isUnresolvedPath($path)
    {
        return !in_array($path, $this->_importPaths);
    }
    
    
    /**
     * @desc Merges given \Zend_Config with local final config object.
     * @param \Zend_Config $config
     */
    protected function mergeImports(\Zend_Config $config)
    {
        $this->_finalConfig->merge($config);
    }
    
    /**
     * @desc Loads optional parameters. Available parameters are:
     * parent - specify the topmost config file path, so that it's impossible
     *          to reload it once by a child config file.
     * @param array $params
     */
    protected function loadParams($params)
    {
        foreach ($params as $key => $value) {
        	switch ($key) {
        		case "parent":
        		$this->_importPaths[] = $value;
        		break;
        		
        		default: /* Do nothing */
        		break;
        	};
        }
    }
    
    /**
     * @desc Attempts to build a \Zend_Config object with the specified path
     * and catches any config exceptions.
     * @param string $configPath
     * @return \Zend_Config
     */
    protected function getConfigFileByPath($configPath)
    {
        try {
            $type         = $this->getConfigType();
            $isZendConfig = 'Zend_Config' === $type;
            
            if( $isZendConfig && is_string($configPath) ) {
                throw new Exception("Cannot instanciate a Zend_Config with a
                                    string");
            }
            
            $config = new $type($configPath, $this->_configSectionName, true);
        } catch (\Zend_Config_Exception $e) {
            throw new Exception("Cannot instanciate {$type} with
                                path {$configPath}.
            Message was {$e->getMessage()}");
        }
        
        return $config;
    }
    
    /**
     * @desc Sets the configuration file type used to instanciate config objects
     * in _getConfigFileByPath function.
     * @param \Zend_Config $config
     */
    public function setConfigType(\Zend_Config $config)
    {
        $this->_configType = 'Zend_Config_Ini';
    }
    
    /**
     * @desc $_configType getter
     * @return \Zend_Config configType
     */
    public function getConfigType()
    {
        return $this->_configType;
    }
    
    /**
     * @desc Config handler composite object getter
     * @return Chain
     */
    public function getChainHandler()
    {
        return $this->_chainHandler;
    }
    
    /**
     * @desc $_importPaths getter
     * @return array
     */
    public function getImportPaths()
    {
        return $this->_importPaths;
    }

    /**
     * @desc Returns the options.
     *
     * @return array The options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @desc Sets the options.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
    }
}