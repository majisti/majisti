<?php

namespace Majisti\Model\Crud;

/**
 * This class represents a CRUD which will be used
 * by most lists in this application<br><br>
 * 
 * The CRUD will be an iterable, serializable, cloneable and observable model<br><br>
 * 
 * Note that except for the provided methods, this Crud is read-only
 * meaning that getting it's container will return a clone<br><br>
 * 
 * Based on Observer, Iterator, CRUD patterns
 * 
 * @author Steven Rosato
 *
 * @param <E> The generic element this CRUD is holding
 * @param <T> The class type this crud is representing
 */
class Crud extends CrudAbstract
{
    /**
     * @var Array
     */
    protected $_container = array();
    
    /**
     * Removes all of the elements from this Crud.
     */
    public function clear()
    {
        $this->_container = array();
    }
    
    public function create($element)
    {
        array_push($this->_container, $element);
    }
    
    public function createAll($crud)
    {
//        this.container.addAll(crud.getContainer());
    }
    
    public function delete($element) 
    {
//        boolean removed = this.container.remove(element);
//        this.setChanged();
//        this.notifyObservers();
//        
//        return removed;
    }
    
    public function delete($index) 
    {
//        E removedElement = this.container.remove(index);
//        this.setChanged();
//        this.notifyObservers();
//        
//        return removedElement;
    }
    
    public function getContainer()
    {
//        Vector<E> newVector = new Vector<E>();
//        
//        for (E e : this.container) {
//            newVector.add(e);
//        }
//        
//        return newVector;
    }

    public function has($element)
    {
//        return this.indexOf(element) >= 0;
    }

    public function indexOf($element)
    {
//        return this.container.indexOf(element);
    }

    public function iterator() 
    {
//        return this.container.iterator();
    }
    
    public function isEmpty()
    {
//        return 0 == size();
    }

    public function read($index)
    {
//        return this.container.get(index);
    }

    /**
     * @return The size of the CRUD
     */
    public function size()
    {
        return container.size();
    }
    
    public function update($element) 
    {
//        this.container.set(this.indexOf(element), element);
//        this.setChanged();
//        this.notifyObservers();
    }
    
    public function update($index, $element)
    {
//        this.container.set(index, element);
//        this.setChanged();
//        this.notifyObservers();
    }

    public function update($oldElement, $newElement)
    {
//        update(this.indexOf(oldElement), newElement);
    }
}
