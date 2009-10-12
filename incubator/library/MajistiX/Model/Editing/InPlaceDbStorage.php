<?php

namespace MajistiX\Model\Editing;

class InPlaceDbStorage extends \Majisti\Model\Storage\DbStorageAbstract implements IInPlaceStorage
{
    public function read($key, $locale)
    {
        $table      = $this->getTable();
        $adapter    = $table->getAdapter();
        
        $select = $table->select($this->proxy('content'))
            ->where($adapter->quoteIdentifier($this->proxy('key')) . ' = ?', $key)
            ->where($adapter->quoteIdentifier($this->proxy('locale')) . ' = ?', $locale);
            
        return $table->fetchRow($select);
    }
    
    public function upsert($key, $locale, $content)
    {
        $table  = $this->getTable();
        $row    = $this->getRow(array($key, $locale));
            
        $row->{$this->proxy('key')}       = $key;
        $row->{$this->proxy('locale')}    = $locale;
        $row->{$this->proxy('content')}   = $content;
        $row->save();
    }
    
    public function getContent($key, $locale)
    {
        $row = $this->read($key, $locale);
        
        return null !== $row
            ? $row->content
            : null;
    }
    
    public function editContent($key, $locale, $content)
    {
        $this->upsert($key, $locale, $content);
    }
}