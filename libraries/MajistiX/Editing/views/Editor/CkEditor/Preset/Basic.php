<?php

namespace MajistiX\Editing\View\Editor\CkEditor\Preset;

class Basic extends \Zend_Config
{
    public function __construct()
    {
        parent::__construct(array(
            'toolbar' => 'Basic'
        ));
    }
}
