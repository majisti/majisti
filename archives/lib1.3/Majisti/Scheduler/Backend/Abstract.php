<?php

/**
 * 
 * @author Yanick Rochon
 */
abstract class Majisti_Scheduler_Backend_Abstract
{
	
	/**
	 * Instantiate a new Backend
	 *
	 * @param array $options
	 */
	public function __construct($options) {
		/* nothing */
	}
	
	/**
	 * Load all tasks from the data source and return them. The method
	 * should return an array of Majisti_Scheduler_Task
	 * 
	 * @return array
	 */
	abstract public function load(); 
	
	/**
	 * Save all tasks to the data source. This method should receive an
	 * array of Majisti_Scheduler_Task
	 *
	 * @param array $data
	 */
	abstract public function save($tasks);
	
}