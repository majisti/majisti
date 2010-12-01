<?php

namespace MajistiX\Extension\InPlaceEditing\Model;

use \Doctrine\ORM\Mapping\ClassMetadata,
    \MajistiX\Extension\InPlaceEditing\View;

/**
 * @desc InPlaceEditing entity model.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class InPlaceEditing
{
    /**
     * @var Editor\IEditor
     */
    protected $_editor;

    protected $id;

    protected $content;

    /**
     * @desc Constructs the InPlaceEditing.
     *
     * @param $editor The editor
     */
    public function __construct(View\Editor\IEditor $editor, $locale = null)
    {
        $this->setEditor($editor);
    }

    public static function loadMetadata(ClassMetadata $metadata)
    {
        $metadata->setTableName('myapp_inPlaceEditing');
        $metadata->mapField(array(
           'id' => true,
           'fieldName' => 'id',
           'type' => 'integer',
        ));

        $metadata->mapField(array(
           'fieldName' => 'content',
           'type' => 'string'
        ));

        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_AUTO);
    }

    /**
     * @desc Returns the key.
     *
     * @return string The Key
     */
    public function getKey()
    {
        return 'key';
    }

    /**
     * @desc Returns the editor.
     *
     * @return Editor\IEditor The editor
     */
    public function getEditor()
    {
        return $this->_editor;
    }

    /**
     * @desc Sets the editor.
     *
     * @param Editor\IEditor $editor The editor
     *
     * @return InPlaceEditing this
     */
    public function setEditor(View\Editor\IEditor $editor)
    {
        $this->_editor = $editor;

        return $this;
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
     * @desc Renders the content using the editor.
     */
    public function render()
    {
        return $this->getEditor()->render($this->getContent(),
            array('key' => $this->getKey()));
    }

    /**
     * @desc Returns the current locale if the locale is null.
     *
     * @param string $locale
     *
     * @return string The locale
     */
    protected function getLocale($locale)
    {
        if( null === $locale ) {
            $locale = \Majisti\Application\Locales::getInstance()->getCurrentLocale();
        }

        return $locale->toString();
    }
}
