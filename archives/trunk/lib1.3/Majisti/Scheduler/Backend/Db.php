<?php


class Majisti_Scheduler_Backend_Db extends Majisti_Scheduler_Backend_Abstract 
{
	
	/**
	 * @var Zend_Db_Table
	 */
	private $_table;
	/**
	 * The task group (default to null, unspecified)
	 *
	 * @var null|string
	 */
	private $_taskGroup;
	/**
	 * @var Zend_Db_Table_Rowset
	 */
	private $_cache;
	
	
	/**
	 * Create a new Backend for a Majisti_Scheduler using the given
	 * options.
	 * 
	 *   'table'            an instance of Zend_Db_Table_Abstract
	 *   'group' OPTIONAL   the task group name
	 *
	 * @param unknown_type $table
	 */
	public function __construct($options) {
		
		if ( !isset($options['table']) ) {
			throw new Majisti_Scheduler_Exception('backend db must have a table set');
		}
		
		$this->_table = $options['table'];
		unset($options['table']);
		
		if ( isset($options['group']) ) {
			$this->_taskGroup = $options['group'];
			unset($options['group']);
		} else {
			$this->_taskGroup = null;
		}
		
	}
	
	
	/**
	 * Load all tasks from the data source and return them. The method
	 * should return an array of Majisti_Scheduler_Task
	 * 
	 * @return array
	 */
	public function load() {
		$tasks = array();
		
		$select = $this->_table->select();
		if ( null !== $this->_taskGroup ) {
			$select->where('groupName = ?', $this->_taskGroup);
		}

		$this->_cache = $this->_table->fetchAll($select);

		foreach ($this->_cache as $row) {
			
			$className = $row->className;
			
			if ( @class_exists($className, true) ) {
				$task = new $className(array(
					'name' => $row->name, 
					'priority' => $row->priority,
					'data' => unserialize($row->data)
				));
			} else {
				throw new Majisti_Scheduler_Exception('task class not found : ' . $className);
			}
			
			$rules = $row->findNotificationRules();
			foreach ($rules as $rule) {
				$taskRule = new Majisti_Scheduler_Task_Rule($rule->toArray());
				
				$constraints = $rule->findNotificationConstraints();
				foreach ($constraints as $constraint) {
					$taskRule->addConstraint( array_filter($constraint->toArray()) );
				}
				$task->addRule($taskRule);
			}
			
			$runs = $row->findNotificationTaskRuns();
			foreach ($runs as $run) {
				$task->addLastRun($run->value);
			}
			
			$tasks[] = $task;
		}
		
		return $tasks;
	}
	
	/**
	 * Save all tasks to the data source. This method should receive an
	 * array of Majisti_Scheduler_Task
	 *
	 * @param array $data
	 */
	public function save($tasks) {
		
		list( $ruleTable, $runTable ) = $this->_table->getDependentTables();
		$ruleTable = new $ruleTable();
		$runTable = new $runTable();
		list( $constraintTable ) = $ruleTable->getDependentTables();
		$constraintTable = new $constraintTable();
		
		//echo "<br /><br />" . $this->_taskGroup . " = Cache : " . count($this->_cache);
		//echo ", tasks : " . count($tasks). ' ; ';
		
		$this->_cache->rewind();
		foreach ($tasks as $task) {
			if ( $this->_cache->valid() ) {
				$row = $this->_cache->current();
				$this->_cache->next();
			} else {
				$row = $this->_table->fetchNew();
			}
			
			$row->name = $task->getName();
			$row->groupName = $this->_taskGroup;
			$row->className = get_class($task);
			$row->priority = $task->getPriority();
			$row->data = serialize($task->getData());
			
			//echo "<br /><br />" . "Saving ... "; print_r($row->toArray());
			$taskId = $row->save();
			
			$_cacheRules = $row->findNotificationRules();
			foreach ($task->getRules() as $rule) {
				if ( $_cacheRules->valid() ) {
					$ruleRow = $_cacheRules->current();
					$_cacheRules->next();
				} else {
					$ruleRow = $ruleTable->fetchNew();
				}
				
				$ruleRow->task_id = $taskId;
				$ruleRow->type = $rule->getType();
				$ruleRow->start = (string) $rule->getStartDate();
				$ruleRow->expire = (string) $rule->getExpireDate();
				$ruleRow->interval = $rule->getInterval();
				
				//echo "<br /><br />" . "Rule : "; print_r($ruleRow->toArray());
				$ruleId = $ruleRow->save();
				//echo "Saved !";

				$_cacheConstraints = $ruleRow->findNotificationConstraints();
				foreach ($rule->getConstraints() as $constraint) {
					if ( $_cacheConstraints->valid() ) {
						$constraintRow = $_cacheConstraints->current();
						$_cacheConstraints->next();
					} else {
						$constraintRow = $constraintTable->fetchNew();
					}

					foreach ($constraint as $key => $value) {
						$key = strtolower($key);
						$constraintRow->$key = $value;
					}
					$constraintRow->rule_id = $ruleId;
										
					//echo "<br /><br />" . "Constraint : "; print_r($constraintRow->toArray());
					$constraintRow->save();
				}
				while ( $_cacheConstraints->valid() ) {
					$_cacheConstraints->current()->delete();
					$_cacheConstraints->next();
				}
			}
			while ( $_cacheRules->valid() ) {
				$_cacheRules->current()->delete();
				$_cacheRules->next();
			}
			
			// ************ LAST RUNS ************
			$_cacheRuns = $row->findNotificationTaskRuns();
			foreach ($task->getLastRuns() as $lastRun) {
				if ( $_cacheRuns->valid() ) {
					$runRow = $_cacheRuns->current();
					$_cacheRuns->next();
				} else {
					$runRow = $runTable->fetchNew();
				}
				
				$runRow->task_id = $taskId;
				$runRow->value = $lastRun->getIso();
				
				//echo "<br /><br />" . "Run : "; print_r($runRow->toArray());
				$runRow->save();
			}
			// delete all remaining runs
			while ( $_cacheRuns->valid() ) {
				$_cacheRuns->current()->delete();
				$_cacheRuns->next();
			}
			// ************ LAST RUNS ************
		}
		while ( $this->_cache->valid() ) {
			$this->_cache->current()->delete();
			$this->_cache->next();
		}
	}
	
}