<?php

namespace Majisti\Application\Resource;

class Extensions extends \Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        return $this->_getExtensions();
    }
    
    protected function _getExtensions()
    {
        $handle = opendir(MAJISTIX_EXTENSIONS_PATH);
        
        $loadedExtensions = array();
        
        /* walk directory for extensions and add the extension structure */
        while( false !== ($file = readdir($handle)) ) {
            /* skip non-directories, hidden files and current and parent directories */
            if( !is_dir(MAJISTIX_EXTENSIONS_PATH . '/' . $file) || '.' == $file{0} ) {
                continue;
            }
            
            /* bootstrap */
            $bootstrap = $this->getBootstrap();
            $bootstrap->bootstrap('view');
            
            /* add helpers */
            $view = $bootstrap->getResource('view');
            $view->addHelperPath('MajistiX/Extensions/' . $file . '/Helper', 'MajistiX_View_Helper');
            
            /* add controller plugins */
            $plugins = $this->_getControllerPlugins(MAJISTIX_EXTENSIONS_PATH . '/' . $file . '/Plugin');
            foreach ($plugins as $plugin) {
            	$bootstrap->getResource('frontController')->registerPlugin($plugin);
            }
            
            array_push($loadedExtensions, $file);
        }
        
        return $loadedExtensions;
    }
    
    /**
     * @desc Returns the controller plugin instanciated classes for a given
     * directory.
     * 
     * @param $dir The directory that contains controller plugins only
     */
    private function _getControllerPlugins($dir)
    {
        $plugins = array();
        
        if( false !== ($handle = @opendir($dir)) ) {
            while( false != ($file = readdir($handle)) ) {
                if( is_file($dir . '/' . $file) ) {
                    $classPrefix = 'MajistiX\Controller\Plugin\\';
                    
                    if ( \Zend_Loader::loadFile($file, $dir, true) ) {
                        $class = $classPrefix . trim($file, '.php');
                        
                        array_push($plugins, new $class());
                    }
                }
            }
        }
        
        return $plugins;
    }
}