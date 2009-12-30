<?php

namespace Majisti\Model\Storage;

/**
 * @desc Provides database storage and proxy column reading. Proxy column
 * reading is simply a mapping between a virtual column name setup on
 * object instanciation/mutator and the real database table column.
 * 
 * @author Steven Rosato
 */
abstract class DbStorageAbstract extends StorageAbstract
{
    /**
     * @var \Zend_Db_Table_Abstract
     */
    protected $_table;
    
    public function __construct(array $params)
    {
        if( !isset($params['table']) ) {
            throw new Exception("A table name or instance of Zend_Db_Table_Abstract
                must be given as 'table' key/value within constructor \$params parameter");
        }
        
        $table = $params['table'];
        
        if( !($table instanceof \Zend_Db_Table_Abstract) ) {
            $table = \Majisti\Model\Storage\DbTableContainer::getInstance()
                ->getTable($table);
        }
        
        //TODO: add up proxy columns from params
        
        $this->setTable($table);
    }
    
    public function setProxyColumns(array $proxyColumns)
    {
        //TODO: complete method stub
    }
    
    public function getProxyColumns()
    {
        //TODO: complete method stub
    }
    
    public function proxy($column)
    {
        //TODO: complete method stub
        return $column;
    }
    
    public function setTable(\Zend_Db_Table_Abstract $table)
    {
        $this->_table = $table;
    }
    
    public function getTable()
    {
        return $this->_table;
    }
    
    public function getRow(array $args)
    {
        $row = $this->read($args);
        
        return null === $row
            ? $this->getTable()->createRow()
            : $row;
    }
}
