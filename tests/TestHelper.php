<?php

require_once __DIR__ . '/../libraries/Majisti/Test/Helper.php';

$helper = new \Majisti\Test\Helper();

$helper->setOptions(array(
    'registerAsDefault' => true,
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
