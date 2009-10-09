<?php

/**
 * @desc Model containing everything related to a therapy
 *
 * @author Steven Rosato
 */
class Anato_Therapy
{
	const XML_FILE_NAME = 'therapy';
	
	/**
	 * @return Array What is anatotherapy
	 */
	public function getAbout()
	{
		return Anato_Util_Content::getXmlContent(self::XML_FILE_NAME, 'about')->data->toArray();
	}
	
	/**
	 * @return Array What is anatotherapy
	 */
	public function getExplications()
	{
		return Anato_Util_Content::getXmlContent(self::XML_FILE_NAME, 'explications')->data->toArray();
	}
	
	/**
	 * @return Array The benefits
	 */
	public function getBenefits()
	{
		return Anato_Util_Content::getXmlContent(self::XML_FILE_NAME, 'benefits')->data->toArray();
	}
	
	/**
	 * @return Array The simplified techniques
	 */
	public function getSimplifiedTechniques()
	{
		return Anato_Util_Content::getXmlContent(self::XML_FILE_NAME, 'simplifiedTechniques')->data->toArray();
	}

	/**
	 * @return Array the treatable problems
	 */
	public function getTreatableProblems()
	{
		return Anato_Util_Content::getXmlContent(self::XML_FILE_NAME, 'treatableProblems')->data->toArray();
	}
}