<?php

namespace MajistiX\Extensions\InPlaceEditing\Model\Storage;

/**
 * @desc Storage interface for InPlaceEditing model which accepts
 * only that type of editor.
 *  
 * @author Steven Rosato
 */
interface IStorage
{
    /**
     * @see \Majisti\Model\Storage\IStorage::getContent()
     */
    public function getContent($key, $locale);
    
    /**
     * @see \Majisti\Model\Storage\IStorage::editContent() 
     */
    public function editContent($key, $content, $locale);
}
