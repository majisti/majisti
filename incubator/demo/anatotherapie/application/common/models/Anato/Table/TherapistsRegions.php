<?php

/**
 * @desc The TherapistsRegions association table
 *
 * @author Steven Rosato
 */
class Anato_Table_TherapistsRegions extends Anato_Util_Table
{
	protected $_referenceMap = array(				
		'Therapists' => array(
			'columns' => 'therapistId',
			'refTableClass' => 'Anato_Table_Therapists',
			'refColumns'	=> 'id'
		),
		
		'Regions' => array(
			'columns' => 'regionId',
			'refTableClass' => 'Anato_Table_Regions',
			'refColumns'	=> 'id'
		)
	);
}