<?php

namespace MajistiX\Extension\InPlaceEditing\Model;

require_once 'TestHelper.php';

class InPlaceEditingTest extends \Majisti\Test\TestCase
{
    public function setUp()
    {

    }

    public function testFoo()
    {
        $ipe = new InPlaceEditing();
        $ipe->setId('services_para1')
            ->setContent('Lorem ipsum')
            ->setLocale(new \Zend_Locale('en'));
    }
}

InPlaceEditingTest::runAlone();
