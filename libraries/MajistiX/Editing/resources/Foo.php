<?php

namespace MajistiX\Editing\Application\Resource;

class Foo extends \Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        print 'here';
        exit;
    }
}