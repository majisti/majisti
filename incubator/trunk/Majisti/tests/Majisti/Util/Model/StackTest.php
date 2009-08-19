<?php

namespace Majisti\Util\Model;

require_once 'TestHelper.php';

/**
 * @desc Tests the Stack implementation
 * @author Steven Rosato
 */
class StackTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    private $_indexedElement;

    /**
     * @desc Setups the test case.
     */
    public function setUp()
    {
        $this->_indexedElements = array('1', '2', '3', '4');
    }

    /**
     * @desc Tests constructs with no elements
     */
    public function test__construct()
    {
        new Stack();
        $this->markTestIncomplete();  
    }
    
    /**
     * @desc Tests constructs with one element
     */
    public function test__constructWithOneElement()
    {
        $element = 'foo';
        new Stack($element);
        $this->markTestIncomplete();  
    }

    /**
     * @desc Tests construct with an array containing elements
     */
    public function test__constructWithArray()
    {
        $indexedArray = array('foo', 'bar');
        new Stack($indexedArray);
        $this->markTestIncomplete();  
    }
    
    /**
     * Wdesc Tests pop function
     */
    public function testPop()
    {
        $stack = new Stack($this->_indexedElements);
        $size = $stack->count();
        
        $element = $stack->pop();
        
        $this->markTestSkipped();
        
        $this->assertNotNull($element);
        $this->assertEquals($size - 1, $stack->count());
        $this->assertNotContains($element, $stack);
        $this->markTestIncomplete();  
    }
    
    /**
     * @desc Tests peek function
     */
    public function testPeek()
    {
        $stack = new Stack($this->_indexedElements);
        
        $this->assertEquals('4', $stack->peek());
        $this->assertEquals(4, $stack->count());
        $this->markTestIncomplete();  
    }
    
    /**
     * @desc Tests that the returned iterator should
     * iterate in a reverse order, which means from the
     * last element to the first one
     */
    public function testGetIterator()
    {
        $stack = new Stack($this->_indexedElements);
        
        $iterator = $stack->getIterator();
        
        $this->assertEquals('4', $iterator->current());
        
        $wentInLoop = false;
        foreach ($stack as $element) {
            $wentInLoop = true;
        }
        $this->assertTrue($wentInLoop, 'Stack never looped on foreach');
        
        for ($i = 4 ; $i > 0 ; $i--) {
        	$this->assertEquals($i, $iterator->current());
        	$iterator->next();
        }
        
        $this->assertNull($iterator->current());
        $this->markTestIncomplete();  
    }
}

StackTest::runAlone();
