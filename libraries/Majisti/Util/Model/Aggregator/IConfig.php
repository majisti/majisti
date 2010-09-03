<?php

namespace Majisti\Util\Model\Aggregator;

/**
 * @desc Interface that provides configuration to other models that
 * implements it, using \Zend_Config. The implementing class must
 * use a configuration mechanism. Serves as a Marker Interface
 * at the same time.
 *
 * @author Majisti
 */
interface IConfig
{
    /**
     * @link IConfig::getConfig()
     */
    public function getConfig();

    /**
     * @link IConfig::setConfig()
     */
    public function setConfig(\Zend_Config $config);
}
