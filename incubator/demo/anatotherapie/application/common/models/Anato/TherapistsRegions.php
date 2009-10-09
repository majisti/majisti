<?php

/**
 * @desc The association model for therapists and regions
 * 
 * @author Steven Rosato
 */
class Anato_TherapistsRegions extends Majisti_Db_Table_General
{
	/**
	 * @desc Constructs the TherapistsRegions model
	 */
	public function __construct()
	{
		$this->_table = new Anato_Table_TherapistsRegions();
	}
	
	/**
	 * @desc Manages a therapist's regions with the ones provided in parameter.
	 * Every regions currently in the database not present in the array will
	 * be deleted and every one not in the database will be inserted. Those already
	 * there won't be touched.
	 *
	 * @param int $therapistId The therapist's id
	 * @param array[opt] $regions The regions to update. Won't do nothing on a null/empty array
	 */
	public function manageTherapistRegions($therapistId, array $regions = null)
	{
		if( null !== $regions && count($regions) ) {
			$select = $this->_table->select()->where('therapistId = ?', $therapistId);
			$rowset = $this->_table->fetchAll($select);
			
			foreach ($rowset as $row) {
				/* delete removed regions */
				if( !in_array($row->regionId, $regions) ) {
					$row->delete();
				} /* already in the database, unset from the regions array */ 
				elseif( false !== ($index = array_search($row->regionId, $regions)) ) {
					unset($regions[$index]);
				}
			}
			
			/* insert new regions which were not found in the database */
			foreach ($regions as $region) {
				$newRow = $this->_table->fetchNew();
				$newRow->therapistId	= $therapistId;
				$newRow->regionId 		= $region;
				$newRow->save();
			}
		}
	}
}