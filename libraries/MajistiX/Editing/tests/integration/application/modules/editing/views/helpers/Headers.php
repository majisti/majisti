<?php

namespace MajistiX\Editing\View\Helper;

class Headers extends \Majisti\View\Helper\Headers
{
    public function prepare()
    {
        parent::prepare();

        $config = $this->getConfig()->majisti;

        $this->view->headLink()->appendStylesheet($config->url . '/jquery/styles/redmond/redmond.css');
    }
}
