<?php

namespace Majisti\Model\Storage;

/**
 * @desc Storage interface Based on CRUD pattern. A storage
 * provides means to access or save its stored data
 * via the CRUD principles: Create Read Update Delete.
 * Although read and write access should be provided at the same time,
 * the storage model can restrict its access to either writing
 * or reading, but not both. 
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface IStorage
{
    /**
     * @desc Creates an entry into this storage.
     * 
     * @param $args The args to store
     */
    public function create(array $args);
    
    /**
     * @desc Reads an entry from this storage
     * 
     * @param $args The args
     * 
     * @return The entry
     */
    public function read(array $args);
    
    /**
     * @desc Returns whether this storage has the provided entry value from
     * args key.
     * 
     * @param $args The args
     * 
     * @return True if this storage contains the provided entry value from
     * args key
     */
    public function has(array $args);
    
    /**
     * @desc Updates an entry according to the args given
     * 
     * @param $args The args
     */
    public function update(array $args);
    
    /**
     * @desc Updates an entry if it exists in the storage, create it if not.
     * 
     * @param $args The args
     */
    public function upcreate(array $args);
    
    /**
     * @desc Deletes an entry from the storage
     * 
     * @param $args The args
     */
    public function delete(array $args);
    
    /**
     * @desc Returns whether this storage is read only.
     * 
     * @return True if this storage is read only.
     */
    public function isReadOnly();
}