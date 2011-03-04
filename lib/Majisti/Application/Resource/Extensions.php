<?php

namespace Majisti\Application\Resource;

use Majisti\Application\Extension\Manager;

/**
 * @desc Addons resource that will load any dropped extensions
 * and modules under the supported addons path provided.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Extensions extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @desc Inits the addons resource
     *
     * @return Manager
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $resources = $bootstrap->getPluginResources();

        /* MA-67 ensure all resources are loaded first */
        foreach( $resources as $name => $resource ) {
            if( 'extensions' !== $name ) {
                $bootstrap->bootstrap($name);
            }
        }

        return $this->getExtensionManager();
    }

    /**
     * @desc Returns the default options.
     *
     * @return Array The default options
     */
    protected function getDefaultOptions()
    {
        $app = $this->getBootstrap()->getApplication();
        $maj = $app->getOption('majisti');

        return array(
            'paths' => array(array(
                'namespace' => $maj['app']['namespace'],
                'path'      => $maj['app']['path'] . '/library/extensions',
            ), array(
                'namespace' => 'MajistiX',
                'path'      => $maj['path'] . '/lib/MajistiX'
            )),
        );
    }

    /**
     * @desc Loads the extensions and returns the extension manager.
     *
     * @return Manager The extension manager.
     * @throws Exception If there is a misconfiguration
     */
    protected function getExtensionManager()
    {
        $app        = $this->getBootstrap()->getApplication();
        $manager    = new Manager($app);

        $this->getDefaultOptions();

        $options  = array_merge_recursive(
            $this->getDefaultOptions(),
            $this->getOptions()
        );

        $manager->setExtensionPaths($options['paths']);

        unset($options['paths']);

        /*
         * support following extension syntax for loading:
         *
         * 1) resources.extension[] = Foo
         *
         * 2) resources.extension.Foo = 1
         *
         * 3) resources.extension.Foo.enabled = 1
         *
         * 4) resources.extension.Foo.anOption = aValue //implicitely enabled
         *
         * 5)
         * resources.extension.Foo.enabled  = 0
         * resources.extension.Foo.anOption = aValue
         */
        foreach( $options as $key => $name ) {
            $enabled = true;

            if( !is_int($key) ) {
                if( is_array($name) ) {
                    if( isset($name['enabled']) ) {
                        $enabled = $name['enabled'];
                    } else {
                        $enabled = !empty($name);
                    }
                } else {
                    $enabled = $name;
                }

                $name = $key;
            }

            if( $enabled ) {
                $extOptions = array();
                if( isset($options[$name]) ) {
                    $extOptions = $options[$name];
                    unset($extOptions['enabled']);
                }

                $manager->loadExtension($name, $extOptions);
            }
        }

        return $manager;
    }
}