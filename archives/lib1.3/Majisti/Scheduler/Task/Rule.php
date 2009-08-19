<?php

/**
 * 
 * @author Yanick Rochon
 */
class Majisti_Scheduler_Task_Rule
{
	
	//static public $DEBUG = false;
	
	/** @var int dayly based task rule */
	const RULE_DAILY = 1;
	/** @var int weekly based task rule */
	const RULE_WEEKLY = 2;
	/** @var int monthly based task rule */
	const RULE_MONTHLY = 3;
	/** @var int yearly based task rule */
	const RULE_YEARLY = 4;
	
	/** @var bool inclusion constraint */
	const RULE_CONSTRAINT_ALLOWED = true;
	/** @var bool exclusion constraint */
	const RULE_CONSTRAINT_DENIED = false;
	
	/** @var array */
	static private $_partInterval = array(
		self::RULE_DAILY => Majisti_Date::DAY_OF_YEAR,
		self::RULE_WEEKLY => Majisti_Date::WEEK,
		self::RULE_MONTHLY => Majisti_Date::MONTH,
		self::RULE_YEARLY => Majisti_Date::YEAR
	);
	
	/** @var array */
	static private $_significantDateParts = array(
		Majisti_Date::YEAR,
		Majisti_Date::MONTH,	
		Majisti_Date::WEEK,
		Majisti_Date::DAY_OF_YEAR,
		Majisti_Date::DAY,
		Majisti_Date::WEEKDAY_DIGIT,
		Majisti_Date::HOUR,
		Majisti_Date::MINUTE,			
		Majisti_Date::SECOND
	);
	
	/** @var int the type of the task rule (see the RULE_xxxx constants) */
	private $_type;
	
	/** @var Majisti_Date */
	private $_start;
	
	/** @var Majisti_Date */
	private $_expires;
	
	/** @var int */
	private $_interval;
	
	/** @var Majisti_Date */
	private $_time;
	
	/** @var array */
	private $_constraints;
	/** @var array */
	private $_exConstraints;
	/** @var array */
	private $_cache;
	
	
	/**
	 * Construct a new task rule based on a time interval
	 * specified by one of the constant RULE_xxxx. The rule may also
	 * have specific options :
	 * 
	 *  'type' => int                         the time interval type (see RULE_xxxx)
	 *  'start' => string|int|Majisti_Date    the date/time when the first task should occure
	 *  'expire' => string|int|Majisti_Date   the date/time when the task should expire
	 *  'interval' => int                     the minimum 'gap' of time that should elapse since
	 *                                        the last task run. This value depends on $type
	 *                                        (0 = undefined)
	 *  
	 *
	 * @param array $options OPTIONAL
	 */
	public function __construct(array $options = array()) {
		
		if ( !isset($options['type']) ) {
			throw new Majisti_Scheduler_Task_Rule_Exception('undefined rule type');
		}

		$this->setOptions($options);
	}

	/**
	 * @desc
	 * Check if a given date is excluded from the exclusion constraints.
	 * The method returns TRUE if the date is valid, FALSE if it should be
	 * excluded
	 *
	 * @param Majisti_Date $time
	 * @return bool
	 */
	private function _checkExcelusionConstraint($time) {
		$included = true;  // by default, the value is included
		
		foreach ($this->_exConstraints as $exConstraint) {
			$partCount = 0;
			foreach ($exConstraint as $part => $value) {
				if ( $time->get($part) == $value ) {
					$partCount++;
				}
			}
			if ( $partCount == count($exConstraint) ) {
				$included = false;  // all exclusion constraints where found in $time, exclude it
				break;
			}
		}
		
		return $included;
	}
	
	/**
	 * Return the time limit of the interval given a specified time.
	 * The limit is defined by the $direction value, where a negative
	 * value will return the beginning of the interval, and 0 or a
	 * positive value will return the end of the interval. The $time
	 * is not specified, the current speicified time is used (see getTime())
	 *
	 * @param int $direction
	 * @param null|Majisti_Date $time OPTIONAL
	 * @return Majisti_Date
	 */
	private function _getIntervalLimit($direction, $time = null) {
		if ( null === $time ) {
			$limit = clone $this->getTime();
		} else {
			$limit = clone $time;
		}
		
		switch ($this->getType()) {
			case self::RULE_DAILY:
				if ( $direction >= 0 ) {
					$limit->setTime('23:59:59');
				} else {
					$limit->setTime('00:00:00');
				}
				break;
			case self::RULE_WEEKLY:
				if ( $direction >= 0 ) {
					$limit->set(6, Majisti_Date::WEEKDAY_DIGIT);
					$limit->setTime('23:59:59');
				} else {
					$limit->set(0, Majisti_Date::WEEKDAY_DIGIT);
					$limit->setTime('00:00:00');
				}
				break;
			case self::RULE_MONTHLY:
				$limit->setDay(1);
				$limit->setTime('00:00:00');
				if ( $direction >= 0 ) {
					$limit->addMonth(1);
					$limit->subSecond(1);
				} 
				break;
			case self::RULE_YEARLY:
				$year = $limit->get( Majisti_Date::YEAR );
				if ( $direction >= 0 ) {
					$limit->set( $year . '-12-31 23:59:59', Majisti_Date::ISO_8601 );
				} else {
					$limit->set( $year . '-01-01 00:00:00', Majisti_Date::ISO_8601 );
				}
				break;
		}
		return $limit;		
		
	}
	
	/**
	 * Returns the time at the end of the previous interval
	 *
	 * @param null|Majisti_Date $time OPTIONAL
	 * @return Majisti_Date
	 */
	private function _getLastIntervalLimit($time = null) {
		$endInterval = $this->_getIntervalLimit(-1, $time);
		
		switch ($this->getType()) {
			case self::RULE_DAILY:
				$endInterval->subDay( $this->_interval - 1 );
				break;
			case self::RULE_WEEKLY:
				$endInterval->subWeek( $this->_interval - 1 );
				break;
			case self::RULE_MONTHLY:
				// it's easier to set the beginning of the month and substract a second
				$endInterval->subMonth( $this->_interval - 1 );
				break;
			case self::RULE_YEARLY:
				$endInterval->subYear( $this->_interval - 1 );
				break;
		}
		$endInterval->subSecond(1);
		
		return $endInterval;		
	}
	
	/**
	 * Returns the time at the beginning of the next interval
	 *
	 * @param null|Majisti_Date $time OPTIONAL
	 * @return Majisti_Date
	 */
	private function _getNextIntervalLimit($time = null) {
		$startInterval = $this->_getIntervalLimit(1, $time);
				
		switch ($this->getType()) {
			case self::RULE_DAILY:
				$startInterval->addDay( $this->_interval );
				break;
			case self::RULE_WEEKLY:
				$startInterval->addWeek( $this->_interval );
				break;
			case self::RULE_MONTHLY:
				$startInterval->addMonth( $this->_interval );
				break;
			case self::RULE_YEARLY:
				$startInterval->addYear( $this->_interval );
				break;
		}
		return $startInterval;		
	}
	
	/**
	 * Returns an array of significant value to be added to the constraints array,
	 * or compared with an existing constraints. Use the list() php function to
	 * extract the value returned by this method : 
	 * 
	 *  list($allowed, $significantValue) = $this->_getSignificantConstraintValue(...);  
	 * 
	 * @param string|int|Zend_Date|array $constraint
	 * @return array
	 */
	private function _getSignificantConstraintValue($constraint) {
		$constraintrValue = array();
		$parts = self::$_significantDateParts;
		
		if ( is_array($constraint) ) {
			if ( isset($constraint['fields']) ) {
				$parts = array_intersect(
					array_change_key_case($parts, CASE_UPPER), 
					array_change_key_case($constraint['fields'], CASE_UPPER));
				unset($constraint['fields']);
			}
		
			if ( isset($constraint['date']) ) {
				$constraint = $constraint['date'];
			}
			
			if ( isset($constraint['allow'] )) {
				$allow = (bool) $constraint['allow'];
				unset($constraint['allow']);
			} else {
				$allow = true;
			}
		} else {
			$allow = true;
		}
		
		if ( $constraint instanceof Zend_Date ) {
			$constraint = $constraint->toArray();
		} else if ( !is_array($constraint) ) {
			throw new Majisti_Scheduler_Task_Rule_Exception('invalid constraint');
		} 
		
		$constraint = array_change_key_case($constraint, CASE_UPPER);
		
		foreach ($parts as $part) {
			if ( isset($constraint[$part]) ) {
				$constraintrValue[$part] = $constraint[$part];
			}
		}		
		
		return array($allow, $constraintrValue);
	}
	
	/**
   * Determine if the given time is in the same interval as getTime()
   * 
   * @param Majisti_Date $time
   * @return bool
   */
	private function _isSameInterval($time) {
		return     $this->_getIntervalLimit(-1, $this->getTime())
			->equals($this->_getIntervalLimit(-1, $time));
	}

	/**
   * Returns the index of the constraint in the constraints array
   * If the constraint cannot be found, the method returns null.
   * 
   * @param array $constraint    the constraint as returned by _getSignificantConstraintValue
   * @param bool $type OPTIONAL  the type of constraint (inclusion / exclusion)
   * @return int
   */
	private function _searchConstraints($constraint, $type = self::RULE_CONSTRAINT_INCLUSION) {
		$index = null;
		$list = $type == self::RULE_CONSTRAINT_ALLOWED
		      ? '_constraints'
		      : '_exConstraints';
		
		foreach ($this->{$list} as $i => $c) {
			if ( $constraint == $c ) {
				$index = $i;
			}
		}
		
		return $index;
	}
	
	/**
	 * Add a time constraint to the rule. 
	 * 
	 * Note that if $constraint is a Zend_Date instance, all significant
	 * parts will be used as constraint values. Which means that, for a yearly
	 * rule, using a Zend_Date instance as constraint will check for all 
	 * the values from the month value to the number of seconds.
	 * 
	 * When using a Zend_Date instance, to specify which significant fields
	 * should be used for the constraint, wrap it inside an array like
	 * array( 'date' => $date_obj, 'fields' => array( ... ) ) 
	 *
	 * @param string|int|array|Majisti_Date $constraint
	 * @return Majisti_Scheduler_Task_Rule
	 */
	public function addConstraint($constraint) {
		list($allowed, $constraintValue) = $this->_getSignificantConstraintValue($constraint);
		
		if ( !empty($constraintValue) && null === $this->_searchConstraints($constraintValue, $allowed) ) {
			if ( $allowed == self::RULE_CONSTRAINT_ALLOWED ) {
				$this->_constraints[] = $constraintValue;
			} else {
				$this->_exConstraints[] = $constraintValue;
			}
		}
		$this->_resetCache();
		
		return $this;
	}
	
	/**
	 * Add all the time constraints to the rule.
	 *
	 * @param array $constraints
	 * @return Majisti_Scheduler_Task_Rule
	 */
	public function addConstraints(array $constraints) {
		foreach ($constraints as $constraint) {
			$this->addConstraint($constraint);
		}
		
		return $this;
	}	
	
	/**
	 * Clear all the constriants
	 * 
	 * @return Majisti_Scheduler_Task_Rule
	 */
	public function clearConstraints() {
		$this->_constraints = array();
		$this->_exConstraints = array();
		$this->_resetCache();
		
		return $this;
	}
	
	/**
	 * Return the expire time of rhe rule. If none is set,
	 * the method returns null
	 *
	 * @return null|Majisti_Date
	 */
	public function getExpireDate() {
		return $this->_expires;
	}
	
	/**
	 * Return all the constraints registered to the rule.
	 *
	 * @return array
	 */
	public function getConstraints() {
		$constraints = array();
		foreach ($this->_constraints as $constraint) {
			$constraints[] = array_merge($constraint, array('allow' => true));
		}
		foreach ($this->_exConstraints as $constraint) {
			$constraints[] = array_merge($constraint, array('allow' => false));
		}
		
		return $constraints;
	}
	
	/**
	 * Returns the gap interval between to runs. The value depends
	 * on the rule type. A value of 0 means undefined.
	 *
	 * @return int
	 */
	public function getInterval() {
		return $this->_interval;
	}
	
	/**
	 * Return the last logical run before the specified time (see getTime()). The 
	 * method will try to calculate the latest run satisfying all constraints and 
	 * return it. If there are no constraint set, the method will return the time
	 * at the beginning of the rule time interval. If no last run time can be 
	 * calculated, the method will return null. If a start time is set and the 
	 * speicified time is earlier, the method will return null. If an end time is
	 * set and the speicified time is later, the method will return the end time. 
	 *
	 * @return null|Majisti_Date
	 */
	public function getLastLogicalRun() {
		if ( isset($this->_cache['lastLogicalRun']) ) {
			if ( null === $this->_cache['lastLogicalRun'] ) {
				return null;
			} else {
				return clone $this->_cache['lastLogicalRun'];
			}
		}
		
		$time = $this->getTime();
		
		if ( $this->_start && $time->isEarlier($this->_start) ) {
		  return null;   // before the task has even started
	  } else if ( $this->_expires && !$time->isEarlier($this->_expires) ) {
	  	return clone $this->_expires;  // after the task has ended (return that time)
		} else {
			// constraints, try to get the latest constraint before time
			$lastRun = clone $time;
			
//			do {
//				$found = true; // by default (no constraint)
				foreach ($this->_constraints as $constraint) {
					$latestRun = clone $lastRun;
					foreach ( $constraint as $field => $value ) {
						$latestRun->set($value, $field);
					}
					if ( ( ($lastRun->equals($time) && $latestRun->isEarlier($time)) 
					 	 || (!$lastRun->equals($time) && $latestRun->isLater($lastRun)) )
						&& $this->_checkExcelusionConstraint($latestRun) ) {
							
						$lastRun = $latestRun;
//						$found = false;
					}
				}
//			} while (!$found);
		}
		
		// NOTE : at this point, if $lastRun is not equal to $time, the
		//        passed the constraints exclusion check... 
		
		// if $lastRun === $time, we failed to find a satisfaying constraint
		// therefore the task should not have run
		if ( $lastRun->equals($time) ) {
			// the last run cannot be the current date, try to find a logical previous one
			// from the time interval; return end of last interval
			$lastRun = $this->_getLastIntervalLimit();
			
			// does this time checks out?
			if ( !$this->_checkExcelusionConstraint($lastRun) ) {
				$lastRun = $this->_satisfyBeforeExcelusionConstraints($lastRun);
			}
		}
		
		$this->_cache['lastLogicalRun'] = clone $lastRun; // save it if needed again
			
		return $lastRun;
	}
	
	/**
	 * Return the next logical run after the specified time (see getTime()). 
	 * The method will try to calculate the earliest run satisfying all constraints 
	 * and return it. If no such time can be calculated, the method will return null. 
	 *
	 * @return null|Majisti_Date
	 */
	public function getNextLogicalRun() {
		if ( isset($this->_cache['nextLogicalRun']) ) {
			if ( null === $this->_cache['nextLogicalRun'] ) {
				return null;
			} else {
				return clone $this->_cache['nextLogicalRun'];
			}
		}
		
		$time = $this->getTime();

		if ( $this->_start && $time->isEarlier($this->_start) ) { 
		  return clone $this->_start;   // before the task has even started (return that time)
	  } else if ( $this->_expires && !$time->isEarlier($this->_expires) ) {
	  	return null;  // after the task has ended (no further run)
		} else {
			// constraints, try to get the earliest constraint after time
			$nextRun = clone $time;
			
//			do {
//				$found = true; // by default (no constraint)
				foreach ($this->_constraints as $constraint) {
					$earliestRun = clone $nextRun;
					foreach ( $constraint as $field => $value ) {
						$earliestRun->set($value, $field);
					}
					//echo " > " . $earliestRun->toString();
					if ( ( ($nextRun->equals($time) && $earliestRun->isLater($time)) 
					 	 || (!$nextRun->equals($time) && $earliestRun->isEarlier($nextRun)) )
						&& $this->_checkExcelusionConstraint($earliestRun) ) {
							
						$nextRun = $earliestRun;
//						$found = false;
					}
				}
//			} while (!$found);
		}
		
		// NOTE : at this point, if $lastRun is not equal to $time, the
		//        passed the constraints exclusion check... 
		
		// if $lastRun === $time, we failed to find a satisfaying constraint
		// therefore the task should not have run
		if ( $nextRun->equals($time) ) {
			// the last run cannot be the current date, try to find a logical previous one
			// from the time interval; return end of last interval
			$nextRun = $this->_getNextIntervalLimit();
			
			// does this time checks out?
			if ( !$this->_checkExcelusionConstraint($nextRun) ) {
				$nextRun = $this->_satisfyAfterExcelusionConstraints($nextRun);
			}
		}
		
		$this->_cache['nextLogicalRun'] = clone $nextRun; // save it if needed again

		return $nextRun;
	}
	
	/**
	 * Return the start time of the rule. If none is set,
	 * the method returns null.
	 *
	 * @return null|Majisti_Date
	 */
	public function getStartDate() {
		return $this->_start;
	}
	
	/**
	 * Return the interval type of this rule
	 *
	 * @return int
	 */
	public function getType() {
		return $this->_type;
	}
	
	/**
	 * Get the time of the rule
	 *
	 * @return Majisti_Date
	 */
	public function getTime() {
		if ( empty($this->_time) ) {
			$this->_time = new Majisti_Date();
		}
		return $this->_time;
	}
	
	/**
	 * Test whether the rule satisfies against a set of last runes.
	 * The method returns true if the task should be executed based on
	 * the specified last runs, or returns false the last runs does not
	 * satisfy the rule set.
	 * 
	 * Note : A week spans from Monday to Sunday. Therefore, Sunday comes
	 *        AFTER Monday
	 *
	 * @param array $lastRuns
	 * @return bool
	 */
	public function isSatisfied($lastRuns) {
		// if there are constraints, check if we have constraints remaining
		// within the same interval
		if ( !empty($this->_constraints) ) {
			// test agains remaining tasks for the interval if we have some constraints
			$nextLogicalRun = $this->getNextLogicalRun();
		} else {
			$nextLogicalRun = null; // no more run
		}
		
		if ( null !== $nextLogicalRun && $this->_isSameInterval($nextLogicalRun) ) {
			$satisfied = true;  // still something to run this interval (based on constraints)
		} else {
			// test against the last logical run compared to all last runs 
			$lastLogicalRun = $this->getLastLogicalRun();
			
			if ( null === $lastLogicalRun ) {
				// the task have no logical run before, therefore, it does not
				// satisfy the constraints for the given date
				$satisfied = false;  
			} else {
				$satisfied = true;
				
				if ( !is_array($lastRuns) ) {
					$lastRuns = array($lastRuns);
				}
				
				foreach ($lastRuns as $lastRun) {
					if ( null === $lastRun ) {
						continue;
					} else if ( !($lastRun instanceof Zend_Date) ) {
						$lastRun = new Majisti_Date($lastRun);
					}
					
					if ( !$lastRun->isEarlier($lastLogicalRun) ) {
						$satisfied = false;
						break;
					}
				}
			}
		}
		
		return $satisfied;
	}
		
	/**
	 * Remove a given constraint from the constraints array. The method
	 * use a best comparaison attempt to remove the constraint(s) that
	 * corresponds best to the given constraint to remove based on the
	 * defined rule type
	 * 
	 * @param string|int|Zend_Date|array $constraint
	 * @return Majisti_Scheduler_Task_Rule
	 */
	public function removeConstraint($constraint) {
		// ignore first parameter if specified
		list(,$constraintValue) = $this->_getSignificantConstraintValue($constraint);

		foreach ($this->_constraints as $constraintIndex => $constraint) {
			if ( $constraint == $constraintValue ) {
				unset($this->_constraints[$constraintIndex]);
			}
		}
		foreach ($this->_exConstraints as $constraintIndex => $constraint) {
			if ( $constraint == $constraintValue ) {
				unset($this->_exConstraints[$constraintIndex]);
			}
		}
		
		$this->_resetCache();
		
		return $this;
	}
	
	/**
	 * Removes all the specified constraints from the rule constraints
	 *
	 * @param array $constraints
	 * @return Majisti_Scheduler_Task_Rule
	 */
	public function removeConstraints(array $constraints) {
		foreach ($constraints as $constraint) {
			$this->removeConstraint($constraint);
		}
		return $this;
	}
	
	/**
	 * Clear all cached data
	 */
	private function _resetCache() {
		$this->_cache = array();		
	}
	
	/**
	 * Sets the time constraints to apply to this rule.
	 * 
	 * @see addConstraint()
	 *
	 * @param array $constraints
	 * @return Majisti_Scheduler_Task_Rule
	 */
	public function setConstraints(array $constraints) {
		return $this->clearConstraints()->addConstraints($constraints);
	}
	
	/**
	 * Sets the rule options
	 *
	 * @param array $options
	 * @return Majisti_Scheduler_Task_Rule
	 */
	public function setOptions(array $options) {

		if ( array_key_exists('type', $options) ) {
			$this->setType($options['type']);
			unset($options['type']);
		} else if ( null === $this->_type ) {
			$this->_type = self::RULE_YEARLY;   // default
		}
		
		if ( array_key_exists('start', $options) ) {
			if ( empty($options['start']) ) {
				$this->_start = null;
			} else {
				$this->_start = new Majisti_Date($options['start']);
			}
			unset($options['start']);
		}  // default null

		if ( array_key_exists('expire', $options) ) {
			if ( empty($options['expire']) ) {
				$this->_expires = null;
			} else {
				$this->_expires = new Majisti_Date($options['expire']);
			}
			unset($options['expire']);
		}  // default null
		
		if ( array_key_exists('interval', $options) ) {
			$this->_interval = (int) $options['interval'];
			unset($options['interval']);
			
			if ( $this->_interval < 1 ) {
				throw new Majisti_Scheduler_Task_Rule_Exception('invalid interval value');
			}
		} else if ( null === $this->_interval ) {
			$this->_interval = 1;  // default value
		}
		
		if ( array_key_exists('constraints', $options) ) {
			$this->setConstraints($options['constraints']);
			unset($options['constraints']);
		} else if ( null === $this->_constraints ) {
			$this->clearConstraints(); 
		}
		
		
		// validation...
		if ( $this->_expires && $this->_start && $this->_start->isLater($this->_expires) ) {
			throw new Majisti_Scheduler_Task_Rule_Exception('rule expires before it has started');
		}
		
		$this->_resetCache();
	}
	
	/**
	 * Sets time of the rule.
	 *
	 * @param string|int|Majisti_Date $time
	 */
	public function setTime($time) {
		$this->_time = new Majisti_Date($time);
		$this->_resetCache();
		
		return $this;
	}
	
	/**
	 * Set the type of the rule
	 *
	 * @param int $type   one of the RULE_xxxx constant
	 * @return Majisti_Scheduler_Task_Rule
	 */
	protected function setType($type) {
		switch ($type) {
			case self::RULE_DAILY:
			case self::RULE_WEEKLY:
			case self::RULE_MONTHLY:
			case self::RULE_YEARLY:
				$this->_type = $type;
				break;
			default:
				throw new Majisti_Scheduler_Task_Rule_Exception('unknown rule type');			
		}
		$this->_resetCache();
		
		return $this;
	}
}