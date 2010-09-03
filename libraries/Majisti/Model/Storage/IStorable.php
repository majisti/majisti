<?php

namespace Majisti\Model\Storage;

/**
 * @desc Storable models provide methods to keep or retrieve
 * a storage model. This interface provides the functionnality of
 * Generic Storage which lets concrete classes specify a rule on
 * the storage model that can be given. That way, only a
 * StorageModel<T> of type T can be given to that storable model.
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface IStorable
{
    /**
     * @desc Returns the generic storage type
     *
     * @return String the generic storage used
     */
    public function getGenericStorage();
    
    /**
     * @desc Returns the storage model
     * 
     *  @return The storage model
     */
    public function getStorageModel();
    
    /**
     * @desc Sets the storage model. The storage model must be of generic type <T>
     * which was defined by this class.
     * 
     * @param $storageModel the storage model
     * 
     * @throws Exception if storage is null or storage model is of wrong generic type
     * 
     * @return StorableModel
     */
    public function setStorageModel($storageModel);
}