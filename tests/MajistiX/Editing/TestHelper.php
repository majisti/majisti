<?php

namespace MajistiX\Editing;

/* fallsback to parent test helper */
require_once dirname(__DIR__) . '/TestHelper.php';

\Majisti\Test\Helper::getInstance()->addTestModuleDirectory(
    __NAMESPACE__,
    __DIR__
);
