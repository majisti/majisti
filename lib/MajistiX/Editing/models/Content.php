<?php

namespace MajistiX\Editing\Model;

use \Doctrine\ORM\Mapping\ClassMetadata,
    \Doctrine\ORM\EntityRepository,
    \MajistiX\Editing\View\Editor,
    \MajistiX\Editing\Util\Filter as Filters,
    \Majisti\Config\Configuration;

/**
 * @desc InPlaceEditing entity model.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Content
{
    protected $id;

    protected $name;

    protected $content = '';

    protected $locale;

    protected $_options;

    /**
     * @var \Zend_Filter 
     */
    static protected $_encryptFilters;

    /**
     * @var \Zend_Filter 
     */
    static protected $_decryptFilters;

    static protected $_tableName = 'majistix_editing_content';

    /**
     * @desc Constructs the InPlaceEditing.
     */
    public function __construct($name, \Zend_Locale $locale, $options = array())
    {
        $this->name = $name;
        $this->locale = $locale->getLanguage();
    }

    static public function setTableName($tableName)
    {
        static::$_tableName = $tableName;
    }

    /**
     * @desc Maps this entity.
     *
     * @param ClassMetadata $metadata The metadata
     */
    public static function loadMetadata(ClassMetadata $metadata)
    {
        $metadata->mapField(array(
           'id' => true,
           'fieldName' => 'id',
           'type' => 'integer',
        ));

        $metadata->mapField(array(
           'fieldName' => 'name',
           'type' => 'string'
        ));

        $metadata->mapField(array(
           'fieldName' => 'content',
           'type' => 'text'
        ));

        $metadata->mapField(array(
           'fieldName' => 'locale',
           'type' => 'string'
        ));

        $metadata->setTableName(static::$_tableName);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_AUTO);
        $metadata->setCustomRepositoryClass(
            __NAMESPACE__ . '\ContentRepository');
    }

    /**
     * @desc Returns the key.
     *
     * @return string The Key
     */
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @desc Returns the content of this model from a given locale.
     *
     * @param $key The key
     * @param $locale [optionnal] The locale to fetch, if no locale is specified
     * the application's current locale is used.
     *
     * @return string The content
     */
    public function getContent()
    {
        if( empty($this->content) ) {
            return $this->content;
        }

        return $this->getDecryptFilters()->filter($this->content);
    }

    /**
     * @return \Zend_Filter 
     */
    static public function getEncryptFilters()
    {
        if( null === static::$_encryptFilters ) {
            static::$_encryptFilters = new \Zend_Filter();
        }

        return static::$_encryptFilters;
    }

    /**
     * @return \Zend_Filter 
     */
    static public function getDecryptFilters()
    {
        if( null === static::$_encryptFilters ) {
            static::$_decryptFilters = new \Zend_Filter();
        }

        return static::$_decryptFilters;
    }

    static public function setEncryptFilters(\Zend_Filter $chain)
    {
        static::$_encryptFilters = $chain;
    }

    static public function setDecryptFilters(\Zend_Filter $chain)
    {
        static::$_decryptFilters = $chain;
    }

    /**
     * @desc Edits the content of the storage model
     * from a given key, content and locale.
     *
     * @param $key The key
     * @param $content The content
     * @param $locale [optionnal] The locale. If no locale is specified the
     * application's current locale is used.
     *
     * @throws Majisti\Model\Storage\Exception if the storage model is read-only.
     *
     * @return InPlaceEditing this
     */
    public function setContent($content)
    {
        $this->content = $this->getEncryptFilters()->filter($content);

        return $this;
    }

    /**
     * @desc Returns the current locale if the locale is null.
     *
     * @return \Zend_Locale The locale
     */
    public function getLocale()
    {
        return new \Zend_Locale($this->locale);
    }

    public function setLocale(\Zend_Locale $locale)
    {
        $this->locale = $locale->getLanguage();

        return $this;
    }
}

class ContentRepository extends EntityRepository
{
    public function findOrCreate($name, \Zend_Locale $locale)
    {
        $criteria = array('name' => $name, 'locale' => $locale->getLanguage());

        if( !$entity = $this->findOneBy($criteria) ) {
            $entity = new Content($name, $locale);
        }

        return $entity;
    }
}
