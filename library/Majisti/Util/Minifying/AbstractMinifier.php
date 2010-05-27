<?php

namespace Majisti\Util\Minifying;

abstract class AbstractMinifier implements IMinifier
{
    public function minify($type, $content, $options = array())
    {
        if ( 'js' === $type ) {
            return $this->minifyJs($content, $options);
        } else if ( 'css' === $type ) {
            return $this->minifyCss($content, $options);
        } else {
            throw new Exception("Type {$type} not supported.");
        }
    }
}