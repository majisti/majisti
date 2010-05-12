<?php

interface ICompressor
{
    public function compressJs($content);

    public function compressCss($content);
}