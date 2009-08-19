<?php

/**
 *
 * @author Yanick Rochon
 */
abstract class Majisti_Scheduler_Task_Abstract implements Majisti_Scheduler_Task 
{
	
	const LOW_PRIORITY = -10;
	const DEFAULT_PRIORITY = 0;
	const HIGH_PRIORITY = 10;
		
	/** @var array */
	private $_lastRuns;
	/** @var bool */
	private $_lastRunsSorted;
	/** @var array */
	private $_rules;
	/** @var Majisti_Date */
	private $_time;
	/** @var string */
	private $_name;
	/** @var int */
	private $_priority;
	/** @var mixed **/
	private $_data;
	
	
	/**
	 * Construct a new task. The $options shoudl be an array
	 * with at least one parameter: name. The name of the task
	 * must be specified in the options array.
	 *
	 * @param array $options
	 */
	public function __construct(array $options) {
		
		if ( !isset($options['name']) || empty($options['name']) ) {
			throw new Majisti_Scheduler_Exception('empty or no name specified');
		} else {
			$this->_name = $options['name'];
			unset($options['name']);	
		}
		
		if ( isset($options['rules']) ) {
			$this->addRules( $options['rules'] );
			unset($options['rules']);
		}
		
		if ( isset($options['lastruns']) ) {
			foreach ($options['lastruns'] as $lastRun) {
				$this->addLastRun($lastRun);
			}
			unset($options['lastruns']);
		}
		
		if ( isset($options['priority']) ) {
			$this->_priority = (int) $options['priority'];
			
			unset($options['priority']);
		} else {
			$this->_priority = self::DEFAULT_PRIORITY;
		}
		
		if ( isset($options['data']) ) {
			$this->_data = $options['data'];
			unset($options['data']);
		}
	}
	
	/**
   * Add a run to the list of task runs
   * 
   * @param int|string|array|Zend_Date $lartRun
   * @return Majisti_Scheduler_Task_Abstract
   */
	public function addLastRun($lastRun) {
		if ( null === $this->_lastRuns ) {
			$this->_lastRuns = array();	
		}
		$this->_lastRuns[] = new Majisti_Date($lastRun);
		$this->_lastRunsSorted = false;  // invalidate the last runs sorting
		
		return $this;
	}
	
	/**
	 * Add all rules to the task
	 *
	 * @param array $rules
	 * @return Majisti_Scheduler_Task_Abstract
	 */
	public function addRules($rules) {
		foreach ($rules as $rule) {
			$this->addRule($rule);
		}
		return $this;
	}
	
	/**
	 * Add a rule to the task.
	 *
	 * @param Majisti_Scheduler_Task_Rule $rule
	 * @return Majisti_Scheduler_Task_Abstract 
	 */
	public function addRule($rule) {
		if ( !($rule instanceof Majisti_Scheduler_Task_Rule) ) {
			throw new Majisti_Scheduler_Exception('invalid rule');
		}
		
		if ( null === $this->_rules ) {
			$this->_rules = array();
		}
		if ( false === array_search($rule, $this->_rules) ) {
			$this->_rules[] = $rule;
		}
		return $this;
	}
	
	/**
	 * Determine if the specified task is scheduled to be
	 * executed since the last run
	 * 
	 * @return bool
	 */
	public function isScheduled() {
		$scheduled = true;
		$time = $this->getTime();
		$lastRuns = $this->getLastRuns();
		
		foreach ($this->getRules() as $rule) {
			$rule->setTime($time);
			if ( !$rule->isSatisfied($lastRuns) ) {
				$scheduled = false;
				break;
			}
		}
		return $scheduled;		
	}
	
	/**
	 * Retrieve the data associated with this task
	 *
	 * @return mixed
	 */
	public function getData() {
		return $this->_data;
	}
	
	/**
	 * Return all the last run dates of this task. The function
	 * returns a sorted array of Majisti_Date where the first
	 * index is the latest run (last) 
	 * 
	 * @return array
	 */
	public function getLastRuns() {
		if ( null === $this->_lastRuns ) {
			$this->_lastRuns = array();	
		} else if ( !$this->_lastRunsSorted) {
			rsort($this->_lastRuns);  // latest run first
			$this->_lastRunsSorted = true;
		}
		
		return $this->_lastRuns;		
	}
	
	/**
   * Return the task name
   * 
   * @return string
   */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Return the task's priority. The priority may be any
	 * integer value.
	 *
	 * @return int
	 */
	public function getPriority() {
		return $this->_priority;
	}
	
	/**
	 * Return all rules associated with the task
	 *
	 * @return array
	 */
	public function getRules() {
		if ( null === $this->_rules ) {
			$this->_rules = array();
		}
		return $this->_rules;
	}
	
	/**
	 * Return the time to use when executing the task. If no time has
	 * been set with setTime(), today's time is returned.
	 *
	 * @return Majisti_Date
	 */
	public function getTime() {
		if ( null === $this->_time ) {
			return new Majisti_Date();
		} else {
			return clone $this->_time;
		}
	}
	
	/**
	 * Sets the task's priority.
	 *
	 * @param int $priority
	 * @return Majisti_Scheduler_Task_Abstract
	 */
	public function setPriority($priority) {
		$this->_priority = (int) $priority;
		return $this;
	}
	
	/**
	 * Remove all rules associated with the task
	 * 
	 * @return Majisti_Scheduler_Task_Abstract
	 */
	public function removeAllRules() {
		$this->_rules = array();
		return $this;
	}
	
	/**
	 * Remove all the specified rules from the task
	 *
	 * @param array $rules
	 * @return int
	 */
	public function removeRules($rules) {
		$count = 0;
		foreach ($rules as $rule) {
			$count += $this->removeRule($rule);
		}
		return $count;
	}
	
	/**
	 * Remove a rule associated to the task. Should return 1 on
	 * success and 0 on failure.
	 *
	 * @param unknown_type $rule
	 * @return int
	 */
	public function removeRule($rule) {
		if ( !($rule instanceof Majisti_Scheduler_Task_Rule) ) {
			throw new Majisti_Scheduler_Exception('invalid rule');
		}
		$count = 0;
		
		while ( false !== ($index = array_search($rule, $this->_rules))) {
			$count++;
			unset($this->_rules[$index]);
		}
		return $count;
	}
	
	/**
	 * Set the data associated with this task
	 * 
	 * @return Majisti_Scheduler_Task_Abstract
	 */
	public function setData($data) {
		$this->_data = $data;
		return $this;
	}
	
	/**
	 * Set the actual running time of the task. This method only
	 * sets the time to use when setting the next run date.
	 * 
	 * @see run()
	 *
	 * @param int|string|array|Zend_Date $time
	 */
	public function setTime($time) {
		$this->_time = new Majisti_Date($time);
		return $this;
	}
	
}