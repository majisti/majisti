<?php

namespace Majisti;

final class Version
{
    /**
     * Majisti Framework version identification - see compareVersion()
     */
    const VERSION = '0.4.0alpha2';

    /**
     * Compare the specified Majisti Framework version string $version
     * with the current Majisti\Version::VERSION of Majisti Framework.
     *
     * @param  string  $version  A version string (e.g. "0.7.1").
     * @return boolean           -1 if the $version is older,
     *                           0 if they are the same,
     *                           and +1 if $version is newer.
     *
     */
    public static function compareVersion($version)
    {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        return version_compare($version, strtolower(self::VERSION));
    }
}
