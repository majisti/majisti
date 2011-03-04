<?php

$settings = array('majisti' => array(
    'app' => array(
        'namespace' => 'MajistiT',
        'env'       => 'development',
    ),
    'lib'       => array(
        'majisti' => 'majisti-0.4/libraries',
    ),
    'url' => array(
        'production' => 'http://static.majisti.com'
    )
));

require_once 'application/Loader.php';
$appLoader = new \Majisti\Application\Loader($settings);

unset($settings);
