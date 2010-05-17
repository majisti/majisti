<?php

namespace Majisti\Util\Minifying;

interface IMinifier
{
    public function minifyCss($content, $options = array());
    public function minifyJs($content,  $options = array());

}