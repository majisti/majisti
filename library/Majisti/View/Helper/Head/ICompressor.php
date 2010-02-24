<?php

namespace Majisti\View\Helper\Head;

/**
 * @desc Defines an interface for compressable meta
 *
 * @author Majisti
 */
interface ICompressor
{
    public function compress($path, $header = null);
}
