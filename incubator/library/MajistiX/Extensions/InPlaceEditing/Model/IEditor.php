<?php

namespace MajistiX\Extensions\InPlaceEditing\Model;

interface IEditor
{
    public function render($content, array $params = array());
}