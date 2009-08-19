<?php

//class Majisti_Db_Table_Banned implements Majisti_Db_Table_Banned_Interface
//{
//	private $_table;
//	
//	public function __construct($tableName)
//	{
//		$this->_table = new Zend_Db_Table_Abstract(array(
//			'name' => $tableName
//		));
//	}
//	
//	public function contains($key)
//	{
//		
//	}
//	
//	public function remove($key)
//	{
//		
//	}
//	
//	public function add($key)
//	{
//		$db->insert($this->_table, array(
//				'ip' 		=> $key, 
//				'dateStart' => $date->get(self::$_mySqlDateFormat),
//				'dateUntil' => $time->get(self::$_mySqlDateFormat))
//			);
//	}
//	
//	public function lastInsertId()
//	{
//		
//	}
//	
//	public function updateBan()
//	{
//		$db->update($tableName, array('dateUntil' => $time->get(self::$_mySqlDateFormat)), "ip = '$key'");
//	}
//	
//	public function extendBan($ip, $tableName, $banTime, $append = false)
//	{
//		$db = Zend_Db_Table::getDefaultAdapter();
//		
//		$select = $db->select()->from($tableName)->where('ip = ?', $ip);
//		$results = $db->query($select)->fetchAll();
//		
//		$dateNow = new Zend_Date(Zend_Date::now(), Zend_Date::ISO_8601);
//		
//		if( empty($results[0]['dateUntil']) ) {
//			$dateBanned = $dateNow;
//		} else {
//			$dateBanned = new Zend_Date($results[0]['dateUntil'], Zend_Date::ISO_8601);
//		}
//		
//		if ( $dateNow->isLater($dateBanned) ) { //date expired
//			self::unban($ip, $tableName);
//			return ;
//		} else if ( !$append ) { //append the difference from the date banned with the new ban time
//			$difference = $dateBanned->getTimestamp() - $dateNow->getTimestamp();
//			$dateBanned->subSecond($difference);
//			$dateBanned->addMinute($banTime);
//		} else { //fully append the new ban time
//			$dateBanned->addMinute($banTime);
//		}
//		self::$_bannedUntil = $dateBanned;
//		$db->update($tableName, array('dateUntil' => $dateBanned->get(self::$_mySqlDateFormat)), "ip = '$ip'");
//	}
//	
//	private function containsByMode($mode, $ip, $registerBannedUntil = false)
//	{
//		$select = $db->select()->from($tableName, array('ip', 'dateUntil'))->where('ip = ?', $ip);
//		$bannedIP = $db->query($select)->fetchAll();
//		
//		if (count($bannedIP) > 0) {
//			if( $registerBannedUntil ) {
//				self::$_bannedUntil = new Zend_Date($bannedIP[0]['dateUntil'], Zend_Date::ISO_8601);
//			}
//			switch($mode) {
//				case 'contains': 
//					return true;
//				case 'isBanned':
//					return !empty($bannedIP[0]['dateUntil']);
//			}
//		}
//		return false;
//	}
//	
//public function incrementLoginAttemp($ip, $tableName)
//	{
//		$db = Zend_Db_Table::getDefaultAdapter();
//		
//		if( !self::contains($ip, $tableName) ) {
//			$date = new Zend_Date(Zend_Date::now(), Zend_Date::ISO_8601);
//			$db->insert($tableName, array(
//					'ip' 			=> $ip, 
//					'dateStart' 	=> $date->get(self::$_mySqlDateFormat),
//					'loginAttemps' 	=> 1
//				)
//			);
//		} else {
//			$select = $db->select()->from($tableName, 'loginAttemps')->where('ip = ?', $ip);
//			$results = $db->query($select)->fetchAll();
//			
//			if( count($results) > 0 ) {
//				$incremented = $results[0]['loginAttemps'] + 1;
//				
//				$db->update($tableName, array('loginAttemps' => $incremented), "ip = '$ip'");
//			}
//		}
//	}
//	
//	public function clearLoginAttemps($ip, $tableName)
//	{
//		$db = Zend_Db_Table::getDefaultAdapter();
//		
//		$select = $db->select()->from($tableName, 'id')->where('ip = ?', $ip);
//		$results = $db->query($select)->fetchAll();
//		
//		if( count($results) > 0 ) {
//			$db->delete($tableName, "ip = '$ip'");
//		}
//	}
//	
//	public function getNumberLoginAttemps($ip, $tableName, $expiredTime = 0)
//	{
//		/* expiredtime = minutes */
//		
//		$db = Zend_Db_Table::getDefaultAdapter();
//		
//		$select = $db->select()->from($tableName, array('dateStart', 'loginAttemps', 'dateUntil'))->where('ip = ?', $ip);
//		$results = $db->query($select)->fetchAll();
//		
//		if( count($results) > 0 ) {
//			if( $expiredTime > 0 ) {
//				$dateStart = new Zend_Date($results[0]['dateStart'], Zend_Date::ISO_8601);
//				if( ( Zend_Date::now()->getTimestamp() - $dateStart->getTimestamp() ) / 60 > $expiredTime && !isset($resutls[0]['dateUntil']) ) {
//					self::clearLoginAttemps($ip, $tableName);
//					return 0;
//				}
//			}
//			return $results[0]['loginAttemps'];
//		}
//		
//		return -1;
//	}
//	
//	public function isExpired($ip, $tableName, $defaultBanTime = 120)
//	{
//		if( self::isBanned( $ip, $tableName ) ) {
//			$db = Zend_Db_Table::getDefaultAdapter();
//			
//			$select = $db->select()->from($tableName, array('dateStart', 'dateUntil') )->where('ip = ?', $ip);
//			$results = $db->query($select)->fetchAll();
//			
//			$dateStart = new Zend_Date($results[0]['dateStart'], Zend_Date::ISO_8601);
//			$dateStart->addMinute($defaultBanTime);
//			
//			$dateUntil = new Zend_Date($results[0]['dateUntil'], zend_date::ISO_8601);
//			
//			return $dateStart->compare($dateUntil) > 0; //later
//		}
//		return false;	
//	}
//	
//	public function cleanExpired($tableName, $defaultBanTime = 120)
//	{
//		$db = Zend_Db_Table::getDefaultAdapter();
//		
//		$dateNow = new Zend_Date(Zend_Date::now(), Zend_Date::ISO_8601);
//		$dateNow->subMinute($defaultBanTime);
//		
//		$select = $db->select()->from($tableName);
//		$results = $db->query($select)->fetchAll();
//		
//		foreach ($results as $result) {
//			$dateStart = new Zend_Date($result['dateStart'], Zend_Date::ISO_8601);
//			$dateUntil = new Zend_Date($result['dateUntil'], Zend_Date::ISO_8601);
//			
//			if( $dateStart->compare($dateNow) < 0 || $dateUntil->compare($dateNow) < 0 ) {
//				$db->delete($tableName, 'id = '.$result['id']);
//			}
//		}		
//	}
//}