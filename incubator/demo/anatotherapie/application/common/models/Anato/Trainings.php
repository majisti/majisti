<?php

/**
 * @desc The list of available trainings
 * 
 * FIXME: support 1 data aggregation
 *
 * @author Steven Rosato
 */
class Anato_Trainings extends Anato_Util_ArrayObject
{
	/**
	 * @desc Constructs the list of trainings
	 */
	public function __construct()
	{
		$xml = Anato_Util_Content::getXmlContent('trainings');
		
		$trainings = array();
		
		foreach ($xml as $data) {
			$trainings[$data->key] = new Anato_Training(
				$data->key, 
				$data->name, 
				$data->desc, 
				$data->data->toArray()
			);
		}
		
		parent::__construct($trainings);
	}
}
