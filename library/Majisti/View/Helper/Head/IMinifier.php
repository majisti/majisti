<?php

namespace Majisti\View\Helper\Head;

/**
 * @desc The Bundable interface specifies whether bundling should be enabled
 * and used for a header tag (such as link and script files). This is
 * particularly useful in production when files can be packed up in one
 * master file.
 *
 * @author Majisti
 */
interface IMinifier
{
    /**
     * @desc Bundle the files together
     */
    public function minify($header, $path, $url);
}