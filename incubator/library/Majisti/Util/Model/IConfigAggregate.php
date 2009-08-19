<?php

namespace Majisti\Util\Model;

interface IConfigAggregate
{
    public function getConfig();
    public function setConfig(\Zend_Config $config);
}
