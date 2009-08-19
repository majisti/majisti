<?php

/**
 * @desc Implements a rating system using a database table 
 * 
 * Rating consists of having three distinct types: A rater, a rated
 * and a rating. A 'rater' is usually who rated the 'rated' object
 * by applying a 'rating' on it. All of these can then be used
 * to calculate an average rating and thus could be used in a star
 * rating system.
 * 
 * @author Steven Rosato
 */
class Majisti_Rating_Db implements Majisti_Rating_Interface
{
	/** @var Zend_Db_Table_Abstract */
	private $_db;
	
	private $_table;
	
	private $_columnMap;
	
	protected $_scale;
	protected $_allowMultipleVotes;
	
	/**
	 * @desc Will construct a rating instance that will be usable for rating different types
	 * of objects, using their id as reference in a table.
	 * 
	 * The following keys in the columnMapping are needed:
	 * 
	 * primary: the primary key, must be auto_increment
	 * rater: the rater's column nane
	 * rated: the rated object's column name
	 * rating: the rating column name
	 *
	 * @param Zend_Db_Adapter_Abstract $db The prefactorised database
	 * @param string $table The table Name
	 * @param array $columnMap The columnMap that respects the four keys needed.
	 * @param integer $scale The maximum scale for rated objects.
	 * @param boolean (optionnal, false) $allowMultipleVotes Allow a rater to vote multiple times or not
	 */
	public function __construct(Zend_Db_Adapter_Abstract $db, $table, array $columnMap, $scale, $allowMultipleVotes = false)
	{
		$this->_validateColumnMapping($columnMap);
		
		$this->_db    				= $db;
        $this->_table 				= $table;
        $this->_columnMap 			= $columnMap;
        
        $this->_scale				= $scale;
        $this->_allowMultipleVotes 	= $allowMultipleVotes;
	}
	
	/**
	 * @desc Validates that the column mapping has all the required keys in it.
	 *
	 * @param Array $data The array to check
	 */
	private function _validateColumnMapping($data)
	{
		if( !isset($data['primary']) ) {
			throw new Majisti_Rating_Exception("The key 'primary' must be setted in the column map");
		}
		
		if( !isset($data['rater']) ) {
				throw new Majisti_Rating_Exception("The key 'rater' must be setted in the column map");
			}
		
		if( !isset($data['rated']) ) {
			throw new Majisti_Rating_Exception("The key 'rated' must be setted in the column map");
		}
		
		if( !isset($data['rating']) ) {
			throw new Majisti_Rating_Exception("The key 'rating' must be setted in the column map");
		}
	}
	
	/**
	 * @desc Rates a 'rated' object using a 'rating' and storing the
	 * 'rater' as well in the database table. 
	 *
	 * @param integer $rater The rater's ID
	 * @param integer $rated The rated object's ID
	 * @param integer $rating The rating to apply, must be lower or equal than previously setted scale.
	 * @return true if the vote was cast correctly.
	 */
	public function rate($rater, $rated, $rating)
	{
		if( $rating > $this->getScale() ) {
			throw new Majisti_Rating_Exception("Rating can't be higher than predefined scale");	
		}
		
		
		if( !$this->_allowMultipleVotes ) {
			if( !$this->hasRated($rater, $rated) ) {
				$this->_insert($rater, $rated, $rating);
				return true;
			}
		} else {
			$this->_insert($rater, $rated, $rating);
			return true;
		}
		
		return false;
	}
	
	private function _insert($rater, $rated, $rating)
	{
		$this->_db->insert($this->_table, array(
			$this->_columnMap['rater'] 	=> $rater,
			$this->_columnMap['rated'] 	=> $rated,
			$this->_columnMap['rating'] => $rating,
		));
	}
	
	/**
	 * @desc Returns whether a 'rater' has already rated a 'rated' object
	 *
	 * @param integer $rater The rater's ID
	 * @param integer $rated The rated object's ID
	 * @return boolean true is the 'rater' has already rated a 'rated' object
	 */
	public function hasRated($rater, $rated)
	{
		return $this->_getRating($rater, $rated) != NULL;
	}
	
	/**
	 * @desc Returns the average rating, based on the total rating for a current 'rated' object with the total
	 * votes done by the 'raters' on it.
	 *
	 * @param integer $rated The rated object's ID
	 * @return float The average rating
	 */
	public function getAverageRating($rated)
	{
		$totalVotes = $this->getTotalVotes($rated);
		return $totalVotes > 0 ? $this->getTotalRating($rated) / $totalVotes : 0;
	}
	
	/**
	 * @desc Returns the total rating of a 'rated' object.
	 *
	 * @param integer $rated The rated object's ID
	 * @return integer The total rating of a 'rated' object.
	 */
	public function getTotalRating($rated)
	{
		$select = $this->_db
			->select()
			->from($this->_table, array('totalRating' => 'SUM(' . $this->_columnMap['rating']. ')'))
			->where($this->_db->quoteIdentifier($this->_columnMap['rated']) . ' = ' . $rated);
			
		$row = $this->_db->fetchRow($select);
		
		return $row['totalRating'] == NULL ? 0 : $row['totalRating'];
	}
	
	/**
	 * @desc Returns the total votes applied on a 'rated' object
	 *
	 * @param integer $rated The rated object's ID
	 * @return integer The number of total votes
	 */
	public function getTotalVotes($rated)
	{
		$select = $this->_db
			->select()
			->from($this->_table, array('totalVotes' => 'COUNT(' . $this->_columnMap['rated'] . ')'))
			->where($this->_db->quoteIdentifier($this->_columnMap['rated']) . ' = ' . $rated);
			
		$row = $this->_db->fetchRow($select);
		
		return $row['totalVotes'];
	}
	
	/**
	 * @desc Returns the rating of a rater.
	 *
	 * @param integer $rater The rater's ID
	 * @param integer $rated The rated object's ID
	 * @return Zend_Db_Table_Row | null The fetched row or null if the rater has not voted
	 */
	private function _getRating($rater, $rated)
	{
		$select = $this->_db
			->select()
			->from($this->_table)
			->where($this->_db->quoteIdentifier($this->_columnMap['rater']) . ' = ' . $rater)
			->where($this->_db->quoteIdentifier($this->_columnMap['rated']) . ' = ' . $rated);
			
		return $this->_db->fetchRow($select);
	}
	
	/**
	 * @desc Returns the rating of a rater.
	 *
	 * @param integer $rater The rater's ID
	 * @param integer $rated The rated object's ID
	 * @return int The rater's rating or -1 if the rater has never rated.
	 */
	public function getRating($rater, $rated)
	{
		$rating = $this->_getRating($rater, $rated);
		
		return $rating != NULL ? $rating['rating'] : -1;
	}
	
	/**
	 * @return integer This current rating system scale
	 */
	public function getScale()
	{
		return $this->_scale;
	}
}
