<?php

namespace MajistiX\Model\Editing;

interface IEditor
{
    public function render($content, array $params = array());
}