<?php

/**
 * Db Backend for the Ads banner class
 * 
 * @author Yanick Rochon
 */
class Majisti_Ads_Backend_Db extends Majisti_Ads_Backend_Abstract 
{
	// Table : ad
	const FIELDS_TABLE_OFFSET = 0;
	const FIELDS_TABLE_COUNT = 8;
	
	const FIELD_ID = 'id';
	
	const FIELD_TYPE = 'ad_type';
	
	const FIELD_NAME = 'ad_name';
	
	const FIELD_URL = 'ad_url';
	
	const FIELD_HEIGHT = 'ad_height';
	
	const FIELD_WIDTH = 'ad_width';
	
	const FIELD_WEIGHT = 'ad_weight';
	
	const FIELD_CONTENT = 'ad_content';
	
	// Table : ad_attribs
	const FIELDS_ATTRIBS_OFFSET = 8;
	const FIELDS_ATTRIBS_COUNT = 2;
	
	const FIELD_ATTRIB_NAME = 'ad_attrib_name';
	
	const FIELD_ATTRIB_VALUE = 'ad_attrib_value';
	
	// Table : ad_params
	const FIELDS_PARAMS_OFFSET = 10;
	const FIELDS_PARAMS_COUNT = 3;
	
	const FIELD_PARAM_INDEX = 'ad_param_index'; 
	
	const FIELD_PARAM_NAME = 'ad_param_name';

	const FIELD_PARAM_VALUE = 'ad_param_value';
	
	/**
	 * @var Zend_Db_Table
	 */
	private $_table;
	/**
	 * @var Zend_Db_Table
	 */
	private $_attribsTable;
	/**
	 * @var Zend_Db_Table
	 */
	private $_paramsTable;
	/**
	 * @var array
	 */
	private $_fields;
	/**
	 * @var array
	 */
	private $_extraFields;
	/**
	 * @var array
	 */
	private $_cache;
	
	
	/**
	 * @desc 
	 * Construct a new ads db backend linked to a given table. The attributes
	 * of the banner will be fetched from $attribsTable and The params will be 
	 * fetched from $paramsTable By default, the fields for the ads are defined
	 * using the predefined constants FIELD_xxxxx. The fields may be overriden
	 * by specifying $options['fields'] = array(...) where the array is indexed
	 * using the constant's name and assigning new field name values
	 * 
	 * Ex : $options['fields']['id'] = 'my_id';             // overrides FIELD_ID
	 *      $options['fields']['content'] = 'my_content';   // overrides FIELD_CONTENT
	 *      $options['fields']['param_name'] = 'my_p_name'; // overrides FIELD_PARAM_NAME
	 * 
	 * A single backend may be used to serve multiple Majisti_Ads instances. If
	 * each instance have distinct ads in the same table, these may be filtered
	 * using the 'extraFields' option in the $options array.
	 * 
	 * Ex : $options['extraFields'] = array('ad_manager' => 'My manager')
	 * 
	 * Will apply an extra where condition filtering all the rows where the column
	 * 'ad_manager' equals 'My manager'. If necessary, multiple conditions may be
	 * specified.
	 * 
	 * Ex : $options['extraFields'] = array('ad_manager' => 'My manager',
	 *                                      'ad_section' => 'My section')
	 *
	 *  
	 * @param Zend_Db_Table $table
	 * @param Zend_Db_Table $attribsTable
	 * @param Zend_Db_Table $paramsTable
	 * @param array $options (optional)
	 */
	public function __construct($table, $attribsTable, $paramsTable, $options = array())
	{
		
		if ( !($table instanceof Zend_Db_Table_Abstract) ) {
			throw new Majisti_Ads_Backend_Exception('$table must be an instance of Zend_Db_Table');
		}
		if ( !($attribsTable instanceof Zend_Db_Table_Abstract) ) {
			throw new Majisti_Ads_Backend_Exception('$attribsTable must be an instance of Zend_Db_Table');
		}
		if ( !($paramsTable instanceof Zend_Db_Table_Abstract) ) {
			throw new Majisti_Ads_Backend_Exception('$paramsTable must be an instance of Zend_Db_Table');
		}
		
		$this->_table = $table;
		$this->_attribsTable = $attribsTable;
		$this->_paramsTable = $paramsTable;
		
		$this->_fields = array(
			'id'           => self::FIELD_ID,
			'type'         => self::FIELD_TYPE,
			'name'         => self::FIELD_NAME,
			'url'          => self::FIELD_URL,
			'height'       => self::FIELD_HEIGHT,
			'width'        => self::FIELD_WIDTH,
			'weight'       => self::FIELD_WEIGHT,
			'content'      => self::FIELD_CONTENT,
			'attrib_name'  => self::FIELD_ATTRIB_NAME,
			'attrib_value' => self::FIELD_ATTRIB_VALUE,
			'param_index'  => self::FIELD_PARAM_INDEX, 
			'param_name'   => self::FIELD_PARAM_NAME,
			'param_value'  => self::FIELD_PARAM_VALUE
		);
		
		if ( isset($options['fields']) ) {
			if ( !is_array($options['fields']) ) {
				throw new Majisti_Ads_Backend_Exception('option "fields" must be an array');
			}
			
			array_change_key_case($options['fields'], CASE_LOWER);
			$this->_fields = array_merge($this->_fields, $options['fields']);
		}
		
		$this->_extraFields = array();
		if ( isset( $options['extraFields']) ) {
			if ( !is_array($options['extraFields']) ) {
				throw new Majisti_Ads_Backend_Exception('option "extraFields" must be an array');
			}
			
			foreach ($options['extraFields'] as $field => $value) {
				// if the key is not a string, we assume $c is the WHERE clause
				if (!is_string($field) || !is_string($value)) {
					throw new Majisti_Ads_Backend_Exception('invalid field in extraFields');
				}
				$this->_extraFields[$field] = $value;
			}
		}
		
		$this->_cache = array();
		
	}
	
	/**
	 * Return a new unused id for a newly created banner
	 *
	 * @return mixed
	 */
	public function getNewId() {
		$select = $this->_table->select()->from($this->_table, array('newId' => 'MAX('.$this->_fields['id'].')+1'));
		$row = $this->_table->fetchRow($select);
		
		if ( empty($row) || empty($row->newId) ) {
			$newId = 1;
		} else {
			$newId = $row->newId;
		}
		
		return $newId;
	}
	
	/**
	 * Check whether a given id is valid (true) or not (false)
	 * 
	 * NOTE : A valid id may be an unassigned id.
	 *
	 * @param mixed $id
	 * @return bool
	 */
	public function isValidId($id) {
		$select = $this->_createTableSelect(true)->where($this->_fields['id'].' = ?', $id);
		$ownedRow = $this->_table->fetchRow($select);

		$select = $this->_createTableSelect(false)->where($this->_fields['id'].' = ?', $id);
		$anyRow = $this->_table->fetchRow($select);
		
		return !is_null($ownedRow) || is_null($anyRow);
	}
	
	
	/**
	 * Create a new table select from the ads banner table
	 *
	 * @param bool $useFilters OPTIONAL   use the constructor filters when returning the select
	 * @return Zend_Db_Table_Select
	 */
	private function _createTableSelect($useFilters = true)
	{
		$tableFields = array_filter(array_slice($this->_fields, self::FIELDS_TABLE_OFFSET, self::FIELDS_TABLE_COUNT));
		$tableFields = array_merge(array_keys($this->_extraFields), array_values($tableFields));
		
		$select = $this->_table->select()->from($this->_table->info(Zend_Db_Table::NAME), $tableFields);
		if ( $useFilters ) {
			if ( !empty($this->_extraFields) ) {
				foreach ($this->_extraFields as $field => $value) {
					$select = $select->where($field.' = ?', $value);
				}
			}
		}
		return $select;
	}
	
	/**
	 * Create a new attributes select from the ads banner attribs table
	 *
	 * @return Zend_Db_Table_Select
	 */
	private function _createAttribsSelect() 
	{
		$attribsFields = array_filter(array_slice($this->_fields, self::FIELDS_ATTRIBS_OFFSET, self::FIELDS_ATTRIBS_COUNT));

		return $this->_attribsTable->select()->from($this->_attribsTable->info(Zend_Db_Table::NAME), array_values($attribsFields));
	}

	/**
	 * Create a new parameters select from the ads banner params table
	 *
	 * @return Zend_Db_Table_Select
	 */
	private function _createParamsSelect()
	{
		$paramsFields = array_filter(array_slice($this->_fields, self::FIELDS_PARAMS_OFFSET, self::FIELDS_PARAMS_COUNT));
		
		return $this->_paramsTable->select()->from($this->_paramsTable->info(Zend_Db_Table::NAME), array_values($paramsFields));
	}
	
	/**
	 * This function should load all the ads and return an array 
	 * of abstract banners.
	 * 
	 * @return array 
	 */
	public function load() {
		$banners = array();
		$this->_cache = array();
		
		$attribsSelect = $this->_createAttribsSelect();
		$paramsSelect = $this->_createParamsSelect();
		
		$db_banners = $this->_table->fetchAll($this->_createTableSelect());
		foreach ($db_banners as $db_banner) {
			$options = array();
			foreach ($db_banner->toArray() as $fieldKey => $fieldValue) {
				$mapKey = array_search($fieldKey, $this->_fields);
				$options[$mapKey] = $fieldValue;
			}
			
			// fetch attribs
			$db_attribs = $db_banner->findAdsAttribs($attribsSelect);
			$options['attribs'] = array();
			foreach ($db_attribs as $db_attrib) {
				$options['attribs'][$db_attrib->__get($this->_fields['attrib_name'])] = $db_attrib->__get($this->_fields['attrib_value']);
			}
			
			// fetch params
			$db_params = $db_banner->findAdsParams($paramsSelect);
			$options['params'] = array();
			foreach ($db_params as $db_param) {
				if ( !isset($db_param->param_index) ) {
					$options['params'][$db_param->__get($this->_fields['param_index'])] = array();
				}
				$options['params'][$db_param->__get($this->_fields['param_index'])][$db_param->__get($this->_fields['param_name'])] = $db_param->__get($this->_fields['param_value']);
			}
			
			$banner = Majisti_Ads_Banner::factory($options['type'], $options);
			$banners[$db_banner->id] = $banner;
			$db_banner->setReadOnly(false);
			
			$this->_cache[$db_banner->id] = new Majisti_Object(array(
				'banner' => $banner,
				'row' => $db_banner
			)); 
		}
		
		return $banners;
	}
	
	/**
	 * This function should save all the ads. The $data is an array 
	 * of abstract banners
	 * 
	 * (1) if the banner ad type is text, the url is the target url of the
	 *     banner text
	 * 
	 * @return array 
	 */
	public function save($data) {
		// only save if array
		if ( is_array($data) ) {

			foreach ($this->_cache as $cache) {
				if ( false !== ($id = array_search($cache->banner, $data)) ) {
					$banner = $data[$id];
					
					// if the banner still exists in the cache at $id in $data
					// check if we need to update it
					if ( $banner->hasChanged() ) {
						$this->_saveBanner($banner, $cache->row);
					}
					
					unset($data[$id]);
				} else {
					// the banner was removed or does not exist any longuer in $data
					// delete it
					$cache->row->delete();				
				}
			}
			
			// all banners left in the data array are new banners that needs to be inserted
			foreach ($data as $banner) {
				$this->_saveBanner($banner);				
			}
		}
	}			

	/**
	 * Save the given banner row with the given banner's data
	 * 
	 * Note : be aware, the code may be hard to understand...
	 *
	 * @param Majisti_Ads_Banner_Abstract $baonner
	 * @param Zend_Db_Table_Row $bannerRow OPTIONAL
	 * @param bool $useColumnMappings OPTIONAL      the row uses column mappings?
	 */
	private function _saveBanner($banner, $bannerRow = null) {
		
		if ( null === $bannerRow ) {
			$bannerRow = $this->_table->createRow();
		}
		
		$bannerObj = $this->_bannerToObject($banner);

		// TODO : save banner
		$tableFields = array_filter(array_slice($this->_fields, self::FIELDS_TABLE_OFFSET, self::FIELDS_TABLE_COUNT));
		
		foreach ($tableFields as $fieldMap => $field) {
			$bannerRow->{$field} = $bannerObj->get($fieldMap);
		}
		// apply any extra fields
		foreach ($this->_extraFields as $field => $value) {
			$bannerRow->{$field} = $value;
		}
		
		$bannerRow->save();
		
		$db_attribs = $bannerRow->findAdsAttribs();
		foreach ($db_attribs as $db_attrib) {
			if ( $bannerObj->attribs->count() ) {
				// pop the first item off $bannerObj->attribs
				$bannerObj->attribs->rewind();
				$attribKey = $bannerObj->attribs->key();
				$attribValue = $bannerObj->attribs->current();
				
				$db_attrib->__set($this->_fields['attrib_name'], $attribKey);
				$db_attrib->__set($this->_fields['attrib_value'], $attribValue);
				$db_attrib->save();
				
				$bannerObj->attribs->__unset($attribKey);
			} else {
				$db_attrib->delete();  // this attrib is no longer associated to the banner, delete it
			}
		}
		
		// insert remaining
		foreach ($bannerObj->attribs as $attribKey => $attribValue) {
			$db_attrib = $this->_attribsTable->createRow();
			
			$db_attrib->__set($this->_fields['attrib_name'], $attribKey );
			$db_attrib->__set($this->_fields['attrib_value'], $attribValue );
			$db_attrib->save();
		}
		
		
		$db_params = $bannerRow->findAdsParams();
		foreach ($db_params as $db_param) {
			$param = $bannerObj->params->get($db_param->__get($this->_fields['param_index']));
			if ( null !== $param ) {
				// Pop the first param for this index
				$param->rewind();
				$paramKey = $param->key();
				$paramValue = $param->current();
				
				$db_param->__set($this->_fields['param_name'], $paramKey);
				$db_param->__set($this->_fields['param_value'], $paramValue);
				$db_param->save();
				
				$bannerObj->params->__unset($paramKey);
			} else {
				// no param for the given index
				$db_param->delete();
			}
		}
		
		foreach ($bannerObj->params as $paramIndex => $params) {
			foreach ($params as $paramKey => $paramValue) {
				$db_param = $this->_paramsTable->createRow();
				$db_param->__set($this->_fields['param_index'], $paramIndex );
				$db_param->__set($this->_fields['param_name'], $paramKey );
				$db_param->__set($this->_fields['param_value'], $paramValue );
				$db_param->save();
			}
		}
		
		$banner->setChanged(false);
	}
	
}