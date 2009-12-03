<?php

namespace MajistiX\Extensions\InPlaceEditing\Model;

class InPlaceDbStorage extends \Majisti\Model\Storage\DbStorageAbstract implements IInPlaceStorage
{
    public function read(array $args)
    {
        $table      = $this->getTable();
        $adapter    = $table->getAdapter();
        
        $select = $table->select($this->proxy('content'))
            ->where($adapter->quoteIdentifier($this->proxy('key')) . ' = ?', $args[0])
            ->where($adapter->quoteIdentifier($this->proxy('locale')) . ' = ?', $args[1]);
            
        return $table->fetchRow($select);
    }
    
    public function upcreate(array $args)
    {
        $table  = $this->getTable();
        $row    = $this->getRow(array($args[0], $args[2]));
            
        $row->{$this->proxy('key')}       = $args[0];
        $row->{$this->proxy('content')}   = $args[1];
        $row->{$this->proxy('locale')}    = $args[2];
        $row->save();
    }
    
    public function getContent($key, $locale)
    {
        $row = $this->read(array($key, $locale));
        
        return null !== $row
            ? $row->content
            : null;
    }
    
    public function editContent($key, $content, $locale)
    {
        $this->upcreate(array($key, $content, $locale));
    }
}