<?php

namespace MajistiX\Extensions\InPlaceEditing\Model;

interface IInPlaceStorage
{
    public function getContent($key, $locale);
    public function editContent($key, $content, $locale);
}
