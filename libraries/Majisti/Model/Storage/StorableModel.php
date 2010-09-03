<?php

namespace Majisti\Model\Storage;

/**
 * @desc A storable model stores a storage model while providing generic
 * control to what type of storage that can be used.
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class StorableModel implements IStorable
{
    /** @var String */
    protected $_genericStorage = 'IStorage';
    
    /* generic storage model */
    protected $_storageModel;
    
    /**
     * @desc Constructs the storable model with the provided
     * storage model. The model must abide to the generic storage
     * set by this class.
     * 
     * @param $storageModel The storage model
     */
    public function __construct($storageModel)
    {
        $this->setStorageModel($storageModel);
    }
    
    /**
     * @see IStorable::getGenericStorage()
     */
    public function getGenericStorage()
    {
        return $this->_genericStorage;
    }
    
    /**
     * @see IStorable::getStorageModel()
     */
    public function getStorageModel()
    {
        return $this->_storageModel;
    }
    
    /**
     * @see IStorable::setStorageModel()
     */
    public function setStorageModel($storageModel)
    {
        /* storage model can't be null */
        if( null === $storageModel ) {
            throw new Exception("Storage can't be null");
        }
        
        /* storage model does not follow generic type */
        if( !is_a($storageModel, $this->getGenericStorage()) ) {
            throw new Exception('StorageModel of type [' . get_class($storageModel) . 
                ' must comply to the generic type [' . $this->getGenericStorage() . ']');
        }
        
        $this->_storageModel = $storageModel;
        
        return $this;
    }
}