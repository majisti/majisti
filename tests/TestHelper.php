<?php

require_once __DIR__ . '/../libraries/Majisti/Test/TestHelper.php';

$helper = \Majisti\Test\TestHelper::getInstance();

$helper->setOptions(array('majisti' => array(
    'path' => $helper->getMajistiPath(),
    'app' => array(
        'path' => __DIR__ . '/Majisti/Application/_project'
    )
)));

$helper->init();

unset($helper);
