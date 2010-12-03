<?php

require_once dirname(__DIR__) . '/bootstrap.php';

global $appLoader;

$app = $appLoader->createApplicationManager()->getApplication();
$app->bootstrap();

$cliLoader = new \Majisti\Util\Cli\Doctrine\CliLoader($app);
$cliLoader->runCli();

unset($appLoader, $app, $cliLoader);
