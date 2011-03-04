<?php

namespace MajistiX;

use \Doctrine\Common\ClassLoader;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('MajistiX - All tests');

        foreach ( new \DirectoryIterator(__DIR__) as $fileInfo ) {
            if( $fileInfo->isDot() || !$fileInfo->isDir() ) {
                continue;
            }

            $class = __NAMESPACE__ . "\\{$fileInfo}\AllTests";

            if( class_exists($class) ) {
                $suite->addTestSuite($class::suite());
            }
        }
        
        return $suite;
    }
}

AllTests::runAlone();
