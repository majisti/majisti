<?php

namespace Majisti\View\Helper\Head;

/**
 * @desc The ICompressor interface specifies whether bundling should be enabled
 * and used for a header tag (such as link and script files). This is
 * particularly useful in production when files can be packed up in one
 * master file.
 *
 * @author Majisti
 */
interface IOptimizer
{
    public function optimize($path, $url);

    /**
     * @desc Bundle the files together
     */
    public function bundle($path, $url);

    /*
     * @desc Minifies the files contained in the header
     */
    public function minify($cacheNamespace);
}