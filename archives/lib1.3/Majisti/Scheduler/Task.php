<?php

/**
 * @desc
 * Interface for the Majisti_Scheduler_Task implementation
 * 
 * @author Yanick Rochon
 */
interface Majisti_Scheduler_Task
{

	/**
	 * Determine if the specified task is scheduled to be
	 * executed since the last run
	 * 
	 * @return bool
	 */
	public function isScheduled();
	
	/**
	 * Return all the last run dates of this task. The function
	 * returns an array of Majisti_Date
	 * 
	 * @return array
	 */
	public function getLastRuns();
	
	/**
   * Return the task name
   * 
   * @return string
   */
	public function getName();
	
	/**
	 * Return the task's priority. The priority may be any
	 * integer value.
	 *
	 * @return int
	 */
	public function getPriority();
	
	/**
	 * Return all rules associated with the task
	 *
	 * @return array
	 */
	public function getRules();
	
	/**
	 * Return the time to use when executing the task. If no time has
	 * been set with setTime(), today's time is returned.
	 *
	 * @return Majisti_Date
	 */
	public function getTime();
	
	/**
	 * Execute the task and return the task's response. The response value
	 * may be any relevant object, or NULL.
	 *
	 * @return mixed
	 */
	public function run();


}