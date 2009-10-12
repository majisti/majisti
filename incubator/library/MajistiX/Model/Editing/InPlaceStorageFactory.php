<?php

namespace MajistiX\Model\Editing;

class InPlaceStorageFactory
{
    static public function createStorage($name, array $params = array())
    {
        $className = __NAMESPACE__ . '\InPlace' . ucfirst((string)$name) . 'Storage';
        
        if( !class_exists($className) ) {
            throw new Exception("Adapter {$className} not found");
        }
        
        return new $className($params);
    }
}
