<?php

/**
 * @desc Applies needed patches in case fixes are needed until they
 * get applied by the corresponding vendor.
 *
 * @author Steven Rosato
 */
foreach ( new \DirectoryIterator(__DIR__) as $dir ) {
    if( $dir->isDot() || !$dir->isDir() ) {
        continue;
    }

    $vendor = realpath(__DIR__ . "/../lib/vendor/{$dir}");

    if( $vendor ) {
        chdir($vendor);

        $patchDir = __DIR__ . '/'. $dir->getFilename();

        foreach( new \DirectoryIterator($patchDir) as $patch) {
            if( $patch->isDot() || $patch->isDir() ) {
                continue;
            }

            $pathInfo = pathinfo($patch);
            if( 'patch' !== $pathInfo['extension']) {
                continue;
            }

            exec("git apply {$patchDir}/{$patch} 2> /dev/null");
        }
    }
}
