<?php

namespace Majisti\Model\Storage;

abstract class StorageAdapter implements IStorage
{
    public function create(array $args)
    {
        throw new \Majisti\Util\Exception\UnsupportedOperationException();
    }
    
    public function read(array $args)
    {
        throw new \Majisti\Util\Exception\UnsupportedOperationException();
    }
    
    public function has(array $args)
    {
        throw new \Majisti\Util\Exception\UnsupportedOperationException();
    }
    
    public function update(array $args)
    {
        throw new \Majisti\Util\Exception\UnsupportedOperationException();
    }
    
    public function upcreate(array $args)
    {
        throw new \Majisti\Util\Exception\UnsupportedOperationException();
    }
    
    public function delete(array $args)
    {
        throw new \Majisti\Util\Exception\UnsupportedOperationException();
    }
} 
