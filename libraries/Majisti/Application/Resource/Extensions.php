<?php

namespace Majisti\Application\Resource;

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

        return $this->getExtensions();
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
                'path'      => $maj['path'] . '/libraries/MajistiX'
            )),
        );
    }

    /**
     * @desc Loads the extensions and modules (addons) and
     * returns the addons manager.
     *
     * @return \Majisti\Application\Addons\Manager The addons manager.
     */
    protected function getExtensions()
    {
        $app        = $this->getBootstrap()->getApplication();
        $manager    = new \Majisti\Application\Extension\Manager($app);

        $this->getDefaultOptions();

        $options  = array_merge_recursive(
            $this->getDefaultOptions(),
            $this->getOptions()
        );

        $manager->setExtensionPaths($options['paths']);

        unset($options['paths']);

        /* load extensions */
        foreach( $options as $key => $name ) {
            if( !is_int($key) ) {
                continue;
            }

            $extOptions = isset($options[$name])
                ? $options[$name]
                : array();

            $manager->loadExtension($name, $extOptions);
        }

        return $manager;
    }
}