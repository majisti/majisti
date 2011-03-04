<?php

namespace Majisti\View\Helper;

class Config extends AbstractHelper
{
    public function helper($selection)
    {
        $selector = $this->getSelector();

        return $selector->find($selection);
    }
}