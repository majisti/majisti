<?php

/**
 * @desc This is the list of services offered by Anato
 * 
 * Though the list may be huge since it is not in a database,
 * it only aggregates Anato_Service objects according
 * to a key.
 * 
 * Note: I know the entire xml is loaded each time, but it still very fast.
 * For better performance consider loading only one section depending
 * on the current action name.
 * 
 * @see Anato_Service
 * 
 * @author Steven Rosato
 */
class Anato_Services extends Anato_Util_ArrayObject
{
	/**
	 * @desc Constructs this list of services
	 */
	public function __construct()
	{
		$xml = Anato_Util_Content::getXmlContent('services');
		
		$services = array();
		
		foreach ($xml as $data) {
			/* optional data is provided */
			if( isset($data->data) ) {
				$services[$data->key] = new Anato_Service(
					$data->key, 
					$data->name, 
					$data->desc, 
					$data->data->toArray()
				);
			/* no data provided */
			} else {
				$services[$data->key] = new Anato_Service(
					$data->key, 
					$data->name, 
					$data->desc 
				);
			}
		}
		
		parent::__construct($services);
	}
}