<?php

class Majisti_Date_Difference
{
	/** @var Majisti_Date */
	private $_dateSrc;
	
	/** @var Majisti_Date */
	private $_dateTrg;
	
	/** @var int */
	private $_timestampDiff;
	
	
	public function __construct(Zend_Date $firstDate, Zend_Date $secondDate)
	{
		$this->_dateSrc = $firstDate;
		
		$this->_dateTrg = $secondDate;
	}
	
	private function _computeDifference()
	{
		if( $this->_timestampDiff == null ) {
			$this->_timestampDiff = $this->_dateTrg->getTimestamp() - $this->_dateSrc->getTimestamp();
		}
	}
	
	private function _computeMonths($days)
	{
		
	}
	
//	const DAY            = 'DAY';            // d - 2 digit day of month, 01-31
//    const WEEK           = 'WEEK';           // W - number of week ISO8601, 1-53
//    const MONTH          = 'MONTH';          // m - 2 digit month, 01-12
//    const YEAR           = 'YEAR';           // Y - 4 digit year
//    const HOUR           = 'HOUR';           // H - 2 digit hour, leading zeros, 00-23
//    const MINUTE         = 'MINUTE';         // i - 2 digit minute, leading zeros, 00-59
//    const SECOND         = 'SECOND';         // s - 2 digit second, leading zeros, 00-59
//    const MILLISECOND    = 'MILLISECOND';    // --- milliseconds
    
	public function get($part)
	{
		$result = $this->getTimestamp();
		
		switch($part) {
			case Zend_Date::MONTH: $result = $this->_computeMonths($this->getDays());
			break;
			case Zend_Date::YEAR: $result /= 12;
			case Zend_Date::WEEK: $result /= 7;
			case Zend_Date::DAY: $result /= 24;
			case Zend_Date::HOUR: $result /= 60;
			case Zend_Date::MINUTE: $result /= 60;
			case Zend_Date::SECOND: ;
			break;
			case Zend_Date::MILLISECOND: $result *= 1000;
		}
		
		return $result;
	}
	
	public function getTimestamp()
	{
		$this->_computeDifference();
		return $this->_timestampDiff;
	}
	
	public function getYears()
	{
		throw new Majisti_Exception('Not implemented yet');
	}
	
	public function getMonths()
	{
		throw new Majisti_Exception('Not implemented yet');
	}
	
	public function getWeeks()
	{
		return $this->get(Zend_Date::WEEK);		
	}
	
	public function getDays()
	{
		return $this->get(Zend_Date::DAY);
	}
	
	public function getHours()
	{
		return $this->get(Zend_Date::HOUR);	
	}
	
	public function getMinutes()
	{
		return $this->get(Zend_Date::MINUTE);	
	}
	
	public function getSeconds()
	{
		return $this->get(Zend_Date::SECOND);
	}
	
	public function getMilliseconds()
	{
		return $this->get(Zend_Date::MILLISECOND);	
	}
	
	public function toString()
	{
		return (string) $this->getDays();
	}
	
	public function __toString()
	{
		return $this->toString();	
	}
}
