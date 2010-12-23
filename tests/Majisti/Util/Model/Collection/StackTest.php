<?php

namespace Majisti\Util\Model\Collection;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Tests the Stack implementation
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class StackTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;
    
    private $_indexedElements;

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
        $stack = new Stack();
        $this->assertTrue($stack instanceof
        \Majisti\Util\Model\Collection\Stack,
        "Stack has to be an instance of Stack class.");
    }
    
    /**
     * @desc Tests constructs with one element
     */
    public function test__constructWithOneElement()
    {
        $element = 'foo';
        $stack = new Stack($element);
        $this->assertFalse($stack->isEmpty());
        $this->assertEquals(1, $stack->count(),
        'Stack has to contain only one element');
        $this->assertEquals($element, $stack->peek());
    }

    /**
     * @desc Tests construct with an array containing elements
     */
    public function test__constructWithArray()
    {
        $indexedArray = array('foo', 'bar');
        $stack = new Stack($indexedArray);
        $this->assertFalse($stack->isEmpty());
        $this->assertEquals(2, $stack->count(),
        'Stack has to contain only two elements');
        $this->assertEquals('bar', $stack->pop());
        $this->assertEquals('foo', $stack->pop());
    }
    
    /**
     * @desc Tests pop function
     */
    public function testPop()
    {
        $stack = new Stack($this->_indexedElements);
        $testingArraySize = sizeof($this->_indexedElements);
        $size = $stack->count();

        for($i = 0 ; $i < $testingArraySize ; $i++) {
            $element = $stack->pop();
            $this->assertNotNull($element);
            $this->assertEquals(--$size, $stack->count());
            $this->assertNotContains($element, $stack);
            $this->assertEquals($testingArraySize - $i, $element);
        }
    }
    
    /**
     * @desc Tests peek function
     */
    public function testPeek()
    {
        $stack = new Stack($this->_indexedElements);
        
        $this->assertEquals('4', $stack->peek());
        $this->assertEquals(4, $stack->count());

        $stack->pop();
        $stack->pop();
        $this->assertEquals('2', $stack->peek());
        $this->assertEquals(2, $stack->count());
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
    }

    /**
     * @desc Tests push function
     */
    public function testPush()
    {
        $stack = new Stack($this->_indexedElements);

        $stack->push('foo');
        $this->assertEquals(5, $stack->count());
        $this->assertNotNull($stack->peek());
        $this->assertEquals('foo', $stack->peek());

        $array = array('6', '7', '8', '9', '10');
        $stack->push($array);
        $this->assertEquals(10, $stack->count());
        $this->assertNotNull($stack->peek());
        $this->assertEquals('10', $stack->pop());
        $this->assertEquals('9', $stack->pop());
        $this->assertEquals('8', $stack->pop());
        $this->assertEquals('7', $stack->pop());
        $this->assertEquals('6', $stack->pop());
        $this->assertEquals('foo', $stack->pop());
    }

    /**
     * @desc Tests the toArray function
     */
    public function testToArray()
    {
       $stack = new Stack($this->_indexedElements);
       $array = $stack->toArray();

       $this->assertNotNull($array);
       $this->assertEquals(4, sizeof($array));

        $value = 1;
       foreach ($array as $element) {
           $this->assertEquals($value, $element);
           $value++;
        }
    }

    /**
     * @desc Tests for the clear function
     */
    public function testClear()
    {
        $stack = new Stack($this->_indexedElements);
        $stack->clear();

        $this->assertEquals(0, ($stack->count()));
    }

    /**
     * @desc Tests for the search function
     */
    public function testSearch()
    {
        $stack = new Stack($this->_indexedElements);

        $this->assertEquals(3, $stack->search('4'));
        $this->assertEquals(2, $stack->search('3'));
        $this->assertEquals(1, $stack->search('2'));
        $this->assertEquals(0, $stack->search('1'));
    }
}

StackTest::runAlone();
