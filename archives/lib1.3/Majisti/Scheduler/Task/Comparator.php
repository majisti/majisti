<?php

/**
 * Compares two tasks to see which is "greater" than the other.
 * 
 * @author Yanick Rochon
 */
class Majisti_Scheduler_Task_Comparator implements Majisti_Util_Comparator 
{
	
	/**
	 * @see Majisti_Util_Comparator::compare
	 * 
	 * @param Majisti_Scheduler_Task $o1   the first task to be compared
	 * @param Majisti_Scheduler_Task $o2   the second task to be compared
	 * @return int    a negative integer, zero, or a positive integer as the first 
	 *                task is less than, equal to, or greater than the second.
	 */
	public function compare($o1, $o2) {
		if ( $o1->getPriority() == $o2->getPriority() ) {
			$runs1 = $o1->getLastRuns();
			$runs2 = $o2->getLastRuns();

			if ( count($runs1) == 0 && count($runs2) == 0 ) {
				return 0;
			} else if ( count($runs1) == 0 ) {
				return 1;
			} else if ( count($runs2) == 0 ) {
				return -1;
			} else {
				$r1 = reset($runs1);
				$r2 = reset($runs2);
				
				return $r1->equals($r2) ? 0 : ($r1->isEarlier($r2) ? -1 : 1);
			}
		} else {
			return $o2->getPriority() - $o1->getPriority();
		}
	}
	
}