<?php

namespace Majisti\Config\Handler;

/**
 * @desc Provides a fluent way to handle configuration.
 * Handlers are able to treat the received config data
 * and parse it to change the values contained in the configuration
 * with the rules that the concrete handler specifies.
 * 
 * Handlers can only change data on non read-only configuration
 * and therefore should do nothing on locked ones. The handler
 * always return the configuration modified or not.
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface IHandler
{
    /**
     * @desc Handles a configuration by parsing its values
     * and returning the parsed configuration.
     * 
     * @param Zend_Config $config
     * @return Zend_Config
     */
    public function handle(\Zend_Config $config);
}