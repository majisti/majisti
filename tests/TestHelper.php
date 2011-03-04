<?php

require_once __DIR__ . '/../lib/Majisti/Test/Helper.php';

$helper = \Majisti\Test\Helper::getInstance();

$helper->setOptions(array(
    'majisti' => array(
        'app' => array(
            'path'      => __DIR__ . '/Majisti/Application/_project',
            'namespace' => 'MajistiT',
            'env'       => 'development'
        ),
    ),
    'resources' => array(
        'doctrine' => true,
        'db' => array(
            'params' => array(
                'dbname' => 'majisti_test',
                'username' => 'root',
                'password' => '',
                'host'    => 'localhost',
            ),
            'adapter' => 'pdo_mysql',
            'isDefaultTableAdapter' => true,
        )
    )
));

$helper->init();

unset($helper);
