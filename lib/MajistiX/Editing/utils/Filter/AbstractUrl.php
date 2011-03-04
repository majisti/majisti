<?php

namespace MajistiX\Editing\Util\Filter;

use \Majisti\Config\Configuration;

abstract class AbstractUrl implements \Zend_Filter_Interface
{
    protected $_conf;

    public function __construct(Configuration $conf)
    {
        $this->setConfiguration($conf);
    }

    public function getConfiguration()
    {
        return $this->_conf;
    }

    public function setConfiguration(Configuration $conf)
    {
        $this->_conf = $conf;
    }
}
