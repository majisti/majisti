<?php

/** Majisti_Scheduler_Backend_Abstract */
require_once 'Majisti/Scheduler/Backend/Abstract.php';

/** Majisti_Scheduler_Task */
require_once 'Majisti/Scheduler/Task.php';

class Majisti_Scheduler {
	
	/** @var string */
	static private $_backendPrefix = 'Majisti_Scheduler_Backend';
	
	/**
	 * Create a new Scheduler based on a backend
	 *
	 * @param string|Majisti_Scheduler_Backend_Abstract $backend
	 * @param array $options OPTIONAL
	 * 
	 * @return Majisti_Scheduler
	 */
	public static function factory($backend, array $options = array()) {
		if (is_string( $backend )) {
			$backendName = ucfirst( strtolower( $backend ) );

			$class = self::$_backendPrefix . '_' . $backendName;
			if ( @class_exists($class, true) ) {
				$backend = new $class( $options );
			} else {
				try { // to load user-implemented backend interface
					$backend = new $backend( $options );
				} catch ( Exception $e ) {
					throw new Majisti_Scheduler_Exception('Invalid backend : ' . $e->getMessage() );
				}
			}
		} // $backend is now an object
		
		if (! $backend instanceof Majisti_Scheduler_Backend_Abstract) {
			throw new Majisti_Scheduler_Exception( 'Backend must extend Majisti_Scheduler_Backend_Abstract' );
		}
		
		return new Majisti_Scheduler($backend);
	}
	
	
	/** @var Majisti_Date Request time */
	protected $_time = null;
	
	/** @var array Tasks */
	protected $_tasks = array();
	
	/** @var int Maximum allowable tasks to run on a given request */
	protected $_limit = 0;
	
	/** @var Majisti_Scheduler_Backend_Abstract Backend */
	protected $_backend = null;
	
	/** @var Majisti_Util_Comparator */
	private $_comparator;
	
	/**
	 * Constructor.
	 * 
	 * @param Majisti_Scheduler_Backend_Abstract|string $backend Backend or name of backend ('File', etc.)
	 * @param array $options Backend options
	 */
	private function __construct($backend) {
		$this->_backend = $backend;
		
		// Load queued tasks
		$this->_tasks = $this->_backend->load();
		
		$this->_comparator = new Majisti_Scheduler_Task_Comparator();
	}
	
	/**
	 * Cleanup
	 */
	public function __destruct() {
		$this->_backend->save($this->_tasks);
	}
	
	/**
	 * Adds multiple tasks. Useful in combination with caching an 
	 * array of tasks with Zend_Cache, for example.
	 *
	 * @param  array $tasks Array of tasks
	 * @return Majisti_Scheduler This instance
	 */
	public function addTasks(array $tasks) {
		foreach ( $tasks as $task ) {
			$this->addTask( $task );
		}
		return $this;
	}
	
	/**
	 * Adds a task.
	 *
	 * @param  string $name Task name
	 * @param  Majisti_Scheduler_Task $task
	 * @return Majisti_Scheduler This instance
	 */
	public function addTask($task) {
		if (! $task instanceof Majisti_Scheduler_Task) {
			throw new Majisti_Scheduler_Exception( 'not a valid task' );
		}
		if ( false === array_search($task, $this->_tasks, true) ) {
			$this->_tasks[] = $task;
		} 
		
		return $this;
	}
	
	/**
	 * Clear all tasks from the scheduler
	 *
	 * @return Majisti_Scheduler This instance 
	 */
	public function clearTasks() {
		$this->_tasks = array();
	}
	
	/**
	 * Checks if scheduler has a task.
	 *
	 * @param  string|Majisti_Scheduler_Task $task the task or task name to check 
	 * @return bool True if task by that name has been added
	 */
	public function hasTask($task) {
		return null !== $this->getTask($task);
	}
	
	/**
	 * Return the maximum tasks to execute at once. A value of 0 means unlimited.
	 *
	 * @return int
	 */
	public function getLimit() {
		return $this->_limit;
	}
	
	/**
	 * Return a task object. The method works that if $task is
	 * a task object and exists in this instance, the returned
	 * value will be $task itself. If $task is the name of an
	 * existing task in this instance, the task object is returned.
	 * IF the task does not exists, the method returns null.
	 * 
	 * If $task is a task name and there are more than one task with
	 * the same name, then all the task will be returned in the form
	 * of an array.
	 *
	 * @param string|Majisti_Scheduler_Task $task
	 * @return null|array|Majisti_Scheduler_Task
	 */
	public function getTask($task) {
		$taskFound = null;
		if ( $task instanceof Majisti_Scheduler_Task ) {
			if ( false !== ($index = array_search($task, $this->_tasks, true)) ) {
				$taskFound = $this->_tasks[$index];	
			}
		} else {
			foreach ($this->_tasks as $t) {
				if ( $t->getName() == $task ) {
					if ( !empty($taskFound) ) {
						if ( !is_array($taskFound) ) {
							$taskFound = array($taskFound);
						}
						$taskFound[] = $t;
					} else {
						$taskFound = $t;
					}
				}
			}
		}
		
		return $taskFound;
	}
	
	/**
	 * Return all tasks in this scheduler
	 *
	 * @return array
	 */
	public function getTasks() {
		return $this->_tasks;
	}
	
	/**
	 * Get the preset time for the scheduler. If no such time
	 * is set, the time returned is the server's request time stamp
	 *
	 * @return Majisti_Date
	 */
	public function getTime() {
		if ( empty( $this->_time ) ) {
			return new Majisti_Date( $_SERVER['REQUEST_TIME'] );
		}
		return $this->_time;
	}
	
	/**
	 * Removes a task.
	 *
	 * @param string|Majisti_Scheduler_Task $name Task name
	 * @return Majisti_Scheduler This instance
	 */
	public function removeTask($task) {
		if ( $task instanceof Majisti_Scheduler_Task ) {
			if ( false !== ($index = array_search($task, $this->_tasks)) ) {
				unset($this->_tasks[$index]);	
			}
		} else {
			foreach ($this->_tasks as $index => $t) {
				if ( $t->getName() == $task ) {
					unset($this->_tasks[$index]);	
				}
			}
		}
		return $this;
	}
	
	/**
	 * Remove all specified tasks.
	 *
	 * @param array $tasks
	 * @return Majisti_Scheduler This instance 
	 */
	public function removeTasks(array $tasks) {
		foreach ($tasks as $task) {
			$this->removeTask($task);
		}
		return $this;
	}
	
	/**
	 * Executes all scheduled tasks, or until the set limit. The returned
	 * value is an array of all the response from the executed tasks. If
	 * there is no task to execute, the method returns an empty array 
	 *
	 * @return array Array of Response objects
	 */
	public function run() {
		if (empty( $this->_tasks )) {
			return array();
		}
		
		$time = $this->getTime();
		$tasks = array();
		$responses = array();
		$completed = 0;

		// get all tasks by last run date, discard all tasks that cannot
		// bu run and sort them in ascending order
		foreach ( $this->_tasks as $index => $task ) {
			$task->setTime( $time );
			if ($task->isScheduled()) {
				// get last run
				$lastRuns = $task->getLastRuns();
				if ( empty($lastRuns) ) {
					$tasks[$index] = null;
				} else {
					$lastRun = reset($lastRuns);
					$tasks[$index] = $lastRun->getIso();
				}
			}
		}
		
		uasort($this->_tasks, array($this->_comparator, 'compare')); // sort tasks

		// Add currently scheduled tasks to queue
		foreach ( $this->_tasks as $task ) {
			if ($task->isScheduled()) {
				if ( isset($responses[$task->getName()]) ) {
					if ( !is_array($responses[$task->getName()]) ) {
						$responses[$task->getName()] = array($responses[$task->getName()]);
					}
					$responses[$task->getName()][] = $task->run();
				} else {
					$responses[$task->getName()] = $task->run();
				}
				$task->addLastRun( $time );
				
				$completed ++;
				
				if ( ($this->_limit && $completed >= $this->_limit) ) {
					break;
				}
			}
		}
		
		return $responses;
	}
	
	/**
	 * Sets the maximum allowable tasks to run on a given request.  To allow 
	 * an unlimited number of tasks to run, set to 0. 
	 * 
	 * Note that if a limit is set and multiple tasks are to be executed with 
	 * the same last run time, the order of executed tasks will be unpredictable.
	 *
	 * @throws Majisti_Scheduler_Exception if negative
	 * @param  int $limit Task execution limit
	 * @return Majisti_Scheduler This instance
	 */
	public function setLimit($limit = 0) {
		if ( $limit < 0 ) {
			throw new Majisti_Scheduler_Exception('negative limit');
		}
		$this->_limit = (int) $limit;
		return $this;
	}
	
	/**
	 * Set the time (by default, the request time).  For testing purposes a
	 * different time can be passed in.
	 *
	 * @param  array|string|int|Majisti_Date $time
	 * @return Majisti_Scheduler This instance
	 */
	public function setTime($time = '') {
		$this->_time = new Majisti_Date( $time );
		return $this;
	}

}
