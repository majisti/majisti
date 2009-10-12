<?php

namespace Majisti\Model\Storage;

abstract class DbStorageAbstract implements IStorage
{
    /**
     * @var \Zend_Db_Table_Abstract
     */
    protected $_table;
    
    public function __construct($table = null)
    {
        if( null !== $table ) {
            $this->_table = $table;
        }
    }
    
    public function setProxyColumns(array $columnsMap)
    {
        //TODO: complete method stub
    }
    
    public function getProxyColumns()
    {
        //TODO: complete method stub
    }
    
    public function proxy($column)
    {
        return $column;
    }
    
    public function setTable(\Zend_Db_Table_Abstract $table)
    {
        $this->_table = $table;
    }
    
    public function getTable()
    {
        //TODO: should it throw exception or should I implement a NullObject table?
        return $this->_table;
    }
    
    public function getRow(array $args)
    {
        $row = call_user_func_array(array($this, 'read'), $args);
        
        return null === $row
            ? $this->getTable()->createRow()
            : $row;
         
    }
}
