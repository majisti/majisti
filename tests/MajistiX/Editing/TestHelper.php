<?php

namespace MajistiX\Editing;

/* fallsback to parent test helper */
require_once dirname(__DIR__) . '/TestHelper.php';

$helper = \Majisti\Test\Helper::getInstance();
$helper->addExtension(__NAMESPACE__, __DIR__);
