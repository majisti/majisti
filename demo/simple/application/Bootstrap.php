<?php

namespace MajistiD\Simple\Application;

class Bootstrap extends \Majisti\Application\Bootstrap
{
    public function run()
    {
        /* short tags for view scripts */
        $this->getResource('view')->setUseStreamWrapper(true);

        parent::run();
    }
}
