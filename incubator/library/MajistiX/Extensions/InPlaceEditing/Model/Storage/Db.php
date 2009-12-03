<?php

namespace MajistiX\Extensions\InPlaceEditing\Model\Storage;

/**
 * @desc Db storage implementation for InPlaceEditing model.
 * 
 * @author Steven Rosato
 */
class Db extends \Majisti\Model\Storage\DbStorageAbstract implements IStorage
{
    /**
     * @desc Reads an entry from the table with the key, locale
     * provided in the arguments.
     * 
     * @param $args The args, 0 => containing the key, 1 => the locale
     * 
     * @return \Zend_Db_Table_Row_Abstract
     */
    public function read(array $args)
    {
        $table      = $this->getTable();
        $adapter    = $table->getAdapter();
        
        $select = $table->select($this->proxy('content'))
            ->where($adapter->quoteIdentifier($this->proxy('key')) . ' = ?', $args[0])
            ->where($adapter->quoteIdentifier($this->proxy('locale')) . ' = ?', $args[1]);
            
        return $table->fetchRow($select);
    }
    
    /**
     * @desc Creates a row in the table according to the arguments.
     * 
     * @param array $args 0 => key, 1 => the content, 2 => the locale
     */
    public function upcreate(array $args)
    {
        $table  = $this->getTable();
        $row    = $this->getRow(array($args[0], $args[2]));
            
        $row->{$this->proxy('key')}       = $args[0];
        $row->{$this->proxy('content')}   = $args[1];
        $row->{$this->proxy('locale')}    = $args[2];
        $row->save();
    }
    
    /**
     * @desc Returns the content according to the key and locale provided.
     * Completes the IStorage interface.
     * 
     * @param $key The key
     * @param $locale The locale
     */
    public function getContent($key, $locale)
    {
        $row = $this->read(array($key, $locale));
        
        return null !== $row
            ? $row->content
            : null;
    }
    
    /**
     * @desc Edits the content with the key, content and locale provided.
     * Completes the IStorage interface.
     * 
     * @param $key The key
     * @param $content The content
     * @param $locale The locale
     */
    public function editContent($key, $content, $locale)
    {
        $this->upcreate(array($key, $content, $locale));
    }
}
