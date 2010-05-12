<?php

namespace Majisti\Application\Resource;

/**
 * @desc Extension resource that will load any dropped extensions
 * under the MajistiX/Extensions namespace.
 *
 * TODO: check this class integrity, should it load Majisti/Extensions ?
 * MajistiC/Extensions? application's library extensions?
 * Should it automatically register controller plugins or should extensions
 * contain boostraping?
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Extensions extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @desc Inits the extensions resource
     */
    public function init()
    {
        return $this->getExtensions();
    }

    /**
     * @desc Loads the extensions and return them.
     *
     * An extension can have controller plugins and view helpers which
     * will be automatically loaded.
     *
     * @return Array the loaded extensions
     */
    protected function getExtensions()
    {
        $handle = opendir(MAJISTIX_EXTENSIONS);

        $loadedExtensions = array();

        /* walk directory for extensions and add the extension structure */
        while( false !== ($extension = readdir($handle)) ) {
            /*
             * skip non-directories, hidden files, current and parent directories
             * and non activated extensions
             */
            if( !is_dir(MAJISTIX_EXTENSIONS . '/' . $extension) || '.' == $extension{0}
                || !array_key_exists($extension, $this->getOptions()) ) {
                continue;
            }

            /* bootstrap */
            $bootstrap = $this->getBootstrap();
            $bootstrap->bootstrap('view');

            /* add helpers */
            $view = $bootstrap->getResource('view');
            $view->addHelperPath('MajistiX/Extensions/' . $extension
                . '/Helper', 'MajistiX_View_Helper');

            /* add controller plugins */
            $plugins = $this->getControllerPlugins(MAJISTIX_EXTENSIONS
                . '/' . $extension . '/Plugin');
            foreach ($plugins as $plugin) {
            	$bootstrap ->getResource('frontController')
            	           ->registerPlugin($plugin);
            }

            array_push($loadedExtensions, $extension);
        }

        return $loadedExtensions;
    }

    /**
     * @desc Returns the controller plugin instanciated classes for a given
     * directory.
     *
     * @param $dir The directory that contains controller plugins only
     */
    private function getControllerPlugins($dir)
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