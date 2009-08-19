<?php

/*
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . str_repeat('..'.DIRECTORY_SEPARATOR, 1) . 'TestHelper.php';

/**
 * Test the Majisti_Scheduler
 *
 * @lastmodified 2009-03-27
 *
 * @author Yanick Rochon
 * @version 1
 */
class Majisti_SchedulerTest extends Majisti_Test_PHPUnit_TestCase
{

	/**
	 * @var Majisti_Scheduler 
	 */
	private $_scheduler;
	
	public function setUp() {
		set_time_limit(30); // 30 seconds for each tests

		// all tests use YYYY-MM-DD
		$locale = new Zend_Locale('fr');
		Zend_Registry::set('Zend_Locale', $locale);
		
		$this->_scheduler = Majisti_Scheduler::factory('SchedulerBackend_Mock',
			array(
			
			)
		);
	}

	public function testLimit() {
		
		for ($i=0; $i<100; $i++) {
			$this->_scheduler->setLimit($i);
			$this->assertEquals($i, $this->_scheduler->getLimit() );
		}
		
		// try to set an invalid limit (negative)
		try {
			$this->_scheduler->setLimit(-1);
		} catch (Exception $e) {
			/* ignore errors */
		}
		$this->assertNotEquals(-1, $this->_scheduler->getLimit() );
		
	}
	
	public function testTime() {
		
		$defaultTime = new Majisti_Date( $_SERVER['REQUEST_TIME'] );
		
		// consistency checks....
		for ($i=0; $i<100; $i++) {
			$this->assertEquals( $defaultTime, $this->_scheduler->getTime() );
		}
		
		$time = new Majisti_Date();
		$this->_scheduler->setTime( $time );
		// should not be the same...
		$this->assertNotSame( $time, $this->_scheduler->getTime() );
		// ... but equsls... with consistency checks....
		for ($i=0; $i<100; $i++) {
			$this->assertEquals( $time, $this->_scheduler->getTime() );
		}
		
		
	}
	
	public function testTasks() {

		$task1 = new SchedulerTask_Mock(array('name' => 'Test 1'));
		$this->_scheduler->addTask($task1);
		$this->assertEquals(1, count($this->_scheduler->getTasks()) );
		
		// cannot add the same task twice
		$this->_scheduler->addTask($task1);
		$this->assertEquals(1, count($this->_scheduler->getTasks()) );
		$this->_scheduler->removeTask($task1);
		$this->assertEquals(0, count($this->_scheduler->getTasks()) );

		// retry...
		$this->_scheduler->addTask($task1);
		$this->assertEquals(1, count($this->_scheduler->getTasks()) );
		// remove it by name instead
		$this->_scheduler->removeTask($task1->getName());
		$this->assertEquals(0, count($this->_scheduler->getTasks()) );
		
		// to to add two different task with the same name
		$task2 = new SchedulerTask_Mock(array('name' => 'Test 1'));
		$this->_scheduler->addTask($task1);
		$this->_scheduler->addTask($task2);
		$this->assertEquals(2, count($this->_scheduler->getTasks()) );
		// removing tasks one by one
		$this->_scheduler->removeTask($task1);
		$this->assertEquals(1, count($this->_scheduler->getTasks()) );
		// the remaining task is $task2
		$remainingTasks = $this->_scheduler->getTasks();
		$this->assertSame($task2, reset($remainingTasks));
		$this->_scheduler->removeTask($task2);
		$this->assertEquals(0, count($this->_scheduler->getTasks()) );
		// retry...
		$this->_scheduler->addTask($task1);
		$this->_scheduler->addTask($task2);
		$this->assertEquals(2, count($this->_scheduler->getTasks()) );
		// removing all tasks at once by name
		$this->_scheduler->removeTask('Test 1');
		$this->assertEquals(0, count($this->_scheduler->getTasks()) );
		
		$tasks = array();
		for ($i=0; $i<100; $i++) {
			$tasks[] = new SchedulerTask_Mock(array('name' => 'Task #' . $i));
		}
		$this->_scheduler->addTasks($tasks);
		$this->assertEquals(count($tasks), count($this->_scheduler->getTasks()) );
		$this->_scheduler->clearTasks();
		$this->assertEquals(0, count($this->_scheduler->getTasks()) );
		
		// an array with duplicates...
		for ($i=0; $i<200; $i++) {
			$tasks[] = $tasks[ mt_rand(0, 99) ];
		}
		$this->assertEquals(300, count($tasks) );
		$this->_scheduler->addTasks($tasks);
		$this->assertEquals(100, count($this->_scheduler->getTasks()) );
		$this->_scheduler->removeTasks($tasks);  // remove all
		$this->assertEquals(0, count($this->_scheduler->getTasks()) );
		
	}
	
	public function testRun() {
		
		$time = new Majisti_Date();
		$this->assertEquals(array(), $this->_scheduler->run() );
		
		// add a test to the scheduler
		$this->_scheduler->addTask( new SchedulerTask_Mock(array('name' => 'Test 1')) );
		$this->_scheduler->setTime($time);
		$this->assertEquals(array('Test 1' => 'foo'), $this->_scheduler->run() );
		
		// the task's last run should be today's
		$this->assertEquals( array($time), $this->_scheduler->getTask('Test 1')->getLastRuns() );
		
		
	}
	
	public function testMultipleRuns() {
		set_time_limit(60);  // this test MAY be long...
		
		$tasks = array();
		for ($i=0; $i<37; $i++) {
			// daily tasks with rules, so we don't repeat them twice
			$task = new SchedulerTask_Mock(array('name' => 'Task #' . $i));
			$task->addRule( new Majisti_Scheduler_Task_Rule(array(
				'type' => Majisti_Scheduler_Task_Rule::RULE_DAILY
			)));
			$tasks[] = $task; 
		}
		$this->_scheduler->clearTasks();
		$this->_scheduler->addTasks($tasks);
		$this->_scheduler->setLimit( 5 );
		
		for ($i=0; $i< (int) (37 / 5); $i++) {
			$responses = $this->_scheduler->run();
			$this->assertEquals( $this->_scheduler->getLimit(), count($responses) );
		}
		// remaining...
		$responses = $this->_scheduler->run();
		$this->assertEquals( 2, count($responses) );
		
	}
	
	/**
	 * Run some tasks, then see how long we can run them again...
	 */
	public function testRunIntervalMonthly() {
		
		$task1 = new SchedulerTask_Mock(array('name' => 'Task 1'));
		$task1->addRule( new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_MONTHLY,
			'interval' => 6		
		)));
		$task1->addLastRun( new Majisti_Date('2009-01-01') );
		$this->_scheduler->addTask( $task1 );
		
		$task2 = new SchedulerTask_Mock(array('name' => 'Task 2'));
		$task2->addRule( new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_MONTHLY,
			'interval' => 6		
		)));
		$task2->addLastRun( new Majisti_Date('2009-02-01') );
		$this->_scheduler->addTask( $task2 );
		
		$task3 = new SchedulerTask_Mock(array('name' => 'Task 3'));
		$task3->addRule( new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_MONTHLY,
			'interval' => 6		
		)));
		$task3->addLastRun( new Majisti_Date('2009-03-01') );
		$this->_scheduler->addTask( $task3 );
		
		$tests = array(
			1 => array('Task 1'),
			2 => array('Task 2'),
			3 => array('Task 3'),
			4 => array(),
			5 => array(),
			6 => array(),
			7 => array('Task 1'),
			8 => array('Task 2'),
			9 => array('Task 3'),
			10 => array(),
			11 => array(),
			12 => array(),
		);
		$time = new Majisti_Date('2009-04-01');
		
		// 2 years monthly test...
		for ($i=0; $i<24; $i++) {
			//echo "Testing : " . $time->toString() . '...';
			$this->_scheduler->setTime( $time );
			$response = $this->_scheduler->run();
			$expectedKeys = $tests[(int) $time->get(Majisti_Date::MONTH)];
			$this->assertEquals( count($expectedKeys), count($response) );
			$this->assertEquals( $expectedKeys, array_keys($response) );
			
			$time->addMonth( 1 );
		}
		
	}
	

	/**
	 * Run some tasks, then see how long we can run them again...
	 */
	public function testRunIntervalMonthlyDelayed() {
		
		$task1 = new SchedulerTask_Mock(array('name' => 'Task 1'));
		$task1->addRule( new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_MONTHLY,
			'interval' => 6		
		)));
		$task1->addLastRun( new Majisti_Date('2009-01-01') );
		$this->_scheduler->addTask( $task1 );
		
		$task2 = new SchedulerTask_Mock(array('name' => 'Task 2'));
		$task2->addRule( new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_MONTHLY,
			'interval' => 6		
		)));
		$task2->addLastRun( new Majisti_Date('2009-02-01') );
		$this->_scheduler->addTask( $task2 );
		
		$task3 = new SchedulerTask_Mock(array('name' => 'Task 3'));
		$task3->addRule( new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_MONTHLY,
			'interval' => 6		
		)));
		$task3->addLastRun( new Majisti_Date('2009-03-01') );
		$this->_scheduler->addTask( $task3 );
		
		$time = new Majisti_Date('2009-10-01');
		
		//echo "Testing : " . $time->toString() . '...';
		$this->_scheduler->setTime( $time );
		$response = $this->_scheduler->run();
		$responseKeys = array_keys($response);
		sort( $responseKeys );
		$this->assertEquals( 3, count($response) );
		$this->assertEquals( array('Task 1', 'Task 2', 'Task 3'), $responseKeys );
		
	}
	
	public function testDuplicateNameTask() {
		
		$task1 = new SchedulerTask_Mock(array('name' => 'Test 1'));
		$task2 = new SchedulerTask_Mock(array('name' => 'Test 1'));
		
		$this->_scheduler->addTask($task1);
		$this->_scheduler->addTask($task2);
		$this->assertEquals( 2, count($this->_scheduler->getTasks()) );
		
		$responses = $this->_scheduler->run();
		
		$this->assertEquals(array('Test 1' => array('foo', 'foo')), $responses);
		
	}
	
	
}

class SchedulerTask_Mock extends Majisti_Scheduler_Task_Abstract {
	public function run() {
		return 'foo';
	}
}

class SchedulerBackend_Mock extends Majisti_Scheduler_Backend_Abstract 
{
	public function load() {
		return array();
	}
	public function save($tasks) {
		
	}
}

Majisti_Test_Runner::run('Majisti_SchedulerTest');
