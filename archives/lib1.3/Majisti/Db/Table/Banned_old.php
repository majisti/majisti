<?php

/**
 * TODO: doc
 * FIXME: review this class design
 *
 * @author Steven Rosato
 */
//class Majisti_Db_Table_Banned
//{
//	private static $_bannedUntil = null;
//	
//	private static $_mySqlDateFormat = 'YYYY-MM-dd HH:mm:ss';
//	
//	private $_banTime;
//	
//	private $_table;
//	
//	public function __construct($banTime = 120)
//	{
//		$this->_banTime = $banTime;
//	}
//	
//	public function setBanTime($banTime)
//	{
//		
//	}
//	
//	public function ban($key, $extendBan = true, $append = false)
//	{
//		$date = new Zend_Date(Zend_Date::now(), Zend_Date::ISO_8601);
//		$time = new Zend_Date($date->get(self::$_mySqlDateFormat));
//		
//		$time->addMinute($this->_banTime);
//		
//		if( !$this->_table->contains($key) ) {
//			$this->_table->add($key);
//		} elseif( !$this->isBanned($key) ) {
//			$this->_table->updateBan();
//		} elseif( $extendBan ) {
//			$this->extendBan($key, $banTime, $append);
//		}
//		
//		return $this->_table->lastInsertId();
//	}
//
//	public function unban($key)
//	{
//		if( $this->_table->contains($key) ) {
//			$this->_table->remove($key);
//		}
//	}
//	
//	public function getBannedUntil($ip, $tableName)
//	{
//		if( self::$_bannedUntil == null ) {
//			$db = Zend_Db_Table::getDefaultAdapter();
//			
//			$select = $db->select()->from($tableName, 'dateUntil')->where('ip = ?', $ip);
//			$results = $db->query($select)->fetchAll();
//			
//			if( count($results) > 0 ) {
//				self::$_bannedUntil = new Zend_Date($results[0]['dateUntil'], Zend_Date::ISO_8601);
//			} else {
//				self::$_bannedUntil = null;
//			}
//		}
//		return self::$_bannedUntil;
//	}
//}