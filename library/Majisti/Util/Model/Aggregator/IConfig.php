<?php

namespace Majisti\Util\Model\Aggregator;

interface IConfig
{
    public function getConfig();
    public function setConfig(\Zend_Config $config);
}
