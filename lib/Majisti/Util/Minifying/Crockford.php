<?php

namespace Majisti\Util\Minifying;

if( !defined('JSMIN_AS_LIB') ) {
    define('JSMIN_AS_LIB', true);
}

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
        $jsmin = new \JSMin($js, false);
        return $jsmin->minify();
    }
}
