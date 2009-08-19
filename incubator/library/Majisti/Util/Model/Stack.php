<?php

namespace Majisti\Util\Model;

/**
 * @desc Implementation of a simple Stack in LIFO order. Even the
 * returned Iterator is reversed which means that iterating
 * with it starts up with the last element of this stack, which
 * is the last in.
 * 
 * @author Steven Rosato
 */
class Stack implements \IteratorAggregate, \Countable
{
    /**
     * @var Array
     */
    protected $_elements = array();
    
    /**
     * @desc Constructs the stack with the provided
     * elements, if any.
     * @param mixed|array $elements The elements to append to the stack.
     * Can either be an array or a mixed element. If an array is given
     * the last element contained in the array correspond to the last
     * element that will be added to that stack. Note also that array keys
     * are ignored.
     */
    public function __construct($elements = array())
    {
        $this->push($elements);
        end($this->_elements);
    }
    
    /**
     * @desc Returns the stack's iterator. Note that the iterator first
     * element will be the last element added to that stack (meaning
     * that the iterator reverses the element on instanciation).
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_reverse($this->_elements));
    }
    
    /**
     * @desc Pushes one or multiple elements to the top of stack.
     * @param mixed|array $elements Pushes an array or a single element
     * on the stack. If an array is given, the last element of that array
     * correspond to the last element added to this stack. Note also that
     * array keys are ignored.
     * @return Stack
     */
    public function push($elements)
    {
        if( is_array($elements) ) {
            foreach ($elements as $element) {
                $this->_elements[] = $element;
            }    
        } else {
            $this->_elements[] = $elements;
        }
        return $this;
    }
    
    /**
     * @desc Returns whether this stack is empty or not.
     * @return bool true is the stack is empty
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }
    
    /**
     * @desc Returns the stack's count
     * @return int The stack's count
     */
    public function count()
    {
        return count($this->_elements);
    }
    
    /**
     * @desc Removes an element from the stack and returns it.
     * @return The popped element, if the stack is not empty.
     */    
    public function pop()
    {
        return array_pop($this->_elements);
    }
    
    /**
     * @desc Peeks at the top of the stack, returning the element
     * but not removing it from the stack.
     * @return The peeked element.
     */
    public function peek()
    {
        return $this->_elements[$this->count() - 1];
    }
}