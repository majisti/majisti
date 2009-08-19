<?php

require_once 'Zend/Date.php';

/**
 * Extends the functionality of Zend_Date
 * 
 * @author Yanick Rochon
 */
class Majisti_Date extends Zend_Date
{

	/**
	 * The default string format for the toString() method if no format
	 * is given.
	 *
	 * @var string
	 */
	public static $DEFAULT_STRING_FORMAT = 'YYYY-MM-dd HH:mm:ss';
	
	/**
	 * The weekday integer representation of Monday using ISO-8601 standard value
	 *
	 * @var integer
	 */
	const MONDAY = 1;
	/**
	 * The weekday integer representation of Tuesday using ISO-8601 standard value
	 *
	 * @var integer
	 */
	const TUESDAY = 2;
	/**
	 * The weekday integer representation of Wednesday using ISO-8601 standard value
	 *
	 * @var integer
	 */
	const WEDNESDAY = 3;
	/**
	 * The weekday integer representation of Thursday using ISO-8601 standard value
	 *
	 * @var integer
	 */
	const THURSDAY = 4;
	/**
	 * The weekday integer representation of Friday using ISO-8601 standard value
	 *
	 * @var integer
	 */
	const FRIDAY = 5;
	/**
	 * The weekday integer representation of Saturday using ISO-8601 standard value
	 *
	 * @var integer
	 */
	const SATURDAY = 6;
	/**
	 * The weekday integer representation of Sunday using ISO-8601 standard value
	 *
	 * @var integer
	 */
	const SUNDAY = 7;
	
	/**
	 * Gets the next part $value of the current date. If the $value part doesn't
	 * have a next part, the method returns null. The goal of this method is
	 * to find the next occurance of the part from the current date value. For
	 * example, finding the next Friday if today is thursday will return the
	 * tomorrow, but if today is saturday the function will return the next week
	 * Friday's date.
	 *
	 * For this reason, finding the next year 1970 doesn't make any sense if today
	 * is 2009! Therefore, the method returns false. But finding the next 2072 will
	 * return today's date at the year 2072.
	 *
	 * @param Zend_Date|string|int $value
	 * @param string $part
	 * @param string|Zend_Locale $locale
	 * @return Zend_Date
	 */
	public function getNext($value, $part, $locale = null)
	{
		$_date = clone $this;
		$_date->set($value, $part, $locale);

		if ( $_date->equals($this) || $_date->isEarlier($this) ) {
			switch ($part) {
				case Zend_Date::MILLISECOND:			// second
					$_date->addSecond(1, $locale);
					break;
				case Zend_Date::SECOND:						// minute
				case Zend_Date::SECOND_SHORT:			// minute
					$_date->addMinute(1, $locale);
					break;
				case Zend_Date::MINUTE:						// hour
				case Zend_Date::MINUTE_SHORT:			// hour
					$_date->addHour(1, $locale);
					break;
				case Zend_Date::MERIDIEM:					// day
				case Zend_Date::HOUR:							// day
				case Zend_Date::HOUR_AM:					// day
				case Zend_Date::HOUR_SHORT:				// day
				case Zend_Date::HOUR_SHORT_AM:		// day
				case Zend_Date::TIMES:            // day
				case Zend_Date::TIME_SHORT:       // day
				case Zend_Date::TIME_MEDIUM:      // day
				case Zend_Date::TIME_LONG:        // day
				case Zend_Date::TIME_FULL:        // day
					$_date->addDay(1, $locale);
					break;
				case Zend_Date::WEEKDAY:					// week
				case Zend_Date::WEEKDAY_8601:     // week
				case Zend_Date::WEEKDAY_DIGIT:    // week
				case Zend_Date::WEEKDAY_NAME:     // week
				case Zend_Date::WEEKDAY_SHORT:		// week
				case Zend_Date::WEEKDAY_NARROW:   // week
					$_date->addWeek(1, $locale);
					break;
				case Zend_Date::MONTH_DAYS:       // month (next with number of days)
				case Zend_Date::DAY:              // month
				case Zend_Date::DAY_SHORT:        // month
				case Zend_Date::DAY_SUFFIX:       // month
					$_date->addMonth(1, $locale);
					break;
				case Zend_Date::WEEK:             // year (week number in year)
				case Zend_Date::DAY_OF_YEAR:      // year
				case Zend_Date::MONTH:            // year
				case Zend_Date::MONTH_SHORT:      // year
				case Zend_Date::MONTH_NAME:       // year
				case Zend_Date::MONTH_NAME_SHORT: // year
				case Zend_Date::MONTH_NAME_NARROW:// year
					$_date->addYear(1, $locale);
					break;
				case Zend_Date::LEAPYEAR:     		// year * n (set next leap year?)
					$_date->addYear(1, $locale);
					while ($_date->isLeapYear()) {
						$_date->addYear(1, $locale);
					}
					break;
				case Zend_Date::SWATCH:						// ???
				case Zend_Date::YEAR:         		// ???
				case Zend_Date::YEAR_8601:    		// ???
				case Zend_Date::YEAR_SHORT:   		// ???
				case Zend_Date::YEAR_SHORT_8601:	// ???

				default:  // all other contants return false (cannot set next)
					$_date = false;
					break;
			}
		}

		return $_date;
	}

	/**
	 * Get the next time from the current date. If the value is a string
	 * the function will attempt to autodetect the time format. Such as
	 * '0' == '0:00' == '0:00:00'. Any other value format may throw an exception
	 *
	 * @param Zend_Date|string|int $value
	 * @param Zend_Locale|string $locale
	 * @return Zend_Date
	 */
	public function getNextTime($value, $locale = null)
	{
		if ( is_string($value) ) {
			switch (substr_count($value, ':')) {
				case 0:
					return $this->getNext($value, Zend_Date::HOUR);
				case 1:
					return $this->getNext($value, Zend_Date::TIME_SHORT);
				default:
					return $this->getNext($value, Zend_Date::TIMES);
			}
		} else {
			return $this->getNext($value, Zend_Date::TIMES);
		}
	}

	/**
	 * Get the next week from the current date. The week is the week number
	 * within the year. Therefore, getNextWeek(1) will get the next
	 * first week of the next year.
	 *
	 * Note : to get the next week day, use addDay(7[, $locale])
	 *
	 * @param Zend_Date|string|int $value
	 * @param Zend_Locale|string $locale
	 * @return Zend_Date
	 */
	public function getNextWeek($value, $locale = null)
	{
		return $this->getNext($value, Zend_Date::WEEK, $locale);
	}

	/**
	 * Get the next weekday from the current date.
	 *
	 * @param Zend_Date|string|int $value
	 * @param Zend_Locale|string $locale
	 * @return Zend_Date
	 */
	public function getNextWeekday($value, $locale = null)
	{
		return $this->getNext($value, Zend_Date::WEEKDAY_DIGIT, $locale);
	}




	/**
	 * Gets the previous part $value of the current date. If the $value part doesn't
	 * have a previous part, the method returns null. The goal of this method is
	 * to find the previous occurance of the part from the current date value. For
	 * example, finding the previous Friday if today is thursday will return the
	 * previous week Friday's date, but if today is saturday the function will return
	 * yesterday's date.
	 *
	 * For this reason, finding the previous year 2072 doesn't make any sense if today
	 * is 2009! Therefore, the method returns false. But finding the previous 1970 will
	 * return today's date at the year 1970.
	 *
	 * @param Zend_Date|string|int $value
	 * @param string $part
	 * @param string|Zend_Locale $locale
	 * @return Zend_Date
	 */
	public function getPrevious($value, $part, $locale = null)
	{
		$_date = clone $this;
		$_date->set($value, $part, $locale);

		if ( $_date->equals($this) || $_date->isLater($this) ) {
			switch ($part) {
				case Zend_Date::MILLISECOND:			// second
					$_date->subSecond(1, $locale);
					break;
				case Zend_Date::SECOND:						// minute
				case Zend_Date::SECOND_SHORT:			// minute
					$_date->subMinute(1, $locale);
					break;
				case Zend_Date::MINUTE:						// hour
				case Zend_Date::MINUTE_SHORT:			// hour
					$_date->subHour(1, $locale);
					break;
				case Zend_Date::MERIDIEM:					// day
				case Zend_Date::HOUR:							// day
				case Zend_Date::HOUR_AM:					// day
				case Zend_Date::HOUR_SHORT:				// day
				case Zend_Date::HOUR_SHORT_AM:		// day
				case Zend_Date::TIMES:            // day
				case Zend_Date::TIME_SHORT:       // day
				case Zend_Date::TIME_MEDIUM:      // day
				case Zend_Date::TIME_LONG:        // day
				case Zend_Date::TIME_FULL:        // day
					$_date->subDay(1, $locale);
					break;
				case Zend_Date::WEEKDAY:					// week
				case Zend_Date::WEEKDAY_8601:     // week
				case Zend_Date::WEEKDAY_DIGIT:    // week
				case Zend_Date::WEEKDAY_NAME:     // week
				case Zend_Date::WEEKDAY_SHORT:		// week
				case Zend_Date::WEEKDAY_NARROW:   // week
					$_date->subWeek(1, $locale);
					break;
				case Zend_Date::MONTH_DAYS:       // month (next with number of days)
				case Zend_Date::DAY:              // month
				case Zend_Date::DAY_SHORT:        // month
				case Zend_Date::DAY_SUFFIX:       // month
					$_date->subMonth(1, $locale);
					break;
				case Zend_Date::WEEK:             // year (week number in year)
				case Zend_Date::DAY_OF_YEAR:      // year
				case Zend_Date::WEEK:             // year (week number in year)
				case Zend_Date::MONTH:            // year
				case Zend_Date::MONTH_SHORT:      // year
				case Zend_Date::MONTH_NAME:       // year
				case Zend_Date::MONTH_NAME_SHORT: // year
				case Zend_Date::MONTH_NAME_NARROW:// year
					$_date->subYear(1, $locale);
					break;
				case Zend_Date::LEAPYEAR:     		// year * n (set next leap year?)
					$_date->subYear(1, $locale);
					while ($_date->isLeapYear()) {
						$_date->subYear(1, $locale);
					}
					break;
				case Zend_Date::SWATCH:						// ???
				case Zend_Date::YEAR:         		// ???
				case Zend_Date::YEAR_8601:    		// ???
				case Zend_Date::YEAR_SHORT:   		// ???
				case Zend_Date::YEAR_SHORT_8601:	// ???

				default:  // all other contants return false (cannot set next)
					$_date = false;
					break;
			}
		}

		return $_date;
	}

	/**
	 * Get the previous time from the current date. If the value is a string
	 * the function will attempt to autodetect the time format. Such as
	 * '0' == '0:00' == '0:00:00'. Any other value format may throw an exception
	 *
	 * @param Zend_Date|string|int $value
	 * @param Zend_Locale|string $locale
	 * @return Zend_Date
	 */
	public function getPreviousTime($value, $locale = null)
	{
		if ( is_string($value) ) {
			switch (substr_count($value, ':')) {
				case 0:
					return $this->getPrevious($value, Zend_Date::HOUR);
				case 1:
					return $this->getPrevious($value, Zend_Date::TIME_SHORT);
				default:
					return $this->getPrevious($value, Zend_Date::TIMES);
			}
		} else {
			return $this->getPrevious($value, Zend_Date::TIMES);
		}
	}

	/**
	 * Get the previous week from the current date. The week is the week number
	 * within the year. Therefore, getNextWeek(1) will get the next first week
	 * of the current or previous year (if the current week equals 1).
	 *
	 * Note : to get the previous week day, use subDay(7[, $locale])
	 *
	 * @param Zend_Date|string|int $value
	 * @param Zend_Locale|string $locale
	 * @return Zend_Date
	 */
	public function getPreviousWeek($value, $locale = null)
	{
		return $this->getPrevious($value, Zend_Date::WEEK, $locale);
	}

	/**
	 * Get the next weekday from the current date.
	 *
	 * @param Zend_Date|string|int $value
	 * @param Zend_Locale|string $locale
	 * @return Zend_Date
	 */
	public function getPreviousWeekday($value, $locale = null)
	{
		return $this->getPrevious($value, Zend_Date::WEEKDAY_DIGIT, $locale);
	}
	
	/**
	 * @desc
	 * Check if the current date is beetween the two given dates inclusively.
	 * The method does not check the order of the given dates, so the first
	 * parameter ($date1) should be less or equal to the second parameter ($date2)
	 *
	 * @param int|string|array|Zend_Date $date1
	 * @param int|string|array|Zend_Date $date2
	 */
	public function isBetween($date1, $date2) {
		if ( !$date1 instanceof Zend_Date ) {
			$date1 = new Majisti_Date($date1);
		}
		if ( !$date2 instanceof Zend_Date ) {
			$date2 = new Majisti_Date($date2);
		}
		return !$this->isEarlier($date1) && !$this->isLater($date2);
	}

	/**
	 * Sets a new weekday
	 * The weekday can be a number or a string. If a localized weekday name is given,
	 * then it will be parsed as a date in $locale (defaults to the same locale as $this).
	 * Returned is the new date object.
	 * Example: setWeekday(3); will set the wednesday of this week as day.
	 *
	 * The weekday value may be 0=Sunday, 1=Monday, ..., 6=Saturday, 7=Sunday
	 * To retrieve the integer value, simply call get(Zend_Date::WEEKDAY_8601)
	 *
	 * @param  string|integer|array|Zend_Date  $month   Weekday to set
	 * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
	 * @return Zend_Date  new date
	 * @throws Zend_Date_Exception
	 */
	public function setWeekday($weekday, $locale = null)
	{
		if ( $weekday == 0 ) $weekday = 7;

		return parent::setWeekday($weekday, $locale);
	}
	
	
  /**
   * Returns a string representation of the object
   * Supported format tokens are:
   * G - era, y - year, Y - ISO year, M - month, w - week of year, D - day of year, d - day of month
   * E - day of week, e - number of weekday (1-7), h - hour 1-12, H - hour 0-23, m - minute, s - second
   * A - milliseconds of day, z - timezone, Z - timezone offset, S - fractional second, a - period of day
   *
   * Additionally format tokens but non ISO conform are:
   * SS - day suffix, eee - php number of weekday(0-6), ddd - number of days per month
   * l - Leap year, B - swatch internet time, I - daylight saving time, X - timezone offset in seconds
   * r - RFC2822 format, U - unix timestamp
   *
   * Not supported ISO tokens are
   * u - extended year, Q - quarter, q - quarter, L - stand alone month, W - week of month
   * F - day of week of month, g - modified julian, c - stand alone weekday, k - hour 0-11, K - hour 1-24
   * v - wall zone
   *
   * @param  string              $format  OPTIONAL Rule for formatting output. If null the default date format is used
   * @param  string              $type    OPTIONAL Type for the format string which overrides the standard setting
   * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
   * @return string
   */
	public function toString($format = null, $type = null, $locale = null) {
		if ( null === $format ) {
			$format = self::$DEFAULT_STRING_FORMAT;
		}
		return parent::toString($format, $type, $locale);
	}

}