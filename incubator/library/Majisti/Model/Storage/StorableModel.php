<?php

namespace Majisti\Model\Storage;

class StorableModel implements IStorable
{
    protected $_genericStorage = 'IStorage';
    protected $_storageModel;
    
    public function getGenericStorage()
    {
        return $this->_genericStorage;
    }
    
    public function getStorageModel()
    {
        return $this->_storageModel;
    }
    
    public function setStorageModel($storageModel)
    {
        if( !is_a($storageModel, $this->getGenericStorage()) ) {
            throw new Exception('StorageModel of type [' . get_class($storageModel) . 
                ' must comply to the generic type [' . $this->getGenericStorage() . ']');
        }
        
        $this->_storageModel = $storageModel;
    }
}