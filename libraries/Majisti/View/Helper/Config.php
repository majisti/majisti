<?php

namespace Majisti\View\Helper;

class Config extends AbstractHelper
{
    public function config($selection)
    {
        $selector = $this->getSelector();

        return $selector->find($selection);
    }
}