<?php

$settings = array('majisti' => array(
    'app' => array(
        'namespace' => 'MyApp',
        'env'       => 'development',
    ),
    'lib'       => array(
        'majisti' => 'majisti-0.4/libraries',
    ),
));

require_once 'application/Loader.php';
$appLoader = new \Majisti\Application\Loader($settings);

unset($settings);
