<?php

namespace Majisti\Util\Minifying;

class NullMinifier extends AbstractMinifier
{
    public function minifyCss($content, $options = array())
    {
        return $content;
    }

    public function minifyJs($content, $options = array())
    {
        return $content;
    }
}
