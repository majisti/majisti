<?php

namespace Majisti\Application\Resource;

/**
 * @desc Addons resource that will load any dropped extensions
 * and modules under the supported addons path provided.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Addons extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @desc Inits the addons resource
     */
    public function init()
    {
        return $this->getAddons();
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
            'extension' => array(
                'paths' => array(array(
                    'namespace' => $maj['app']['namespace'],
                    'path'      => $maj['app']['path'] . '/library/extensions',
                ), array(
                    'namespace' => 'MajistiX',
                    'path'      => $maj['path'] . '/libraries/MajistiX/Extension'
                )),
            ),
        );
    }

    /**
     * @desc Loads the extensions and modules (addons) and
     * returns the addons manager.
     *
     * @return \Majisti\Application\Addons\Manager The addons manager.
     */
    protected function getAddons()
    {
        $app        = $this->getBootstrap()->getApplication();
        $manager    = new \Majisti\Application\Addons\Manager($app);

        $this->getDefaultOptions();

        $options  = array_merge_recursive(
            $this->getDefaultOptions(),
            $this->getOptions()
        );

        $manager->setExtensionPaths($options['extension']['paths']);

        unset($options['extension']['paths']);

        /* load extensions */
        foreach( $options['extension'] as $key => $name ) {
            if( !is_int($key) ) {
                continue;
            }

            $extOptions = isset($options['extension'][$name])
                ? $options['extension'][$name]
                : array();

            $manager->loadExtension($name, $extOptions);
        }

        /* load modules */
//        foreach( $options->module as $namespace => $name ) {
//            if( is_int($namespace) ) {
//                $namespace = static::DEFAULT_NAMESPACE;
//            }
//            $manager->loadModule($name, $namespace);
//        }

        return $manager;
    }
}