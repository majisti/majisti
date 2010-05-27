<?php

namespace Majisti\Util\Minifying;

require_once 'Crockford/JsMin.php';
require_once 'Crockford/CssMin.php';

class Crockford extends AbstractMinifier
{
    public function minifyCss($css, $options = array())
    {
        return \cssmin::minify($css);
    }

    public function minifyJs($js, $options = array())
    {
        return \JSMin::minify($js);
    }
}
