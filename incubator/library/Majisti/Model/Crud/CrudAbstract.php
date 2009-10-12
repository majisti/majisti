<?php

namespace Majisti\Model\Crud;

class CrudAbstract implements ICrud
{
    public function setReadOnly()
    {
        
    }
    
    /**
     * Creates an element (adds the element to the CRUD's container)
     * 
     * @param element The element to create (add)
     */
    public function create($element)
    {
        
    }
    
    /**
     * Reads the element in the CRUD's container
     * 
     * @param index The index to search
     * 
     * @return The element
     */
    public function read($index)
    {
        \Zend_Debug::dump($index);
    }
    
    /**
     * Updates a CRUD element
     * 
     * @param oldElement The old element to replace
     * @param newElement The new element to replace the old one with
     */
    public function update($oldElement, $newElement)
    {
        
    }
    
    /**
     * Deletes a CRUD element
     * 
     * @param index The index where the CRUD element is stored
     * 
     * @return The deleted CRUD element
     */
    public function delete($index)
    {
        
    }
}