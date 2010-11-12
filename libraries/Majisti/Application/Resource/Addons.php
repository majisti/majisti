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
    const DEFAULT_NAMESPACE = 'MajistiX';

    /**
     * @desc Inits the extensions resource
     */
    public function init()
    {
        return $this->getAddons();
    }

    /**
     * @desc Loads the extensions and modules (addons) and
     * returns the addons manager.
     *
     * @return Array the loaded addons
     */
    protected function getAddons()
    {
        $app        = $this->getBootstrap()->getApplication();
        $manager    = new \Majisti\Application\Addons\Manager();
        $options    = new \Zend_Config($this->getOptions());

        $manager->setAddonsPaths($options->paths->toArray());

        /* load extensions */
        foreach( $options->ext as $namespace => $name ) {
            if( is_int($namespace) ) {
                $namespace = static::DEFAULT_NAMESPACE;
            }
            $manager->loadExtension($name, $namespace);
        }

        /* load modules */
        foreach( $options->module as $namespace => $name ) {
            if( is_int($namespace) ) {
                $namespace = static::DEFAULT_NAMESPACE;
            }
            $manager->loadModule($name, $namespace);
        }

        return $manager;
    }
}