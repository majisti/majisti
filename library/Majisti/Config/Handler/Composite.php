<?php

namespace Majisti\Config\Handler;

/**
 * @desc Composite of IHandler. It is a basic stack that adds up
 * handlers that implements IHandler and provides a composition
 * to handling a configuration. Note that since this Composite
 * Handler is foremost a stack, the last handler added will be
 * the first one to parse the given config (LIFO order).
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Composite extends \Majisti\Util\Model\Stack implements IHandler
{
    /**
     * @desc Handles the configuration by delegating to this
     * Composite Handler's handlers.
     *
     * @param $config The configuration to parse in LIFO order from the handlers
     *
     * @throws Exception if one of the class provided in the stack is not an
     * instance of IHandler.
     *
     * @return \Zend_Config the parsed configuration
     */
    public function handle(\Zend_Config $config)
    {
        foreach ($this as $handler) {
            if( !($handler instanceof IHandler) ) {
                throw new Exception(get_class($handler) .
                    " must be an instance of \Majisti\Config\Handler\IHandler");
            }
           $config->merge($handler->handle($config));
        }
        return $config;
    }
}
