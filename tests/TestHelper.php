<?php

require_once __DIR__ . '/../libraries/Majisti/Test/Helper.php';

$helper = \Majisti\Test\Helper::getInstance();

$helper->setOptions(array(
    'majisti' => array(
        'app' => array(
            'path'      => __DIR__ . '/Majisti/Application/_project',
            'namespace' => 'MajistiT',
            'env'       => 'development'
        ),
    )
));

$helper->init();

unset($helper);
