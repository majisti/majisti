<?php

/**
 * @desc Anato's history
 * 
 * @author Steven Rosato
 */
class Anato_History
{
	/**
	 * @return String The history
	 */
	public function getHistory()
	{
		return Anato_Util_Content::getXmlContent('history', 'history')->data;
	}
	
	/**
	 * @return Array The discovery
	 */
	public function getDiscovery()
	{
		return Anato_Util_Content::getXmlContent('history', 'discovery')->toArray();
	}
	
	/**
	 * @return Array The founder's biography
	 */
	public function getFounderBiography()
	{
		return Anato_Util_Content::getXmlContent('history', 'founderBiography')->toArray();
	}
	
	/**
	 * @return String The professional training
	 */
	public function getProfessionalTraining()
	{
		return Anato_Util_Content::getXmlContent('history', 'professionalTraining')->data;
	}
	
	/**
	 * @return Array The other information
	 */
	public function getOtherInformation()
	{
		return Anato_Util_Content::getXmlContent('history', 'otherInformation')->data->toArray();
	}
}