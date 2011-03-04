<?php

namespace Majisti\Util\Minifying;

require_once 'Yui/Yui.php';

class Yui extends AbstractMinifier
{
    protected $_minifier;

    public function __construct()
    {
        \Yui::$jarFile = MAJISTI_ROOT . '/externals/yuicompressor-2.4.2.jar';
        \Yui::$tempDir = '/tmp';

        $this->_minifier = new \Yui();
    }

    public function minifyJs($content, $options = array())
    {
        return $this->_minifier->minifyJs($content, $options);
    }

    public function minifyCss($content, $options = array())
    {
        return $this->_minifier->minifyCss($content, $options);
    }
}
