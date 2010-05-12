<?php

namespace Majisti\Model\Storage;

class DbTableContainer 
{
    protected $_tables = array();
    
    static protected $_instance; 
    
    public function getTable($tableName)
    {
        $tableName  = (string)$tableName;
        $tables     = $this->getTables();
        
        if( !array_key_exists($tableName, $tables) ) {
            $table = $this->_createTable($tableName);
            $this->addTable($table);
            return $table;
        }
        
        return $tables[$tableName];
    }
    
    protected function _createTable($tableName)
    {
        return new $tableName();
    }
    
    public function getTables()
    {
        return $this->_tables;
    }
    
    public function addTable($table)
    {
        
    }

    static public function getInstance()
    {
        if( null === static::$_instance ) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }
}
