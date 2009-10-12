<?php

namespace Majisti\Model\Crud;

/**
 * A CRUD (Create Read Update Delete) is usually found in database manipulation
 * but can be easily converted to software models manipulation which require basically the
 * same thing. CRUDs ensure that a model is flexible as such as it can create, update and remove
 * it's presented elements.<br><br>
 * 
 * Concrete classes should be dealing with which container they use for storing the CRUD elements
 * by use of heritage or aggregation<br><br>
 * 
 * Based on CRUD Pattern [Creg Larmann]
 * 
 * @param <E> The generic element this CRUD will contain
 * 
 * @author Steven Rosato
 */
interface ICrud
{
    /**
     * Creates an element (adds the element to the CRUD's container)
     * 
     * @param element The element to create (add)
     */
    public function create($element);
    
    /**
     * Reads the element in the CRUD's container
     * 
     * @param index The index to search
     * 
     * @return The element
     */
    public function read($index);
    
    /**
     * Updates a CRUD element
     * 
     * @param oldElement The old element to replace
     * @param newElement The new element to replace the old one with
     */
    public function update($oldElement, $newElement);
    
    /**
     * Deletes a CRUD element
     * 
     * @param index The index where the CRUD element is stored
     * 
     * @return The deleted CRUD element
     */
    public function delete($index);
}
