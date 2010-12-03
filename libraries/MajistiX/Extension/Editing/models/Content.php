<?php

namespace MajistiX\Extension\Editing\Model;

use \Doctrine\ORM\Mapping\ClassMetadata,
    \MajistiX\Extension\Editing\View\Editor;

/**
 * @desc InPlaceEditing entity model.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Content
{
    /**
     * @var Editor\IEditor
     */
    protected $_editor;

    protected $id;

    protected $name;

    protected $content = '';

    protected $locale;

    protected $_options;

    static protected $_tableName = 'majistix_content';

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
        $metadata->setTableName(static::$_tableName);
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
           'type' => 'string'
        ));

        $metadata->mapField(array(
           'fieldName' => 'locale',
           'type' => 'string'
        ));

        $metadata->setCustomRepositoryClass(
                'MajistiX\Extension\Editing\Model\ContentRepository');

        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_AUTO);
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
        return $this->content;
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
        $this->content = $content;

        return $this;
    }

    /**
     * @desc Renders the content using a provided editor.
     */
    public function render(Editor\IEditor $editor)
    {
        return $editor->render($this->getContent(),
            array('key' => $this->getName()));
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

class ContentRepository extends \Doctrine\ORM\EntityRepository
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
