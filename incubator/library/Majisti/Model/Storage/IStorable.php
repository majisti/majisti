<?php

namespace Majisti\Model\Storage;

interface IStorable
{
    public function getGenericStorage();
    public function getStorageModel();
    public function setStorageModel($storageModel);
}