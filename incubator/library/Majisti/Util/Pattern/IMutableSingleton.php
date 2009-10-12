<?php

namespace Majisti\Util\Pattern;

interface IMutableSingleton extends ISingleton
{
    public function setInstance(IMutableSingleton $instance);
}
