<?php

namespace Majisti\Model\Storage;

/**
 * @desc Adapter for IStorage interface. Class methods do nothing
 * unless overriden by child class. Based on Null Object pattern.
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
abstract class StorageAbstract implements IStorage
{
    /** @var boolean */
    protected $_readOnly = false;
    
    /**
     * @see IStorage::create() 
     */
    public function create(array $args)
    {
        
    }
    
    /**
     * @see IStorage::read()
     */
    public function read(array $args)
    {
        return null;
    }
    
    /**
     * @see IStorage::has()
     */
    public function has(array $args)
    {
        return false;
    }
    
    /**
     * @see IStorage::update()
     */
    public function update(array $args)
    {
        
    }
    
    /**
     * @see IStorage::upcreate() 
     */
    public function upcreate(array $args)
    {
        
    }
    
    /**
     * @see IStorage::delete()
     */
    public function delete(array $args)
    {
        
    }
    
    /**
     * @see IStorage::setReadOnly()
     */
    public function setReadOnly($readOnly = true)
    {
        $this->_readOnly = $readOnly;
    }
    
    /**
     * @see IStorage::isReadOnly 
     */
    public function isReadOnly()
    {
        return $this->_readOnly;
    }
} 
