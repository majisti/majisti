<?php

namespace Majisti\Config\Handler;

/**
 * @desc Import Handler enabling a [ini] configuration file to import 
 * another [ini] configuration file and merging the files into the current Zend_Config object.
 * The import handler will merge on an ascendant manner, overriding the parent configuration 
 * values, if necessary, with the children configuration files imported.
 * 
 * Ex:  A core module calling upon the Users module, thus importing it's configuration file, will cause
 *      the core configuration values to be overriden by the Users' configuration values if duplicate
 *      data is found.
 * 
 * The Import Handler digs recursively into the configuration files, meaning that a configuration file
 * can import one or several other files, wich can themselves import and so on.
 * 
 * TODO: Abstract this class one more level so it may support XML configuration files also.
 * FIXME: Steven's note: I find this class excellent except that no occurence to
 * ini configuration files should be referenced. This class should interract
 * only with \Zend_Config and not with its subsequent children. The test class
 * may tests Ini or XML just like PropertyTest does though. Be carefull of the allowed
 * print margin which is 80.
 * 
 * TODO: Steven's note: When I meant documentation I meant XML documentation that can be generated
 * by Phing and Docbook :) though class comments are excellent.
 * 
 * @author Jean-Francois Hamelin
 */
class Import implements IHandler
{
    /*
     * Array containing the URLs to import
     * Parent URL is the configuration file making the import.
     * Children URL is/are the configuration files demanded recursively.
     * Structure goes like this: array[key]=>array['parent']   = 'SomeUrl'
     *                                     =>array['children'] = array['url1'] [...] 
     */
    protected $_importUrls = array();
    
    /**
     * @desc Handles the configuration by finding the import URLs and then merging everything.
     * @param Zend_Config $config
     * @return Zend_Config
     */
    public function handle(\Zend_Config $config)
    {
        $this->clear();
        if( isset( $config->import ) ) {
            $this->_findImports($config->import);
            $this->_mergeAllImports($config);
            $this->resolveProperties($config, $this->getAllImportUrls());
            unset($config->import);
        }
        return $config;
    }
    
    /**
     * @desc Clears the import handler from its URLs.
     * @return Property this
     */
    public function clear()
    {
        $this->_importUrls = array();
        return $this;
    }
    
    /**
     * @desc Function called on the FIRST PARENT configuration file.
     *       Function will parse the 1st file and look for the first importations.
     *       Then, it will validate the depth of the requested files and dig through them if necessary.
     * @param Zend_Config $config
     * @return void
     */
    protected function _findImports(\Zend_Config $config)
    {
        foreach( $config as $key => $value ) {
            $this->_importUrls[$key]["parent"] = $value;
            $this->_validateDepth($value, $key);
        }
    }
    
    /**
     * @desc Returns the import URLs as an array.
     * @see The array's structure above at variable declaration.
     * @return array The import URLs
     */
    public function getImports()
    {
        return $this->_importUrls;
    }
    
    /**
     * Returns a flat array containing all the import URLs based on the associative array $_importUrls.
     * @return array a flat array containing all the import URLs.
     */
    public function getAllImportUrls()
    {
        $imports = array();
        foreach ($this->_importUrls as $urlSet) {
            foreach( $urlSet as $url) {
                if( is_array($url) ) {
                    foreach( $url as $child ) {
                        $imports[] = $child;
                    }
                } else {
                    $imports[] = $url;
                }
            }
        }
        
        return $imports;
    }
    
    /**
     * Resolves the properties within the imported configuration files.
     * 
     * @param Zend_Config $config
     * @param array $urls
     * @return void
     */    
    public function resolveProperties(\Zend_Config $config, $urls)
    {
        $propertyHandler = new Property();
        $config = $propertyHandler->handle($config);
    }
    
    /**
     * @desc Merging function that iterates through the $_importUrls array and will merge both
     *       the PARENT URLs (1st importations) and the CHILDREN URLs.
     *       
     *       Exemple: Core configuration imports the Forums module's configuration.  Forums will be PARENT.
     *                Forums' configuration relies on the Users' configuration and imports it in his own file.
     *                Users' configuration URL will be CHILDREN.  Any files required by the Users' configuration 
     *                will also be considered children URLs of the parent Forums.
     * @param Zend_Config $config
     * @return void
     */
    protected function _mergeAllImports(\Zend_Config $config)
    {
        $imports = $this->getImports();
        foreach($imports as $import) {
            foreach( $import as $key => $value ) {
                if( is_array($value) ) {
                    foreach( $value as $url ) {
                        $config->merge($this->buildConfigFile($url));
                    }
                } else {
                    $config->merge($this->buildConfigFile($value));
                }
            }
        }
    }
    
    /**
     * @desc Attempts to build a Zend_Config_ini object with the specified path and catches the exception
     *       in case the path is invalid.
     * @param $configPath
     * @param $allowModifications true|false
     * @return Zend_Config_ini
     * 
     * FIXME: steven's note: if you can't catch the exception it is probably becuase
     * you are currently trying to catch \Majisti\Config\Handler\Exception
     * since you are catching Exception. If you catch \Exception it's gonna
     * be any php exception and if you catch \Zend_Config_Exception well you are
     * going to catch the right exception I'm guessing ;). N.B. Do not put any
     * occurences to Zend_Config_Ini here, it should interact with Zend_Config
     * only. Note also the case sensitive here that won't work on my linux machine :)
     */
    public function buildConfigFile($configPath, $allowModifications = true)
    {
        /*
         * FIXME: Not working properly.  If path is invalid, a core Zend Exception is thrown and the custom 
         * printing never occurs.
         */
        try {
            $config = new \Zend_Config_ini($configPath, null, $allowModifications);
        } catch (\Zend_Config_Exception $e) {
            throw new Exception ( "Error: Invalid configuration path specified.  Invalid path given: " . $configPath );
        }
        
        return $config;
    }
    
    /**
     * @desc Validates the depth of the imports demanded.  If the importation URL does not contain any other
     *       importations, no action is required.  If it does however, recursive digging will occur. 
     * @param $configPath
     * @param $parentKey
     * @return void
     */
    protected function _validateDepth($configPath, $parentKey)
    {
        $tempConfig = $this->buildConfigFile($configPath);
        if( isset( $tempConfig->import ) ) {
            foreach ($tempConfig->import as $url) {
                $this->_dig($url, $parentKey);
            }
        }
    }
    
    /**
     * @desc Inserts the children URL found in the import array and digs every URL found
     *       afterward until none is found.
     * @param $url
     * @param $parentKey
     * @return void
     */
    protected function _dig($url, $parentKey)
    {
        $this->_importUrls[$parentKey]["children"][] = $url;
        
        $config = $this->buildConfigFile($url);
        if( $config->import ) {
            foreach($config->import as $url) {
                $this->_dig($url, $parentKey);
            }
        }
    }
}