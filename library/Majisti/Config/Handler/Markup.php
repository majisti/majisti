<?php

namespace Majisti\Config\Handler;

/**
 * @desc This handler applies a stack of Zend_Markup on the
 * given configuration.
 *
 * @see \Zend_Markup (if it ever makes it in the standard library)
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * TODO: factory constructor ex: bbCode_html adds BbCode to Html markup
 */
use Majisti\Util\Model;

class Markup extends \Majisti\Util\Model\Collection\Stack implements IHandler
{
    /**
     * @link IHandler::handle()
     * @throws Exception if no markup was pushed
     *
     * @return \Zend_Config
     */
    public function handle(\Zend_Config $config)
    {
        if( $this->isEmpty() ) {
            throw new Exception('No markup was pushed to the stack');
        }

        return $this->_parseConfig(clone $config);
    }

    /**
     * @desc Parses the configuration, applying the Markup stack
     * on every values.
     *
     * @throws Exception if Markup provided in not an instance of
     *  \Zend_Markup_RendererAbstract
     */
    protected function _parseConfig(\Zend_Config $config)
    {
        foreach ($config as $key => $value) {
            if( $value instanceof \Zend_Config ) {
                $config->{$key}->merge($this->_parseConfig($value));
            } else {
                foreach ($this as $markup) {
                    if( !($markup instanceof \Zend_Markup_Renderer_RendererAbstract) ) {
                        $markup = is_object($markup)
                            ? get_class($markup)
                            : $markup;
                        throw new \Exception("Markup {$markup} must be an"
                            . " instance of Zend_Markup_RendererAbstract");
                    }
                    $config->{$key} = $markup->render($value);
                }
            }
        }
        return $config;
    }
}
