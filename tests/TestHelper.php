<?php

require_once __DIR__ . '/../libraries/Majisti/Test/Helper.php';

$helper = \Majisti\Test\Helper::getInstance();

$helper->setOptions(array('majisti' => array(
    'path' => $helper->getMajistiPath(),
    'app' => array(
        'path'      => __DIR__ . '/Majisti/Application/_project',
        'namespace' => 'MajistiT',
    )
)));

$helper->init();

unset($helper);
