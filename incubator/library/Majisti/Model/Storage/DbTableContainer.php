<?php

namespace Majisti\Model\Storage;

class Blocks extends \Zend_Db_Table_Abstract
{
    protected $_name = "majisti_demo_simple_blocks";
}

class DbTableContainer extends \Majisti\Util\Pattern\SingletonAbstract
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
        return new Blocks();
    }
    
    public function getTables()
    {
        return $this->_tables;
    }
    
    public function addTable($table)
    {
        
    }
}
