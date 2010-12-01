<?php

namespace MajistiT\Extension\Foo;

class Bootstrap implements \Majisti\Application\Addons\IAddonsBootstrapper
{
    public function load()
    {
        return true;
    }
}
