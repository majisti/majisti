<?php

namespace MajistiX\Model\Editing;

interface IInPlaceStorage
{
    public function getContent($key, $locale);
    public function editContent($key, $content, $locale);
}
